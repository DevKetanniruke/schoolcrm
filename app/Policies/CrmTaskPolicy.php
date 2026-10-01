<?php

namespace App\Policies;

use App\Models\CrmTask;
use App\Models\User;

class CrmTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('tasks.view');
    }

    public function view(User $user, CrmTask $task): bool
    {
        if ($user->school_id && $task->school_id !== $user->school_id) {
            return false;
        }

        return $user->hasPermissionTo('tasks.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('tasks.create');
    }

    public function update(User $user, CrmTask $task): bool
    {
        if ($user->school_id && $task->school_id !== $user->school_id) {
            return false;
        }

        // Assignee, Creator, or user with tasks.edit permission
        return $task->assigned_to === $user->id 
            || $task->created_by === $user->id 
            || $user->hasPermissionTo('tasks.edit');
    }

    public function delete(User $user, CrmTask $task): bool
    {
        if ($user->school_id && $task->school_id !== $user->school_id) {
            return false;
        }

        return $user->hasPermissionTo('tasks.delete');
    }

    public function assign(User $user, CrmTask $task): bool
    {
        if ($user->school_id && $task->school_id !== $user->school_id) {
            return false;
        }

        return $user->hasPermissionTo('tasks.assign');
    }
}
