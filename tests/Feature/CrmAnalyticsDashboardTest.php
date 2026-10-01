<?php

namespace Tests\Feature;

use App\Core\Services\CrmAnalyticsService;
use App\Jobs\RefreshCrmAnalyticsCacheJob;
use App\Livewire\Admin\CrmAnalyticsDashboard;
use App\Models\AdmissionApplication;
use App\Models\AdmissionLead;
use App\Models\Communication;
use App\Models\CrmTask;
use App\Models\LeadSource;
use App\Models\ParentComplaint;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\StudentEngagementRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class CrmAnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->school = School::first() ?? School::create(['name' => 'Oakridge Intl Academy', 'code' => 'OIA-01']);

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'CRM Analytics Super Admin',
            'email' => 'analytics.admin@smartschool.edu',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('super-admin');

        // Seed sample metrics data
        AdmissionLead::create([
            'school_id' => $this->school->id,
            'lead_code' => 'LEAD-2026-TEST1',
            'parent_name' => 'Anil Kapoor',
            'parent_email' => 'anil@example.com',
            'parent_phone' => '+91 9876543210',
            'student_name' => 'Rohan Kapoor',
            'status' => 'converted',
            'stage' => 'admission_confirmed',
        ]);

        AdmissionApplication::create([
            'school_id' => $this->school->id,
            'lead_id' => 1,
            'application_number' => 'APP-2026-TEST1',
            'application_stage' => 'approved',
        ]);

        Communication::create([
            'school_id' => $this->school->id,
            'title' => 'Welcome Message',
            'channel' => 'email',
            'recipient_type' => 'individual',
            'subject' => 'Welcome to Oakridge',
            'message_body' => 'Welcome to the school.',
            'status' => 'delivered',
            'sent_at' => now(),
        ]);

        StudentEngagementRecord::create([
            'school_id' => $this->school->id,
            'student_id' => 1,
            'attendance_percentage' => 95.0,
            'assignment_completion_rate' => 90.0,
            'overall_status' => 'normal',
        ]);

        ParentComplaint::create([
            'school_id' => $this->school->id,
            'parent_id' => 1,
            'ticket_number' => 'TKT-2026-TEST1',
            'subject' => 'Transport Delay',
            'description' => 'Bus was late today.',
            'category' => 'transport',
            'status' => 'resolved',
        ]);

        CrmTask::create([
            'school_id' => $this->school->id,
            'title' => 'Follow up on document',
            'created_by' => $this->adminUser->id,
            'due_at' => now()->addDays(1),
            'status' => 'completed',
        ]);
    }

    public function test_authorized_admin_can_access_crm_analytics_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.crm-analytics'));
        $response->assertStatus(200);
        $response->assertSee('School CRM Analytics');
    }

    public function test_crm_analytics_service_computes_all_6_dashboards(): void
    {
        $service = app(CrmAnalyticsService::class);
        $filters = ['school_id' => $this->school->id, 'date_preset' => 'this_month'];

        $exec = $service->getExecutiveMetrics($filters);
        $this->assertEquals('executive', $exec['dashboard']);
        $this->assertArrayHasKey('lead_volume', $exec['summary']);

        $adm = $service->getAdmissionsMetrics($filters);
        $this->assertEquals('admissions', $adm['dashboard']);
        $this->assertGreaterThanOrEqual(1, $adm['lead_volume']);

        $comm = $service->getCommunicationMetrics($filters);
        $this->assertEquals('communication', $comm['dashboard']);

        $stud = $service->getStudentEngagementMetrics($filters);
        $this->assertEquals('student_engagement', $stud['dashboard']);

        $parent = $service->getParentEngagementMetrics($filters);
        $this->assertEquals('parent_engagement', $parent['dashboard']);

        $tasks = $service->getStaffTasksMetrics($filters);
        $this->assertEquals('staff_tasks', $tasks['dashboard']);
    }

    public function test_livewire_component_switches_dashboards_and_applies_filters(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(CrmAnalyticsDashboard::class)
            ->assertSet('selectedDashboard', 'executive')
            ->call('setDashboard', 'admissions')
            ->assertSet('selectedDashboard', 'admissions')
            ->call('setDashboard', 'student_engagement')
            ->assertSet('selectedDashboard', 'student_engagement')
            ->set('datePreset', 'last_30_days')
            ->assertSet('datePreset', 'last_30_days');
    }

    public function test_refresh_cache_dispatches_background_job(): void
    {
        Queue::fake();

        Livewire::actingAs($this->adminUser)
            ->test(CrmAnalyticsDashboard::class)
            ->call('refreshCache');

        Queue::assertPushed(RefreshCrmAnalyticsCacheJob::class);
    }
}
