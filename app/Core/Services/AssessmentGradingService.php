<?php

namespace App\Core\Services;

use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\StudentTopicPerformance;
use Illuminate\Support\Facades\DB;

class AssessmentGradingService
{
    /**
     * Grade an attempt and calculate final score, result, and topic performance aggregation.
     */
    public function gradeAttempt(AssessmentAttempt $attempt): AssessmentResult
    {
        return DB::transaction(function () use ($attempt) {
            $attempt->load(['assessment.questions.options', 'answers.question.options', 'student']);

            $totalMarksPossible = 0.00;
            $totalMarksObtained = 0.00;
            $totalQuestions = $attempt->assessment->questions->count();
            $answeredCount = 0;

            // Map question items to marks
            $questionItems = $attempt->assessment->questions->keyBy('id');

            foreach ($questionItems as $questionId => $question) {
                $questionMarks = (float) ($question->pivot->marks ?? $question->marks);
                $totalMarksPossible += $questionMarks;

                /** @var AssessmentAnswer|null $answer */
                $answer = $attempt->answers->firstWhere('question_id', $questionId);

                if ($answer && (! is_null($answer->selected_option_ids) || ! is_null($answer->given_answer_text))) {
                    $answeredCount++;
                    $isCorrect = $this->evaluateQuestionAnswer($question, $answer);
                    $earnedMarks = $isCorrect ? $questionMarks : 0.00;

                    $answer->update([
                        'is_correct' => $isCorrect,
                        'marks_obtained' => $earnedMarks,
                    ]);

                    $totalMarksObtained += $earnedMarks;
                } else {
                    if ($answer) {
                        $answer->update([
                            'is_correct' => false,
                            'marks_obtained' => 0.00,
                        ]);
                    }
                }
            }

            $percentage = $totalMarksPossible > 0 ? round(($totalMarksObtained / $totalMarksPossible) * 100, 2) : 0.00;
            $passingMarks = (float) $attempt->assessment->passing_marks;
            $isPassed = $totalMarksObtained >= $passingMarks;

            // Update attempt record
            $attempt->update([
                'status' => 'graded',
                'submitted_at' => $attempt->submitted_at ?? now(),
                'total_questions' => $totalQuestions,
                'answered_questions' => $answeredCount,
                'total_marks_possible' => $totalMarksPossible,
                'total_marks_obtained' => $totalMarksObtained,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
            ]);

            // Calculate Grade
            $grade = $this->calculateGrade($percentage);

            // Create or update Assessment Result
            $result = AssessmentResult::updateOrCreate(
                ['attempt_id' => $attempt->id],
                [
                    'assessment_id' => $attempt->assessment_id,
                    'student_id' => $attempt->student_id,
                    'school_id' => $attempt->school_id,
                    'score' => $totalMarksObtained,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'is_passed' => $isPassed,
                    'published_at' => now(),
                ]
            );

            // Update Student Topic Performance aggregation
            $this->updateStudentTopicPerformance($attempt);

            return $result;
        });
    }

    /**
     * Evaluate correctness of a single answer.
     */
    public function evaluateQuestionAnswer(AssessmentQuestion $question, AssessmentAnswer $answer): bool
    {
        $type = $question->question_type;

        switch ($type) {
            case 'mcq':
            case 'single_answer':
            case 'true_false':
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $selectedIds = (array) ($answer->selected_option_ids ?? []);
                if (empty($selectedIds) || empty($correctOptionIds)) {
                    return false;
                }
                return count($selectedIds) === 1 && in_array($selectedIds[0], $correctOptionIds);

            case 'multiple_answer':
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->sort()->values()->toArray();
                $selectedIds = collect((array) ($answer->selected_option_ids ?? []))->map(fn ($val) => (int) $val)->sort()->values()->toArray();
                return $correctOptionIds === $selectedIds;

            case 'short_answer':
                if (empty($answer->given_answer_text) || empty($question->correct_answer)) {
                    return false;
                }
                $givenStr = strtolower(trim($answer->given_answer_text));
                $correctStr = strtolower(trim($question->correct_answer));
                return $givenStr === $correctStr;

            default:
                return false;
        }
    }

    /**
     * Grade tier conversion.
     */
    protected function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
    }

    /**
     * Aggregate topic performance for student based on attempts.
     */
    protected function updateStudentTopicPerformance(AssessmentAttempt $attempt): void
    {
        $answers = AssessmentAnswer::where('attempt_id', $attempt->id)
            ->with('question')
            ->get();

        foreach ($answers as $answer) {
            $question = $answer->question;
            if (! $question || ! $question->topic_id) {
                continue;
            }

            $topicId = $question->topic_id;
            $subjectId = $question->subject_id;
            $studentId = $attempt->student_id;
            $schoolId = $attempt->school_id;

            // Fetch or initialize topic performance record
            $perf = StudentTopicPerformance::firstOrCreate(
                [
                    'school_id' => $schoolId,
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'topic_id' => $topicId,
                ],
                [
                    'total_questions_attempted' => 0,
                    'correct_questions_count' => 0,
                    'accuracy_percentage' => 0.00,
                    'mastery_level' => 'developing',
                ]
            );

            $perf->increment('total_questions_attempted');
            if ($answer->is_correct) {
                $perf->increment('correct_questions_count');
            }

            $attempted = $perf->total_questions_attempted;
            $correct = $perf->correct_questions_count;
            $accuracy = $attempted > 0 ? round(($correct / $attempted) * 100, 2) : 0.00;

            $mastery = 'developing';
            if ($accuracy < 50) {
                $mastery = 'needs_improvement';
            } elseif ($accuracy < 70) {
                $mastery = 'developing';
            } elseif ($accuracy < 85) {
                $mastery = 'proficient';
            } else {
                $mastery = 'mastered';
            }

            $perf->update([
                'accuracy_percentage' => $accuracy,
                'mastery_level' => $mastery,
                'last_assessed_at' => now(),
            ]);
        }
    }
}
