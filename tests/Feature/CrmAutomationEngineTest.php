<?php

namespace Tests\Feature;

use App\Core\Services\CrmAutomationEngine;
use App\Livewire\Admin\AutomationRules;
use App\Models\AdmissionEnquiry;
use App\Models\AdmissionLead;
use App\Models\CrmAutomationLog;
use App\Models\CrmAutomationRule;
use App\Models\CrmTask;
use App\Models\LeadFollowUp;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CrmAutomationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unprivilegedUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first();

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'CRM Automation Super Admin',
            'email' => 'auto.admin@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('super-admin');

        $this->unprivilegedUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Restricted User',
            'email' => 'restricted.user@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);

        CrmAutomationRule::create([
            'school_id' => $this->school->id,
            'name' => 'Lead Creation Workflow',
            'trigger_event' => 'lead.created',
            'conditions_json' => [],
            'actions_json' => [
                ['action' => 'assign_counselor', 'params' => []],
                ['action' => 'create_followup', 'params' => ['subject' => 'Initial Call', 'days' => 1]],
                ['action' => 'send_acknowledgement', 'params' => ['subject' => 'Thanks for interest']],
            ],
            'status' => 'active',
            'created_by' => $this->adminUser->id,
        ]);

        CrmAutomationRule::create([
            'school_id' => $this->school->id,
            'name' => 'Overdue Followup Alert',
            'trigger_event' => 'followup.overdue',
            'conditions_json' => [],
            'actions_json' => [
                ['action' => 'create_reminder', 'params' => []],
                ['action' => 'notify_assigned_staff', 'params' => []],
            ],
            'status' => 'active',
            'created_by' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function authorized_admin_can_access_automation_rules_dashboard()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.automation-rules'));

        $response->assertStatus(200);
        $response->assertSee('CRM Automation Engine');
        $response->assertSee('Lead Creation Workflow');
    }

    /** @test */
    public function automation_engine_executes_lead_created_rule_actions()
    {
        $engine = app(CrmAutomationEngine::class);

        $lead = AdmissionLead::first() ?? AdmissionLead::create([
            'school_id' => $this->school->id,
            'lead_code' => 'LEAD-AUTO-001',
            'student_name' => 'Rohan Varma',
            'parent_name' => 'Sanjay Varma',
            'phone' => '9876543210',
            'email' => 'sanjay.v@example.com',
            'stage' => 'inquiry',
            'status' => 'new',
        ]);

        $results = $engine->dispatchTrigger('lead.created', $lead, $this->adminUser);

        $this->assertNotEmpty($results);
        $this->assertEquals('success', $results[0]['status']);

        // Audit Log Assertion
        $this->assertDatabaseHas('crm_automation_logs', [
            'trigger_event' => 'lead.created',
            'entity_id' => $lead->id,
            'execution_status' => 'success',
        ]);
    }

    /** @test */
    public function automation_engine_executes_followup_overdue_rule_actions()
    {
        $engine = app(CrmAutomationEngine::class);

        $task = CrmTask::first() ?? CrmTask::create([
            'school_id' => $this->school->id,
            'title' => 'Overdue Lead Contact Task',
            'category' => 'admissions',
            'priority' => 'high',
            'status' => 'pending',
            'due_at' => Carbon::now()->subDays(2),
            'created_by' => $this->adminUser->id,
        ]);

        $results = $engine->dispatchTrigger('followup.overdue', $task, $this->adminUser);

        $this->assertNotEmpty($results);
        $this->assertEquals('success', $results[0]['status']);

        $this->assertDatabaseHas('crm_automation_logs', [
            'trigger_event' => 'followup.overdue',
            'execution_status' => 'success',
        ]);
    }

    /** @test */
    public function automation_engine_blocks_execution_when_user_lacks_permissions()
    {
        $engine = app(CrmAutomationEngine::class);

        $lead = AdmissionLead::first() ?? AdmissionLead::create([
            'school_id' => $this->school->id,
            'lead_code' => 'LEAD-AUTO-002',
            'student_name' => 'Neha Gupta',
            'parent_name' => 'Vikas Gupta',
            'phone' => '9876543211',
            'stage' => 'inquiry',
            'status' => 'new',
        ]);

        // Dispatch trigger with unprivileged user (no permissions granted)
        $results = $engine->dispatchTrigger('lead.created', $lead, $this->unprivilegedUser);

        $this->assertNotEmpty($results);
        $this->assertEquals('blocked_by_auth', $results[0]['status']);

        // Verify audit log recorded security block
        $this->assertDatabaseHas('crm_automation_logs', [
            'trigger_event' => 'lead.created',
            'execution_status' => 'blocked_by_auth',
        ]);
    }

    /** @test */
    public function livewire_component_allows_creating_and_toggling_automation_rules()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(AutomationRules::class)
            ->set('name', 'Custom Enquiry Notification')
            ->set('trigger_event', 'enquiry.submitted')
            ->set('selected_actions', ['create_crm_activity', 'send_acknowledgement'])
            ->call('createRule')
            ->assertSee('Custom Enquiry Notification');

        $this->assertDatabaseHas('crm_automation_rules', [
            'name' => 'Custom Enquiry Notification',
            'trigger_event' => 'enquiry.submitted',
        ]);
    }
}
