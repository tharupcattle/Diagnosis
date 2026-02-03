<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Interactive symptom checker for farmers
     */
    public function analyzeSymptom(string $symptom)
    {
        $prompt = $this->buildSymptomPrompt($symptom);
        return $this->callGeminiAPI($prompt);
    }

    /**
     * General Q&A for cattle health
     */
    public function askQuestion(string $question, array $context = [])
    {
        $prompt = $this->buildQuestionPrompt($question, $context);
        return $this->callGeminiAPI($prompt);
    }

    /**
     * Get educational content about cattle care
     */
    public function getEducationalContent(string $topic)
    {
        $prompt = "You are a cattle farming educator. Explain {$topic} in simple terms for farmers.
        
Include:
1. What it is and why it matters
2. How to identify it
3. Prevention strategies
4. Cost-effective solutions

Keep it practical and actionable. Use simple language.";

        return $this->callGeminiAPI($prompt);
    }

    /**
     * Build symptom analysis prompt
     */
    protected function buildSymptomPrompt(string $symptom)
    {
        return "You are a friendly AI veterinary assistant helping farmers diagnose cattle health issues.

Farmer's observation: \"{$symptom}\"

Provide:
1. **Likely Issue**: What this symptom indicates
2. **Severity**: How urgent is this? (🟢 Mild / 🟡 Moderate / 🔴 Critical)
3. **Immediate Actions**: 3-4 simple steps the farmer should take RIGHT NOW
4. **When to Call Vet**: Specific signs that require professional help
5. **Prevention**: How to avoid this in future

Be conversational, supportive, and clear. Remember, the farmer may not have advanced medical knowledge.";
    }

    /**
     * Build Q&A prompt
     */
    protected function buildQuestionPrompt(string $question, array $context)
    {
        $contextStr = !empty($context) ? "\n\nContext:\n" . json_encode($context, JSON_PRETTY_PRINT) : '';

        return "You are a helpful cattle farming expert assistant. Answer the farmer's question clearly and practically.

Question: {$question}{$contextStr}

Provide a clear, actionable answer. Include examples if helpful. Keep it concise but complete.";
    }

    /**
     * Call Gemini API
     */
    protected function callGeminiAPI(string $prompt)
    {
        if (!$this->apiKey) {
            return [
                'status' => 'error',
                'message' => 'Gemini API key not configured. Add GEMINI_API_KEY to .env file.',
                'response' => 'Please configure the Gemini API to use this feature.'
            ];
        }

        try {
            $response = Http::post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

                return [
                    'status' => 'success',
                    'response' => $text,
                    'model' => 'gemini-pro'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'API request failed: ' . $response->body(),
                'response' => 'Unable to analyze symptom at this time. Please try again or consult a veterinarian.'
            ];

        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
                'response' => 'Service temporarily unavailable. Please try again.'
            ];
        }
    }

    /**
     * Chat interface for conversational AI
     */
    public function chat(array $messages)
    {
        if (!$this->apiKey) {
            return [
                'status' => 'error',
                'response' => 'API not configured'
            ];
        }

        try {
            $contents = collect($messages)->map(function ($msg) {
                return [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]]
                ];
            })->toArray();

            $response = Http::post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => $contents
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => 'success',
                    'response' => $data['candidates'][0]['content']['parts'][0]['text'] ?? ''
                ];
            }

            return ['status' => 'error', 'response' => 'Failed to get response'];

        } catch (\Exception $e) {
            Log::error('Gemini Chat Error: ' . $e->getMessage());
            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }
}
