<?php

namespace App\Contracts;

interface LLMProviderInterface
{
    public function getName(): string;
    public function isAvailable(): bool;
    public function getAvailableModels(): array;
    public function analyzeCode(string $code, array $options = []): array;
    public function getUsageStats(): array;
    public function getCostPerRequest(): float;
    public function getMaxTokens(): int;
    public function supportsStreaming(): bool;
    public function getDefaultModel(): string;
}
