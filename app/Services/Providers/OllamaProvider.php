<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;
use Illuminate\Support\Facades\Http;

class OllamaProvider extends BaseProvider implements LLMProviderInterface
{
    private array $models = [
        'deepseek-r1:8b' => [
            'name' => 'Deepseek R1 8B',
            'description' => 'DeepSeek-R1 is a family of open reasoning models. It is designed to be a versatile model for various tasks, including code analysis.',
            'cost_per_request' => 0.0, // Free!
            'max_tokens' => 4096,
            'streaming' => true,
            'size' => '4GB'
        ],
        'qwen2.5-coder:7b' => [
            'name' => 'Qwen2.5-Coder 7B',
            'description' => 'Best overall - PHP, JavaScript, React, Vue, Node.js specialist',
            'cost_per_request' => 0.0,
            'max_tokens' => 32768,
            'streaming' => true,
            'size' => '4.7GB',
            'languages' => ['PHP', 'JavaScript', 'TypeScript', 'Vue', 'React', 'Node.js', 'Laravel']
        ],
        'deepseek-coder:6.7b' => [
            'name' => 'DeepSeek-Coder 6.7B',
            'description' => 'Efficient full-stack development with React/Vue expertise',
            'cost_per_request' => 0.0,
            'max_tokens' => 16384,
            'streaming' => true,
            'size' => '3.4GB',
            'languages' => ['PHP', 'JavaScript', 'TypeScript', 'React', 'Vue', 'Node.js']
        ],
        'codellama:7b' => [
            'name' => 'CodeLlama 7B',
            'description' => 'Security-focused with strong JavaScript support',
            'cost_per_request' => 0.0,
            'max_tokens' => 16384,
            'streaming' => true,
            'size' => '4.0GB',
            'languages' => ['PHP', 'JavaScript', 'TypeScript', 'Python', 'C++']
        ],
        'codegemma:7b' => [
            'name' => 'CodeGemma 7B',
            'description' => 'Google-optimized for JavaScript frameworks and PHP',
            'cost_per_request' => 0.0,
            'max_tokens' => 8192,
            'streaming' => true,
            'size' => '4.2GB',
            'languages' => ['JavaScript', 'TypeScript', 'PHP', 'Python', 'Go']
        ]
    ];

    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://127.0.0.1:11434');
    }

    public function getName(): string
    {
        return 'Ollama (Local AI)';
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/tags');

            if ($response->successful()) {
                // Check if we have any models installed
                $models = $response->json('models', []);
                return count($models) > 0;
            }

            return false;
        } catch (\Exception $e) {
            \Log::info('Ollama not available: ' . $e->getMessage());
            return false;
        }
    }

    public function getAvailableModels(): array
    {
        try {
            // Get installed models from Ollama
            $response = Http::timeout(10)->get($this->baseUrl . '/api/tags');

            if (!$response->successful()) {
                return [];
            }

            $installedModels = collect($response->json('models', []))
                ->pluck('name')
                ->toArray();

            // Return only models that are both defined and installed
            return collect($this->models)
                ->filter(function ($model, $name) use ($installedModels) {
                    return in_array($name, $installedModels);
                })
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Failed to get Ollama models: ' . $e->getMessage());
            return [];
        }
    }

    public function analyzeCode(string $code, array $options = []): array
    {
        $model = $options['model'] ?? 'deepseek-r1:8b';

        // Validate model is available
        $availableModels = $this->getAvailableModels();
        if (!isset($availableModels[$model])) {
            return [
                'status' => 'error',
                'provider' => 'ollama',
                'message' => "Model {$model} is not installed. Run: ollama pull {$model}"
            ];
        }

        $prompt = $this->buildStandardPrompt($code, $options);

        try {
            $startTime = microtime(true);

            $response = Http::timeout(120) // Local models can be slower
                ->post($this->baseUrl . '/api/generate', [
                    'model' => $model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.3, // Lower for more consistent analysis
                        'top_p' => 0.9,
                        'num_predict' => 2048, // Limit response length
                    ]
                ]);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds

            if (!$response->successful()) {
                return [
                    'status' => 'error',
                    'provider' => 'ollama',
                    'model' => $model,
                    'message' => 'Ollama API error: ' . $response->body()
                ];
            }

            $data = $response->json();
            $analysisText = $data['response'] ?? '';

            if (empty($analysisText)) {
                return [
                    'status' => 'error',
                    'provider' => 'ollama',
                    'model' => $model,
                    'message' => 'Empty response from Ollama'
                ];
            }

            return [
                'status' => 'success',
                'provider' => 'ollama',
                'model' => $model,
                'model_name' => $this->models[$model]['name'] ?? $model,
                'analysis' => $analysisText,
                'suggestions' => $this->extractSuggestions($analysisText),
                'score' => $this->calculateScore($analysisText),
                'cost' => 0.0, // Always free!
                'tokens_used' => strlen($analysisText) / 4,
                'response_time_ms' => $responseTime,
                'local' => true // Flag to show it's local
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'provider' => 'ollama',
                'model' => $model,
                'message' => 'Connection error: ' . $e->getMessage() . '. Is Ollama running?'
            ];
        }
    }

    public function getUsageStats(): array
    {
        return [
            'requests_today' => 0,
            'tokens_used' => 0,
            'cost_today' => 0.0, // Always free!
        ];
    }

    public function getCostPerRequest(): float
    {
        return 0.0; // Always free!
    }

    public function getMaxTokens(): int
    {
        $model = $model ?? 'deepseek-r1:8b';
        return $this->models[$model]['max_tokens'] ?? 4096;
    }

    public function supportsStreaming(): bool
    {
        return true;
    }

    public function getDefaultModel(): string
    {
        return 'deepseek-r1:8b';
    }
}
