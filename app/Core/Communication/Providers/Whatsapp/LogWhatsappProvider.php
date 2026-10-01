<?php

namespace App\Core\Communication\Providers\Whatsapp;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use Illuminate\Support\Facades\Log;

class LogWhatsappProvider implements CommunicationProviderInterface
{
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse
    {
        Log::info("[CommunicationCRM - LogWhatsappProvider] WhatsApp message sent to {$recipient}", [
            'subject' => $subject,
            'body' => $body,
            'metadata' => $metadata,
        ]);

        return CommunicationResponse::success('WA-LOG-' . uniqid());
    }

    public function getProviderName(): string
    {
        return 'log_whatsapp';
    }
}
