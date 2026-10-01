<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\Student;
use App\Models\User;

class AssessmentPolicy
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
        return $user->hasPermission('users.view') || $user->hasRole(['teacher', 'student', 'parent', 'school-admin', 'principal']);
    }

    public function view(User $user, Assessment $assessment): bool
    {
        if ((int) $user->school_id !== (int) $assessment->school_id) {
            return false;
        }

        if ($user->hasRole('student')) {
            if (! in_array($assessment->status, ['published', 'active', 'completed'])) {
                return false;
            }
            $student = Student::where('user_id', $user->id)->first();
            if (! $student) {
                return false;
            }
            return (int) $student->class_id === (int) $assessment->class_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']) || $user->hasPermission('users.view');
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return (int) $user->school_id === (int) $assessment->school_id &&
            ($user->hasRole(['super-admin', 'school-admin', 'principal']) || (int) $assessment->created_by === (int) $user->id);
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return (int) $user->school_id === (int) $assessment->school_id &&
            ($user->hasRole(['super-admin', 'school-admin']) || (int) $assessment->created_by === (int) $user->id);
    }

    public function review(User $user, Assessment $assessment): bool
    {
        return (int) $user->school_id === (int) $assessment->school_id &&
            $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']);
    }

    public function publish(User $user, Assessment $assessment): bool
    {
        return (int) $user->school_id === (int) $assessment->school_id &&
            $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']);
    }
}
