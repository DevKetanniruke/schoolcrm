<?php

namespace App\Policies;

use App\Models\StaffProfile;
use App\Models\User;

class StaffPolicy
{
    /**
     * Determine whether the user can view any staff directory.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher', 'counselor', 'admission-staff', 'accountant', 'hr'])
            || $user->hasPermission('staff.view');
    }

    /**
     * Determine whether the user can view a specific staff profile.
     */
    public function view(User $user, StaffProfile $staff): bool
    {
        // Data boundary check
        if ($user->school_id && $staff->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher', 'counselor', 'admission-staff', 'accountant', 'hr'])
            || $user->hasPermission('staff.view')
            || $staff->user_id === $user->id;
    }

    /**
     * Determine whether the user can create staff records.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'hr'])
            || $user->hasPermission('staff.create');
    }

    /**
     * Determine whether the user can update a staff record.
     */
    public function update(User $user, StaffProfile $staff): bool
    {
        if ($user->school_id && $staff->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'hr'])
            || $user->hasPermission('staff.edit');
    }

    /**
     * Determine whether the user can delete a staff record.
     */
    public function delete(User $user, StaffProfile $staff): bool
    {
        if ($user->school_id && $staff->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'hr'])
            || $user->hasPermission('staff.delete');
    }

    /**
     * Determine whether the user can assign subjects or classes to staff.
     */
    public function assign(User $user, StaffProfile $staff): bool
    {
        if ($user->school_id && $staff->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'hr'])
            || $user->hasPermission('staff.assign');
    }

    /**
     * Determine whether the user can view confidential staff notes & sensitive documents.
     */
    public function viewConfidential(User $user, StaffProfile $staff): bool
    {
        if ($user->school_id && $staff->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'hr'])
            || $user->hasPermission('staff.view_confidential');
    }
}
