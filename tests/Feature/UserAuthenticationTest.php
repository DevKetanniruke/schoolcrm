<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\School;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertNotNull($user->last_login_at);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.login',
            'user_id' => $user->id,
        ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.failed_login',
        ]);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'status' => 'suspended',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.logout',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_update_profile_and_password(): void
    {
        Storage::fake('uploads');
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\EditProfile::class)
            ->set('name', 'Updated Name')
            ->set('phone', '+15559876543')
            ->call('updateProfileInformation');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('+15559876543', $user->phone);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Profile\EditProfile::class)
            ->set('current_password', 'oldpassword')
            ->set('new_password', 'newsecret123')
            ->set('new_password_confirmation', 'newsecret123')
            ->call('updatePassword');

        $user->refresh();
        $this->assertTrue(Hash::check('newsecret123', $user->password));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.password_changed',
            'user_id' => $user->id,
        ]);
    }
}
