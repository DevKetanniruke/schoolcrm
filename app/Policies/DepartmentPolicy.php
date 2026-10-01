<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
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
        return true;
    }

    public function view(User $user, Department $department): bool
    {
        return $user->school_id === $department->school_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }

    public function update(User $user, Department $department): bool
    {
        return $user->hasRole(['school-admin', 'principal']) && $user->school_id === $department->school_id;
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->hasRole('school-admin') && $user->school_id === $department->school_id;
    }
}
