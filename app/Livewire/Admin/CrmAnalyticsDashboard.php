<?php

namespace App\Livewire\Admin;

use App\Core\Services\CrmAnalyticsService;
use App\Jobs\RefreshCrmAnalyticsCacheJob;
use App\Models\LeadSource;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CrmAnalyticsDashboard extends Component
{
    public string $selectedDashboard = 'executive';

    // Filters
    public string $datePreset = 'this_month';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $schoolId = 1;
    public ?int $classId = null;
    public ?int $sourceId = null;
    public ?int $counselorId = null;

    public bool $isRefreshing = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->school_id) {
            $this->schoolId = $user->school_id;
        }
    }

    public function setDashboard(string $dashboard)
    {
        if (in_array($dashboard, ['executive', 'admissions', 'communication', 'student_engagement', 'parent_engagement', 'staff_tasks'])) {
            $this->selectedDashboard = $dashboard;
        }
    }

    public function updatedDatePreset($value)
    {
        if ($value !== 'custom') {
            $this->startDate = null;
            $this->endDate = null;
        }
    }

    public function resetFilters()
    {
        $this->datePreset = 'this_month';
        $this->startDate = null;
        $this->endDate = null;
        $this->classId = null;
        $this->sourceId = null;
        $this->counselorId = null;
    }

    public function refreshCache()
    {
        RefreshCrmAnalyticsCacheJob::dispatch($this->schoolId);
        $this->dispatch('notify', message: __('messages.analytics_cache_refresh_queued') ?? 'Analytics cache refresh initiated in background.');
    }

    public function render(CrmAnalyticsService $analyticsService)
    {
        $filters = [
            'school_id' => $this->schoolId ?? 1,
            'date_preset' => $this->datePreset,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'class_id' => $this->classId,
            'source_id' => $this->sourceId,
            'counselor_id' => $this->counselorId,
        ];

        // Fetch metrics for currently active tab
        $metrics = match ($this->selectedDashboard) {
            'admissions' => $analyticsService->getAdmissionsMetrics($filters),
            'communication' => $analyticsService->getCommunicationMetrics($filters),
            'student_engagement' => $analyticsService->getStudentEngagementMetrics($filters),
            'parent_engagement' => $analyticsService->getParentEngagementMetrics($filters),
            'staff_tasks' => $analyticsService->getStaffTasksMetrics($filters),
            default => $analyticsService->getExecutiveMetrics($filters),
        };

        // Filter dropdown options
        $schools = School::select('id', 'name')->get();
        $classes = SchoolClass::where('school_id', $this->schoolId ?? 1)->select('id', 'name')->get();
        $sources = LeadSource::where('school_id', $this->schoolId ?? 1)->select('id', 'name')->get();
        $counselors = User::where('school_id', $this->schoolId ?? 1)->select('id', 'name')->get();

        return view('livewire.admin.crm-analytics-dashboard', [
            'metrics' => $metrics,
            'schools' => $schools,
            'classes' => $classes,
            'sources' => $sources,
            'counselors' => $counselors,
        ])->layout('components.layouts.admin', ['title' => 'School CRM Analytics']);
    }
}
