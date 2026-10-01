<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class RbacAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_has_all_permissions_implicitly(): void
    {
        $superAdmin = User::where('email', 'admin@smartschool.edu')->first();

        $this->assertTrue($superAdmin->hasRole('super-admin'));
        $this->assertTrue($superAdmin->hasPermission('users.view'));
        $this->assertTrue($superAdmin->hasPermission('random.nonexistent.permission'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAny', User::class));
    }

    public function test_role_permission_assignment_and_checks(): void
    {
        $teacherUser = User::factory()->create();
        $teacherRole = Role::where('slug', 'teacher')->first();

        $teacherUser->assignRole($teacherRole);

        $this->assertTrue($teacherUser->hasRole('teacher'));
        $this->assertFalse($teacherUser->hasRole('super-admin'));
        $this->assertTrue($teacherUser->hasPermission('academics.view'));
        $this->assertFalse($teacherUser->hasPermission('users.create'));
    }

    public function test_unprivileged_user_is_denied_from_user_management(): void
    {
        $teacherUser = User::factory()->create();
        $teacherRole = Role::where('slug', 'teacher')->first();
        $teacherUser->assignRole($teacherRole);

        $response = $this->actingAs($teacherUser)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $superAdmin = User::where('email', 'admin@smartschool.edu')->first();

        $response = $this->actingAs($superAdmin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('User Management');
    }

    public function test_admin_can_create_user_and_toggle_status(): void
    {
        $superAdmin = User::where('email', 'admin@smartschool.edu')->first();
        $teacherRole = Role::where('slug', 'teacher')->first();

        Livewire::actingAs($superAdmin)
            ->test(\App\Livewire\Admin\Users\Index::class)
            ->set('name', 'Jane Teacher')
            ->set('email', 'janeteacher@smartschool.edu')
            ->set('phone', '+15559871111')
            ->set('status', 'active')
            ->set('password', 'password123')
            ->set('selectedRoles', [$teacherRole->id])
            ->call('saveUser');

        $createdUser = User::where('email', 'janeteacher@smartschool.edu')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('teacher'));

        // Toggle status
        Livewire::actingAs($superAdmin)
            ->test(\App\Livewire\Admin\Users\Index::class)
            ->call('toggleUserStatus', $createdUser->id);

        $createdUser->refresh();
        $this->assertEquals('inactive', $createdUser->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.status_changed',
            'user_id' => $superAdmin->id,
        ]);
    }
}
