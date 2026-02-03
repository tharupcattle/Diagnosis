<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\HealthLog;
use App\Models\MilkProduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IoTController extends Controller
{
    /**
     * Store health log data from IoT sensors
     * POST /api/iot/health-log
     */
    public function storeHealthLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cattle_id' => 'required|string|exists:cattle,tag_id',
            'ph_level' => 'required|numeric|between:4.0,8.0',
            'temperature' => 'required|numeric|between:35.0,42.0',
            'rumination_rate' => 'nullable|integer|between:0,100',
            'activity_level' => 'nullable|in:low,normal,high',
            'heart_rate' => 'nullable|integer|between:40,120',
            'sensor_id' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cattle = Cattle::where('tag_id', $request->cattle_id)->first();

        $healthLog = $cattle->healthLogs()->create([
            'temperature' => $request->temperature,
            'ph_level' => $request->ph_level,
            'rumination_rate' => $request->rumination_rate ?? null,
            'activity_level' => $request->activity_level ?? 'normal',
            'heart_rate' => $request->heart_rate ?? null,
        ]);

        // Observer automatically calculates health score and creates alerts

        return response()->json([
            'success' => true,
            'message' => 'Health log recorded successfully',
            'health_score' => $healthLog->fresh()->health_score,
            'risk_level' => $cattle->fresh()->risk_level,
            'alerts' => $cattle->unresolvedAlerts()->get(),
        ], 201);
    }

    /**
     * Store milk production data
     * POST /api/iot/milk-production
     */
    public function storeMilkProduction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cattle_id' => 'required|string|exists:cattle,tag_id',
            'production_date' => 'required|date',
            'morning_yield' => 'required|numeric|min:0',
            'evening_yield' => 'required|numeric|min:0',
            'milk_fat_percentage' => 'nullable|numeric|between:0,10',
            'milk_protein_percentage' => 'nullable|numeric|between:0,10',
            'lactation_day' => 'nullable|integer|min:0',
            'lactation_stage' => 'nullable|in:early,peak,mid,late',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cattle = Cattle::where('tag_id', $request->cattle_id)->first();

        $production = $cattle->milkProduction()->updateOrCreate(
            [
                'production_date' => $request->production_date
            ],
            [
                'morning_yield' => $request->morning_yield,
                'evening_yield' => $request->evening_yield,
                'milk_fat_percentage' => $request->milk_fat_percentage,
                'milk_protein_percentage' => $request->milk_protein_percentage,
                'lactation_day' => $request->lactation_day,
                'lactation_stage' => $request->lactation_stage,
            ]
        );

        // Update cattle's average yield
        $avgYield = $cattle->calculateAverageDailyYield(30);
        $cattle->update(['average_daily_yield' => $avgYield]);

        return response()->json([
            'success' => true,
            'message' => 'Milk production recorded successfully',
            'total_daily_yield' => $production->total_daily_yield,
            'average_30day_yield' => $avgYield,
        ], 201);
    }

    /**
     * Bulk import health logs
     * POST /api/iot/bulk-health-logs
     */
    public function bulkHealthLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logs' => 'required|array',
            'logs.*.cattle_id' => 'required|exists:cattle,tag_id',
            'logs.*.ph_level' => 'required|numeric|between:4.0,8.0',
            'logs.*.temperature' => 'required|numeric|between:35.0,42.0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $imported = 0;
        $errors = [];

        foreach ($request->logs as $logData) {
            try {
                $cattle = Cattle::where('tag_id', $logData['cattle_id'])->first();
                $cattle->healthLogs()->create($logData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Failed to import log for {$logData['cattle_id']}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
            'total' => count($request->logs),
            'errors' => $errors
        ]);
    }

    /**
     * Get cattle health status
     * GET /api/iot/cattle/{tag_id}/status
     */
    public function getCattleStatus($tagId)
    {
        $cattle = Cattle::where('tag_id', $tagId)
            ->with(['latestLog', 'unresolvedAlerts', 'latestMilkProduction'])
            ->first();

        if (!$cattle) {
            return response()->json([
                'success' => false,
                'message' => 'Cattle not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cattle' => [
                'name' => $cattle->name,
                'tag_id' => $cattle->tag_id,
                'breed' => $cattle->breed,
                'age' => $cattle->age,
                'health_score' => $cattle->health_score,
                'risk_level' => $cattle->risk_level,
                'latest_vitals' => [
                    'ph_level' => $cattle->latestLog->ph_level ?? null,
                    'temperature' => $cattle->latestLog->temperature ?? null,
                    'rumination_rate' => $cattle->latestLog->rumination_rate ?? null,
                ],
                'active_alerts' => $cattle->unresolvedAlerts->map(fn($alert) => [
                    'type' => $alert->type,
                    'severity' => $alert->severity,
                    'description' => $alert->description,
                ]),
                'milk_production' => [
                    'latest_yield' => $cattle->latestMilkProduction->total_daily_yield ?? null,
                    'average_yield' => $cattle->average_daily_yield,
                ],
            ]
        ]);
    }
}
