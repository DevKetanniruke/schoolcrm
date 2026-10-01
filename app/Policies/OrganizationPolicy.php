<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
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
        return $user->hasRole(['school-admin']);
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->organization_id === $organization->id || ($user->school && $user->school->organization_id === $organization->id);
    }

    public function create(User $user): bool
    {
        return false; // Only Super Admin can create new Organizations
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->hasRole('school-admin') && ($user->organization_id === $organization->id || ($user->school && $user->school->organization_id === $organization->id));
    }

    public function delete(User $user, Organization $organization): bool
    {
        return false;
    }
}
