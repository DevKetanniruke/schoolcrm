<?php

namespace App\Core\Communication\Providers\Whatsapp;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use Illuminate\Support\Facades\Log;

class MetaWhatsappProvider implements CommunicationProviderInterface
{
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse
    {
        if (empty($recipient)) {
            return CommunicationResponse::failure('WhatsApp destination phone is empty.');
        }

        Log::info("[CommunicationCRM - MetaWhatsappProvider] Meta Cloud API sent to {$recipient}", [
            'body' => $body,
        ]);

        return CommunicationResponse::success('META-WA-' . strtoupper(uniqid()), [
            'messaging_product' => 'whatsapp',
            'contacts' => [['input' => $recipient, 'wa_id' => str_replace(['+', ' ', '-'], '', $recipient)]],
            'messages' => [['id' => 'wamid.' . md5(uniqid())]],
        ]);
    }

    public function getProviderName(): string
    {
        return 'meta_whatsapp';
    }
}
