<?php

namespace Tests\Feature;

use App\Livewire\Staff\AssignmentMatrix as StaffAssignmentMatrix;
use App\Livewire\Staff\Index as StaffIndex;
use App\Livewire\Staff\Show360 as StaffShow360;
use App\Models\School;
use App\Models\StaffEvaluation;
use App\Models\StaffNote;
use App\Models\StaffProfile;
use App\Models\StaffTask;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StaffManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected StaffProfile $robertStaff;

    protected function setUp(): void
    {
        parent::setUp();

        // Run full database seeders
        $this->artisan('db:seed');

        $school = School::where('code', 'OIA-01')->first() ?? School::first();

        // Admin User
        $this->adminUser = User::factory()->create([
            'school_id' => $school->id,
            'email' => 'admin_staff_test@smartschool.edu',
        ]);
        $this->adminUser->assignRole('super-admin');

        // Fetch Seeded Teacher Prof. Robert Vance
        $this->robertStaff = StaffProfile::where('employee_code', 'EMP-2024-001')->first();
        $this->teacherUser = User::where('email', 'robert.vance@smartschool.edu')->first();

        $this->adminUser->refresh();
        $this->teacherUser->refresh();
        $this->robertStaff->refresh();
    }

    /** @test */
    public function exact_staff_sample_data_is_seeded_correctly()
    {
        // Assert Robert Vance profile
        $this->assertNotNull($this->robertStaff);
        $this->assertEquals('Robert', $this->robertStaff->first_name);
        $this->assertEquals('Vance', $this->robertStaff->last_name);
        $this->assertEquals('Senior Physics Teacher', $this->robertStaff->designation);

        // Assert Physics subject mapping
        $physics = Subject::where('code', 'PHY-101')->first();
        $this->assertNotNull($physics);
        $this->assertTrue($this->robertStaff->subjects->contains($physics->id));

        // Assert Class Assignment (Grade 5 as Class Teacher)
        $this->assertTrue($this->robertStaff->classAssignments()->where('is_class_teacher', true)->exists());

        // Assert Documents
        $this->assertDatabaseHas('staff_documents', [
            'staff_id' => $this->robertStaff->id,
            'title' => 'M.Sc Degree Certificate',
        ]);

        // Assert Tasks
        $this->assertDatabaseHas('staff_tasks', [
            'staff_id' => $this->robertStaff->id,
            'title' => 'Prepare Grade 5 Mid-Term Physics Exam Papers',
        ]);

        // Assert Evaluations
        $this->assertDatabaseHas('staff_evaluations', [
            'staff_id' => $this->robertStaff->id,
            'review_period' => 'Annual 2025-2026',
        ]);
    }

    /** @test */
    public function authorized_user_can_view_staff_directory_and_search()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('staff.index'));
        $response->assertStatus(200);
        $response->assertSee($this->robertStaff->full_name);
        $response->assertSee('EMP-2024-001');

        Livewire::test(StaffIndex::class)
            ->set('search', 'Robert')
            ->assertSee($this->robertStaff->full_name)
            ->assertSee('Senior Physics Teacher');
    }

    /** @test */
    public function authorized_user_can_view_teacher_360_dashboard()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('staff.show', $this->robertStaff));
        $response->assertStatus(200);
        $response->assertSee($this->robertStaff->full_name);
        $response->assertSee('Senior Physics Teacher');
        $response->assertSee('EMP-2024-001');
    }

    /** @test */
    public function user_can_create_staff_member_via_livewire()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StaffIndex::class)
            ->set('first_name', 'Alexander')
            ->set('last_name', 'Fleming')
            ->set('email', 'alexander.fleming@smartschool.edu')
            ->set('phone', '+1 (555) 777-8888')
            ->set('role_type_input', 'teacher')
            ->set('designation', 'Biology Professor')
            ->set('qualification', 'Ph.D Medicine')
            ->set('experience_years', 14)
            ->set('create_user_account', true)
            ->call('saveStaff')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('staff_profiles', [
            'email' => 'alexander.fleming@smartschool.edu',
            'designation' => 'Biology Professor',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'alexander.fleming@smartschool.edu',
        ]);
    }

    /** @test */
    public function user_can_add_task_to_staff_member()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StaffShow360::class, ['staff' => $this->robertStaff])
            ->set('taskTitle', 'Submit Quarterly Lesson Plans')
            ->set('taskDescription', 'Outline curriculum for optics & electricity.')
            ->set('taskPriority', 'high')
            ->call('addTask')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('staff_tasks', [
            'staff_id' => $this->robertStaff->id,
            'title' => 'Submit Quarterly Lesson Plans',
            'priority' => 'high',
        ]);
    }

    /** @test */
    public function user_can_add_performance_evaluation()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StaffShow360::class, ['staff' => $this->robertStaff])
            ->set('evalReviewPeriod', 'Mid-Term 2026')
            ->set('evalRating', 5)
            ->set('evalFeedback', 'Excellent teaching methodology and peer collaboration.')
            ->set('evalGoals', 'Lead departmental physics seminar.')
            ->call('addEvaluation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('staff_evaluations', [
            'staff_id' => $this->robertStaff->id,
            'review_period' => 'Mid-Term 2026',
            'rating' => 5,
        ]);
    }

    /** @test */
    public function confidential_notes_are_protected_by_policy()
    {
        // Add a confidential note
        $confidentialNote = StaffNote::create([
            'staff_id' => $this->robertStaff->id,
            'author_id' => $this->adminUser->id,
            'note' => 'Confidential background check passed clear.',
            'category' => 'general',
            'is_confidential' => true,
        ]);

        // Teacher role user checking 360 dashboard
        $this->actingAs($this->teacherUser);

        Livewire::test(StaffShow360::class, ['staff' => $this->robertStaff])
            ->set('activeTab', 'notes')
            ->assertDontSee('Confidential background check passed clear.');
    }

    /** @test */
    public function authorized_user_can_view_assignment_matrix()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('staff.matrix'));
        $response->assertStatus(200);
        $response->assertSee($this->robertStaff->full_name);
    }
}
