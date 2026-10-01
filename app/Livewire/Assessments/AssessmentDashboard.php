<?php

namespace App\Livewire\Assessments;

use App\Core\Services\AssessmentEngineService;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentResult;
use App\Models\Student;
use App\Models\StudentTopicPerformance;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssessmentDashboard extends Component
{
    use AuthorizesRequests;

    public string $activeRoleTab = 'teacher'; // teacher, student, parent, admin

    public function mount()
    {
        $user = Auth::user();
        if ($user->hasRole('student')) {
            $this->activeRoleTab = 'student';
        } elseif ($user->hasRole('parent')) {
            $this->activeRoleTab = 'parent';
        } elseif ($user->hasRole(['super-admin', 'school-admin'])) {
            $this->activeRoleTab = 'admin';
        } else {
            $this->activeRoleTab = 'teacher';
        }
    }

    public function startStudentTest(int $assessmentId, AssessmentEngineService $service)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->firstOrFail();
        $assessment = Assessment::findOrFail($assessmentId);

        $attempt = $service->startAttempt($assessment, $student);

        return redirect()->route('assessments.take', ['attemptId' => $attempt->id]);
    }

    public function render()
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        // Teacher Stats
        $teacherTotalCreated = Assessment::where('school_id', $schoolId)->count();
        $teacherPendingReview = Assessment::where('school_id', $schoolId)->where('status', 'review_required')->count();
        $teacherActive = Assessment::where('school_id', $schoolId)->whereIn('status', ['published', 'active'])->count();
        $teacherCompleted = Assessment::where('school_id', $schoolId)->where('status', 'completed')->count();

        // Topic Performance List
        $topicPerformances = StudentTopicPerformance::where('school_id', $schoolId)
            ->with(['student', 'subject', 'topic'])
            ->orderBy('accuracy_percentage', 'asc')
            ->take(10)
            ->get();

        // Student Specific Data
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        $studentAssignments = [];
        $studentAttempts = [];
        if ($student) {
            $studentAssignments = AssessmentAssignment::where('school_id', $schoolId)
                ->where('class_id', $student->class_id)
                ->with(['assessment.subject', 'assessment.schoolClass'])
                ->get();

            $studentAttempts = AssessmentAttempt::where('student_id', $student->id)
                ->with(['assessment.subject', 'result'])
                ->orderBy('id', 'desc')
                ->get();
        }

        // Parent Specific Data
        $parentCompletedResults = [];
        $weakTopics = [];
        if ($user->hasRole('parent')) {
            $parentStudents = Student::where('school_id', $schoolId)->get(); // sample children for demo
            $parentStudentIds = $parentStudents->pluck('id')->toArray();

            $parentCompletedResults = AssessmentResult::whereIn('student_id', $parentStudentIds)
                ->with(['assessment.subject', 'student'])
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

            $weakTopics = StudentTopicPerformance::whereIn('student_id', $parentStudentIds)
                ->where('mastery_level', 'needs_improvement')
                ->with(['student', 'subject', 'topic'])
                ->get();
        }

        return view('livewire.assessments.assessment-dashboard', [
            'teacherTotalCreated' => $teacherTotalCreated,
            'teacherPendingReview' => $teacherPendingReview,
            'teacherActive' => $teacherActive,
            'teacherCompleted' => $teacherCompleted,
            'topicPerformances' => $topicPerformances,
            'studentAssignments' => $studentAssignments,
            'studentAttempts' => $studentAttempts,
            'parentCompletedResults' => $parentCompletedResults,
            'weakTopics' => $weakTopics,
        ])->layout('components.layouts.admin', ['title' => 'Weekly Assessment Intelligence Dashboard']);
    }
}
