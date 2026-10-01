<?php

namespace App\Livewire\Admissions;

use App\Models\AdmissionLead;
use App\Models\LeadSource;
use App\Models\LeadStatusHistory;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Kanban extends Component
{
    use AuthorizesRequests;

    public string $search = '';
    public string $counselor_id = '';
    public string $source_id = '';

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

    public function updateLeadStage(int $leadId, string $newStage, ?string $reason = null): void
    {
        $lead = AdmissionLead::findOrFail($leadId);
        $this->authorize('update', $lead);

        $oldStage = $lead->stage;
        if ($oldStage === $newStage) {
            return;
        }

        $newStatus = match ($newStage) {
            'admission_confirmed' => 'converted',
            'lost' => 'lost',
            default => 'active',
        };

        $lead->update([
            'stage' => $newStage,
            'status' => $newStatus,
            'loss_reason' => $newStage === 'lost' ? $reason : $lead->loss_reason,
        ]);

        LeadStatusHistory::create([
            'lead_id' => $lead->id,
            'changed_by' => Auth::id(),
            'from_stage' => $oldStage,
            'to_stage' => $newStage,
            'reason' => $reason ?: "Stage updated from {$oldStage} to {$newStage}",
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Pipeline Stage Updated',
            'message' => "Moved lead {$lead->lead_code} to stage: {$this->stages[$newStage]['label']}.",
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', AdmissionLead::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $query = AdmissionLead::with(['counselor', 'source', 'gradeApplyingFor', 'followUps'])
            ->where('school_id', $schoolId);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%')
                    ->orWhere('parent_name', 'like', '%' . $this->search . '%')
                    ->orWhere('lead_code', 'like', '%' . $this->search . '%')
                    ->orWhere('parent_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->counselor_id !== '') {
            $query->where('assigned_counselor_id', $this->counselor_id);
        }

        if ($this->source_id !== '') {
            $query->where('source_id', $this->source_id);
        }

        $allLeads = $query->orderBy('score', 'desc')->get();
        $groupedLeads = $allLeads->groupBy('stage');

        $counselors = User::where('school_id', $schoolId)->get();
        $sources = LeadSource::where('school_id', $schoolId)->get();

        return view('livewire.admissions.kanban', [
            'groupedLeads' => $groupedLeads,
            'counselors' => $counselors,
            'sources' => $sources,
        ])->layout('components.layouts.admin', ['title' => 'Admission Pipeline Visual Kanban Board']);
    }
}
