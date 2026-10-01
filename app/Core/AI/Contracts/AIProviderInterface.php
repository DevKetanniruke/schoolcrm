<?php

namespace App\Core\AI\Contracts;

use App\Core\AI\DTOs\AIResponse;

interface AIProviderInterface
{
    /**
     * Generate AI completion response based on prompt and options.
     */
    public function generateCompletion(string $prompt, array $options = []): AIResponse;

    /**
     * Get the human-readable provider name identifier.
     */
    public function getProviderName(): string;
}
