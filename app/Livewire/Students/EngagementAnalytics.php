<?php

namespace App\Livewire\Students;

use App\Core\Services\StudentEngagementService;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEngagementRecord;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EngagementAnalytics extends Component
{
    use AuthorizesRequests;

    public string $search = '';
    public string $statusFilter = 'all'; // all, normal, needs_attention, followup_recommended
    public ?int $classFilter = null;

    // Underlying Evidence Drawer State
    public bool $showUnderlyingDrawer = false;
    public ?int $selectedStudentId = null;
    public ?string $activeStudentName = null;
    public ?StudentEngagementRecord $activeRecord = null;

    public function mount(): void
    {
        $this->ensureRecordsEvaluated();
    }

    public function ensureRecordsEvaluated(): void
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? School::first()->id ?? 1;

        $students = Student::where('school_id', $schoolId)->where('status', 'active')->get();
        $service = app(StudentEngagementService::class);

        foreach ($students as $student) {
            if (! StudentEngagementRecord::where('student_id', $student->id)->exists()) {
                $service->evaluateStudent($student);
            }
        }
    }

    public function recalculateAll(): void
    {
        $this->authorize('viewAny', Student::class);

        $user = Auth::user();
        $schoolId = $user->school_id ?? School::first()->id ?? 1;

        $students = Student::where('school_id', $schoolId)->where('status', 'active')->get();
        $service = app(StudentEngagementService::class);

        foreach ($students as $student) {
            $service->evaluateStudent($student);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Engagement Recalculated',
            'message' => 'Student engagement metrics and neutral indicators refreshed.',
        ]);
    }

    public function viewUnderlyingEvidence(int $studentId): void
    {
        $this->authorize('viewAny', Student::class);

        $record = StudentEngagementRecord::with('student.schoolClass')->where('student_id', $studentId)->first();

        if ($record) {
            $this->selectedStudentId = $studentId;
            $this->activeStudentName = $record->student?->full_name ?? 'Student';
            $this->activeRecord = $record;
            $this->showUnderlyingDrawer = true;
        }
    }

    public function closeDrawer(): void
    {
        $this->showUnderlyingDrawer = false;
        $this->selectedStudentId = null;
        $this->activeRecord = null;
    }

    public function render()
    {
        $this->authorize('viewAny', Student::class);

        $user = Auth::user();
        $schoolId = $user->school_id ?? School::first()->id ?? 1;

        $classes = SchoolClass::whereHas('department', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->orWhereDoesntHave('department')->orderBy('year_level')->get();

        $query = StudentEngagementRecord::with(['student.schoolClass', 'student.division'])
            ->where('school_id', $schoolId);

        if ($this->statusFilter && $this->statusFilter !== 'all') {
            $query->where('overall_status', $this->statusFilter);
        }

        if ($this->classFilter) {
            $query->whereHas('student', function ($q) {
                $q->where('class_id', $this->classFilter);
            });
        }

        if (! empty($this->search)) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('admission_number', 'like', '%' . $this->search . '%');
            });
        }

        $engagementRecords = $query->paginate(15);

        // Aggregate Summary Stats
        $totalEvaluated = StudentEngagementRecord::where('school_id', $schoolId)->count();
        $normalCount = StudentEngagementRecord::where('school_id', $schoolId)->where('overall_status', 'normal')->count();
        $attentionCount = StudentEngagementRecord::where('school_id', $schoolId)->where('overall_status', 'needs_attention')->count();
        $followupCount = StudentEngagementRecord::where('school_id', $schoolId)->where('overall_status', 'followup_recommended')->count();

        $avgAttendance = round(StudentEngagementRecord::where('school_id', $schoolId)->avg('attendance_percentage') ?? 90, 1);
        $avgAssignment = round(StudentEngagementRecord::where('school_id', $schoolId)->avg('assignment_completion_rate') ?? 88, 1);

        return view('livewire.students.engagement-analytics', [
            'engagementRecords' => $engagementRecords,
            'classes' => $classes,
            'totalEvaluated' => $totalEvaluated,
            'normalCount' => $normalCount,
            'attentionCount' => $attentionCount,
            'followupCount' => $followupCount,
            'avgAttendance' => $avgAttendance,
            'avgAssignment' => $avgAssignment,
        ])->layout('components.layouts.admin', ['title' => 'Student Engagement Analytics']);
    }
}
