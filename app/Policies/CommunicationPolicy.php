<?php

namespace App\Policies;

use App\Models\Communication;
use App\Models\User;

class CommunicationPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any communications.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('communications.view');
    }

    /**
     * Determine whether the user can view the communication.
     */
    public function view(User $user, Communication $communication): bool
    {
        return $user->hasPermission('communications.view')
            && (int) $user->school_id === (int) $communication->school_id;
    }

    /**
     * Determine whether the user can create/send communications.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('communications.send');
    }

    /**
     * Determine whether the user can update the communication.
     */
    public function update(User $user, Communication $communication): bool
    {
        return $user->hasPermission('communications.send')
            && (int) $user->school_id === (int) $communication->school_id;
    }

    /**
     * Determine whether the user can delete the communication.
     */
    public function delete(User $user, Communication $communication): bool
    {
        return $user->hasPermission('communications.send')
            && (int) $user->school_id === (int) $communication->school_id;
    }
}
