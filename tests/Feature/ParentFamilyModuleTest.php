<?php

namespace Tests\Feature;

use App\Livewire\Parents\CommunicationLogIndex;
use App\Livewire\Parents\FamilyDashboard;
use App\Livewire\Parents\Index as ParentIndex;
use App\Livewire\Parents\Show360 as ParentShow360;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ParentFamilyModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $janeUser;
    protected ParentProfile $janeParent;
    protected Student $johnDoe;
    protected Student $emmaDoe;

    protected function setUp(): void
    {
        parent::setUp();

        // Run full database seeders (seeds Jane Doe, John Doe, Emma Doe, exact logs & docs)
        $this->artisan('db:seed');

        $school = School::where('code', 'OIA-01')->first() ?? School::first();

        // Admin User
        $this->adminUser = User::factory()->create([
            'school_id' => $school->id,
            'email' => 'admin_family_test@smartschool.edu',
        ]);
        $this->adminUser->assignRole('super-admin');

        // Fetch Seeded Parent Jane Doe and her children
        $this->janeParent = ParentProfile::where('email', 'jane@example.com')->first();
        $this->janeUser = User::where('email', 'jane@example.com')->first();
        $this->janeUser->assignRole('parent');

        $this->johnDoe = Student::where('first_name', 'John')->where('last_name', 'Doe')->first();
        $this->emmaDoe = Student::where('first_name', 'Emma')->where('last_name', 'Doe')->first();

        $this->adminUser->refresh();
        $this->janeUser->refresh();
        $this->janeParent->refresh();
    }

    /** @test */
    public function exact_sample_data_is_seeded_correctly()
    {
        // Assert Parent Jane Doe
        $this->assertNotNull($this->janeParent);
        $this->assertEquals('123-456-7890', $this->janeParent->phone);
        $this->assertEquals('jane@example.com', $this->janeParent->email);

        // Assert Children John Doe (Grade 5) and Emma Doe (Grade 3)
        $this->assertNotNull($this->johnDoe);
        $this->assertEquals('Grade 5', $this->johnDoe->schoolClass->name);
        $this->assertNotNull($this->emmaDoe);
        $this->assertEquals('Grade 3', $this->emmaDoe->schoolClass->name);

        // Assert parent-children linkages
        $this->assertTrue($this->janeParent->students->contains($this->johnDoe->id));
        $this->assertTrue($this->janeParent->students->contains($this->emmaDoe->id));

        // Assert exact required Communication History
        $this->assertDatabaseHas('parent_communications', [
            'parent_id' => $this->janeParent->id,
            'summary' => '2024-02-15: Discussed upcoming parent-teacher meeting',
        ]);
        $this->assertDatabaseHas('parent_communications', [
            'parent_id' => $this->janeParent->id,
            'summary' => '2024-03-01: Sent enrollment form',
        ]);

        // Assert exact required Documents
        $this->assertDatabaseHas('parent_documents', [
            'parent_id' => $this->janeParent->id,
            'title' => 'ID proof',
        ]);
        $this->assertDatabaseHas('parent_documents', [
            'parent_id' => $this->janeParent->id,
            'title' => 'Consent form',
        ]);
    }

    /** @test */
    public function authorized_user_can_view_parent_directory_and_search()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('parents.index'));
        $response->assertStatus(200);
        $response->assertSee('Jane Doe');

        Livewire::test(ParentIndex::class)
            ->set('search', 'Jane')
            ->assertSee('Jane Doe')
            ->assertSee('123-456-7890');
    }

    /** @test */
    public function authorized_user_can_view_parent_360_profile()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('parents.show', $this->janeParent));
        $response->assertStatus(200);
        $response->assertSee('Jane Doe');
        $response->assertSee('John Doe');
        $response->assertSee('Emma Doe');
    }

    /** @test */
    public function user_can_log_communication_entry_via_livewire()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(ParentShow360::class, ['parent' => $this->janeParent])
            ->set('commType', 'email')
            ->set('commDirection', 'outbound')
            ->set('commSubject', 'Quarterly Academic Report')
            ->set('commSummary', 'Sent John Doe grade 5 report card to parent.')
            ->call('addCommunication')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('parent_communications', [
            'parent_id' => $this->janeParent->id,
            'subject' => 'Quarterly Academic Report',
        ]);
    }

    /** @test */
    public function user_can_log_complaint_ticket_via_livewire()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(ParentShow360::class, ['parent' => $this->janeParent])
            ->set('complaintCategory', 'facility')
            ->set('complaintPriority', 'high')
            ->set('complaintSubject', 'Cafeteria Menu Feedback')
            ->set('complaintDescription', 'Requested additional vegetarian options.')
            ->call('addComplaint')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('parent_complaints', [
            'parent_id' => $this->janeParent->id,
            'subject' => 'Cafeteria Menu Feedback',
        ]);
    }

    /** @test */
    public function family_dashboard_renders_hierarchy_and_supports_child_switching()
    {
        $this->actingAs($this->janeUser);

        $response = $this->get(route('parents.family-dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Jane Doe');
        $response->assertSee('John Doe');
        $response->assertSee('Emma Doe');

        // Test Livewire child switcher
        Livewire::test(FamilyDashboard::class)
            ->call('selectChild', $this->emmaDoe->id)
            ->assertSet('selectedChild.id', $this->emmaDoe->id)
            ->assertSee('Emma Doe');
    }

    /** @test */
    public function communication_log_center_filters_interactions()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('parents.communication-log'));
        $response->assertStatus(200);
        $response->assertSee('Discussed upcoming parent-teacher meeting');

        Livewire::test(CommunicationLogIndex::class)
            ->set('search', 'enrollment form')
            ->assertSee('Sent enrollment form');
    }

    /** @test */
    public function parent_cannot_switch_to_unauthorized_student()
    {
        $this->actingAs($this->janeUser);

        // Create an unlinked student
        $unlinkedStudent = Student::create([
            'school_id' => $this->janeParent->school_id,
            'student_id_number' => 'STU-9999',
            'admission_number' => 'ADM-9999',
            'first_name' => 'Stranger',
            'last_name' => 'Kid',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'admission_date' => '2024-01-01',
            'status' => 'active',
        ]);

        Livewire::test(FamilyDashboard::class)
            ->call('selectChild', $unlinkedStudent->id)
            ->assertStatus(403);
    }


}
