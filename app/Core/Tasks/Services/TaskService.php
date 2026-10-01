<?php

namespace App\Core\Tasks\Services;

use App\Core\Communication\Services\CommunicationManager;
use App\Models\CrmTask;
use App\Models\CrmTaskActivity;
use App\Models\CrmTaskComment;
use App\Models\CrmTaskReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    public function __construct(
        protected CommunicationManager $communicationManager
    ) {}

    /**
     * Create a new task and log activity.
     */
    public function createTask(array $data, User $actor): CrmTask
    {
        return DB::transaction(function () use ($data, $actor) {
            $task = CrmTask::create([
                'school_id' => $data['school_id'] ?? $actor->school_id ?? 1,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'taskable_type' => $data['taskable_type'] ?? null,
                'taskable_id' => $data['taskable_id'] ?? null,
                'created_by' => $actor->id,
                'assigned_to' => $data['assigned_to'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'status' => $data['status'] ?? 'pending',
                'due_at' => $data['due_at'],
                'is_recurring' => $data['is_recurring'] ?? false,
                'recurrence_pattern' => $data['recurrence_pattern'] ?? null,
                'recurrence_end_date' => $data['recurrence_end_date'] ?? null,
            ]);

            // Auto-log task creation activity
            $this->logActivity($task, $actor, 'created', "Task created by {$actor->name}");

            // Optional initial reminder
            if (!empty($data['reminder_at'])) {
                $this->addReminder(
                    $task,
                    $data['reminder_at'],
                    $data['reminder_channel'] ?? 'in_app',
                    $task->assigned_to ? User::find($task->assigned_to) : $actor
                );
            }

            return $task;
        });
    }

    /**
     * Update task core details.
     */
    public function updateTask(CrmTask $task, array $data, User $actor): CrmTask
    {
        return DB::transaction(function () use ($task, $data, $actor) {
            $oldPriority = $task->priority;
            $oldAssignee = $task->assigned_to;

            $task->update([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'priority' => $data['priority'] ?? $task->priority,
                'assigned_to' => array_key_exists('assigned_to', $data) ? $data['assigned_to'] : $task->assigned_to,
                'due_at' => $data['due_at'] ?? $task->due_at,
                'is_recurring' => $data['is_recurring'] ?? $task->is_recurring,
                'recurrence_pattern' => $data['recurrence_pattern'] ?? $task->recurrence_pattern,
                'recurrence_end_date' => $data['recurrence_end_date'] ?? $task->recurrence_end_date,
            ]);

            if (isset($data['priority']) && $data['priority'] !== $oldPriority) {
                $this->logActivity($task, $actor, 'priority_changed', "Priority changed from {$oldPriority} to {$data['priority']}");
            }

            if (array_key_exists('assigned_to', $data) && $data['assigned_to'] != $oldAssignee) {
                $assigneeName = $data['assigned_to'] ? User::find($data['assigned_to'])?->name : 'Unassigned';
                $this->logActivity($task, $actor, 'assigned', "Task reassigned to {$assigneeName}");
            }

            return $task;
        });
    }

    /**
     * Update task status.
     */
    public function updateStatus(CrmTask $task, string $newStatus, User $actor): CrmTask
    {
        if ($task->status === $newStatus) {
            return $task;
        }

        return DB::transaction(function () use ($task, $newStatus, $actor) {
            $oldStatus = $task->status;
            
            $task->status = $newStatus;
            if ($newStatus === 'completed') {
                $task->completed_at = now();
            }
            $task->save();

            $this->logActivity($task, $actor, 'status_changed', "Status updated from {$oldStatus} to {$newStatus}", [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            // Handle recurring task generation if completed
            if ($newStatus === 'completed' && $task->is_recurring && $task->recurrence_pattern) {
                $this->createRecurrenceInstance($task);
            }

            return $task;
        });
    }

    /**
     * Assign task to a user.
     */
    public function assignTask(CrmTask $task, ?int $assigneeId, User $actor): CrmTask
    {
        return $this->updateTask($task, ['assigned_to' => $assigneeId], $actor);
    }

    /**
     * Add comment to a task with optional attachment.
     */
    public function addComment(
        CrmTask $task,
        string $commentText,
        ?string $attachmentPath = null,
        ?string $attachmentName = null,
        ?User $actor = null
    ): CrmTaskComment {
        $actor = $actor ?? auth()->user();

        return DB::transaction(function () use ($task, $commentText, $attachmentPath, $attachmentName, $actor) {
            $comment = CrmTaskComment::create([
                'task_id' => $task->id,
                'user_id' => $actor->id,
                'comment' => $commentText,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            $this->logActivity($task, $actor, 'comment_added', "Comment added by {$actor->name}");

            return $comment;
        });
    }

    /**
     * Schedule a task reminder.
     */
    public function addReminder(
        CrmTask $task,
        mixed $remindAt,
        string $channel = 'in_app',
        ?User $user = null
    ): CrmTaskReminder {
        $user = $user ?? $task->assignee ?? $task->creator;

        return CrmTaskReminder::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'remind_at' => Carbon::parse($remindAt),
            'channel' => $channel,
            'is_sent' => false,
        ]);
    }

    /**
     * Check for past-due tasks and mark them as overdue.
     */
    public function checkAndFlagOverdueTasks(?int $schoolId = null): int
    {
        $query = CrmTask::whereIn('status', ['pending', 'in_progress'])
            ->where('due_at', '<', now());

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $overdueTasks = $query->get();
        $count = 0;

        foreach ($overdueTasks as $task) {
            DB::transaction(function () use ($task, &$count) {
                $oldStatus = $task->status;
                $task->update(['status' => 'overdue']);

                $this->logActivity($task, null, 'overdue_flagged', "Task flagged as overdue (due date was {$task->due_at->format('Y-m-d H:i')})", [
                    'old_status' => $oldStatus,
                    'due_at' => $task->due_at->toIso8601String(),
                ]);

                $count++;
            });
        }

        return $count;
    }

    /**
     * Process pending task reminders whose remind_at time has arrived.
     */
    public function processDueReminders(): int
    {
        $pendingReminders = CrmTaskReminder::with(['task', 'user'])
            ->where('is_sent', false)
            ->where('remind_at', '<=', now())
            ->get();

        $processedCount = 0;

        foreach ($pendingReminders as $reminder) {
            try {
                $task = $reminder->task;
                $user = $reminder->user;

                if ($task && $user) {
                    $destination = match ($reminder->channel) {
                        'email' => $user->email,
                        'sms', 'whatsapp' => $user->phone ?? $user->email,
                        default => (string) $user->id,
                    };

                    $subject = "Task Reminder: {$task->title}";
                    $content = "Reminder for task: {$task->title}. Priority: " . strtoupper($task->priority) . ". Due at: {$task->due_at->format('Y-m-d H:i')}.";

                    $provider = $this->communicationManager->resolveProvider($reminder->channel);
                    $provider->send($destination, $subject, $content, [
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                    ]);
                }

                $reminder->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);

                if ($task) {
                    $this->logActivity($task, null, 'reminder_sent', "Reminder dispatched via {$reminder->channel} to {$user?->name}");
                }

                $processedCount++;
            } catch (\Throwable $e) {
                throw $e; // Rethrow for test visibility
            }
        }

        return $processedCount;
    }

    /**
     * Create recurring task instance when a recurring task is completed.
     */
    public function createRecurrenceInstance(CrmTask $task): ?CrmTask
    {
        if (!$task->is_recurring || !$task->recurrence_pattern) {
            return null;
        }

        $nextDueAt = match ($task->recurrence_pattern) {
            'daily' => $task->due_at->addDay(),
            'weekly' => $task->due_at->addWeek(),
            'monthly' => $task->due_at->addMonth(),
            default => null,
        };

        if (!$nextDueAt) {
            return null;
        }

        if ($task->recurrence_end_date && $nextDueAt->gt($task->recurrence_end_date)) {
            return null;
        }

        $newTask = $task->replicate([
            'status',
            'completed_at',
            'created_at',
            'updated_at',
        ]);

        $newTask->status = 'pending';
        $newTask->due_at = $nextDueAt;
        $newTask->save();

        $creator = User::find($task->created_by) ?? auth()->user();
        if ($creator) {
            $this->logActivity($newTask, $creator, 'created', "Recurring task instance created automatically from parent Task #{$task->id}");
        }

        return $newTask;
    }

    /**
     * Internal helper to record task audit logs.
     */
    protected function logActivity(
        CrmTask $task,
        ?User $actor,
        string $type,
        string $description,
        ?array $properties = null
    ): CrmTaskActivity {
        return CrmTaskActivity::create([
            'task_id' => $task->id,
            'user_id' => $actor?->id,
            'activity_type' => $type,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
