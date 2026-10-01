<?php

namespace App\Core\Services;

use App\Jobs\DispatchBulkNotificationJob;
use App\Jobs\DispatchNotificationJob;
use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\NotificationPreference;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CentralizedNotificationEngine
{
    /**
     * Dispatch notification payload to one or multiple recipients asynchronously via Laravel Queue.
     */
    public function dispatchNotification(array $data, array $recipients, array $channels = ['email']): Communication
    {
        $sender = Auth::user();
        $schoolId = $data['school_id'] ?? $sender?->school_id ?? 1;

        $template = null;
        if (! empty($data['template_id'])) {
            $template = CommunicationTemplate::find($data['template_id']);
        }

        $scheduledAt = ! empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null;

        $communication = Communication::create([
            'school_id' => $schoolId,
            'sender_id' => $sender?->id,
            'template_id' => $template?->id,
            'title' => $data['title'] ?? $template?->title ?? 'System Notification',
            'channel' => count($channels) === 1 ? $channels[0] : 'multi_channel',
            'recipient_type' => $data['recipient_type'] ?? 'individual',
            'subject' => $data['subject'] ?? $template?->subject ?? $data['title'] ?? 'Notification',
            'message_body' => $data['message_body'] ?? $template?->content_body ?? '',
            'payload_data_json' => $data['payload'] ?? null,
            'scheduled_at' => $scheduledAt,
            'status' => 'queued',
            'total_recipients' => count($recipients),
            'success_count' => 0,
            'failure_count' => 0,
        ]);

        // Non-blocking asynchronous queue dispatch
        $bulkJob = new DispatchBulkNotificationJob($communication, $recipients, $channels);

        if ($scheduledAt && $scheduledAt->isFuture()) {
            dispatch($bulkJob)->delay($scheduledAt);
        } else {
            dispatch($bulkJob);
        }

        return $communication;
    }

    /**
     * Process single channel dispatch inside queue worker.
     */
    public function processSingleChannel(Communication $communication, array $recipient, string $channel): void
    {
        $schoolId = $communication->school_id;
        $recipientId = $recipient['id'] ?? null;
        $recipientType = $recipient['type'] ?? 'user';
        $email = $recipient['email'] ?? null;
        $phone = $recipient['phone'] ?? null;
        $name = $recipient['name'] ?? 'Valued User';

        // 1. Check Notification Preferences
        if (! $this->isChannelAllowed($schoolId, $recipientType, $recipientId, $channel, $communication->template?->category ?? 'general')) {
            CommunicationLog::create([
                'communication_id' => $communication->id,
                'school_id' => $schoolId,
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'recipient_email' => $email,
                'recipient_phone' => $phone,
                'channel' => $channel,
                'provider_name' => $this->getProviderName($channel),
                'delivery_status' => 'skipped_by_preference',
                'failure_reason' => "Recipient opted out of {$channel} notifications.",
                'last_attempt_at' => now(),
            ]);

            return;
        }

        // 2. Render Template Variables
        $renderedBody = $this->renderContent($communication->message_body, $recipient);
        $renderedSubject = $this->renderContent($communication->subject ?? 'Notification', $recipient);

        // 3. Dispatch Channel Delivery
        try {
            $providerMessageId = $this->deliverViaChannel($channel, $email, $phone, $renderedSubject, $renderedBody);

            CommunicationLog::create([
                'communication_id' => $communication->id,
                'school_id' => $schoolId,
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'recipient_email' => $email,
                'recipient_phone' => $phone,
                'channel' => $channel,
                'provider_name' => $this->getProviderName($channel),
                'provider_message_id' => $providerMessageId,
                'delivery_status' => 'delivered',
                'delivered_at' => now(),
                'last_attempt_at' => now(),
            ]);

            $communication->increment('success_count');
        } catch (\Throwable $e) {
            Log::error("Centralized Notification Engine Error [{$channel}]: " . $e->getMessage());

            CommunicationLog::create([
                'communication_id' => $communication->id,
                'school_id' => $schoolId,
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'recipient_email' => $email,
                'recipient_phone' => $phone,
                'channel' => $channel,
                'provider_name' => $this->getProviderName($channel),
                'delivery_status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'retry_count' => 1,
                'max_retries' => 3,
                'last_attempt_at' => now(),
            ]);

            $communication->increment('failure_count');
        }
    }

    /**
     * Retry failed notification log.
     */
    public function retryFailedLog(CommunicationLog $log): bool
    {
        if ($log->retry_count >= $log->max_retries) {
            return false;
        }

        $log->increment('retry_count');
        $log->update(['last_attempt_at' => now()]);

        $recipient = [
            'id' => $log->recipient_id,
            'type' => $log->recipient_type,
            'email' => $log->recipient_email,
            'phone' => $log->recipient_phone,
            'name' => 'Recipient',
        ];

        DispatchNotificationJob::dispatch(
            $log->communication,
            $recipient,
            $log->channel
        );

        return true;
    }

    /**
     * Mark job failed after queue retries exhausted.
     */
    public function markJobFailed(Communication $communication, array $recipient, string $channel, string $error): void
    {
        CommunicationLog::create([
            'communication_id' => $communication->id,
            'school_id' => $communication->school_id,
            'recipient_type' => $recipient['type'] ?? 'user',
            'recipient_id' => $recipient['id'] ?? null,
            'recipient_email' => $recipient['email'] ?? null,
            'recipient_phone' => $recipient['phone'] ?? null,
            'channel' => $channel,
            'provider_name' => $this->getProviderName($channel),
            'delivery_status' => 'failed',
            'failure_reason' => $error,
            'retry_count' => 3,
            'max_retries' => 3,
            'last_attempt_at' => now(),
        ]);

        $communication->increment('failure_count');
    }

    /**
     * Check if channel is allowed by recipient preference.
     */
    protected function isChannelAllowed(int $schoolId, string $entityType, ?int $entityId, string $channel, string $category): bool
    {
        if (! $entityId) {
            return true;
        }

        $pref = NotificationPreference::where('school_id', $schoolId)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->first();

        if (! $pref) {
            return true;
        }

        return match ($channel) {
            'in_app', 'database' => $pref->channel_in_app,
            'email' => $pref->channel_email,
            'sms' => $pref->channel_sms,
            'whatsapp' => $pref->channel_whatsapp,
            'push' => $pref->channel_push ?? true,
            default => true,
        };
    }

    /**
     * Render template placeholder strings.
     */
    protected function renderContent(string $content, array $recipient): string
    {
        $replacements = [
            '{{user_name}}' => $recipient['name'] ?? 'User',
            '{{recipient_name}}' => $recipient['name'] ?? 'Valued Recipient',
            '{{student_name}}' => $recipient['student_name'] ?? 'Student',
            '{{school_name}}' => 'SmartSchool Academy',
            '{{date}}' => now()->toDateString(),
        ];

        return strtr($content, $replacements);
    }

    /**
     * Channel delivery driver simulator/adapter.
     */
    protected function deliverViaChannel(string $channel, ?string $email, ?string $phone, string $subject, string $body): string
    {
        $msgId = 'MSG-' . strtoupper(bin2hex(random_bytes(4)));

        switch ($channel) {
            case 'database':
            case 'in_app':
                Log::info("Centralized Notification Engine [In-App/Database]: Saved to recipient database notifications.");
                break;
            case 'email':
                Log::info("Centralized Notification Engine [Email]: Dispatched to {$email} | Subject: {$subject}");
                break;
            case 'sms':
                Log::info("Centralized Notification Engine [SMS]: Dispatched SMS to {$phone}");
                break;
            case 'whatsapp':
                Log::info("Centralized Notification Engine [WhatsApp]: Dispatched WhatsApp payload to {$phone}");
                break;
            case 'push':
                Log::info("Centralized Notification Engine [Push-Ready]: Dispatched Push payload to device target.");
                break;
        }

        return $msgId;
    }

    protected function getProviderName(string $channel): string
    {
        return match ($channel) {
            'database', 'in_app' => 'database_in_app',
            'email' => 'smtp_mailgun',
            'sms' => 'twilio_sms',
            'whatsapp' => 'meta_whatsapp',
            'push' => 'firebase_push',
            default => 'system',
        };
    }
}
