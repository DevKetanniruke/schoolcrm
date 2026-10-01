<?php

namespace App\Livewire\Assessments;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentTopic;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionBank extends Component
{
    use AuthorizesRequests, WithPagination;

    // Filters
    public string $search = '';
    public ?int $selectedSubjectId = null;
    public ?int $selectedTopicId = null;
    public string $selectedDifficulty = '';
    public string $selectedQuestionType = '';
    public string $selectedReviewStatus = '';

    // Modal state
    public bool $showQuestionModal = false;
    public ?int $editingQuestionId = null;

    // Form inputs
    public ?int $subject_id = null;
    public ?int $topic_id = null;
    public string $question_text = '';
    public string $question_type = 'mcq'; // mcq, true_false, single_answer, multiple_answer, short_answer
    public string $difficulty = 'medium'; // easy, medium, hard
    public float $marks = 1.00;
    public string $correct_answer = '';
    public string $explanation = '';
    public string $source_reference = '';
    public array $options = [];

    public function mount()
    {
        $this->authorize('viewAny', AssessmentQuestion::class);
        $this->initDefaultOptions();
    }

    public function initDefaultOptions()
    {
        $this->options = [
            ['key' => 'A', 'text' => '', 'is_correct' => false],
            ['key' => 'B', 'text' => '', 'is_correct' => false],
            ['key' => 'C', 'text' => '', 'is_correct' => false],
            ['key' => 'D', 'text' => '', 'is_correct' => false],
        ];
    }

    public function updatedQuestionType($value)
    {
        if ($value === 'true_false') {
            $this->options = [
                ['key' => 'A', 'text' => 'True', 'is_correct' => true],
                ['key' => 'B', 'text' => 'False', 'is_correct' => false],
            ];
        } else {
            $this->initDefaultOptions();
        }
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingQuestionId = null;
        $this->question_text = '';
        $this->question_type = 'mcq';
        $this->difficulty = 'medium';
        $this->marks = 1.00;
        $this->correct_answer = '';
        $this->explanation = '';
        $this->source_reference = '';
        $this->initDefaultOptions();

        $user = Auth::user();
        $firstSubject = Subject::first();
        $this->subject_id = $this->selectedSubjectId ?? $firstSubject?->id;

        $this->showQuestionModal = true;
    }

    public function saveQuestion()
    {
        $this->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string|min:5',
            'question_type' => 'required|in:mcq,true_false,single_answer,multiple_answer,short_answer',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:0.5',
        ]);

        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        if ($this->editingQuestionId) {
            $question = AssessmentQuestion::findOrFail($this->editingQuestionId);
            $this->authorize('update', $question);

            $question->update([
                'subject_id' => $this->subject_id,
                'topic_id' => $this->topic_id,
                'question_text' => $this->question_text,
                'question_type' => $this->question_type,
                'difficulty' => $this->difficulty,
                'marks' => $this->marks,
                'correct_answer' => $this->correct_answer,
                'explanation' => $this->explanation,
                'source_reference' => $this->source_reference,
            ]);

            $question->options()->delete();
        } else {
            $this->authorize('create', AssessmentQuestion::class);

            $question = AssessmentQuestion::create([
                'school_id' => $schoolId,
                'subject_id' => $this->subject_id,
                'topic_id' => $this->topic_id,
                'created_by' => $user->id,
                'question_text' => $this->question_text,
                'question_type' => $this->question_type,
                'difficulty' => $this->difficulty,
                'marks' => $this->marks,
                'correct_answer' => $this->correct_answer,
                'explanation' => $this->explanation,
                'source_reference' => $this->source_reference,
                'is_ai_generated' => false,
                'review_status' => 'approved',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
        }

        // Save options if applicable
        if (in_array($this->question_type, ['mcq', 'true_false', 'single_answer', 'multiple_answer'])) {
            foreach ($this->options as $idx => $opt) {
                if (! empty($opt['text'])) {
                    AssessmentQuestionOption::create([
                        'question_id' => $question->id,
                        'option_key' => $opt['key'] ?? chr(65 + $idx),
                        'option_text' => $opt['text'],
                        'is_correct' => (bool) ($opt['is_correct'] ?? false),
                        'display_order' => $idx + 1,
                    ]);
                }
            }
        }

        $this->showQuestionModal = false;
        $this->dispatch('notify', message: 'Question successfully saved to Question Bank.');
    }

    public function approveQuestion(int $id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        $this->authorize('review', $question);

        $question->update([
            'review_status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->dispatch('notify', message: 'Question approved for use in assessments.');
    }

    public function rejectQuestion(int $id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        $this->authorize('review', $question);

        $question->update([
            'review_status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->dispatch('notify', message: 'Question rejected.');
    }

    public function deleteQuestion(int $id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        $this->authorize('delete', $question);
        $question->delete();

        $this->dispatch('notify', message: 'Question removed from Question Bank.');
    }

    public function render()
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $subjects = Subject::get();
        $topics = $this->selectedSubjectId ? AssessmentTopic::where('subject_id', $this->selectedSubjectId)->get() : AssessmentTopic::get();

        $query = AssessmentQuestion::where('school_id', $schoolId)
            ->with(['subject', 'topic', 'creator', 'options'])
            ->when($this->search, fn ($q) => $q->where('question_text', 'like', '%' . $this->search . '%'))
            ->when($this->selectedSubjectId, fn ($q) => $q->where('subject_id', $this->selectedSubjectId))
            ->when($this->selectedTopicId, fn ($q) => $q->where('topic_id', $this->selectedTopicId))
            ->when($this->selectedDifficulty, fn ($q) => $q->where('difficulty', $this->selectedDifficulty))
            ->when($this->selectedQuestionType, fn ($q) => $q->where('question_type', $this->selectedQuestionType))
            ->when($this->selectedReviewStatus, fn ($q) => $q->where('review_status', $this->selectedReviewStatus))
            ->orderBy('id', 'desc');

        $questions = $query->paginate(12);

        return view('livewire.assessments.question-bank', [
            'questions' => $questions,
            'subjects' => $subjects,
            'topics' => $topics,
        ])->layout('components.layouts.admin', ['title' => 'Question Bank & Review']);
    }
}
