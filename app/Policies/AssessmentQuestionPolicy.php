<?php

namespace App\Policies;

use App\Models\AssessmentQuestion;
use App\Models\User;

class AssessmentQuestionPolicy
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
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']);
    }

    public function view(User $user, AssessmentQuestion $question): bool
    {
        return (int) $user->school_id === (int) $question->school_id &&
            $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']);
    }

    public function update(User $user, AssessmentQuestion $question): bool
    {
        return (int) $user->school_id === (int) $question->school_id &&
            ($user->hasRole(['super-admin', 'school-admin', 'principal']) || (int) $question->created_by === (int) $user->id);
    }

    public function delete(User $user, AssessmentQuestion $question): bool
    {
        return (int) $user->school_id === (int) $question->school_id &&
            ($user->hasRole(['super-admin', 'school-admin']) || (int) $question->created_by === (int) $user->id);
    }

    public function review(User $user, AssessmentQuestion $question): bool
    {
        return (int) $user->school_id === (int) $question->school_id &&
            $user->hasRole(['super-admin', 'school-admin', 'principal', 'teacher']);
    }
}
