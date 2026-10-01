<?php

namespace App\Policies;

use App\Models\AdmissionLead;
use App\Models\User;

class AdmissionPolicy
{
    /**
     * Determine whether the user can view any admission leads.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'admission-staff', 'counselor'])
            || $user->hasPermission('admissions.view');
    }

    /**
     * Determine whether the user can view a specific admission lead.
     */
    public function view(User $user, AdmissionLead $lead): bool
    {
        if ($user->school_id && $lead->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'admission-staff', 'counselor'])
            || $user->hasPermission('admissions.view')
            || $lead->assigned_counselor_id === $user->id;
    }

    /**
     * Determine whether the user can create admission leads.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'admission-staff', 'counselor'])
            || $user->hasPermission('admissions.create');
    }

    /**
     * Determine whether the user can update an admission lead.
     */
    public function update(User $user, AdmissionLead $lead): bool
    {
        if ($user->school_id && $lead->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'admission-staff', 'counselor'])
            || $user->hasPermission('admissions.edit')
            || $lead->assigned_counselor_id === $user->id;
    }

    /**
     * Determine whether the user can delete an admission lead.
     */
    public function delete(User $user, AdmissionLead $lead): bool
    {
        if ($user->school_id && $lead->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin'])
            || $user->hasPermission('admissions.delete');
    }

    /**
     * Determine whether the user can reassign counselors to leads.
     */
    public function assign(User $user, AdmissionLead $lead): bool
    {
        if ($user->school_id && $lead->school_id !== $user->school_id && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'admission-staff'])
            || $user->hasPermission('admissions.assign');
    }
}
