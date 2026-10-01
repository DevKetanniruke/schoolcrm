<?php

namespace Tests\Feature;

use App\Livewire\Students\Index as StudentIndex;
use App\Livewire\Students\Show360 as StudentShow360;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Student360ModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected School $school;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $this->artisan('db:seed');

        $this->school = School::first();

        // Admin User (assigned super-admin slug)
        $this->adminUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'admin_test@smartschool.edu',
        ]);
        $this->adminUser->assignRole('super-admin');

        // Teacher User (assigned teacher slug without confidential notes permission)
        $this->teacherUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'teacher_test@smartschool.edu',
        ]);
        $this->teacherUser->assignRole('teacher');

        // Get seeded student
        $this->student = Student::with(['guardians', 'notes', 'documents'])->first();
    }

    /** @test */
    public function authorized_user_can_access_student_directory()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('students.index'));
        $response->assertStatus(200);
        $response->assertSee('Student Directory');
    }

    /** @test */
    public function student_directory_multi_attribute_search_works()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentIndex::class)
            ->set('search', $this->student->admission_number)
            ->assertSee($this->student->first_name)
            ->assertSee($this->student->admission_number);
    }

    /** @test */
    public function authorized_user_can_view_student_360_dashboard()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('students.show', $this->student));
        $response->assertStatus(200);
        $response->assertSee($this->student->first_name);
        $response->assertSee('Student 360°');
    }

    /** @test */
    public function user_can_add_note_to_student_profile()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentShow360::class, ['student' => $this->student])
            ->set('noteContent', 'Test counseling note for student progress')
            ->set('noteType', 'counseling')
            ->set('isConfidential', false)
            ->call('addNote')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('student_notes', [
            'student_id' => $this->student->id,
            'note' => 'Test counseling note for student progress',
            'category' => 'counseling',
            'is_confidential' => false,
        ]);
    }

    /** @test */
    public function confidential_notes_are_protected_by_policy()
    {
        // Parent User (without confidential notes permission or students.edit)
        $parentUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'parent_test@smartschool.edu',
        ]);
        $parentUser->assignRole('parent');

        // Add a confidential note
        $confidentialNote = StudentNote::create([
            'student_id' => $this->student->id,
            'author_id' => $this->adminUser->id,
            'category' => 'medical',
            'note' => 'Top secret medical record entry',
            'is_confidential' => true,
        ]);

        // Super Admin can view confidential notes policy
        $this->assertTrue($this->adminUser->can('viewConfidentialNotes', $this->student));

        // Parent user does not have viewConfidentialNotes permission
        $this->assertFalse($parentUser->can('viewConfidentialNotes', $this->student));
    }

    /** @test */
    public function user_can_add_guardian()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentShow360::class, ['student' => $this->student])
            ->set('guardianFirstName', 'Robert')
            ->set('guardianLastName', 'Smith')
            ->set('guardianRelationship', 'father')
            ->set('guardianPhone', '+1987654321')
            ->set('guardianEmail', 'robert.smith@example.com')
            ->set('isPrimaryGuardian', true)
            ->call('addGuardian')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('student_guardians', [
            'student_id' => $this->student->id,
            'name' => 'Robert Smith',
            'phone' => '+1987654321',
            'is_primary_contact' => true,
        ]);
    }

    /** @test */
    public function user_can_update_student_status_with_history_log()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentShow360::class, ['student' => $this->student])
            ->set('newStatus', 'suspended')
            ->set('statusReason', 'Disciplinary action')
            ->set('statusNotes', 'Suspended for 3 days')
            ->call('updateStatus')
            ->assertHasNoErrors();

        $this->assertEquals('suspended', $this->student->fresh()->status);

        $this->assertDatabaseHas('student_status_history', [
            'student_id' => $this->student->id,
            'to_status' => 'suspended',
            'reason' => 'Disciplinary action',
        ]);
    }

    /** @test */
    public function user_can_create_student_via_livewire()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(StudentIndex::class)
            ->call('createStudent')
            ->set('first_name', 'Rahul')
            ->set('last_name', 'Sharma')
            ->set('date_of_birth', '2014-05-10')
            ->set('gender', 'male')
            ->set('guardian_name', 'Suresh Sharma')
            ->set('guardian_phone', '+919876543210')
            ->call('saveStudent')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('students', [
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
        ]);
    }
}
