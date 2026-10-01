<?php

namespace Tests\Feature;

use App\Livewire\Admissions\Dashboard;
use App\Livewire\Admissions\Index;
use App\Livewire\Admissions\Kanban;
use App\Livewire\Admissions\Show360;
use App\Models\AdmissionApplication;
use App\Models\AdmissionEnquiry;
use App\Models\AdmissionLead;
use App\Models\LeadCampaign;
use App\Models\LeadDocument;
use App\Models\LeadFollowUp;
use App\Models\LeadSource;
use App\Models\LeadStatusHistory;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdmissionCrmModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected School $school;
    protected AdmissionLead $sampleLead;

    protected function setUp(): void
    {
        parent::setUp();

        // Run full database seeders
        $this->artisan('db:seed');

        $this->school = School::where('code', 'OIA-01')->first() ?? School::first();

        // Super admin user
        $this->adminUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'admin_admission_test@smartschool.edu',
        ]);
        $this->adminUser->assignRole('super-admin');

        $this->sampleLead = AdmissionLead::where('lead_code', 'LEAD-2026-001')->first();
    }

    /** @test */
    public function exact_admission_crm_sample_data_is_seeded_correctly()
    {
        $this->assertGreaterThan(0, LeadSource::count());
        $this->assertGreaterThan(0, LeadCampaign::count());
        $this->assertGreaterThan(0, AdmissionLead::count());

        $this->assertNotNull($this->sampleLead);
        $this->assertEquals('Lucas Pendelton', $this->sampleLead->student_name);
        $this->assertEquals('Arthur Pendelton', $this->sampleLead->parent_name);
        $this->assertGreaterThan(0, AdmissionEnquiry::where('lead_id', $this->sampleLead->id)->count());
        $this->assertGreaterThan(0, LeadFollowUp::where('lead_id', $this->sampleLead->id)->count());
        $this->assertGreaterThan(0, LeadStatusHistory::where('lead_id', $this->sampleLead->id)->count());
    }

    /** @test */
    public function executive_admission_dashboard_renders_successfully()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admissions.dashboard'));
        $response->assertStatus(200);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewHas('totalLeads')
            ->assertViewHas('confirmedAdmissions')
            ->assertViewHas('conversionRate')
            ->assertViewHas('campaignMetrics');
    }

    /** @test */
    public function kanban_board_renders_and_updates_stage_with_audit_trail()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admissions.pipeline'));
        $response->assertStatus(200);

        $lead = AdmissionLead::where('stage', 'contacted')->first();
        $this->assertNotNull($lead);

        Livewire::test(Kanban::class)
            ->assertStatus(200)
            ->call('updateLeadStage', $lead->id, 'campus_visit', 'Scheduled school campus tour with counselor');

        $lead->refresh();
        $this->assertEquals('campus_visit', $lead->stage);

        $history = LeadStatusHistory::where('lead_id', $lead->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($history);
        $this->assertEquals('contacted', $history->from_stage);
        $this->assertEquals('campus_visit', $history->to_stage);
    }

    /** @test */
    public function lead_directory_allows_searching_filtering_and_creating_leads()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admissions.index'));
        $response->assertStatus(200);

        Livewire::test(Index::class)
            ->set('search', 'Lucas')
            ->assertSee('Lucas Pendelton')
            ->set('search', '')
            ->set('student_name', 'Charlotte Bronte')
            ->set('parent_name', 'Patrick Bronte')
            ->set('parent_email', 'patrick.bronte@example.com')
            ->set('parent_phone', '+1 555-888-9999')
            ->call('saveLead')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('admission_leads', [
            'student_name' => 'Charlotte Bronte',
            'parent_name' => 'Patrick Bronte',
            'school_id' => $this->school->id,
        ]);
    }

    /** @test */
    public function lead_360_workspace_logs_inquiry_schedules_followup_and_manages_application()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admissions.show', $this->sampleLead));
        $response->assertStatus(200);

        Livewire::test(Show360::class, ['lead' => $this->sampleLead])
            ->assertStatus(200)
            // Log Inquiry
            ->set('inquiryType', 'fee_structure')
            ->set('question', 'What are the payment schedule options for Grade 10?')
            ->set('response', 'Installment payments allowed in 3 tranches.')
            ->call('saveEnquiry')
            // Schedule Follow Up
            ->set('followUpType', 'phone_call')
            ->set('followUpSubject', 'Follow up on fee installment terms')
            ->set('followUpScheduledAt', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('saveFollowUp')
            // Save Application
            ->set('appStage', 'under_review')
            ->set('appFeeStatus', 'paid')
            ->set('appReviewerNotes', 'Application docs verified. Ready for interview.')
            ->call('saveApplication');

        $this->assertDatabaseHas('admission_enquiries', [
            'lead_id' => $this->sampleLead->id,
            'inquiry_type' => 'fee_structure',
        ]);

        $this->assertDatabaseHas('lead_follow_ups', [
            'lead_id' => $this->sampleLead->id,
            'subject' => 'Follow up on fee installment terms',
        ]);

        $this->assertDatabaseHas('admission_applications', [
            'lead_id' => $this->sampleLead->id,
            'fee_status' => 'paid',
        ]);
    }

    /** @test */
    public function multi_tenant_isolation_prevents_unauthorized_school_access()
    {
        $otherSchool = School::create([
            'organization_id' => $this->school->organization_id,
            'name' => 'Other School Academy',
            'code' => 'OSA-99',
        ]);

        $otherLead = AdmissionLead::create([
            'school_id' => $otherSchool->id,
            'lead_code' => 'LEAD-OSA-001',
            'student_name' => 'Outside Student',
            'parent_name' => 'Outside Parent',
            'parent_phone' => '+1 000-000-0000',
            'parent_email' => 'outside@example.com',
            'stage' => 'new_lead',
            'status' => 'active',
            'score' => 50,
        ]);

        $regularSchoolUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'regular_counselor@smartschool.edu',
        ]);
        $regularSchoolUser->assignRole('counselor');

        $this->actingAs($regularSchoolUser);

        // Attempting to access other school's lead 360 should yield 403 or 404 due to tenant scope
        $response = $this->get(route('admissions.show', $otherLead));
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }
}
