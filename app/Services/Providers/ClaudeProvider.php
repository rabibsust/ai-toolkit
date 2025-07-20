<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;

class ClaudeProvider implements LLMProviderInterface
{
    public function getName(): string
    {
        return 'Claude AI';
    }

    public function isAvailable(): bool
    {
        return false; // We'll implement this later
    }

    public function analyzeCode(string $code, array $options = []): array
    {
        return [
            'status' => 'error',
            'message' => 'Claude provider not implemented yet'
        ];
    }

    public function getUsageStats(): array
    {
        return [];
    }

    public function getCostPerRequest(): float
    {
        return 0.03; // Claude cost estimate
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
            'claude-3-opus-20240229',
            'claude-3-sonnet-20240229',
            'claude-3-haiku-20240307',
            'claude-2.1',
            'claude-2.0',
            'claude-instant-1.2'
        ];
    }

    public function getDefaultModel(): string
    {
        return 'claude-3-opus-20240229';
    }
}
