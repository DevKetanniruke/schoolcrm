<?php

namespace App\Core\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * Get user roles relationship.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has a specific role or any of the given roles.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    /**
     * Check if user has a specific permission via any assigned role.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Super Admin role has all permissions implicitly
        if ($this->hasRole('super-admin')) {
            return true;
        }

        $userPermissions = $this->roles->flatMap(function (Role $role) {
            return $role->permissions->pluck('slug');
        })->unique();

        return $userPermissions->contains($permissionSlug);
    }

    /**
     * Alias for hasPermission.
     */
    public function hasPermissionTo(string $permissionSlug): bool
    {
        return $this->hasPermission($permissionSlug);
    }

    /**
     * Assign role(s) to user.
     */
    public function assignRole(...$roles): self
    {
        $roleIds = collect($roles)->flatMap(function ($role) {
            if ($role instanceof Role) {
                return [$role->id];
            }
            if (is_numeric($role)) {
                return [(int) $role];
            }
            return Role::where('slug', $role)->pluck('id');
        })->unique();

        $this->roles()->syncWithoutDetaching($roleIds);
        $this->unsetRelation('roles');

        return $this;
    }

    /**
     * Remove role(s) from user.
     */
    public function removeRole(...$roles): self
    {
        $roleIds = collect($roles)->flatMap(function ($role) {
            if ($role instanceof Role) {
                return [$role->id];
            }
            if (is_numeric($role)) {
                return [(int) $role];
            }
            return Role::where('slug', $role)->pluck('id');
        });

        $this->roles()->detach($roleIds);
        $this->unsetRelation('roles');

        return $this;
    }

    /**
     * Sync user roles.
     */
    public function syncRoles(array $roles): self
    {
        $roleIds = collect($roles)->flatMap(function ($role) {
            if ($role instanceof Role) {
                return [$role->id];
            }
            if (is_numeric($role)) {
                return [(int) $role];
            }
            return Role::where('slug', $role)->pluck('id');
        })->unique();

        $this->roles()->sync($roleIds);
        $this->unsetRelation('roles');

        return $this;
    }
}
