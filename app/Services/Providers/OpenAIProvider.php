<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;

class OpenAIProvider implements LLMProviderInterface
{
    public function getName(): string
    {
        return 'OpenAI GPT-4';
    }

    public function isAvailable(): bool
    {
        return false; // We'll implement this later
    }

    public function analyzeCode(string $code, array $options = []): array
    {
        return [
            'status' => 'error',
            'message' => 'OpenAI provider not implemented yet'
        ];
    }

    public function getUsageStats(): array
    {
        return [];
    }

    public function getCostPerRequest(): float
    {
        return 0.03; // GPT-4 cost estimate
    }

    public function getMaxTokens(): int
    {
        return 128000;
    }

    public function supportsStreaming(): bool
    {
        return true;
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-4',
            'gpt-4-turbo',
            'gpt-3.5-turbo',
        ];
    }

    public function getDefaultModel(): string
    {
        return 'gpt-4';
    }
}
