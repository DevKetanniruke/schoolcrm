<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
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
        return $user->hasPermission('students.view');
    }

    public function view(User $user, Student $student): bool
    {
        return $user->hasPermission('students.view') && $user->school_id === $student->school_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('students.create');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasPermission('students.edit') && $user->school_id === $student->school_id;
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermission('students.delete') && $user->school_id === $student->school_id;
    }

    public function viewConfidentialNotes(User $user, Student $student): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'counselor']) || $user->hasPermission('students.edit');
    }
}
