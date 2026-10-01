<?php

namespace App\Policies;

use App\Models\Division;
use App\Models\User;

class DivisionPolicy
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

    public function view(User $user, Division $division): bool
    {
        return $user->school_id === $division->schoolClass->department->school_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'principal', 'teacher']);
    }

    public function update(User $user, Division $division): bool
    {
        return $user->hasRole(['school-admin', 'principal']) && $user->school_id === $division->schoolClass->department->school_id;
    }

    public function delete(User $user, Division $division): bool
    {
        return $user->hasRole('school-admin') && $user->school_id === $division->schoolClass->department->school_id;
    }
}
