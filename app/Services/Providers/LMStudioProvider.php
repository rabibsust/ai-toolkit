<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;

class LMStudioProvider implements LLMProviderInterface
{
    public function getName(): string
    {
        return 'LM Studio';
    }

    public function isAvailable(): bool
    {
        return false; // We'll implement this later
    }

    public function analyzeCode(string $code, array $options = []): array
    {
        return [
            'status' => 'error',
            'message' => 'LM Studio provider not implemented yet'
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
            'default' => 'Default LM Studio Model'
        ];
    }

    public function getDefaultModel(): string
    {
        return 'default';
    }
}
