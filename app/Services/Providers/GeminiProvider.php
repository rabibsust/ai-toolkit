<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements LLMProviderInterface
{
    private array $models = [
        'gemini-2.5-pro' => [
            'name' => 'Gemini 2.5 Pro',
            'description' => 'Advanced model for professional use',
            'cost_per_request' => 0.0002,
            'max_tokens' => 1048576,
            'streaming' => true
        ],
        'gemini-2.5-flash' => [
            'name' => 'Gemini 2.5 Flash',
            'description' => 'Fast, cost-effective model for most tasks',
            'cost_per_request' => 0.0002,
            'max_tokens' => 1048576,
            'streaming' => true
        ],
        'gemini-2.0-flash' => [
            'name' => 'Gemini 2.0 Flash',
            'description' => 'Fast, cost-effective model for most tasks',
            'cost_per_request' => 0.001,
            'max_tokens' => 1048576,
            'streaming' => true
        ],
        'gemini-2.0-flash-exp' => [
            'name' => 'Gemini 2.0 Flash Experimental',
            'description' => 'Latest experimental features',
            'cost_per_request' => 0.001,
            'max_tokens' => 1048576,
            'streaming' => true
        ],
        'gemini-1.5-pro' => [
            'name' => 'Gemini 1.5 Pro',
            'description' => 'Advanced reasoning for complex tasks',
            'cost_per_request' => 0.01,
            'max_tokens' => 2097152,
            'streaming' => true
        ]
    ];

    public function getName(): string
    {
        return 'Google Gemini';
    }

    public function isAvailable(): bool
    {
        return !empty(config('gemini.api_key'));
    }

    public function getAvailableModels(): array
    {
        return $this->models;
    }

    public function analyzeCode(string $code, array $options = []): array
    {
        $model = $options['model'] ?? 'gemini-2.0-flash';

        // Validate model exists
        if (!isset($this->models[$model])) {
            $model = 'gemini-2.0-flash';
        }

        $prompt = $this->buildControllerAnalysisPrompt($code, $options);

        try {
            $result = Gemini::generativeModel($model)
                ->generateContent($prompt);

            $analysisText = $result->text();

            return [
                'status' => 'success',
                'provider' => 'gemini',
                'model' => $model,
                'model_name' => $this->models[$model]['name'],
                'analysis' => $analysisText,
                'suggestions' => $this->extractSuggestions($analysisText),
                'score' => $this->calculateScore($analysisText),
                'cost' => $this->getCostPerRequest($model),
                'tokens_used' => strlen($analysisText) / 4,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'provider' => 'gemini',
                'model' => $model,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUsageStats(): array
    {
        return [
            'requests_today' => 0,
            'tokens_used' => 0,
            'cost_today' => 0,
        ];
    }

    public function getCostPerRequest(string $model = null): float
    {
        $model = $model ?? 'gemini-2.0-flash';
        return $this->models[$model]['cost_per_request'] ?? 0.001;
    }

    public function getMaxTokens(string $model = null): int
    {
        $model = $model ?? 'gemini-2.0-flash';
        return $this->models[$model]['max_tokens'] ?? 1048576;
    }

    public function supportsStreaming(string $model = null): bool
    {
        $model = $model ?? 'gemini-2.0-flash';
        return $this->models[$model]['streaming'] ?? true;
    }

    // Move your existing private methods here
    private function buildControllerAnalysisPrompt(string $code, array $options = []): string
    {
        $focusArea = $options['focus'] ?? 'general';
        $detail = $options['detail'] ?? 'standard';

        return "
            You are an expert Laravel developer. Analyze this Laravel controller code and provide detailed feedback.

            Focus area: {$focusArea}
            Detail level: {$detail}

            CONTROLLER CODE:
            ```php
            {$code}
            Please provide analysis in this exact format:
            CODE QUALITY SCORE: [1-10]
            ISSUES FOUND:

            [List specific issues]

            SUGGESTIONS:

            [Specific improvements with code examples]

            BEST PRACTICES:

            [Laravel best practices to follow]

            SECURITY CONCERNS:

            [Any security issues found]

            Keep responses practical and actionable.```";
    }
    private function extractSuggestions(string $analysis): array
    {
        // Your existing implementation
        $suggestions = [];

        // Extract from SUGGESTIONS section
        if (preg_match('/SUGGESTIONS:(.*?)(?=BEST PRACTICES:|ISSUES FOUND:|SECURITY CONCERNS:|$)/s', $analysis, $matches)) {
            $suggestionsText = $matches[1];
            $suggestions = array_merge($suggestions, $this->parseBulletPoints($suggestionsText));
        }

        // Extract from BEST PRACTICES section
        if (preg_match('/BEST PRACTICES:(.*?)(?=SUGGESTIONS:|ISSUES FOUND:|SECURITY CONCERNS:|$)/s', $analysis, $matches)) {
            $practicesText = $matches[1];
            $bestPractices = $this->parseBulletPoints($practicesText);
            $suggestions = array_merge($suggestions, $bestPractices);
        }

        if (empty($suggestions)) {
            $suggestions = [
                'Consider adding proper validation to your methods',
                'Implement proper error handling and response codes',
                'Follow Laravel naming conventions and best practices'
            ];
        }

        return $suggestions;
    }

    private function parseBulletPoints(string $text): array
    {
        // Your existing implementation
        $points = [];
        $lines = explode("\n", $text);
        $currentPoint = '';
        $inCodeBlock = false;
        $collectingPoint = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if (str_starts_with($trimmedLine, '* ') || str_starts_with($trimmedLine, '- ')) {
                if (!empty($currentPoint) && $collectingPoint) {
                    $points[] = trim($currentPoint);
                }

                $currentPoint = $this->cleanBulletPoint($trimmedLine);
                $collectingPoint = true;
                $inCodeBlock = false;
            }
            elseif ($collectingPoint) {
                if (str_contains($trimmedLine, '```')) {
                    $inCodeBlock = !$inCodeBlock;
                    $currentPoint .= "\n" . $line;
                }
                elseif ($inCodeBlock || !empty($trimmedLine)) {
                    $currentPoint .= "\n" . $line;
                }
                elseif ($inCodeBlock) {
                    $currentPoint .= "\n" . $line;
                }
            }
        }

        if (!empty($currentPoint) && $collectingPoint) {
            $points[] = trim($currentPoint);
        }

        return array_filter($points);
    }

    private function cleanBulletPoint(string $line): string
    {
        $cleanLine = preg_replace('/^\*\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $line);
        $cleanLine = preg_replace('/^-\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $cleanLine);
        $cleanLine = preg_replace('/^\*\s*/', '', $cleanLine);
        $cleanLine = preg_replace('/^-\s*/', '', $cleanLine);

        return trim($cleanLine);
    }

    private function calculateScore(string $analysis): int
    {
        // Your existing implementation
        if (preg_match('/CODE QUALITY SCORE:\s*(\d+)/', $analysis, $matches)) {
            return (int)$matches[1];
        }

        $lowercaseAnalysis = strtolower($analysis);
        $score = 7;

        if (strpos($lowercaseAnalysis, 'sql injection') !== false) $score -= 2;
        if (strpos($lowercaseAnalysis, 'mass assignment') !== false) $score -= 1;
        if (strpos($lowercaseAnalysis, 'security') !== false) $score -= 1;
        if (strpos($lowercaseAnalysis, 'validation') !== false) $score -= 1;

        if (strpos($lowercaseAnalysis, 'eloquent') !== false) $score += 1;
        if (strpos($lowercaseAnalysis, 'middleware') !== false) $score += 1;

        return max(1, min(10, $score));
    }

    public function getAvailableProviders(): array
    {
        $available = [];
        foreach ($this->providers as $name => $class) {
            try {
                $provider = app($class);
                if ($provider->isAvailable()) {
                    $available[$name] = [
                        'name' => $provider->getName(),
                        'models' => $provider->getAvailableModels(),
                        'default_cost' => $provider->getCostPerRequest(),
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Provider {$name} failed to load: " . $e->getMessage());
            }
        }
        return $available;
    }

    public function getDefaultModel(): string
    {
        return 'gemini-2.0-flash';
    }
}
