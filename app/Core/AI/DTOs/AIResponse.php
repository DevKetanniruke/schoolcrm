<?php

namespace App\Core\AI\DTOs;

class AIResponse
{
    public function __construct(
        public string $content,
        public bool $success = true,
        public int $promptTokens = 0,
        public int $completionTokens = 0,
        public int $totalTokens = 0,
        public float $costEstimate = 0.0,
        public ?string $errorMessage = null,
        public array $suggestedActions = []
    ) {
        if ($this->totalTokens === 0) {
            $this->totalTokens = $this->promptTokens + $this->completionTokens;
        }
    }

    public static function makeSuccess(
        string $content,
        int $promptTokens = 120,
        int $completionTokens = 180,
        array $suggestedActions = []
    ): self {
        return new self(
            content: $content,
            success: true,
            promptTokens: $promptTokens,
            completionTokens: $completionTokens,
            totalTokens: $promptTokens + $completionTokens,
            costEstimate: round(($promptTokens + $completionTokens) * 0.000002, 6),
            suggestedActions: $suggestedActions
        );
    }

    public static function makeError(string $errorMessage): self
    {
        return new self(
            content: "AI Assistant Error: {$errorMessage}",
            success: false,
            errorMessage: $errorMessage
        );
    }
}
