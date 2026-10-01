<?php

namespace App\Livewire\Communication;

use App\Core\AI\Services\AIService;
use App\Core\Communication\Services\CommunicationManager;
use App\Models\AiCommunicationDraft;
use App\Models\Communication;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AiParentCommunication extends Component
{
    use AuthorizesRequests;

    // Inputs for Authorized Staff
    public string $topic = '';
    public string $audience = 'all_parents';
    public string $tone = 'formal';
    public string $language = 'en'; // en, mr, hi
    public string $important_info = '';

    // Configurable Approval Rules
    public bool $require_approval = true;

    // Draft Metadata
    public ?int $draft_id = null;
    public string $approval_status = 'draft'; // draft, pending_approval, approved, rejected, sent
    public ?string $approved_by_name = null;
    public ?string $approved_at_formatted = null;
    public string $rejection_reason = '';

    // AI Generated Multi-Channel Formats (Staff Editable)
    public string $short_notification = '';
    public string $detailed_message = '';
    public string $email_subject = '';
    public string $email_body = '';
    public string $sms_version = '';
    public string $whatsapp_version = '';

    // UI View State
    public string $active_preview_channel = 'email'; // short_notification, detailed_message, email, sms, whatsapp
    public bool $show_preview_modal = false;

    public array $audienceOptions = [
        'all_parents' => 'All Parents & Guardians',
        'grade_parents' => 'Grade / Class Specific Parents',
        'individual_parent' => 'Individual Parent Profile',
        'admission_leads' => 'Prospective Admission Leads',
    ];

    public array $toneOptions = [
        'formal' => 'Formal & Official',
        'polite_friendly' => 'Polite & Friendly',
        'urgent_important' => 'Urgent / High Priority',
        'informative' => 'Informative & Direct',
        'encouraging' => 'Encouraging & Enthusiastic',
    ];

    public array $languageOptions = [
        'en' => 'English',
        'mr' => 'Marathi (मराठी)',
        'hi' => 'Hindi (हिंदी)',
    ];

    public function mount(?int $draft = null): void
    {
        $this->language = app()->getLocale() === 'mr' ? 'mr' : 'en';

        if ($draft) {
            $this->loadDraft($draft);
        }
    }

    public function generate(): void
    {
        $this->authorize('create', Communication::class);

        $this->validate([
            'topic' => 'required|string|min:3|max:150',
            'audience' => 'required|string',
            'tone' => 'required|string',
            'language' => 'required|in:en,mr,hi',
            'important_info' => 'nullable|string|max:1000',
        ]);

        $aiService = app(AIService::class);
        $user = Auth::user();

        $prompt = "Generate multi-channel parent communication for topic '{$this->topic}', audience '{$this->audience}', tone '{$this->tone}', language '{$this->language}'. Info: {$this->important_info}";

        $response = $aiService->executeCapability(
            capability: 'parent_communication_generator',
            prompt: $prompt,
            user: $user,
            entityIds: [
                'topic' => $this->topic,
                'audience' => $this->audienceOptions[$this->audience] ?? $this->audience,
                'tone' => $this->toneOptions[$this->tone] ?? $this->tone,
                'language' => $this->language,
                'important_info' => $this->important_info,
            ]
        );

        if (! $response->success) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Generation Failed',
                'message' => $response->errorMessage ?? 'Failed to generate AI communication draft.',
            ]);

            return;
        }

        $decoded = json_decode($response->content, true);

        if (! is_array($decoded)) {
            $decoded = [
                'short_notification' => $response->content,
                'detailed_message' => $response->content,
                'email_subject' => "Notice: {$this->topic}",
                'email_body' => $response->content,
                'sms_version' => substr($response->content, 0, 150),
                'whatsapp_version' => $response->content,
            ];
        }

        $this->short_notification = $decoded['short_notification'] ?? '';
        $this->detailed_message = $decoded['detailed_message'] ?? '';
        $this->email_subject = $decoded['email_subject'] ?? '';
        $this->email_body = $decoded['email_body'] ?? '';
        $this->sms_version = $decoded['sms_version'] ?? '';
        $this->whatsapp_version = $decoded['whatsapp_version'] ?? '';

        $this->approval_status = $this->require_approval ? 'pending_approval' : 'approved';

        $draft = AiCommunicationDraft::create([
            'school_id' => $user->school_id ?? 1,
            'author_id' => $user->id,
            'topic' => $this->topic,
            'audience' => $this->audience,
            'tone' => $this->tone,
            'language' => $this->language,
            'important_info' => $this->important_info,
            'short_notification' => $this->short_notification,
            'detailed_message' => $this->detailed_message,
            'email_subject' => $this->email_subject,
            'email_body' => $this->email_body,
            'sms_version' => $this->sms_version,
            'whatsapp_version' => $this->whatsapp_version,
            'approval_status' => $this->approval_status,
            'require_approval' => $this->require_approval,
        ]);

        $this->draft_id = $draft->id;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'AI Communication Generated',
            'message' => '5 multi-channel content formats generated successfully. Review, edit, and approve before sending.',
        ]);
    }

    public function saveEdit(): void
    {
        if (! $this->draft_id) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Active Draft',
                'message' => 'Please generate or select a draft first before saving edits.',
            ]);

            return;
        }

        $draft = AiCommunicationDraft::find($this->draft_id);
        if ($draft) {
            $draft->update([
                'short_notification' => $this->short_notification,
                'detailed_message' => $this->detailed_message,
                'email_subject' => $this->email_subject,
                'email_body' => $this->email_body,
                'sms_version' => $this->sms_version,
                'whatsapp_version' => $this->whatsapp_version,
                'important_info' => $this->important_info,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Edits Saved',
                'message' => 'Communication content updates saved successfully.',
            ]);
        }
    }

    public function approve(): void
    {
        if (! $this->draft_id) {
            return;
        }

        $user = Auth::user();
        $draft = AiCommunicationDraft::find($this->draft_id);

        if ($draft) {
            $draft->update([
                'approval_status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $this->approval_status = 'approved';
            $this->approved_by_name = $user->name;
            $this->approved_at_formatted = now()->format('M d, Y h:i A');

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Draft Approved',
                'message' => 'AI-generated parent communication approved. It is now cleared for multi-channel dispatch.',
            ]);
        }
    }

    public function reject(): void
    {
        if (! $this->draft_id) {
            return;
        }

        $draft = AiCommunicationDraft::find($this->draft_id);
        if ($draft) {
            $draft->update([
                'approval_status' => 'rejected',
                'rejection_reason' => $this->rejection_reason ?: 'Requires revision',
            ]);

            $this->approval_status = 'rejected';

            $this->dispatch('notify', [
                'type' => 'info',
                'title' => 'Draft Rejected',
                'message' => 'Draft status set to rejected. Update parameters and regenerate as needed.',
            ]);
        }
    }

    public function send(): mixed
    {
        $this->authorize('create', Communication::class);

        if (! $this->draft_id) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'No Draft Selected',
                'message' => 'Please generate or load a valid AI communication draft first.',
            ]);

            return null;
        }

        $draft = AiCommunicationDraft::find($this->draft_id);

        // STOPSAFE POLICY RULE: Never automatically send AI-generated communication without approval rules
        if ($this->require_approval && $draft && $draft->approval_status !== 'approved' && $draft->approval_status !== 'sent') {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Approval Required',
                'message' => 'Safety Policy Block: AI-generated communication must be reviewed and approved by staff before dispatch.',
            ]);

            return null;
        }

        $user = Auth::user();
        $schoolId = $user->school_id ?? School::first()->id ?? 1;

        $communication = Communication::create([
            'school_id' => $schoolId,
            'sender_id' => $user->id,
            'title' => "AI Broadcast: {$this->topic}",
            'channel' => 'multi_channel',
            'recipient_type' => $this->audience === 'grade_parents' ? 'class' : ($this->audience === 'admission_leads' ? 'admission_leads' : 'parent_group'),
            'target_filters_json' => ['audience' => $this->audience],
            'subject' => $this->email_subject ?: "Notice: {$this->topic}",
            'message_body' => $this->detailed_message,
            'payload_data_json' => [
                'short_notification' => $this->short_notification,
                'email_body' => $this->email_body,
                'sms_version' => $this->sms_version,
                'whatsapp_version' => $this->whatsapp_version,
            ],
            'status' => 'processing',
            'sent_at' => now(),
        ]);

        if ($draft) {
            $draft->update([
                'approval_status' => 'sent',
                'communication_id' => $communication->id,
            ]);
        }

        $manager = new CommunicationManager();
        $manager->dispatchCommunication($communication);

        $this->approval_status = 'sent';

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Broadcast Sent!',
            'message' => "AI-assisted parent communication for '{$this->topic}' has been dispatched across all selected channels.",
        ]);

        return redirect()->route('communication.logs');
    }

    public function loadDraft(int $id): void
    {
        $draft = AiCommunicationDraft::find($id);

        if ($draft) {
            $this->draft_id = $draft->id;
            $this->topic = $draft->topic;
            $this->audience = $draft->audience;
            $this->tone = $draft->tone;
            $this->language = $draft->language;
            $this->important_info = $draft->important_info ?? '';
            $this->short_notification = $draft->short_notification ?? '';
            $this->detailed_message = $draft->detailed_message ?? '';
            $this->email_subject = $draft->email_subject ?? '';
            $this->email_body = $draft->email_body ?? '';
            $this->sms_version = $draft->sms_version ?? '';
            $this->whatsapp_version = $draft->whatsapp_version ?? '';
            $this->approval_status = $draft->approval_status;
            $this->require_approval = $draft->require_approval;
            $this->approved_by_name = $draft->approvedByUser?->name;
            $this->approved_at_formatted = $draft->approved_at?->format('M d, Y h:i A');
        }
    }

    public function render()
    {
        $this->authorize('viewAny', Communication::class);

        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $recentDrafts = AiCommunicationDraft::with('author', 'approvedByUser')
            ->where('school_id', $schoolId)
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.communication.ai-parent-communication', [
            'recentDrafts' => $recentDrafts,
        ])->layout('components.layouts.admin', ['title' => 'AI-Assisted Parent Communication']);
    }
}
