<?php

namespace App\Core\Communication\Providers\InApp;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use Illuminate\Support\Facades\Log;

class DatabaseInAppProvider implements CommunicationProviderInterface
{
    public function send(string $recipient, string $subject, string $body, array $metadata = []): CommunicationResponse
    {
        Log::info("[CommunicationCRM - DatabaseInAppProvider] In-App Notification created for recipient {$recipient}", [
            'subject' => $subject,
            'body' => $body,
        ]);

        return CommunicationResponse::success('INAPP-' . strtoupper(uniqid()));
    }

    public function getProviderName(): string
    {
        return 'database_in_app';
    }
}
