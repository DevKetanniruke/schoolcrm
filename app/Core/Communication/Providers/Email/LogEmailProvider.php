<?php

namespace App\Core\Communication\Providers\Email;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use Illuminate\Support\Facades\Log;

class LogEmailProvider implements CommunicationProviderInterface
{
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse
    {
        Log::info("[CommunicationCRM - LogEmailProvider] Email sent to {$recipient}", [
            'subject' => $subject,
            'body' => $body,
            'metadata' => $metadata,
        ]);

        return CommunicationResponse::success('EMAIL-LOG-' . uniqid());
    }

    public function getProviderName(): string
    {
        return 'log_email';
    }
}
