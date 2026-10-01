<?php

namespace App\Livewire\Admin\Users;

use App\Core\Services\AuditLogger;
use App\Models\Role;
use App\Models\User;
use App\Rules\PhoneRule;
use App\Rules\SanitizedTextRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $statusFilter = '';
    public string $roleFilter = '';

    // Modal state
    public bool $showUserModal = false;
    public ?int $editingUserId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $status = 'active';
    public string $password = '';
    public array $selectedRoles = [];

    protected $queryString = ['search', 'statusFilter', 'roleFilter'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->authorize('create', User::class);
        $this->reset(['name', 'email', 'phone', 'status', 'password', 'selectedRoles', 'editingUserId']);
        $this->status = 'active';
        $this->showUserModal = true;
        $this->dispatch('open-drawer', 'user-drawer');
    }

    public function openEditModal(int $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->status = $user->status ?? 'active';
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->password = '';

        $this->showUserModal = true;
        $this->dispatch('open-drawer', 'user-drawer');
    }

    public function saveUser(AuditLogger $auditLogger)
    {
        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $this->authorize('update', $user);

            $this->validate([
                'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['nullable', 'string', new PhoneRule],
                'status' => ['required', 'in:active,inactive,suspended'],
                'password' => ['nullable', 'string', 'min:8'],
                'selectedRoles' => ['array'],
            ]);

            $oldStatus = $user->status;

            $user->forceFill([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
            ]);

            if (! empty($this->password)) {
                $user->password = Hash::make($this->password);
            }

            $user->save();

            // Sync assigned roles
            $user->syncRoles($this->selectedRoles);

            $auditLogger->log(
                action: 'user.updated',
                payload: ['user_id' => $user->id, 'changes' => $user->getChanges()]
            );

            if ($oldStatus !== $this->status) {
                $auditLogger->log(
                    action: 'user.status_changed',
                    payload: ['user_id' => $user->id, 'from' => $oldStatus, 'to' => $this->status]
                );
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'User Updated',
                'message' => "User account {$user->email} updated successfully.",
            ]);
        } else {
            $this->authorize('create', User::class);

            $this->validate([
                'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['nullable', 'string', new PhoneRule],
                'status' => ['required', 'in:active,inactive,suspended'],
                'password' => ['required', 'string', 'min:8'],
                'selectedRoles' => ['array'],
            ]);

            $user = User::create([
                'school_id' => auth()->user()->school_id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
            ]);

            if (! empty($this->selectedRoles)) {
                $user->syncRoles($this->selectedRoles);
            }

            $auditLogger->log(
                action: 'user.created',
                payload: ['user_id' => $user->id, 'email' => $user->email]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'User Created',
                'message' => "New user account {$user->email} created successfully.",
            ]);
        }

        $this->showUserModal = false;
        $this->dispatch('close-drawer', 'user-drawer');
    }

    public function toggleUserStatus(int $userId, AuditLogger $auditLogger)
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
        $user->status = $newStatus;
        $user->save();

        $auditLogger->log(
            action: 'user.status_changed',
            payload: ['user_id' => $user->id, 'new_status' => $newStatus]
        );

        $this->dispatch('notify', [
            'type' => $newStatus === 'active' ? 'success' : 'warning',
            'title' => 'Status Changed',
            'message' => "User {$user->name} is now {$newStatus}.",
        ]);
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['roles', 'school']);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->roleFilter)) {
            $query->whereHas('roles', function ($q) {
                $q->where('slug', $this->roleFilter);
            });
        }

        $users = $query->latest()->paginate(10);
        $allRoles = Role::all();

        return view('livewire.admin.users.index', [
            'users' => $users,
            'allRoles' => $allRoles,
        ]);
    }
}
