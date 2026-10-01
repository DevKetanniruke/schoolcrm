<?php

namespace App\Core\Communication\Services;

use App\Core\Communication\Contracts\CommunicationProviderInterface;
use App\Core\Communication\DTOs\CommunicationResponse;
use App\Core\Communication\Providers\Email\LogEmailProvider;
use App\Core\Communication\Providers\InApp\DatabaseInAppProvider;
use App\Core\Communication\Providers\Sms\LogSmsProvider;
use App\Core\Communication\Providers\Sms\TwilioSmsProvider;
use App\Core\Communication\Providers\Whatsapp\LogWhatsappProvider;
use App\Core\Communication\Providers\Whatsapp\MetaWhatsappProvider;
use App\Models\AdmissionLead;
use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\NotificationPreference;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\User;

class CommunicationManager
{
    /**
     * Resolve the channel provider driver.
     */
    public function resolveProvider(string $channel, ?string $preferredDriver = null): CommunicationProviderInterface
    {
        return match ($channel) {
            'in_app' => new DatabaseInAppProvider(),
            'email' => new LogEmailProvider(),
            'sms' => $preferredDriver === 'twilio' ? new TwilioSmsProvider() : new LogSmsProvider(),
            'whatsapp' => $preferredDriver === 'meta' ? new MetaWhatsappProvider() : new LogWhatsappProvider(),
            default => new LogEmailProvider(),
        };
    }

