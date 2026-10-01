<?php

namespace Tests\Feature;

use App\Core\Tasks\Services\TaskService;
use App\Livewire\Tasks\Dashboard as TasksDashboard;
use App\Livewire\Tasks\Workspace as TaskWorkspace;
use App\Models\AdmissionLead;
use App\Models\CrmTask;
use App\Models\CrmTaskActivity;
use App\Models\CrmTaskComment;
use App\Models\CrmTaskReminder;
use App\Models\ParentComplaint;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\School;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\AdmissionSeeder;
use Database\Seeders\HierarchySeeder;
use Database\Seeders\ParentSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\StaffSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\TaskEngineSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CrmTaskEngineModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $staffUser;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(HierarchySeeder::class);

        $this->school = School::first() ?? School::create([
            'name' => 'Test SmartSchool Academy',
            'code' => 'TSSA-01',
            'status' => 'active',
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $teacherRole = Role::where('slug', 'teacher')->first();

        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Task Admin User',
            'email' => 'task.admin@smartschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->adminUser->assignRole($superAdminRole);

        $this->staffUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Task Staff User',
            'email' => 'task.staff@smartschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->staffUser->assignRole($teacherRole);
    }

    public function test_task_engine_seeder_populates_initial_data()
    {
        $this->seed(StudentSeeder::class);
        $this->seed(ParentSeeder::class);
        $this->seed(StaffSeeder::class);
        $this->seed(AdmissionSeeder::class);
        $this->seed(TaskEngineSeeder::class);

        $this->assertDatabaseHas('crm_tasks', [
            'school_id' => $this->school->id,
        ]);

        $this->assertGreaterThan(0, CrmTask::count());
        $this->assertGreaterThan(0, CrmTaskActivity::count());
    }

    public function test_task_service_creates_polymorphic_task_and_logs_activity()
    {
        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        $student = Student::create([
            'school_id' => $this->school->id,
            'student_id_number' => 'STU-TEST-99',
            'admission_number' => 'ADM-TEST-99',
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'date_of_birth' => '2012-04-12',
            'gender' => 'male',
            'admission_date' => '2026-08-15',
            'status' => 'active',
        ]);

        $task = $taskService->createTask([
            'school_id' => $this->school->id,
            'title' => 'Counseling session for Michael',
            'description' => 'Discuss academic goals',
            'taskable_type' => Student::class,
            'taskable_id' => $student->id,
            'assigned_to' => $this->staffUser->id,
            'priority' => 'high',
            'due_at' => now()->addDays(2),
        ], $this->adminUser);

        $this->assertDatabaseHas('crm_tasks', [
            'id' => $task->id,
            'title' => 'Counseling session for Michael',
            'taskable_type' => Student::class,
            'taskable_id' => $student->id,
            'assigned_to' => $this->staffUser->id,
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('crm_task_activities', [
            'task_id' => $task->id,
            'activity_type' => 'created',
        ]);
    }

    public function test_automatic_overdue_task_flagging()
    {
        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        $pastTask = $taskService->createTask([
            'school_id' => $this->school->id,
            'title' => 'Past Due Lead Inquiry',
            'priority' => 'urgent',
            'due_at' => now()->subDays(3),
        ], $this->adminUser);

        $this->assertEquals('pending', $pastTask->status);

        $flaggedCount = $taskService->checkAndFlagOverdueTasks($this->school->id);

        $this->assertEquals(1, $flaggedCount);
        $this->assertEquals('overdue', $pastTask->fresh()->status);

        $this->assertDatabaseHas('crm_task_activities', [
            'task_id' => $pastTask->id,
            'activity_type' => 'overdue_flagged',
        ]);
    }

    public function test_recurring_task_creates_next_instance_on_completion()
    {
        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        $recurringTask = $taskService->createTask([
            'school_id' => $this->school->id,
            'title' => 'Weekly Staff Sync',
            'due_at' => now()->addDays(1),
            'is_recurring' => true,
            'recurrence_pattern' => 'weekly',
        ], $this->adminUser);

        $initialCount = CrmTask::count();

        $taskService->updateStatus($recurringTask, 'completed', $this->adminUser);

        $this->assertEquals('completed', $recurringTask->fresh()->status);
        $this->assertEquals($initialCount + 1, CrmTask::count());

        $newInstance = CrmTask::where('id', '!=', $recurringTask->id)->first();
        $this->assertEquals('pending', $newInstance->status);
        $this->assertEquals('Weekly Staff Sync', $newInstance->title);
    }

    public function test_task_comment_posting_and_reminders_dispatch()
    {
        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        $task = $taskService->createTask([
            'school_id' => $this->school->id,
            'title' => 'Review parent complaint',
            'due_at' => now()->addHours(5),
        ], $this->adminUser);

        $comment = $taskService->addComment(
            $task,
            'Contacted bus driver regarding speed complaint.',
            null,
            null,
            $this->adminUser
        );

        $this->assertDatabaseHas('crm_task_comments', [
            'id' => $comment->id,
            'task_id' => $task->id,
            'comment' => 'Contacted bus driver regarding speed complaint.',
        ]);

        $reminder = $taskService->addReminder($task, now()->subMinutes(10), 'in_app', $this->adminUser);

        $processed = $taskService->processDueReminders();
        $this->assertEquals(1, $processed);
        $this->assertTrue($reminder->fresh()->is_sent);
    }

    public function test_livewire_tasks_dashboard_renders_and_supports_filters()
    {
        $this->seed(StudentSeeder::class);
        $this->seed(ParentSeeder::class);
        $this->seed(StaffSeeder::class);
        $this->seed(AdmissionSeeder::class);
        $this->seed(TaskEngineSeeder::class);

        Livewire::actingAs($this->adminUser)
            ->test(TasksDashboard::class)
            ->assertStatus(200)
            ->assertSee('Task & Follow-up Command Center', false)
            ->set('activeTab', 'todays_tasks')
            ->assertStatus(200)
            ->set('activeTab', 'overdue_tasks')
            ->assertStatus(200)
            ->set('search', 'Follow up')
            ->assertStatus(200);
    }

    public function test_livewire_tasks_workspace_renders_360_overview_and_updates_status()
    {
        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        $task = $taskService->createTask([
            'school_id' => $this->school->id,
            'title' => 'Inspect chemistry lab inventory',
            'due_at' => now()->addDay(),
        ], $this->adminUser);

        Livewire::actingAs($this->adminUser)
            ->test(TaskWorkspace::class, ['id' => $task->id])
            ->assertStatus(200)
            ->assertSee('Inspect chemistry lab inventory')
            ->call('changeStatus', 'in_progress')
            ->assertStatus(200);

        $this->assertEquals('in_progress', $task->fresh()->status);
    }

    public function test_multi_tenant_isolation_prevents_unauthorized_school_access()
    {
        $otherSchool = School::create([
            'name' => 'Other Remote School',
            'code' => 'ORS-01',
            'status' => 'active',
        ]);

        $otherUser = User::create([
            'school_id' => $otherSchool->id,
            'name' => 'Remote Admin',
            'email' => 'remote@otherschool.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        /** @var TaskService $taskService */
        $taskService = app(TaskService::class);

        $secretTask = $taskService->createTask([
            'school_id' => $this->school->id,
            'title' => 'Confidential Internal Task',
            'due_at' => now()->addDay(),
        ], $this->adminUser);

        Livewire::actingAs($otherUser)
            ->test(TaskWorkspace::class, ['id' => $secretTask->id])
            ->assertForbidden();
    }
}
