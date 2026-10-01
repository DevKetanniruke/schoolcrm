<?php

namespace App\Livewire\Assessments;

use App\Core\Services\AssessmentEngineService;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentTopic;
use App\Models\Division;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AssessmentManager extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $activeTab = 'list'; // list, create, review

    // Search & Filters
    public string $search = '';
    public ?int $filterClassId = null;
    public ?int $filterSubjectId = null;
    public string $filterStatus = '';

    // Create / Edit Assessment Form
    public ?int $assessmentId = null;
    public string $title = '';
    public string $type = 'weekly_test';
    public ?int $academic_year_id = null;
    public ?int $class_id = null;
    public ?int $division_id = null;
    public ?int $subject_id = null;
    public int $duration_minutes = 30;
    public float $passing_marks = 10.00;
    public string $difficulty = 'medium';
    public ?string $instructions = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public array $selected_question_ids = [];

    // Review Modal State
    public ?Assessment $reviewingAssessment = null;

    public function mount()
    {
        $this->authorize('viewAny', Assessment::class);
    }

    public function openCreateTab()
    {
        $this->resetValidation();
        $this->assessmentId = null;
        $this->title = '';
        $this->type = 'weekly_test';
        $this->duration_minutes = 30;
        $this->passing_marks = 10.00;
        $this->difficulty = 'medium';
        $this->instructions = '';
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->end_date = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->selected_question_ids = [];

        $firstClass = SchoolClass::first();
        $firstSubject = Subject::first();
        $currentYear = AcademicYear::where('is_current', true)->first();

        $this->class_id = $firstClass?->id;
        $this->subject_id = $firstSubject?->id;
        $this->academic_year_id = $currentYear?->id;

        $this->activeTab = 'create';
    }

    public function saveAssessment(AssessmentEngineService $service)
    {
        $this->validate([
            'title' => 'required|string|min:4',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:weekly_test,practice_test,revision_test',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_marks' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        if ($this->assessmentId) {
            $assessment = Assessment::findOrFail($this->assessmentId);
            $this->authorize('update', $assessment);

            $assessment->update([
                'title' => $this->title,
                'type' => $this->type,
                'academic_year_id' => $this->academic_year_id,
                'class_id' => $this->class_id,
                'division_id' => $this->division_id,
                'subject_id' => $this->subject_id,
                'duration_minutes' => $this->duration_minutes,
                'passing_marks' => $this->passing_marks,
                'difficulty' => $this->difficulty,
                'instructions' => $this->instructions,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            if (! empty($this->selected_question_ids)) {
                $service->attachQuestionsToAssessment($assessment, $this->selected_question_ids);
            }
        } else {
            $this->authorize('create', Assessment::class);

            $assessment = $service->createAssessment([
                'school_id' => $user->school_id ?? 1,
                'academic_year_id' => $this->academic_year_id,
                'class_id' => $this->class_id,
                'division_id' => $this->division_id,
                'subject_id' => $this->subject_id,
                'title' => $this->title,
                'type' => $this->type,
                'duration_minutes' => $this->duration_minutes,
                'passing_marks' => $this->passing_marks,
                'difficulty' => $this->difficulty,
                'instructions' => $this->instructions,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'question_ids' => $this->selected_question_ids,
            ], $user);
        }

        $this->activeTab = 'list';
        $this->dispatch('notify', message: 'Assessment saved successfully as draft.');
    }

    public function publishAssessment(int $id, AssessmentEngineService $service)
    {
        $assessment = Assessment::findOrFail($id);
        $this->authorize('publish', $assessment);

        try {
            $service->publishAndAssignAssessment($assessment, Auth::user());
            $this->dispatch('notify', message: 'Assessment published and assigned to class students.');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function archiveAssessment(int $id)
    {
        $assessment = Assessment::findOrFail($id);
        $this->authorize('delete', $assessment);
        $assessment->update(['status' => 'archived']);

        $this->dispatch('notify', message: 'Assessment archived.');
    }

    public function render()
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $classes = SchoolClass::get();
        $divisions = $this->class_id ? Division::where('class_id', $this->class_id)->get() : Division::get();
        $subjects = Subject::get();
        $academicYears = AcademicYear::get();

        $bankQuestions = [];
        if ($this->subject_id) {
            $bankQuestions = AssessmentQuestion::where('school_id', $schoolId)
                ->where('subject_id', $this->subject_id)
                ->where('review_status', 'approved')
                ->get();
        }

        $assessments = Assessment::where('school_id', $schoolId)
            ->with(['schoolClass', 'division', 'subject', 'creator', 'questions'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->filterClassId, fn ($q) => $q->where('class_id', $this->filterClassId))
            ->when($this->filterSubjectId, fn ($q) => $q->where('subject_id', $this->filterSubjectId))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.assessments.assessment-manager', [
            'assessments' => $assessments,
            'classes' => $classes,
            'divisions' => $divisions,
            'subjects' => $subjects,
            'academicYears' => $academicYears,
            'bankQuestions' => $bankQuestions,
        ])->layout('components.layouts.admin', ['title' => 'Weekly Test & Assessment Manager']);
    }
}