    /**
     * Parse and substitute dynamic variables in body & subject.
     */
    public function parseTemplateVariables(string $content, array $variables = []): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', (string) $value, $content);
        }
        return $content;
    }

    /**
     * Resolve target recipients list for a communication broadcast.
     */
    public function resolveRecipients(Communication $communication): array
    {
        $schoolId = $communication->school_id;
        $recipientType = $communication->recipient_type;
        $filters = $communication->target_filters_json ?? [];
        $recipients = [];

        switch ($recipientType) {
            case 'individual':
                if (! empty($filters['user_id'])) {
                    $user = User::find($filters['user_id']);
                    if ($user) {
                        $recipients[] = [
                            'recipient_type' => 'user',
                            'recipient_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                        ];
                    }
                } elseif (! empty($filters['student_id'])) {
                    $student = Student::with('primaryContact')->find($filters['student_id']);
                    if ($student) {
                        $recipients[] = [
                            'recipient_type' => 'student',
                            'recipient_id' => $student->id,
                            'name' => $student->full_name,
                            'email' => $student->primaryContact?->email,
                            'phone' => $student->primaryContact?->phone,
                        ];
                    }
                } elseif (! empty($filters['parent_id'])) {
                    $parent = ParentProfile::find($filters['parent_id']);
                    if ($parent) {
                        $recipients[] = [
                            'recipient_type' => 'parent',
                            'recipient_id' => $parent->id,
                            'name' => $parent->full_name,
                            'email' => $parent->email,
                            'phone' => $parent->primary_phone,
                        ];
                    }
                } elseif (! empty($filters['lead_id'])) {
                    $lead = AdmissionLead::find($filters['lead_id']);
                    if ($lead) {
                        $recipients[] = [
                            'recipient_type' => 'admission_lead',
                            'recipient_id' => $lead->id,
                            'name' => $lead->parent_name,
                            'email' => $lead->parent_email,
                            'phone' => $lead->parent_phone,
                            'student_name' => $lead->student_name,
                        ];
                    }
                }
                break;

            case 'class':
                $classId = $filters['class_id'] ?? null;
                if ($classId) {
                    $students = Student::where('school_id', $schoolId)
                        ->where('class_id', $classId)
                        ->with('primaryContact')
                        ->get();

                    foreach ($students as $student) {
                        $recipients[] = [
                            'recipient_type' => 'student',
                            'recipient_id' => $student->id,
                            'name' => $student->full_name,
                            'email' => $student->primaryContact?->email,
                            'phone' => $student->primaryContact?->phone,
                            'class_name' => $student->schoolClass?->name ?? 'Class',
                        ];
                    }
                }
                break;

            case 'parent_group':
                $parents = ParentProfile::where('school_id', $schoolId)->get();
                foreach ($parents as $parent) {
                    $recipients[] = [
                        'recipient_type' => 'parent',
                        'recipient_id' => $parent->id,
                        'name' => $parent->full_name,
                        'email' => $parent->email,
                        'phone' => $parent->primary_phone,
                    ];
                }
                break;

            case 'staff_group':
                $staffMembers = StaffProfile::where('school_id', $schoolId)->get();
                foreach ($staffMembers as $staff) {
                    $recipients[] = [
                        'recipient_type' => 'staff',
                        'recipient_id' => $staff->id,
                        'name' => $staff->full_name,
                        'email' => $staff->email,
                        'phone' => $staff->phone,
                    ];
                }
                break;

            case 'admission_leads':
                $leads = AdmissionLead::where('school_id', $schoolId)->get();
                foreach ($leads as $lead) {
                    $recipients[] = [
                        'recipient_type' => 'admission_lead',
                        'recipient_id' => $lead->id,
                        'name' => $lead->parent_name,
                        'email' => $lead->parent_email,
                        'phone' => $lead->parent_phone,
                        'student_name' => $lead->student_name,
                    ];
                }
                break;

            case 'school_wide':
                $users = User::where('school_id', $schoolId)->get();
                foreach ($users as $user) {
                    $recipients[] = [
                        'recipient_type' => 'user',
                        'recipient_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ];
                }
                break;
        }

        return $recipients;
    }

    /**
     * Dispatch a communication to all target recipients.
     */
    public function dispatchCommunication(Communication $communication): void
    {
        $communication->update(['status' => 'processing']);

        $recipients = $this->resolveRecipients($communication);
        $total = count($recipients);
        $success = 0;
        $failure = 0;

        $school = School::find($communication->school_id);
        $schoolName = $school?->name ?? 'SmartSchool';

        $channels = $communication->channel === 'multi_channel'
            ? ['in_app', 'email', 'sms', 'whatsapp']
            : [$communication->channel];

        foreach ($recipients as $target) {
            foreach ($channels as $channel) {

                // Notification preference check
                if (! $this->isChannelAllowed($target, $channel)) {
                    continue;
                }

                $destination = match ($channel) {
                    'email' => $target['email'] ?? null,
                    'sms', 'whatsapp' => $target['phone'] ?? null,
                    'in_app' => (string) ($target['recipient_id'] ?? '1'),
                    default => $target['email'] ?? $target['phone'] ?? 'system',
                };

                if (! $destination && $channel !== 'in_app') {
                    $failure++;
                    CommunicationLog::create([
                        'communication_id' => $communication->id,
                        'school_id' => $communication->school_id,
                        'recipient_type' => $target['recipient_type'],
                        'recipient_id' => $target['recipient_id'],
                        'recipient_email' => $target['email'] ?? null,
                        'recipient_phone' => $target['phone'] ?? null,
                        'channel' => $channel,
                        'provider_name' => 'system',
                        'delivery_status' => 'failed',
                        'failure_reason' => "Missing contact details for channel: {$channel}",
                    ]);
                    continue;
                }

                $parsedBody = $this->parseTemplateVariables($communication->message_body, [
                    'student_name' => $target['student_name'] ?? $target['name'] ?? 'Student',
                    'parent_name' => $target['name'] ?? 'Parent/Guardian',
                    'name' => $target['name'] ?? 'Recipient',
                    'school_name' => $schoolName,
                ]);

                $parsedSubject = $this->parseTemplateVariables($communication->subject ?? $communication->title, [
                    'school_name' => $schoolName,
                ]);

                $provider = $this->resolveProvider($channel);
                $response = $provider->send($destination ?? 'system', $parsedSubject, $parsedBody, [
                    'communication_id' => $communication->id,
                    'recipient_id' => $target['recipient_id'],
                ]);

                if ($response->success) {
                    $success++;
                    CommunicationLog::create([
                        'communication_id' => $communication->id,
                        'school_id' => $communication->school_id,
                        'recipient_type' => $target['recipient_type'],
                        'recipient_id' => $target['recipient_id'],
                        'recipient_email' => $target['email'] ?? null,
                        'recipient_phone' => $target['phone'] ?? null,
                        'channel' => $channel,
                        'provider_name' => $provider->getProviderName(),
                        'provider_message_id' => $response->messageId,
                        'delivery_status' => 'sent',
                        'delivered_at' => now(),
                    ]);
                } else {
                    $failure++;
                    CommunicationLog::create([
                        'communication_id' => $communication->id,
                        'school_id' => $communication->school_id,
                        'recipient_type' => $target['recipient_type'],
                        'recipient_id' => $target['recipient_id'],
                        'recipient_email' => $target['email'] ?? null,
                        'recipient_phone' => $target['phone'] ?? null,
                        'channel' => $channel,
                        'provider_name' => $provider->getProviderName(),
                        'delivery_status' => 'failed',
                        'failure_reason' => $response->errorMessage,
                    ]);
                }
            }
        }

        $finalStatus = $failure === 0 ? 'sent' : ($success > 0 ? 'partially_failed' : 'failed');

        $communication->update([
            'status' => $finalStatus,
            'total_recipients' => max($total, $success + $failure),
            'success_count' => $success,
            'failure_count' => $failure,
            'sent_at' => now(),
        ]);
    }

    /**
     * Check if a recipient allows a specific channel based on preferences.
     */
    protected function isChannelAllowed(array $target, string $channel): bool
    {
        $pref = NotificationPreference::where('entity_type', $target['recipient_type'])
            ->where('entity_id', $target['recipient_id'])
            ->first();

        if (! $pref) {
            return true; // Default allowed
        }

        return match ($channel) {
            'in_app' => $pref->channel_in_app,
            'email' => $pref->channel_email,
            'sms' => $pref->channel_sms,
            'whatsapp' => $pref->channel_whatsapp,
            default => true,
        };
    }
}
