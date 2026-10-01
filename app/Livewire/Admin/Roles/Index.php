<?php

namespace App\Livewire\Admin\Roles;

use App\Core\Services\AuditLogger;
use App\Models\Permission;
use App\Models\Role;
use App\Rules\SanitizedTextRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public bool $showRoleModal = false;
    public ?int $editingRoleId = null;

    public string $name = '';
    public string $description = '';
    public array $selectedPermissions = [];

    public function openCreateModal()
    {
        $this->authorize('create', Role::class);
        $this->reset(['name', 'description', 'selectedPermissions', 'editingRoleId']);
        $this->showRoleModal = true;
        $this->dispatch('open-drawer', 'role-drawer');
    }

    public function openEditModal(int $roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $this->authorize('update', $role);

        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->description = $role->description ?? '';
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();

        $this->showRoleModal = true;
        $this->dispatch('open-drawer', 'role-drawer');
    }

    public function saveRole(AuditLogger $auditLogger)
    {
        if ($this->editingRoleId) {
            $role = Role::findOrFail($this->editingRoleId);
            $this->authorize('update', $role);

            $this->validate([
                'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
                'description' => ['nullable', 'string', 'max:500', new SanitizedTextRule],
                'selectedPermissions' => ['array'],
            ]);

            $role->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $role->permissions()->sync($this->selectedPermissions);

            $auditLogger->log(
                action: 'role.updated',
                payload: ['role_id' => $role->id, 'name' => $role->name]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Role Updated',
                'message' => "Role {$role->name} permissions updated successfully.",
            ]);
        } else {
            $this->authorize('create', Role::class);

            $this->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles,name', new SanitizedTextRule],
                'description' => ['nullable', 'string', 'max:500', new SanitizedTextRule],
                'selectedPermissions' => ['array'],
            ]);

            $slug = Str::slug($this->name);

            $role = Role::create([
                'school_id' => auth()->user()->school_id,
                'name' => $this->name,
                'slug' => $slug,
                'description' => $this->description,
            ]);

            if (! empty($this->selectedPermissions)) {
                $role->permissions()->sync($this->selectedPermissions);
            }

            $auditLogger->log(
                action: 'role.created',
                payload: ['role_id' => $role->id, 'name' => $role->name]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Role Created',
                'message' => "New role {$role->name} created successfully.",
            ]);
        }

        $this->showRoleModal = false;
        $this->dispatch('close-drawer', 'role-drawer');
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::withCount(['users', 'permissions'])->get();
        $permissionsGrouped = Permission::all()->groupBy('group');

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
            'permissionsGrouped' => $permissionsGrouped,
        ]);
    }
}
