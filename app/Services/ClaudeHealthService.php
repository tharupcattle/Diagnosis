<?php

namespace App\Services;

use App\Models\Cattle;
use App\Models\HealthLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeHealthService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = env('CLAUDE_API_KEY');
    }

    /**
     * Advanced health analysis using Claude AI
     */
    public function analyzeHealthTrends(Cattle $cattle)
    {
        $recentLogs = $cattle->healthLogs()
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        if ($recentLogs->isEmpty()) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 7 days of health data for trend analysis'
            ];
        }

        $healthData = $recentLogs->map(fn($log) => [
            'date' => $log->created_at->format('Y-m-d'),
            'ph_level' => $log->ph_level,
            'rumination_rate' => $log->rumination_rate,
            'temperature' => $log->temperature,
            'health_score' => $log->health_score,
            'activity_level' => $log->activity_level,
        ])->toArray();

        $prompt = $this->buildHealthAnalysisPrompt($cattle, $healthData);

        return $this->callClaudeAPI($prompt);
    }

    /**
     * Get feed optimization recommendations
     */
    public function getFeedOptimization(Cattle $cattle)
    {
        $latestLog = $cattle->latestLog;
        $milkProduction = $cattle->milkProduction()
            ->orderBy('production_date', 'desc')
            ->take(7)
            ->get();

        $prompt = $this->buildFeedOptimizationPrompt($cattle, $latestLog, $milkProduction);

        return $this->callClaudeAPI($prompt);
    }

    /**
     * Predict milk yield based on health parameters
     */
    public function predictMilkYield(Cattle $cattle)
    {
        $healthLogs = $cattle->healthLogs()->orderBy('created_at', 'desc')->take(14)->get();
        $milkProduction = $cattle->milkProduction()->orderBy('production_date', 'desc')->take(14)->get();

        $prompt = $this->buildMilkPredictionPrompt($cattle, $healthLogs, $milkProduction);

        return $this->callClaudeAPI($prompt);
    }

    /**
     * Build health analysis prompt for Claude
     */
    protected function buildHealthAnalysisPrompt(Cattle $cattle, array $healthData)
    {
        return "You are a veterinary AI specialist analyzing cattle health data.

Cattle: {$cattle->name} ({$cattle->breed}, {$cattle->age} years old)

Health Data (Last 7 Days):
" . json_encode($healthData, JSON_PRETTY_PRINT) . "

Analyze:
1. Overall health trend (improving/stable/declining)
2. Specific risk factors detected
3. Predicted issues in next 24-48 hours
4. Immediate recommendations
5. Long-term prevention strategies

Provide concise, actionable advice for a farmer. Focus on preventing acidosis, bloat, and maximizing milk production.";
    }

    /**
     * Build feed optimization prompt
     */
    protected function buildFeedOptimizationPrompt(Cattle $cattle, $healthLog, $milkProduction)
    {
        $currentYield = $milkProduction->isNotEmpty() ? $milkProduction->avg('total_daily_yield') : 0;

        return "You are a dairy nutrition expert optimizing feed for maximum milk yield.

Cattle: {$cattle->name} ({$cattle->breed}, Lactation Day: " . ($cattle->getLactationDay() ?? 'Unknown') . ")

Current Health:
- pH Level: {$healthLog->ph_level}
- Rumination: {$healthLog->rumination_rate}/min
- Temperature: {$healthLog->temperature}°C
- Health Score: {$healthLog->health_score}/100

Current Milk Yield: {$currentYield}L/day
Expected Yield: {$cattle->expected_yield}L/day
Yield Gap: " . ($cattle->expected_yield - $currentYield) . "L/day

Provide:
1. Specific feed adjustments (fiber %, grain %, supplements)
2. Feeding schedule optimization
3. Expected milk yield improvement
4. Timeline for results
5. ROI calculation

Be specific with quantities and brands if possible.";
    }

    /**
     * Build milk yield prediction prompt
     */
    protected function buildMilkPredictionPrompt(Cattle $cattle, $healthLogs, $milkProduction)
    {
        return "You are a predictive analytics expert for dairy farming.

Cattle: {$cattle->name}
Historical data: 14 days of health logs and milk production

Predict:
1. Milk yield for next 7 days
2. Probability of yield drop
3. Health risks that could impact production
4. Preventive actions to maintain/boost yield

Provide numeric predictions with confidence levels.";
    }

    /**
     * Call Claude API
     */
    protected function callClaudeAPI(string $prompt)
    {
        if (!$this->apiKey) {
            return [
                'status' => 'error',
                'message' => 'Claude API key not configured. Add CLAUDE_API_KEY to .env file.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->apiUrl, [
                        'model' => 'claude-3-5-sonnet-20241022',
                        'max_tokens' => 1024,
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ]
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => 'success',
                    'analysis' => $data['content'][0]['text'] ?? 'No response',
                    'model' => $data['model'] ?? 'claude-3-5-sonnet',
                ];
            }

            return [
                'status' => 'error',
                'message' => 'API request failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Claude API Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}
