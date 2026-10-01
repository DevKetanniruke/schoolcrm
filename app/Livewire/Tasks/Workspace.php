<?php

namespace App\Livewire\Tasks;

use App\Core\Tasks\Services\TaskService;
use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class Workspace extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public CrmTask $task;

    // Comment form
    public string $newComment = '';
    public $attachment = null;

    // Status update
    public string $updateStatus = '';
    public string $updatePriority = '';
    public ?int $updateAssignedTo = null;

    // Reminder form
    public string $newReminderAt = '';
    public string $newReminderChannel = 'in_app';
    public bool $showReminderModal = false;

    public function mount(int $id)
    {
        $this->task = CrmTask::with(['assignee', 'creator', 'taskable', 'comments.user', 'activities.user', 'reminders.user'])
            ->findOrFail($id);

        $this->authorize('view', $this->task);

        $this->updateStatus = $this->task->status;
        $this->updatePriority = $this->task->priority;
        $this->updateAssignedTo = $this->task->assigned_to;
        $this->newReminderAt = now()->addHours(2)->format('Y-m-d\TH:i');
    }

    public function changeStatus(string $status, TaskService $taskService)
    {
        $this->authorize('update', $this->task);

        $this->task = $taskService->updateStatus($this->task, $status, auth()->user());
        $this->updateStatus = $this->task->status;

        session()->flash('success', "Status changed to {$status}.");
    }

    public function changePriority(TaskService $taskService)
    {
        $this->authorize('update', $this->task);

        $this->task = $taskService->updateTask($this->task, ['priority' => $this->updatePriority], auth()->user());

        session()->flash('success', "Priority updated to {$this->updatePriority}.");
    }

    public function reassign(TaskService $taskService)
    {
        $this->authorize('assign', $this->task);

        $this->task = $taskService->assignTask($this->task, $this->updateAssignedTo, auth()->user());

        session()->flash('success', "Task reassigned successfully.");
    }

    public function postComment(TaskService $taskService)
    {
        $this->authorize('update', $this->task);
        $this->validate([
            'newComment' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // max 10MB
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->store('task-attachments', 'public');
        }

        $taskService->addComment(
            $this->task,
            $this->newComment,
            $attachmentPath,
            $attachmentName,
            auth()->user()
        );

        $this->newComment = '';
        $this->attachment = null;
        $this->task->load(['comments.user', 'activities.user']);

        session()->flash('success', 'Comment posted successfully.');
    }

    public function addReminder(TaskService $taskService)
    {
        $this->authorize('update', $this->task);
        $this->validate([
            'newReminderAt' => 'required|date',
            'newReminderChannel' => 'required|in:in_app,email,sms,whatsapp',
        ]);

        $taskService->addReminder(
            $this->task,
            $this->newReminderAt,
            $this->newReminderChannel,
            auth()->user()
        );

        $this->showReminderModal = false;
        $this->task->load('reminders.user');

        session()->flash('success', 'Reminder scheduled.');
    }

    public function render()
    {
        $users = User::where('school_id', $this->task->school_id)->orderBy('name')->get();

        return view('livewire.tasks.workspace', [
            'users' => $users,
        ])->layout('components.layouts.admin', ['title' => "Task #{$this->task->id}: {$this->task->title}"]);
    }
}
