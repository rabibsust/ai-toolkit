<?php

namespace App\Services;

use App\Contracts\LLMProviderInterface;
use App\Services\Providers\GeminiProvider;
use App\Services\Providers\OpenAIProvider;
use App\Services\Providers\ClaudeProvider;
use App\Services\Providers\LMStudioProvider;
use App\Services\Providers\OllamaProvider;

class LLMProviderFactory
{
    private array $providers = [];

    public function __construct()
    {
        $this->registerProviders();
    }

    private function registerProviders(): void
    {
        // Define potential providers with their class names
        $potentialProviders = [
            'gemini' => GeminiProvider::class,
            'openai' => OpenAIProvider::class,
            'claude' => ClaudeProvider::class,
            'lmstudio' => LMStudioProvider::class,
            'ollama' => OllamaProvider::class,
        ];

        // Only register providers whose classes actually exist
        foreach ($potentialProviders as $name => $class) {
            if (class_exists($class)) {
                $this->providers[$name] = $class;
            }
        }
    }

    public function create(string $provider): LLMProviderInterface
    {
        if (!isset($this->providers[$provider])) {
            throw new \InvalidArgumentException("Provider {$provider} not found or not available");
        }

        return app($this->providers[$provider]);
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
                        'models' => $provider->getAvailableModels(), // Make sure this is included
                        'default_cost' => $provider->getCostPerRequest(),
                    ];

                    // Debug: Log what we're adding
                    \Log::info("Provider {$name} models:", $provider->getAvailableModels());
                }
            } catch (\Exception $e) {
                \Log::warning("Provider {$name} failed to load: " . $e->getMessage());
            }
        }

        // Debug: Log final structure
        \Log::info('Final available providers:', $available);

        return $available;
    }

    public function getRegisteredProviders(): array
    {
        return array_keys($this->providers);
    }

    public function hasProvider(string $provider): bool
    {
        return isset($this->providers[$provider]);
    }
}
