<?php

namespace App\Policies;

use App\Models\ParentProfile;
use App\Models\User;

class ParentPolicy
{
    /**
     * Determine whether the user can view any parents.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher', 'counselor', 'admission-staff', 'accountant'])
            || $user->hasPermission('parents.view');
    }

    /**
     * Determine whether the user can view a specific parent profile.
     */
    public function view(User $user, ParentProfile $parent): bool
    {
        // Data boundary: check school matching
        if ($user->school_id && $parent->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        // Parent role user can only view their own parent profile
        if ($user->hasRole('parent')) {
            return $parent->user_id === $user->id;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher', 'counselor', 'admission-staff', 'accountant'])
            || $user->hasPermission('parents.view');
    }

    /**
     * Determine whether a parent user can view/switch to a specific linked student.
     */
    public function switchChild(User $user, ParentProfile $parent, int $studentId): bool
    {
        if ($user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher', 'counselor'])) {
            return true;
        }

        if ($user->hasRole('parent') && $parent->user_id === $user->id) {
            return $parent->students()->where('students.id', $studentId)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create parents.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'admission-staff'])
            || $user->hasPermission('parents.create');
    }

    /**
     * Determine whether the user can update the parent.
     */
    public function update(User $user, ParentProfile $parent): bool
    {
        if ($user->school_id && $parent->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'admission-staff'])
            || $user->hasPermission('parents.edit');
    }

    /**
     * Determine whether the user can delete the parent.
     */
    public function delete(User $user, ParentProfile $parent): bool
    {
        if ($user->school_id && $parent->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin'])
            || $user->hasPermission('parents.delete');
    }

    /**
     * Determine whether the user can view confidential notes on parent profile.
     */
    public function viewConfidentialNotes(User $user, ParentProfile $parent): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'counselor'])
            || $user->hasPermission('parents.edit');
    }
}
