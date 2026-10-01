<?php

namespace App\Livewire\Assessments;

use App\Core\Services\AssessmentGradingService;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentResult;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentTestPlayer extends Component
{
    use AuthorizesRequests;

    public int $attemptId;
    public int $currentQuestionIndex = 0;
    public array $selectedOptions = []; // question_id => array of option_ids
    public array $textAnswers = []; // question_id => text

    public bool $showSubmitConfirmModal = false;
    public bool $isSubmitted = false;
    public ?int $resultId = null;

    public function mount(int $attemptId)
    {
        $this->attemptId = $attemptId;
        $attempt = $this->getAttempt();
        $this->authorize('takeTest', $attempt);

        $this->isSubmitted = in_array($attempt->status, ['submitted', 'graded']);

        if ($this->isSubmitted && $attempt->result) {
            $this->resultId = $attempt->result->id;
        }

        // Load existing saved answers
        $existingAnswers = AssessmentAnswer::where('attempt_id', $attempt->id)->get();
        foreach ($existingAnswers as $ans) {
            $this->selectedOptions[$ans->question_id] = $ans->selected_option_ids ?? [];
            $this->textAnswers[$ans->question_id] = $ans->given_answer_text ?? '';
        }
    }

    public function getAttempt(): AssessmentAttempt
    {
        return AssessmentAttempt::with(['assessment.questions.options', 'student', 'result'])->findOrFail($this->attemptId);
    }

    public function getQuestionsData(): array
    {
        $attempt = $this->getAttempt();
        $assessment = $attempt->assessment;

        return $assessment->questions->map(function ($q, $idx) {
            return [
                'id' => (int) $q->id,
                'index' => $idx + 1,
                'text' => (string) $q->question_text,
                'type' => (string) $q->question_type,
                'marks' => (float) ($q->pivot->marks ?? $q->marks),
                'options' => $q->options->map(fn ($o) => [
                    'id' => (int) $o->id,
                    'key' => (string) $o->option_key,
                    'text' => (string) $o->option_text,
                ])->values()->all(),
            ];
        })->values()->all();
    }

    public function selectOption(int $questionId, int $optionId, string $type)
    {
        if ($this->isSubmitted) return;

        if ($type === 'multiple_answer') {
            $selected = $this->selectedOptions[$questionId] ?? [];
            if (in_array($optionId, $selected)) {
                $selected = array_values(array_diff($selected, [$optionId]));
            } else {
                $selected[] = $optionId;
            }
            $this->selectedOptions[$questionId] = $selected;
        } else {
            $this->selectedOptions[$questionId] = [$optionId];
        }

        $this->autosaveAnswer($questionId);
    }

    public function updatedTextAnswers($value, $questionId)
    {
        if ($this->isSubmitted) return;
        $this->autosaveAnswer((int) $questionId);
    }

    public function autosaveAnswer(int $questionId)
    {
        if ($this->isSubmitted) return;

        $opts = $this->selectedOptions[$questionId] ?? [];
        $text = $this->textAnswers[$questionId] ?? null;

        AssessmentAnswer::updateOrCreate(
            [
                'attempt_id' => $this->attemptId,
                'question_id' => $questionId,
            ],
            [
                'selected_option_ids' => $opts,
                'given_answer_text' => $text,
                'answered_at' => now(),
            ]
        );

        $answeredCount = AssessmentAnswer::where('attempt_id', $this->attemptId)
            ->where(function ($q) {
                $q->whereNotNull('given_answer_text')->orWhereNotNull('selected_option_ids');
            })->count();

        AssessmentAttempt::where('id', $this->attemptId)->update(['answered_questions' => $answeredCount]);
    }

    public function goToQuestion(int $index)
    {
        $questionsData = $this->getQuestionsData();
        if ($index >= 0 && $index < count($questionsData)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function nextQuestion()
    {
        $questionsData = $this->getQuestionsData();
        if ($this->currentQuestionIndex < count($questionsData) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function submitTest(AssessmentGradingService $gradingService)
    {
        if ($this->isSubmitted) return;

        $attempt = $this->getAttempt();
        $this->authorize('takeTest', $attempt);

        // Grade test automatically
        $result = $gradingService->gradeAttempt($attempt);

        $this->resultId = $result->id;
        $this->isSubmitted = true;
        $this->showSubmitConfirmModal = false;

        $this->dispatch('notify', message: 'Test submitted and graded successfully!');
    }

    public function render()
    {
        $attempt = $this->getAttempt();
        $assessment = $attempt->assessment;
        $result = $this->resultId ? AssessmentResult::find($this->resultId) : null;
        $questionsData = $this->getQuestionsData();

        $totalQuestions = count($questionsData);
        $currentQuestion = $questionsData[$this->currentQuestionIndex] ?? null;

        $answeredCount = 0;
        foreach ($questionsData as $qd) {
            $qId = $qd['id'];
            if (! empty($this->selectedOptions[$qId]) || ! empty($this->textAnswers[$qId])) {
                $answeredCount++;
            }
        }
        $unansweredCount = $totalQuestions - $answeredCount;

        return view('livewire.assessments.student-test-player', [
            'attempt' => $attempt,
            'assessment' => $assessment,
            'result' => $result,
            'questionsData' => $questionsData,
            'totalQuestions' => $totalQuestions,
            'currentQuestion' => $currentQuestion,
            'answeredCount' => $answeredCount,
            'unansweredCount' => $unansweredCount,
        ])->layout('components.layouts.admin', ['title' => 'Student Test Player']);
    }
}
