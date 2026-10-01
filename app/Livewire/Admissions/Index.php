<?php

namespace App\Livewire\Admissions;

use App\Models\AdmissionLead;
use App\Models\LeadCampaign;
use App\Models\LeadSource;
use App\Models\LeadStatusHistory;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';
    public string $stage = '';
    public string $source_id = '';
    public string $campaign_id = '';
    public string $counselor_id = '';
    public string $grade_applying_for_id = '';
    public int $perPage = 10;

    // Create Lead Drawer Form
    public bool $showCreateModal = false;
    public string $parent_name = '';
    public string $parent_email = '';
    public string $parent_phone = '';
    public string $student_name = '';
    public string $student_dob = '';
    public string $gender = 'male';
    public string $new_source_id = '';
    public string $new_campaign_id = '';
    public string $new_counselor_id = '';
    public string $new_grade_id = '';
    public string $initial_stage = 'new_lead';
    public string $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'stage' => ['except' => ''],
        'source_id' => ['except' => ''],
        'counselor_id' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function createLead(): void
    {
        $this->authorize('create', AdmissionLead::class);
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm(): void
    {
        $this->reset([
            'parent_name',
            'parent_email',
            'parent_phone',
            'student_name',
            'student_dob',
            'new_source_id',
            'new_campaign_id',
            'new_counselor_id',
            'new_grade_id',
            'notes',
        ]);
        $this->gender = 'male';
        $this->initial_stage = 'new_lead';
        $this->resetValidation();
    }

    public function saveLead(): void
    {
        $this->authorize('create', AdmissionLead::class);

        $validated = $this->validate([
            'parent_name' => 'required|string|max:100',
            'parent_email' => 'required|email',
            'parent_phone' => 'required|string|max:30',
            'student_name' => 'required|string|max:100',
            'student_dob' => 'nullable|date',
            'gender' => 'required|string',
            'new_source_id' => 'nullable|exists:lead_sources,id',
            'new_campaign_id' => 'nullable|exists:lead_campaigns,id',
            'new_counselor_id' => 'nullable|exists:users,id',
            'new_grade_id' => 'nullable|exists:school_classes,id',
            'initial_stage' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $count = AdmissionLead::where('school_id', $schoolId)->count() + 1;
        $leadCode = 'LEAD-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $lead = AdmissionLead::create([
            'school_id' => $schoolId,
            'source_id' => $this->new_source_id ?: null,
            'campaign_id' => $this->new_campaign_id ?: null,
            'assigned_counselor_id' => $this->new_counselor_id ?: Auth::id(),
            'grade_applying_for_id' => $this->new_grade_id ?: null,
            'academic_year_id' => School::first()->academicYears()->first()->id ?? null,
            'lead_code' => $leadCode,
            'parent_name' => $this->parent_name,
            'parent_email' => $this->parent_email,
            'parent_phone' => $this->parent_phone,
            'student_name' => $this->student_name,
            'student_dob' => $this->student_dob ?: null,
            'gender' => $this->gender,
            'stage' => $this->initial_stage,
            'status' => 'active',
            'score' => 70,
            'notes' => $this->notes,
        ]);

        LeadStatusHistory::create([
            'lead_id' => $lead->id,
            'changed_by' => Auth::id(),
            'from_stage' => null,
            'to_stage' => $this->initial_stage,
            'reason' => 'Lead created in CRM.',
        ]);

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Admission Lead Created',
            'message' => "Successfully registered lead {$leadCode} for applicant {$this->student_name}.",
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', AdmissionLead::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $query = AdmissionLead::with(['source', 'campaign', 'counselor', 'gradeApplyingFor', 'application'])
            ->where('school_id', $schoolId);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%')
                    ->orWhere('parent_name', 'like', '%' . $this->search . '%')
                    ->orWhere('lead_code', 'like', '%' . $this->search . '%')
                    ->orWhere('parent_email', 'like', '%' . $this->search . '%')
                    ->orWhere('parent_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->stage !== '') {
            $query->where('stage', $this->stage);
        }

        if ($this->source_id !== '') {
            $query->where('source_id', $this->source_id);
        }

        if ($this->counselor_id !== '') {
            $query->where('assigned_counselor_id', $this->counselor_id);
        }

        if ($this->grade_applying_for_id !== '') {
            $query->where('grade_applying_for_id', $this->grade_applying_for_id);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        $sources = LeadSource::where('school_id', $schoolId)->get();
        $campaigns = LeadCampaign::where('school_id', $schoolId)->get();
        $counselors = User::where('school_id', $schoolId)->get();
        $classes = SchoolClass::orderBy('year_level')->get();

        return view('livewire.admissions.index', [
            'leads' => $leads,
            'sources' => $sources,
            'campaigns' => $campaigns,
            'counselors' => $counselors,
            'classes' => $classes,
        ])->layout('components.layouts.admin', ['title' => 'Admission Leads Directory']);
    }
}
