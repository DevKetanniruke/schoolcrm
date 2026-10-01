<?php

namespace App\Core\Communication\Contracts;

use App\Core\Communication\DTOs\CommunicationResponse;

interface CommunicationProviderInterface
{
    /**
     * Send a communication message via the provider driver.
     */
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse;

    /**
     * Get the unique name identifier of the provider.
     */
    public function getProviderName(): string;
}
