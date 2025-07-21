<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiProvider extends BaseProvider implements LLMProviderInterface
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

        $prompt = $this->buildStandardPrompt($code, $options);

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

    public function getCostPerRequest(): float
    {
        $model = $model ?? 'gemini-2.0-flash';
        return $this->models[$model]['cost_per_request'] ?? 0.001;
    }

    public function getMaxTokens(): int
    {
        $model = $model ?? 'gemini-2.0-flash';
        return $this->models[$model]['max_tokens'] ?? 1048576;
    }

    public function supportsStreaming(): bool
    {
        $model = $model ?? 'gemini-2.0-flash';
        return $this->models[$model]['streaming'] ?? true;
    }

    protected function extractSuggestions(string $analysis): array
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
