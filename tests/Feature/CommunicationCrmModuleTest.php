<?php

namespace Tests\Feature;

use App\Core\Communication\DTOs\CommunicationResponse;
use App\Core\Communication\Providers\Email\LogEmailProvider;
use App\Core\Communication\Providers\Sms\TwilioSmsProvider;
use App\Core\Communication\Providers\Whatsapp\MetaWhatsappProvider;
use App\Core\Communication\Services\CommunicationManager;
use App\Livewire\Communication\Composer;
use App\Livewire\Communication\Dashboard;
use App\Livewire\Communication\Logs;
use App\Livewire\Communication\Templates;
use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CommunicationCrmModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Run full database seeders
        $this->artisan('db:seed');

        $this->school = School::where('code', 'OIA-01')->first() ?? School::first();

        // Super admin user
        $this->adminUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'admin_comm_test@smartschool.edu',
        ]);
        $this->adminUser->assignRole('super-admin');
    }

    /** @test */
    public function exact_communication_crm_sample_data_is_seeded_correctly()
    {
        $this->assertGreaterThan(0, CommunicationTemplate::count());
        $this->assertGreaterThan(0, Communication::count());
        $this->assertGreaterThan(0, CommunicationLog::count());

        $feeTpl = CommunicationTemplate::where('code', 'TPL-FEE-DUE')->first();
        $this->assertNotNull($feeTpl);
        $this->assertEquals('fees', $feeTpl->category);
    }

    /** @test */
    public function provider_abstraction_layer_dispatches_across_drivers()
    {
        $manager = new CommunicationManager();

        // Email Driver
        $emailDriver = $manager->resolveProvider('email');
        $this->assertInstanceOf(LogEmailProvider::class, $emailDriver);
        $res1 = $emailDriver->send('parent@example.com', 'Test Email', 'Hello Parent');
        $this->assertTrue($res1->success);

        // SMS Driver (Twilio override)
        $smsDriver = $manager->resolveProvider('sms', 'twilio');
        $this->assertInstanceOf(TwilioSmsProvider::class, $smsDriver);
        $res2 = $smsDriver->send('+15550001111', 'SMS Subject', 'Hello via SMS');
        $this->assertTrue($res2->success);

        // WhatsApp Driver (Meta override)
        $waDriver = $manager->resolveProvider('whatsapp', 'meta');
        $this->assertInstanceOf(MetaWhatsappProvider::class, $waDriver);
        $res3 = $waDriver->send('+15559998888', 'WhatsApp Subject', 'Hello via WhatsApp');
        $this->assertTrue($res3->success);
    }

    /** @test */
    public function communication_dashboard_renders_successfully()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('communication.dashboard'));
        $response->assertStatus(200);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewHas('totalDispatches')
            ->assertViewHas('deliveredCount')
            ->assertViewHas('successRate')
            ->assertViewHas('channelStats');
    }

    /** @test */
    public function broadcast_composer_dispatches_message_to_resolved_recipients()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('communication.composer'));
        $response->assertStatus(200);

        Livewire::test(Composer::class)
            ->set('title', 'Exam Schedule Announcement Broadcast')
            ->set('channel', 'email')
            ->set('recipient_type', 'parent_group')
            ->set('subject', 'Mid-Term Exam Schedule for {school_name}')
            ->set('message_body', 'Dear {parent_name}, the mid-term exam schedule for {student_name} is now released.')
            ->call('dispatchBroadcast')
            ->assertRedirect(route('communication.logs'));

        $this->assertDatabaseHas('communications', [
            'title' => 'Exam Schedule Announcement Broadcast',
            'channel' => 'email',
            'recipient_type' => 'parent_group',
            'school_id' => $this->school->id,
        ]);

        $this->assertGreaterThan(0, CommunicationLog::count());
    }

    /** @test */
    public function templates_manager_supports_creating_and_updating_templates()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('communication.templates'));
        $response->assertStatus(200);

        Livewire::test(Templates::class)
            ->set('title', 'Library Book Overdue Alert')
            ->set('code', 'TPL-LIB-OVERDUE')
            ->set('tpl_channel', 'sms')
            ->set('tpl_category', 'general')
            ->set('subject', 'Library Notice')
            ->set('content_body', 'Dear {parent_name}, book borrowed by {student_name} is overdue.')
            ->set('status', 'active')
            ->call('saveTemplate')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('communication_templates', [
            'code' => 'TPL-LIB-OVERDUE',
            'title' => 'Library Book Overdue Alert',
            'school_id' => $this->school->id,
        ]);
    }

    /** @test */
    public function logs_tracker_allows_retrying_failed_dispatches()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('communication.logs'));
        $response->assertStatus(200);

        $failedLog = CommunicationLog::create([
            'communication_id' => Communication::first()->id,
            'school_id' => $this->school->id,
            'recipient_type' => 'parent',
            'recipient_email' => 'retry_parent@example.com',
            'channel' => 'email',
            'provider_name' => 'log_email',
            'delivery_status' => 'failed',
            'failure_reason' => 'Temporary network timeout',
        ]);

        Livewire::test(Logs::class)
            ->call('resendLog', $failedLog->id);

        $failedLog->refresh();
        $this->assertEquals('sent', $failedLog->delivery_status);
        $this->assertNull($failedLog->failure_reason);
    }

    /** @test */
    public function multi_tenant_isolation_prevents_unauthorized_school_access()
    {
        $otherSchool = School::create([
            'organization_id' => $this->school->organization_id,
            'name' => 'Secondary Academy',
            'code' => 'SEC-88',
        ]);

        $otherComm = Communication::create([
            'school_id' => $otherSchool->id,
            'title' => 'Secret School Broadcast',
            'channel' => 'email',
            'recipient_type' => 'school_wide',
            'subject' => 'Secret',
            'message_body' => 'Confidential content',
            'status' => 'sent',
        ]);

        $regularUser = User::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'regular_staff@smartschool.edu',
        ]);
        $regularUser->assignRole('teacher');

        $this->actingAs($regularUser);

        $policy = new \App\Policies\CommunicationPolicy();
        $this->assertFalse($policy->view($regularUser, $otherComm));
    }
}
