<?php

namespace App\Core\Services;

use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssessmentEngineService
{
    /**
     * Create a new Assessment draft.
     */
    public function createAssessment(array $data, User $creator): Assessment
    {
        return DB::transaction(function () use ($data, $creator) {
            $schoolId = $data['school_id'] ?? $creator->school_id;

            $assessment = Assessment::create([
                'school_id' => $schoolId,
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'class_id' => $data['class_id'],
                'division_id' => $data['division_id'] ?? null,
                'subject_id' => $data['subject_id'],
                'created_by' => $creator->id,
                'title' => $data['title'],
                'code' => $data['code'] ?? ('TST-' . strtoupper(bin2hex(random_bytes(3)))),
                'type' => $data['type'] ?? 'weekly_test',
                'week_number' => $data['week_number'] ?? null,
                'duration_minutes' => $data['duration_minutes'] ?? 30,
                'passing_marks' => $data['passing_marks'] ?? 0.00,
                'difficulty' => $data['difficulty'] ?? 'medium',
                'instructions' => $data['instructions'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'status' => 'draft',
            ]);

            if (! empty($data['topic_ids'])) {
                foreach ((array) $data['topic_ids'] as $topicId) {
                    $assessment->assessmentSubjects()->create([
                        'subject_id' => $data['subject_id'],
                        'topic_id' => $topicId,
                    ]);
                }
            }

            if (! empty($data['question_ids'])) {
                $this->attachQuestionsToAssessment($assessment, $data['question_ids']);
            }

            return $assessment;
        });
    }

    /**
     * Attach questions to assessment and recalculate total questions and marks.
     */
    public function attachQuestionsToAssessment(Assessment $assessment, array $questionIds): void
    {
        DB::transaction(function () use ($assessment, $questionIds) {
            $assessment->questions()->detach();

            $order = 1;
            $totalMarks = 0.00;

            $questions = AssessmentQuestion::whereIn('id', $questionIds)->get();
            foreach ($questions as $q) {
                $marks = (float) $q->marks;
                $assessment->questions()->attach($q->id, [
                    'marks' => $marks,
                    'display_order' => $order++,
                ]);
                $totalMarks += $marks;
            }

            $assessment->update([
                'total_questions' => count($questionIds),
                'total_marks' => $totalMarks,
            ]);
        });
    }

    /**
     * Review AI / Draft questions & transitions test status.
     */
    public function transitionStatus(Assessment $assessment, string $newStatus, ?User $actor = null): bool
    {
        $validStatuses = ['draft', 'generated', 'review_required', 'published', 'active', 'completed', 'archived'];
        if (! in_array($newStatus, $validStatuses)) {
            return false;
        }

        // If publishing, ensure questions are approved
        if ($newStatus === 'published') {
            $unapprovedCount = $assessment->questions()->where('review_status', 'pending')->count();
            if ($unapprovedCount > 0) {
                throw new \InvalidArgumentException("Cannot publish assessment: {$unapprovedCount} questions are pending teacher review.");
            }
        }

        $assessment->update(['status' => $newStatus]);
        return true;
    }

    /**
     * Publish assessment and assign to target class / division / students.
     */
    public function publishAndAssignAssessment(Assessment $assessment, User $publisher, ?int $divisionId = null): AssessmentAssignment
    {
        return DB::transaction(function () use ($assessment, $publisher, $divisionId) {
            $this->transitionStatus($assessment, 'published', $publisher);
            $this->transitionStatus($assessment, 'active', $publisher);

            $assignment = AssessmentAssignment::create([
                'assessment_id' => $assessment->id,
                'school_id' => $assessment->school_id,
                'class_id' => $assessment->class_id,
                'division_id' => $divisionId ?? $assessment->division_id,
                'assigned_by' => $publisher->id,
                'due_at' => $assessment->end_date ?? now()->addDays(7),
                'max_attempts' => 1,
                'status' => 'assigned',
            ]);

            return $assignment;
        });
    }

    /**
     * Start a new test attempt for a student.
     */
    public function startAttempt(Assessment $assessment, Student $student): AssessmentAttempt
    {
        return DB::transaction(function () use ($assessment, $student) {
            // Check if active attempt already exists
            $activeAttempt = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('student_id', $student->id)
                ->where('status', 'in_progress')
                ->first();

            if ($activeAttempt) {
                return $activeAttempt;
            }

            $attemptCount = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('student_id', $student->id)
                ->count();

            $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
                ->where('school_id', $student->school_id)
                ->where('class_id', $student->class_id)
                ->first();

            $attempt = AssessmentAttempt::create([
                'assessment_id' => $assessment->id,
                'assignment_id' => $assignment?->id,
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'attempt_number' => $attemptCount + 1,
                'started_at' => now(),
                'status' => 'in_progress',
                'total_questions' => $assessment->total_questions ?? 0,
                'answered_questions' => 0,
                'total_marks_possible' => $assessment->total_marks ?? 0.00,
                'total_marks_obtained' => 0.00,
            ]);

            return $attempt;
        });
    }
}
