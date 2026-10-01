<?php

namespace Tests\Feature;

use App\Core\Services\CentralizedNotificationEngine;
use App\Jobs\DispatchBulkNotificationJob;
use App\Jobs\DispatchNotificationJob;
use App\Livewire\Admin\NotificationCenter;
use App\Models\Communication;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\NotificationPreference;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class CentralizedNotificationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected School $school;
    protected CentralizedNotificationEngine $notificationEngine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first() ?? School::create(['name' => 'SmartSchool Academy', 'code' => 'SSA-01']);

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Notification Admin',
            'email' => 'admin.notifications@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('super-admin');

        $this->notificationEngine = app(CentralizedNotificationEngine::class);
    }

    public function test_authorized_admin_can_access_notification_center_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.notifications'));

        $response->assertStatus(200);
        $response->assertSee('Centralized Notification Engine');
    }

    public function test_dispatch_notification_queues_bulk_job_asynchronously(): void
    {
        Queue::fake();

        $recipients = [
            ['id' => 1, 'type' => 'user', 'name' => 'Alice Parent', 'email' => 'alice@example.com', 'phone' => '+1234567890'],
            ['id' => 2, 'type' => 'user', 'name' => 'Bob Teacher', 'email' => 'bob@example.com', 'phone' => '+0987654321'],
        ];

        $channels = ['email', 'whatsapp', 'push'];

        $communication = $this->notificationEngine->dispatchNotification([
            'title' => 'Important School Notice',
            'subject' => 'School Assembly Update',
            'message_body' => 'Dear {{user_name}}, tomorrow assembly starts at 8 AM.',
        ], $recipients, $channels);

        $this->assertInstanceOf(Communication::class, $communication);
        $this->assertEquals('queued', $communication->status);
        $this->assertEquals(2, $communication->total_recipients);

        Queue::assertPushed(DispatchBulkNotificationJob::class, function ($job) use ($communication) {
            return $job->communication->id === $communication->id;
        });
    }

    public function test_scheduled_notification_delays_queue_dispatch(): void
    {
        Queue::fake();

        $recipients = [
            ['id' => 1, 'type' => 'user', 'name' => 'Future User', 'email' => 'future@example.com'],
        ];

        $scheduledAt = now()->addDays(2);

        $communication = $this->notificationEngine->dispatchNotification([
            'title' => 'Scheduled Fee Reminder',
            'message_body' => 'Reminder for term fees.',
            'scheduled_at' => $scheduledAt->toDateTimeString(),
        ], $recipients, ['sms']);

        $this->assertEquals($scheduledAt->toDateTimeString(), $communication->scheduled_at->toDateTimeString());

        Queue::assertPushed(DispatchBulkNotificationJob::class, function ($job) {
            return $job->delay !== null;
        });
    }

    public function test_process_single_channel_delivers_across_all_five_channels(): void
    {
        $communication = Communication::create([
            'school_id' => $this->school->id,
            'sender_id' => $this->adminUser->id,
            'title' => 'Multi-Channel Test',
            'channel' => 'multi_channel',
            'recipient_type' => 'individual',
            'subject' => 'Hello {{user_name}}',
            'message_body' => 'Welcome to {{school_name}}!',
            'status' => 'processing',
            'total_recipients' => 1,
        ]);

        $recipient = [
            'id' => 10,
            'type' => 'user',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1122334455',
        ];

        $channels = ['database', 'email', 'sms', 'whatsapp', 'push'];

        foreach ($channels as $channel) {
            $this->notificationEngine->processSingleChannel($communication, $recipient, $channel);
        }

        $logs = CommunicationLog::where('communication_id', $communication->id)->get();
        $this->assertCount(5, $logs);

        foreach ($channels as $channel) {
            $log = $logs->firstWhere('channel', $channel);
            $this->assertNotNull($log);
            $this->assertEquals('delivered', $log->delivery_status);
            $this->assertNotNull($log->provider_message_id);
        }
    }

    public function test_respects_user_notification_preference_and_skips_opted_out_channels(): void
    {
        // Disable SMS and WhatsApp for entity_id 100
        NotificationPreference::create([
            'school_id' => $this->school->id,
            'entity_type' => 'user',
            'entity_id' => 100,
            'channel_in_app' => true,
            'channel_email' => true,
            'channel_sms' => false,
            'channel_whatsapp' => false,
            'channel_push' => true,
        ]);

        $communication = Communication::create([
            'school_id' => $this->school->id,
            'sender_id' => $this->adminUser->id,
            'title' => 'Preference Test',
            'channel' => 'multi_channel',
            'recipient_type' => 'user',
            'subject' => 'Notice',
            'message_body' => 'Test Body',
            'status' => 'processing',
            'total_recipients' => 1,
        ]);

        $recipient = [
            'id' => 100,
            'type' => 'user',
            'name' => 'Preference User',
            'email' => 'pref@example.com',
            'phone' => '+9988776655',
        ];

        // Send Email (allowed) and SMS (opted out)
        $this->notificationEngine->processSingleChannel($communication, $recipient, 'email');
        $this->notificationEngine->processSingleChannel($communication, $recipient, 'sms');

        $emailLog = CommunicationLog::where('communication_id', $communication->id)->where('channel', 'email')->first();
        $smsLog = CommunicationLog::where('communication_id', $communication->id)->where('channel', 'sms')->first();

        $this->assertEquals('delivered', $emailLog->delivery_status);
        $this->assertEquals('skipped_by_preference', $smsLog->delivery_status);
        $this->assertStringContainsString('opted out of sms', strtolower($smsLog->failure_reason));
    }

    public function test_retry_failed_log_re_queues_notification_job(): void
    {
        Queue::fake();

        $communication = Communication::create([
            'school_id' => $this->school->id,
            'sender_id' => $this->adminUser->id,
            'title' => 'Retry Test',
            'channel' => 'email',
            'recipient_type' => 'user',
            'subject' => 'Retry Subject',
            'message_body' => 'Retry Body',
            'status' => 'failed',
            'total_recipients' => 1,
        ]);

        $log = CommunicationLog::create([
            'communication_id' => $communication->id,
            'school_id' => $this->school->id,
            'recipient_type' => 'user',
            'recipient_id' => 50,
            'recipient_email' => 'retry@example.com',
            'channel' => 'email',
            'provider_name' => 'smtp_mailgun',
            'delivery_status' => 'failed',
            'failure_reason' => 'Connection timeout',
            'retry_count' => 1,
            'max_retries' => 3,
        ]);

        $success = $this->notificationEngine->retryFailedLog($log);

        $this->assertTrue($success);
        $this->assertEquals(2, $log->fresh()->retry_count);

        Queue::assertPushed(DispatchNotificationJob::class, function ($job) use ($communication) {
            return $job->communication->id === $communication->id && $job->channel === 'email';
        });
    }

    public function test_livewire_notification_center_can_create_template_and_dispatch_broadcast(): void
    {
        Queue::fake();

        Livewire::actingAs($this->adminUser)
            ->test(NotificationCenter::class)
            ->set('tpl_title', 'Fee Due Reminder Template')
            ->set('tpl_code', 'FEE-REM-101')
            ->set('tpl_category', 'fee_reminder')
            ->set('tpl_subject', 'Urgent: Term Fee Payment Due')
            ->set('tpl_content_body', 'Dear {{user_name}}, your fee for {{student_name}} is pending.')
            ->call('createTemplate')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('communication_templates', [
            'school_id' => $this->school->id,
            'title' => 'Fee Due Reminder Template',
            'category' => 'fee_reminder',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(NotificationCenter::class)
            ->set('title', 'Monthly Newsletter')
            ->set('subject', 'School Highlights for Month')
            ->set('message_body', 'Hello {{user_name}}, here are the updates.')
            ->set('recipient_type', 'all_users')
            ->set('selected_channels', ['email', 'whatsapp'])
            ->call('sendBroadcastNotification')
            ->assertHasNoErrors();

        Queue::assertPushed(DispatchBulkNotificationJob::class);
    }
}
