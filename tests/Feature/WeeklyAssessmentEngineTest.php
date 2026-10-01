<?php

namespace Tests\Feature;

use App\Core\Services\AssessmentEngineService;
use App\Core\Services\AssessmentGradingService;
use App\Livewire\Assessments\AssessmentDashboard;
use App\Livewire\Assessments\AssessmentManager;
use App\Livewire\Assessments\QuestionBank;
use App\Livewire\Assessments\StudentTestPlayer;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentTopic;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentTopicPerformance;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WeeklyAssessmentEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherUser;
    protected User $studentUser;
    protected School $school;
    protected SchoolClass $class;
    protected Subject $subject;
    protected AssessmentTopic $topic;
    protected Student $student;
    protected AssessmentEngineService $engineService;
    protected AssessmentGradingService $gradingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first() ?? School::create(['name' => 'SmartSchool Academy', 'code' => 'SSA-01']);
        $department = Department::first() ?? Department::create(['school_id' => $this->school->id, 'name' => 'Science']);
        $this->class = SchoolClass::first() ?? SchoolClass::create(['department_id' => $department->id, 'name' => 'Grade 10']);
        $this->subject = Subject::first() ?? Subject::create(['department_id' => $department->id, 'name' => 'Mathematics', 'code' => 'MATH-10']);

        $this->topic = AssessmentTopic::create([
            'school_id' => $this->school->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'name' => 'Algebra & Polynomials',
            'code' => 'ALG-101',
        ]);

        $this->teacherUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Math Teacher',
            'email' => 'math.teacher@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $this->teacherUser->assignRole('teacher')->load('roles');

        $this->studentUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'John Student',
            'email' => 'john.student@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $this->studentUser->assignRole('student')->load('roles');

        $this->student = Student::create([
            'school_id' => $this->school->id,
            'user_id' => $this->studentUser->id,
            'class_id' => $this->class->id,
            'student_id_number' => 'STU-9901',
            'admission_number' => 'ADM-9901',
            'first_name' => 'John',
            'last_name' => 'Student',
            'gender' => 'male',
            'date_of_birth' => '2010-05-15',
            'admission_date' => '2026-01-10',
            'status' => 'active',
        ]);

        $this->engineService = app(AssessmentEngineService::class);
        $this->gradingService = app(AssessmentGradingService::class);
    }

    public function test_teacher_can_access_question_bank_and_create_questions(): void
    {
        Livewire::actingAs($this->teacherUser)
            ->test(QuestionBank::class)
            ->set('subject_id', $this->subject->id)
            ->set('topic_id', $this->topic->id)
            ->set('question_text', 'What is the root of x^2 - 4 = 0?')
            ->set('question_type', 'mcq')
            ->set('difficulty', 'medium')
            ->set('marks', 2.00)
            ->set('options', [
                ['key' => 'A', 'text' => '2 and -2', 'is_correct' => true],
                ['key' => 'B', 'text' => '0 and 4', 'is_correct' => false],
                ['key' => 'C', 'text' => '1 and 3', 'is_correct' => false],
                ['key' => 'D', 'text' => 'None', 'is_correct' => false],
            ])
            ->call('saveQuestion')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('assessment_questions', [
            'school_id' => $this->school->id,
            'question_text' => 'What is the root of x^2 - 4 = 0?',
            'question_type' => 'mcq',
            'review_status' => 'approved',
        ]);
    }

    public function test_teacher_review_workflow_approves_pending_ai_question(): void
    {
        $question = AssessmentQuestion::create([
            'school_id' => $this->school->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'AI Generated Question text...',
            'question_type' => 'true_false',
            'is_ai_generated' => true,
            'review_status' => 'pending',
            'status' => 'active',
        ]);

        Livewire::actingAs($this->teacherUser)
            ->test(QuestionBank::class)
            ->call('approveQuestion', $question->id)
            ->assertHasNoErrors();

        $this->assertEquals('approved', $question->fresh()->review_status);
        $this->assertEquals($this->teacherUser->id, $question->fresh()->reviewed_by);
    }

    public function test_assessment_engine_creates_and_attaches_questions(): void
    {
        $q1 = AssessmentQuestion::create([
            'school_id' => $this->school->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'Solve 2x = 10',
            'question_type' => 'short_answer',
            'correct_answer' => '5',
            'marks' => 2.00,
            'review_status' => 'approved',
        ]);

        $assessment = $this->engineService->createAssessment([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'title' => 'Algebra Weekly Test 1',
            'type' => 'weekly_test',
            'duration_minutes' => 30,
            'passing_marks' => 2.00,
            'question_ids' => [$q1->id],
        ], $this->teacherUser);

        $this->assertInstanceOf(Assessment::class, $assessment);
        $this->assertEquals(1, $assessment->total_questions);
        $this->assertEquals(2.00, $assessment->total_marks);
    }

    public function test_cannot_publish_assessment_with_pending_unapproved_questions(): void
    {
        $pendingQuestion = AssessmentQuestion::create([
            'school_id' => $this->school->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'Unreviewed AI Question',
            'question_type' => 'short_answer',
            'review_status' => 'pending',
        ]);

        $assessment = $this->engineService->createAssessment([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'title' => 'Unreviewed Test',
            'question_ids' => [$pendingQuestion->id],
        ], $this->teacherUser);

        $this->expectException(\InvalidArgumentException::class);
        $this->engineService->publishAndAssignAssessment($assessment, $this->teacherUser);
    }

    public function test_student_can_start_attempt_autosave_answers_and_submit_for_auto_grading(): void
    {
        // 1. Setup Question with Options
        $q1 = AssessmentQuestion::create([
            'school_id' => $this->school->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'What is 5 + 5?',
            'question_type' => 'mcq',
            'marks' => 5.00,
            'review_status' => 'approved',
        ]);

        $optCorrect = AssessmentQuestionOption::create([
            'question_id' => $q1->id,
            'option_key' => 'A',
            'option_text' => '10',
            'is_correct' => true,
        ]);

        $optWrong = AssessmentQuestionOption::create([
            'question_id' => $q1->id,
            'option_key' => 'B',
            'option_text' => '12',
            'is_correct' => false,
        ]);

        // 2. Create Assessment & Publish
        $assessment = $this->engineService->createAssessment([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'title' => 'Basic Addition Weekly Test',
            'passing_marks' => 3.00,
            'question_ids' => [$q1->id],
        ], $this->teacherUser);

        $this->engineService->publishAndAssignAssessment($assessment, $this->teacherUser);

        // 3. Start Attempt as Student
        $attempt = $this->engineService->startAttempt($assessment, $this->student);
        $this->assertEquals('in_progress', $attempt->status);

        // 4. Student accesses test player page
        $response = $this->actingAs($this->studentUser)->get(route('assessments.take', ['attemptId' => $attempt->id]));
        $response->assertStatus(200);
        $response->assertSee('Basic Addition Weekly Test');

        // 5. Select answer & Grade attempt
        AssessmentAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'selected_option_ids' => [$optCorrect->id],
            'answered_at' => now(),
        ]);

        $result = $this->gradingService->gradeAttempt($attempt);

        $attempt->refresh();
        $this->assertEquals('graded', $attempt->status);
        $this->assertEquals(5.00, $attempt->total_marks_obtained);
        $this->assertEquals(100.00, $attempt->percentage);
        $this->assertTrue($attempt->is_passed);

        // Verify Student Topic Performance record
        $this->assertDatabaseHas('student_topic_performance', [
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'topic_id' => $this->topic->id,
            'accuracy_percentage' => 100.00,
            'mastery_level' => 'mastered',
        ]);
    }

    public function test_authorization_policies_prevent_student_from_viewing_others_attempts(): void
    {
        $otherStudentUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Other Student',
            'email' => 'other.student@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $otherStudentUser->assignRole('student');

        $assessment = $this->engineService->createAssessment([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'title' => 'Security Isolation Test',
        ], $this->teacherUser);

        $attempt = $this->engineService->startAttempt($assessment, $this->student);

        // Unauthorized student should be denied access
        $this->actingAs($otherStudentUser);
        $this->assertFalse($otherStudentUser->can('view', $attempt));
    }

    public function test_assessment_dashboard_renders_role_specific_views(): void
    {
        Livewire::actingAs($this->teacherUser)
            ->test(AssessmentDashboard::class)
            ->assertSee('AI Weekly Assessment Intelligence')
            ->assertSee('Topic Mastery');

        Livewire::actingAs($this->studentUser)
            ->test(AssessmentDashboard::class)
            ->assertSee('Assigned Weekly Tests');
    }
}
