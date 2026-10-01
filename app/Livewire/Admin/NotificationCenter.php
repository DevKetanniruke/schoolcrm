<?php

namespace App\Livewire\Admin;

use App\Core\Services\CentralizedNotificationEngine;
use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\NotificationPreference;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $activeTab = 'broadcast'; // broadcast, templates, preferences, logs

    // Broadcast Form Inputs
    public string $title = '';
    public string $subject = '';
    public string $message_body = '';
    public ?int $selected_template_id = null;
    public array $selected_channels = ['email', 'in_app'];
    public string $recipient_type = 'all_users';
    public ?string $scheduled_at = null;

    // Template Form Inputs
    public bool $showTemplateModal = false;
    public string $tpl_title = '';
    public string $tpl_code = '';
    public string $tpl_channel = 'email';
    public string $tpl_category = 'general';
    public string $tpl_subject = '';
    public string $tpl_content_body = '';

    // Preferences Config State
    public bool $pref_in_app = true;
    public bool $pref_email = true;
    public bool $pref_sms = true;
    public bool $pref_whatsapp = true;
    public bool $pref_push = true;

    // Log Filters
    public string $logChannelFilter = '';
    public string $logStatusFilter = '';

    public array $availableChannels = [
        'database' => '📥 Database (In-App)',
        'email' => '✉️ Email',
        'sms' => '📱 SMS',
        'whatsapp' => '💬 WhatsApp-Ready',
        'push' => '🔔 Push-Ready',
    ];

    public function updatedSelectedTemplateId($value)
    {
        if ($value) {
            $template = CommunicationTemplate::find($value);
            if ($template) {
                $this->title = $template->title;
                $this->subject = $template->subject ?? $template->title;
                $this->message_body = $template->content_body;
            }
        }
    }

    public function sendBroadcastNotification(CentralizedNotificationEngine $engine)
    {
        $this->validate([
            'title' => 'required|string|min:3|max:150',
            'message_body' => 'required|string|min:5',
            'selected_channels' => 'required|array|min:1',
        ]);

        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        // Collect target recipients asynchronously
        $users = User::where('school_id', $schoolId)->get();
        $recipients = $users->map(fn ($u) => [
            'id' => $u->id,
            'type' => 'user',
            'name' => $u->name,
            'email' => $u->email,
            'phone' => '+91 9876543210',
        ])->toArray();

        $engine->dispatchNotification([
            'school_id' => $schoolId,
            'template_id' => $this->selected_template_id,
            'title' => $this->title,
            'subject' => $this->subject ?: $this->title,
            'message_body' => $this->message_body,
            'scheduled_at' => $this->scheduled_at,
            'recipient_type' => $this->recipient_type,
        ], $recipients, $this->selected_channels);

        $this->reset(['title', 'subject', 'message_body', 'selected_template_id', 'scheduled_at']);
        $this->selected_channels = ['email', 'in_app'];

        $this->dispatch('notify', message: 'Notification queued for asynchronous multi-channel processing via Laravel Queue.');
    }

    public function createTemplate()
    {
        $this->validate([
            'tpl_title' => 'required|string|min:3',
            'tpl_code' => 'required|string|min:3|unique:communication_templates,code',
            'tpl_content_body' => 'required|string|min:5',
        ]);

        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        CommunicationTemplate::create([
            'school_id' => $schoolId,
            'title' => $this->tpl_title,
            'code' => strtoupper($this->tpl_code),
            'channel' => $this->tpl_channel,
            'category' => $this->tpl_category,
            'subject' => $this->tpl_subject ?: $this->tpl_title,
            'content_body' => $this->tpl_content_body,
            'status' => 'active',
            'created_by' => $user?->id,
        ]);

        $this->showTemplateModal = false;
        $this->reset(['tpl_title', 'tpl_code', 'tpl_subject', 'tpl_content_body']);

        $this->dispatch('notify', message: 'Notification template created successfully.');
    }

    public function saveMyPreferences()
    {
        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        NotificationPreference::updateOrCreate(
            [
                'school_id' => $schoolId,
                'user_id' => $user->id,
            ],
            [
                'channel_in_app' => $this->pref_in_app,
                'channel_email' => $this->pref_email,
                'channel_sms' => $this->pref_sms,
                'channel_whatsapp' => $this->pref_whatsapp,
                'channel_push' => $this->pref_push,
            ]
        );

        $this->dispatch('notify', message: 'Your notification preferences have been updated.');
    }

    public function retryLog(int $logId, CentralizedNotificationEngine $engine)
    {
        $log = CommunicationLog::findOrFail($logId);
        $success = $engine->retryFailedLog($log);

        if ($success) {
            $this->dispatch('notify', message: 'Retry attempt dispatched for failed notification log.');
        } else {
            $this->dispatch('notify', message: 'Max retry attempts exceeded for this log.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $schoolId = $user?->school_id ?? 1;

        $templates = CommunicationTemplate::where('school_id', $schoolId)->get();

        $logsQuery = CommunicationLog::with('communication')
            ->where('school_id', $schoolId);

        if (! empty($this->logChannelFilter)) {
            $logsQuery->where('channel', $this->logChannelFilter);
        }

        if (! empty($this->logStatusFilter)) {
            $logsQuery->where('delivery_status', $this->logStatusFilter);
        }

        $deliveryLogs = $logsQuery->latest()->paginate(15);

        return view('livewire.admin.notification-center', [
            'templates' => $templates,
            'deliveryLogs' => $deliveryLogs,
        ])->layout('components.layouts.admin', ['title' => 'Centralized Notification Engine']);
    }
}
