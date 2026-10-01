<?php

namespace App\Livewire\Admin\Permissions;

use App\Core\Services\AuditLogger;
use App\Models\Permission;
use App\Rules\SanitizedTextRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public bool $showPermissionModal = false;
    public ?int $editingPermissionId = null;

    public string $name = '';
    public string $group = 'general';
    public string $description = '';

    public function openCreateModal()
    {
        $this->authorize('create', Permission::class);
        $this->reset(['name', 'group', 'description', 'editingPermissionId']);
        $this->group = 'general';
        $this->showPermissionModal = true;
        $this->dispatch('open-drawer', 'permission-drawer');
    }

    public function openEditModal(int $permId)
    {
        $perm = Permission::findOrFail($permId);
        $this->authorize('update', $perm);

        $this->editingPermissionId = $perm->id;
        $this->name = $perm->name;
        $this->group = $perm->group;
        $this->description = $perm->description ?? '';

        $this->showPermissionModal = true;
        $this->dispatch('open-drawer', 'permission-drawer');
    }

    public function savePermission(AuditLogger $auditLogger)
    {
        if ($this->editingPermissionId) {
            $perm = Permission::findOrFail($this->editingPermissionId);
            $this->authorize('update', $perm);

            $this->validate([
                'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
                'group' => ['required', 'string', 'max:50', new SanitizedTextRule],
                'description' => ['nullable', 'string', 'max:500', new SanitizedTextRule],
            ]);

            $perm->update([
                'name' => $this->name,
                'group' => $this->group,
                'description' => $this->description,
            ]);

            $auditLogger->log(
                action: 'permission.updated',
                payload: ['permission_id' => $perm->id, 'slug' => $perm->slug]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Permission Updated',
                'message' => "Permission {$perm->name} updated successfully.",
            ]);
        } else {
            $this->authorize('create', Permission::class);

            $this->validate([
                'name' => ['required', 'string', 'max:255', new SanitizedTextRule],
                'group' => ['required', 'string', 'max:50', new SanitizedTextRule],
                'description' => ['nullable', 'string', 'max:500', new SanitizedTextRule],
            ]);

            $slug = Str::slug($this->group . '.' . $this->name);

            $perm = Permission::create([
                'name' => $this->name,
                'slug' => $slug,
                'group' => strtolower($this->group),
                'description' => $this->description,
            ]);

            $auditLogger->log(
                action: 'permission.created',
                payload: ['permission_id' => $perm->id, 'slug' => $perm->slug]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Permission Created',
                'message' => "New permission {$perm->name} created successfully.",
            ]);
        }

        $this->showPermissionModal = false;
        $this->dispatch('close-drawer', 'permission-drawer');
    }

    #[Layout('components.layouts.admin')]
    public function render()
    {
        $this->authorize('viewAny', Permission::class);

        $permissionsGrouped = Permission::all()->groupBy('group');

        return view('livewire.admin.permissions.index', [
            'permissionsGrouped' => $permissionsGrouped,
        ]);
    }
}
