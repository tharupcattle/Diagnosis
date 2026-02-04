<?php

namespace App\Http\Controllers;

use App\Models\Cattle;
use App\Models\Alert;
use App\Services\HealthAnalysisService;
use App\Services\GeminiChatService;
use App\Services\ClaudeHealthService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $geminiService;
    protected $claudeService;

    public function __construct(GeminiChatService $gemini, ClaudeHealthService $claude)
    {
        $this->geminiService = $gemini;
        $this->claudeService = $claude;
    }

    public function index()
    {
        $cattle = Cattle::with(['latestLog', 'unresolvedAlerts', 'latestMilkProduction'])->get();
        $alerts = Alert::where('is_resolved', false)->with('cattle')->latest()->get();
        $diagnosis = session('diagnosis');

        // Calculate aggregate statistics
        $stats = [
            'total_cattle' => $cattle->count(),
            'healthy_cattle' => $cattle->where('health_score', '>=', 70)->count(),
            'at_risk' => $cattle->where('health_score', '<', 70)->count(),
            'critical' => $cattle->where('health_score', '<', 40)->count(),
            'avg_health_score' => round($cattle->avg('health_score'), 1),
            'avg_milk_yield' => round($cattle->avg('average_daily_yield'), 1),
        ];

        // Prepare chart data for last 7 days
        $milkTrends = \App\Models\MilkProduction::selectRaw('production_date, SUM(total_daily_yield) as total')
            ->groupBy('production_date')
            ->orderBy('production_date', 'desc')
            ->take(7)
            ->get()
            ->reverse();

        $healthDistribution = [
            'Optimal' => $stats['healthy_cattle'],
            'Caution' => $stats['at_risk'] - $stats['critical'],
            'Critical' => $stats['critical']
        ];

        return view('dashboard', compact('cattle', 'alerts', 'diagnosis', 'stats', 'milkTrends', 'healthDistribution'));
    }

    public function checkSymptom(Request $request)
    {
        $symptom = $request->input('symptom');

        // Use Gemini AI for symptom analysis
        $geminiResponse = $this->geminiService->analyzeSymptom($symptom);

        $diagnosis = [
            'symptom' => $symptom,
            'advice' => $geminiResponse['response'] ?? 'Unable to analyze symptom.',
            'ai_powered' => $geminiResponse['status'] === 'success'
        ];

        return redirect()->route('dashboard')->with('diagnosis', $diagnosis);
    }

    public function simulate()
    {
        $cattle = Cattle::inRandomOrder()->first();
        if (!$cattle) {
            return redirect()->route('dashboard')->with('error', 'No cattle available');
        }

        // Create a critical health log
        $cattle->healthLogs()->create([
            'temperature' => rand(395, 415) / 10, // 39.5-41.5°C
            'ph_level' => rand(50, 55) / 10, // 5.0-5.5 (acidosis)
            'rumination_rate' => rand(20, 35), // Low rumination
            'activity_level' => ['low', 'very_low'][rand(0, 1)],
            'heart_rate' => rand(85, 105),
        ]);

        return redirect()->route('dashboard')->with('success', 'Critical health issue simulated for ' . $cattle->name);
    }

    /**
     * Show individual cattle details with AI insights
     */
    public function showCattle($id)
    {
        $cattle = Cattle::with([
            'healthLogs' => function ($q) {
                $q->orderBy('created_at', 'desc')->take(30);
            },
            'milkProduction' => function ($q) {
                $q->orderBy('production_date', 'desc')->take(30);
            },
            'alerts'
        ])->findOrFail($id);

        // Get Claude AI analysis
        $claudeAnalysis = $this->claudeService->analyzeHealthTrends($cattle);
        $feedOptimization = $this->claudeService->getFeedOptimization($cattle);

        return view('cattle-details', compact('cattle', 'claudeAnalysis', 'feedOptimization'));
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(Request $request, $id)
    {
        $alert = Alert::findOrFail($id);
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $request->input('resolved_by', 'Farmer')
        ]);

        return redirect()->back()->with('success', 'Alert resolved successfully');
    }

    /**
     * Get AI chat response
     */
    public function aiChat(Request $request)
    {
        $message = $request->input('message');
        $response = $this->geminiService->askQuestion($message);

        return response()->json($response);
    }

    /**
     * Analytics dashboard
     */
    public function analytics()
    {
        $cattle = Cattle::with(['healthLogs', 'milkProduction'])->get();

        // Prepare data for charts
        $healthTrends = [];
        $milkTrends = [];

        return view('analytics', compact('cattle', 'healthTrends', 'milkTrends'));
    }
}