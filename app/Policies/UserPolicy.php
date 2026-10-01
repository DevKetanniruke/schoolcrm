<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Perform pre-authorization checks (Super Admin bypass).
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('users.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('users.edit');
    }

    public function delete(User $user, User $model): bool
    {
        // Prevent deleting oneself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermission('users.delete');
    }

    public function manageRoles(User $user, User $model): bool
    {
        return $user->hasPermission('users.manage-roles');
    }
}
