<?php

namespace App\Core\Communication\Providers\Sms;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use Illuminate\Support\Facades\Log;

class LogSmsProvider implements CommunicationProviderInterface
{
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse
    {
        Log::info("[CommunicationCRM - LogSmsProvider] SMS dispatched to {$recipient}", [
            'subject' => $subject,
            'body' => $body,
            'metadata' => $metadata,
        ]);

        return CommunicationResponse::success('SMS-LOG-' . uniqid());
    }

    public function getProviderName(): string
    {
        return 'log_sms';
    }
}
