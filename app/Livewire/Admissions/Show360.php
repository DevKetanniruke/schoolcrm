<?php

namespace App\Livewire\Admissions;

use App\Models\AdmissionApplication;
use App\Models\AdmissionEnquiry;
use App\Models\AdmissionLead;
use App\Models\LeadDocument;
use App\Models\LeadFollowUp;
use App\Models\LeadStatusHistory;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show360 extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public AdmissionLead $lead;
    public string $activeTab = 'overview';

    // Stage update modal
    public bool $showStageModal = false;
    public string $selectedStage = '';
    public string $stageReason = '';

    // Enquiry form
    public bool $showEnquiryModal = false;
    public string $inquiryType = 'general';
    public string $question = '';
    public string $response = '';

    // Follow-up form
    public bool $showFollowUpModal = false;
    public string $followUpType = 'phone_call';
    public string $followUpSubject = '';
    public string $followUpNotes = '';
    public string $followUpScheduledAt = '';
    public string $followUpDueDate = '';

    // Application modal/form
    public bool $showApplicationModal = false;
    public string $appStage = 'draft';
    public string $appFeeStatus = 'pending';
    public string $appReviewerNotes = '';

    // Document form
    public bool $showDocumentModal = false;
    public string $docTitle = '';
    public string $docType = 'birth_certificate';
    public $docFile;

    // AI Admission Assistant State
    public ?string $aiOutput = null;
    public ?array $aiSuggestedActions = [];
    public string $activeAiCapability = 'lead_history_summary';
    public bool $isAiLoading = false;

    public array $aiCapabilities = [
        'lead_history_summary' => ['label' => 'Summarize Lead History', 'icon' => '📋'],
        'lead_followup_suggestions' => ['label' => 'Generate Follow-Up Suggestions', 'icon' => '💡'],
        'draft_personalized_message' => ['label' => 'Draft Personalized Message', 'icon' => '✉️'],
        'overdue_followups_identifier' => ['label' => 'Identify Overdue Follow-Ups', 'icon' => '⏳'],
        'enquiry_conversation_summary' => ['label' => 'Summarize Enquiry Conversations', 'icon' => '💬'],
        'counselor_call_notes' => ['label' => 'Generate Counselor Call Notes', 'icon' => '📞'],
        'visit_summaries' => ['label' => 'Generate Campus Visit Summary', 'icon' => '🏫'],
        'next_admin_actions' => ['label' => 'Suggest Next Admin Actions', 'icon' => '⚙️'],
    ];

    public function runAiAssistant(string $capability, \App\Core\AI\Services\AIService $aiService): void
    {
        $this->activeAiCapability = $capability;
        $label = $this->aiCapabilities[$capability]['label'] ?? 'Admission Insight';

        $prompt = "Execute admission capability '{$capability}' for lead {$this->lead->student_name} (Parent: {$this->lead->parent_name}, Stage: {$this->lead->stage}).";

        $response = $aiService->executeCapability(
            $capability,
            $prompt,
            Auth::user(),
            ['lead_id' => $this->lead->id]
        );

        if ($response->success) {
            $this->aiOutput = $response->content;
            $this->aiSuggestedActions = $response->suggestedActions;
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'AI Insight Generated',
                'message' => "AI generated suggestions for: {$label}.",
            ]);
        } else {
            $this->aiOutput = "⚠️ " . $response->errorMessage;
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'AI Request Failed',
                'message' => $response->errorMessage,
            ]);
        }
    }

    public array $stages = [
        'new_lead' => ['label' => 'New Lead', 'color' => 'bg-sky-500'],
        'contacted' => ['label' => 'Contacted', 'color' => 'bg-indigo-500'],
        'counselling' => ['label' => 'Counselling', 'color' => 'bg-purple-500'],
        'campus_visit' => ['label' => 'Campus Visit', 'color' => 'bg-amber-500'],
        'application_started' => ['label' => 'App Started', 'color' => 'bg-blue-500'],
        'documents_pending' => ['label' => 'Docs Pending', 'color' => 'bg-orange-500'],
        'application_submitted' => ['label' => 'App Submitted', 'color' => 'bg-teal-500'],
        'under_review' => ['label' => 'Under Review', 'color' => 'bg-yellow-600'],
        'approved' => ['label' => 'Approved', 'color' => 'bg-lime-600'],
        'admission_confirmed' => ['label' => 'Admission Confirmed', 'color' => 'bg-emerald-600'],
        'lost' => ['label' => 'Lost / Closed', 'color' => 'bg-slate-500'],
    ];

    public function mount(AdmissionLead $lead): void
    {
        $this->authorize('view', $lead);
        $this->lead = $lead->load(['source', 'campaign', 'counselor', 'gradeApplyingFor', 'academicYear', 'enquiries.answeredBy', 'application', 'followUps.assignedTo', 'documents.uploadedBy', 'statusHistories.changedBy']);
        $this->selectedStage = $this->lead->stage;
    }

    public function changeStage(): void
    {
        $this->authorize('update', $this->lead);

        $oldStage = $this->lead->stage;
        if ($oldStage === $this->selectedStage) {
            $this->showStageModal = false;
            return;
        }

        $newStatus = match ($this->selectedStage) {
            'admission_confirmed' => 'converted',
            'lost' => 'lost',
            default => 'active',
        };

        $this->lead->update([
            'stage' => $this->selectedStage,
            'status' => $newStatus,
            'loss_reason' => $this->selectedStage === 'lost' ? $this->stageReason : $this->lead->loss_reason,
        ]);

        LeadStatusHistory::create([
            'lead_id' => $this->lead->id,
            'changed_by' => Auth::id(),
            'from_stage' => $oldStage,
            'to_stage' => $this->selectedStage,
            'reason' => $this->stageReason ?: "Moved to {$this->stages[$this->selectedStage]['label']}",
        ]);

        $this->showStageModal = false;
        $this->stageReason = '';
        $this->lead->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Stage Updated',
            'message' => "Lead moved to stage {$this->stages[$this->selectedStage]['label']}.",
        ]);
    }

    public function saveEnquiry(): void
    {
        $this->authorize('update', $this->lead);

        $this->validate([
            'question' => 'required|string|min:3',
            'inquiryType' => 'required|string',
            'response' => 'nullable|string',
        ]);

        AdmissionEnquiry::create([
            'lead_id' => $this->lead->id,
            'answered_by' => Auth::id(),
            'inquiry_type' => $this->inquiryType,
            'question' => $this->question,
            'response' => $this->response,
            'inquiry_date' => now(),
        ]);

        $this->showEnquiryModal = false;
        $this->reset(['question', 'response', 'inquiryType']);
        $this->lead->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Inquiry Logged',
            'message' => 'Inquiry recorded successfully.',
        ]);
    }

    public function saveFollowUp(): void
    {
        $this->authorize('update', $this->lead);

        $this->validate([
            'followUpSubject' => 'required|string|min:3',
            'followUpType' => 'required|string',
            'followUpScheduledAt' => 'required|date',
            'followUpDueDate' => 'nullable|date',
            'followUpNotes' => 'nullable|string',
        ]);

        LeadFollowUp::create([
            'lead_id' => $this->lead->id,
            'assigned_to' => Auth::id(),
            'type' => $this->followUpType,
            'subject' => $this->followUpSubject,
            'notes' => $this->followUpNotes,
            'scheduled_at' => $this->followUpScheduledAt,
            'due_date' => $this->followUpDueDate ?: null,
            'status' => 'pending',
        ]);

        $this->showFollowUpModal = false;
        $this->reset(['followUpSubject', 'followUpNotes', 'followUpScheduledAt', 'followUpDueDate']);
        $this->lead->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Follow-up Scheduled',
            'message' => 'Follow-up task scheduled successfully.',
        ]);
    }

    public function markFollowUpComplete(int $followUpId): void
    {
        $this->authorize('update', $this->lead);

        $followUp = LeadFollowUp::where('lead_id', $this->lead->id)->findOrFail($followUpId);
        $followUp->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->lead->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Task Completed',
            'message' => 'Follow-up marked as completed.',
        ]);
    }

    public function saveApplication(): void
    {
        $this->authorize('update', $this->lead);

        $this->validate([
            'appStage' => 'required|string',
            'appFeeStatus' => 'required|string',
            'appReviewerNotes' => 'nullable|string',
        ]);

        $application = $this->lead->application;
        if (! $application) {
            $appNum = 'APP-' . date('Y') . '-' . str_pad((string) (AdmissionApplication::count() + 1), 4, '0', STR_PAD_LEFT);
            $application = AdmissionApplication::create([
                'lead_id' => $this->lead->id,
                'school_id' => $this->lead->school_id,
                'class_id' => $this->lead->grade_applying_for_id,
                'academic_year_id' => $this->lead->academic_year_id,
                'application_number' => $appNum,
                'application_stage' => $this->appStage,
                'fee_status' => $this->appFeeStatus,
                'reviewer_notes' => $this->appReviewerNotes,
                'submission_date' => now(),
            ]);
        } else {
            $application->update([
                'application_stage' => $this->appStage,
                'fee_status' => $this->appFeeStatus,
                'reviewer_notes' => $this->appReviewerNotes,
                'decision_date' => in_array($this->appStage, ['approved', 'rejected', 'enrolled']) ? now() : $application->decision_date,
            ]);
        }

        $this->showApplicationModal = false;
        $this->lead->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Application Saved',
            'message' => 'Admission application details updated.',
        ]);
    }

    public function saveDocument(): void
    {
        $this->authorize('update', $this->lead);

        $this->validate([
            'docTitle' => 'required|string|min:3',
            'docType' => 'required|string',
        ]);

        $filePath = 'documents/leads/' . $this->lead->id . '_doc_' . time() . '.pdf';

        if ($this->docFile) {
            $filePath = $this->docFile->store('lead-documents', 'public');
        }

        LeadDocument::create([
            'lead_id' => $this->lead->id,
            'application_id' => $this->lead->application?->id,
            'title' => $this->docTitle,
            'document_type' => $this->docType,
            'file_path' => $filePath,
            'uploaded_by' => Auth::id(),
        ]);

        $this->showDocumentModal = false;
        $this->reset(['docTitle', 'docType', 'docFile']);
        $this->lead->refresh();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Document Added',
            'message' => 'Lead document uploaded successfully.',
        ]);
    }

    public function render()
    {
        return view('livewire.admissions.show360', [
            'lead' => $this->lead,
        ])->layout('components.layouts.admin', ['title' => "Lead 360 Workspace: {$this->lead->student_name}"]);
    }
}
