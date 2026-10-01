<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
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

    public function view(User $user, School $school): bool
    {
        return $user->school_id === $school->id || ($user->school && $user->school->organization_id === $school->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('school-admin');
    }

    public function update(User $user, School $school): bool
    {
        return $user->hasRole('school-admin') && ($user->school_id === $school->id || ($user->school && $user->school->organization_id === $school->organization_id));
    }

    public function delete(User $user, School $school): bool
    {
        return false;
    }
}
