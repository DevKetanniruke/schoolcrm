<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('permissions.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('permissions.manage');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permissions.manage');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permissions.manage');
    }
}
