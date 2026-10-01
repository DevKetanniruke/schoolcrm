<?php

namespace App\Livewire\Admissions;

use App\Models\AdmissionApplication;
use App\Models\AdmissionLead;
use App\Models\LeadCampaign;
use App\Models\LeadFollowUp;
use App\Models\LeadSource;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use AuthorizesRequests;

    public function render()
    {
        $this->authorize('viewAny', AdmissionLead::class);

        $currentUser = Auth::user();
        $schoolId = $currentUser->school_id ?? School::first()->id ?? 1;

        $leadsQuery = AdmissionLead::where('school_id', $schoolId);

        // KPI Calculations
        $totalLeads = (clone $leadsQuery)->count();
        $newLeads = (clone $leadsQuery)->where('stage', 'new_lead')->count();
        $confirmedAdmissions = (clone $leadsQuery)->where('stage', 'admission_confirmed')->count();
        $conversionRate = $totalLeads > 0 ? round(($confirmedAdmissions / $totalLeads) * 100, 1) : 0.0;

        $applicationsInProgress = AdmissionApplication::where('school_id', $schoolId)
            ->whereIn('application_stage', ['draft', 'submitted', 'under_review', 'interview_scheduled'])
            ->count();

        // Follow-ups scheduled for TODAY
        $todayFollowUpsCount = LeadFollowUp::whereHas('lead', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })
            ->whereDate('scheduled_at', now()->format('Y-m-d'))
            ->where('status', 'pending')
            ->count();

        // OVERDUE Follow-ups
        $overdueFollowUpsCount = LeadFollowUp::whereHas('lead', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })
            ->whereDate('scheduled_at', '<', now()->format('Y-m-d'))
            ->where('status', 'pending')
            ->count();

        // Counselor Leaderboard
        $counselors = User::where('school_id', $schoolId)
            ->whereHas('roles', function ($q) {
                $q->whereIn('slug', ['counselor', 'admission-staff', 'school-admin', 'super-admin']);
            })
            ->withCount([
                'assignedLeads',
                'assignedLeads as converted_leads_count' => function ($q) {
                    $q->where('stage', 'admission_confirmed');
                },
            ])
            ->get();

        // Source Performance Metrics
        $sourceMetrics = LeadSource::where('school_id', $schoolId)
            ->withCount([
                'leads',
                'leads as converted_count' => function ($q) {
                    $q->where('stage', 'admission_confirmed');
                },
            ])
            ->get();

        // Campaign Performance Metrics
        $campaignMetrics = LeadCampaign::where('school_id', $schoolId)
            ->withCount([
                'leads',
                'leads as converted_count' => function ($q) {
                    $q->where('stage', 'admission_confirmed');
                },
            ])
            ->get();

        // Stage Distribution Summary
        $stageCounts = [
            'new_lead' => (clone $leadsQuery)->where('stage', 'new_lead')->count(),
            'contacted' => (clone $leadsQuery)->where('stage', 'contacted')->count(),
            'counselling' => (clone $leadsQuery)->where('stage', 'counselling')->count(),
            'campus_visit' => (clone $leadsQuery)->where('stage', 'campus_visit')->count(),
            'application_started' => (clone $leadsQuery)->where('stage', 'application_started')->count(),
            'documents_pending' => (clone $leadsQuery)->where('stage', 'documents_pending')->count(),
            'application_submitted' => (clone $leadsQuery)->where('stage', 'application_submitted')->count(),
            'under_review' => (clone $leadsQuery)->where('stage', 'under_review')->count(),
            'approved' => (clone $leadsQuery)->where('stage', 'approved')->count(),
            'admission_confirmed' => (clone $leadsQuery)->where('stage', 'admission_confirmed')->count(),
            'lost' => (clone $leadsQuery)->where('stage', 'lost')->count(),
        ];

        return view('livewire.admissions.dashboard', [
            'totalLeads' => $totalLeads,
            'newLeads' => $newLeads,
            'confirmedAdmissions' => $confirmedAdmissions,
            'conversionRate' => $conversionRate,
            'applicationsInProgress' => $applicationsInProgress,
            'todayFollowUpsCount' => $todayFollowUpsCount,
            'overdueFollowUpsCount' => $overdueFollowUpsCount,
            'counselors' => $counselors,
            'sourceMetrics' => $sourceMetrics,
            'campaignMetrics' => $campaignMetrics,
            'stageCounts' => $stageCounts,
        ])->layout('components.layouts.admin', ['title' => 'Admissions CRM Executive Dashboard']);
    }
}
