<?php

namespace App\Core\Communication\Providers\Sms;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use Illuminate\Support\Facades\Log;

class TwilioSmsProvider implements CommunicationProviderInterface
{
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse
    {
        // Simulated Twilio SMS API provider implementation
        if (empty($recipient)) {
            return CommunicationResponse::failure('Recipient phone number is invalid or missing.');
        }

        Log::info("[CommunicationCRM - TwilioSmsProvider] Twilio API call to {$recipient}", [
            'body' => $body,
        ]);

        return CommunicationResponse::success('TW-SMS-' . strtoupper(uniqid()), [
            'sid' => 'SM' . md5(uniqid()),
            'status' => 'queued',
            'to' => $recipient,
        ]);
    }

    public function getProviderName(): string
    {
        return 'twilio_sms';
    }
}
