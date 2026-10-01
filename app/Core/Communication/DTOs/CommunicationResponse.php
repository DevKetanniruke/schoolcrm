<?php

namespace App\Core\Communication\DTOs;

class CommunicationResponse
{
    public function __construct(
        public bool $success,
        public ?string $messageId = null,
        public ?string $status = 'sent',
        public ?string $errorMessage = null,
        public array $rawResponse = []
    ) {}

    public static function success(?string $messageId = null, array $rawResponse = []): self
    {
        return new self(
            success: true,
            messageId: $messageId ?? 'MSG-' . strtoupper(uniqid()),
            status: 'sent',
            errorMessage: null,
            rawResponse: $rawResponse
        );
    }

    public static function failure(string $errorMessage, array $rawResponse = []): self
    {
        return new self(
            success: false,
            messageId: null,
            status: 'failed',
            errorMessage: $errorMessage,
            rawResponse: $rawResponse
        );
    }
}
