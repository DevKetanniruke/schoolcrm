<?php

namespace App\Policies;

use App\Models\AssessmentAttempt;
use App\Models\Student;
use App\Models\User;

class AssessmentAttemptPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    public function view(User $user, AssessmentAttempt $attempt): bool
    {
        if ((int) $user->school_id !== (int) $attempt->school_id) {
            return false;
        }

        if ($user->hasRole('student') || $user->hasRole('Student')) {
            $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            return $student && (int) $student->id === (int) $attempt->student_id;
        }

        return true;
    }

    public function takeTest(User $user, AssessmentAttempt $attempt): bool
    {
        if ((int) $user->school_id !== (int) $attempt->school_id) {
            return false;
        }

        if ($user->hasRole('student') || $user->hasRole('Student')) {
            $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            return $student && (int) $student->id === (int) $attempt->student_id;
        }

        return true;
    }
}
