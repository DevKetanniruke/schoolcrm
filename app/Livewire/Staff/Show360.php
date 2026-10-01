<?php

namespace App\Livewire\Staff;

use App\Models\StaffEvaluation;
use App\Models\StaffNote;
use App\Models\StaffProfile;
use App\Models\StaffTask;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show360 extends Component
{
    use AuthorizesRequests;

    public StaffProfile $staff;
    public string $activeTab = 'overview';

    // Task Form
    public bool $showTaskModal = false;
    public string $taskTitle = '';
    public string $taskDescription = '';
    public string $taskDueDate = '';
    public string $taskPriority = 'medium';

    // Evaluation Form
    public bool $showEvalModal = false;
    public string $evalReviewPeriod = '';
    public int $evalRating = 5;
    public string $evalFeedback = '';
    public string $evalGoals = '';

    // Note Form
    public bool $showNoteModal = false;
    public string $noteContent = '';
    public string $noteCategory = 'general';
    public bool $noteIsConfidential = false;

    public function mount(StaffProfile $staff): void
    {
        $this->staff = $staff;
        $this->authorize('view', $this->staff);
        $this->taskDueDate = now()->addDays(7)->format('Y-m-d');
        $this->evalReviewPeriod = 'Q' . ceil(date('n') / 3) . ' ' . date('Y');
    }

    public function addTask(): void
    {
        $this->authorize('update', $this->staff);

        $this->validate([
            'taskTitle' => 'required|string|max:150',
            'taskDescription' => 'nullable|string',
            'taskDueDate' => 'nullable|date',
            'taskPriority' => 'required|in:low,medium,high,urgent',
        ]);

        StaffTask::create([
            'staff_id' => $this->staff->id,
            'assigned_by' => Auth::id(),
            'title' => $this->taskTitle,
            'description' => $this->taskDescription,
            'due_date' => $this->taskDueDate ?: null,
            'priority' => $this->taskPriority,
            'status' => 'pending',
        ]);

        $this->reset(['taskTitle', 'taskDescription']);
        $this->taskPriority = 'medium';
        $this->showTaskModal = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Task Assigned',
            'message' => 'Successfully assigned new task to staff member.',
        ]);
    }

    public function toggleTaskStatus(StaffTask $task): void
    {
        $this->authorize('update', $this->staff);

        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        $this->dispatch('notify', [
            'type' => 'info',
            'title' => 'Task Updated',
            'message' => "Task status changed to {$newStatus}.",
        ]);
    }

    public function addEvaluation(): void
    {
        $this->authorize('update', $this->staff);

        $this->validate([
            'evalReviewPeriod' => 'required|string|max:50',
            'evalRating' => 'required|integer|min:1|max:5',
            'evalFeedback' => 'required|string',
            'evalGoals' => 'nullable|string',
        ]);

        StaffEvaluation::create([
            'staff_id' => $this->staff->id,
            'evaluator_id' => Auth::id(),
            'review_period' => $this->evalReviewPeriod,
            'rating' => $this->evalRating,
            'feedback' => $this->evalFeedback,
            'goals' => $this->evalGoals,
            'evaluation_date' => now(),
        ]);

        $this->reset(['evalFeedback', 'evalGoals']);
        $this->evalRating = 5;
        $this->showEvalModal = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Evaluation Recorded',
            'message' => 'Performance evaluation review saved successfully.',
        ]);
    }

    public function addNote(): void
    {
        $this->authorize('update', $this->staff);

        $this->validate([
            'noteContent' => 'required|string',
            'noteCategory' => 'required|string',
        ]);

        StaffNote::create([
            'staff_id' => $this->staff->id,
            'author_id' => Auth::id(),
            'note' => $this->noteContent,
            'category' => $this->noteCategory,
            'is_confidential' => $this->noteIsConfidential,
        ]);

        $this->reset(['noteContent']);
        $this->noteCategory = 'general';
        $this->noteIsConfidential = false;
        $this->showNoteModal = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Note Added',
            'message' => 'Administrative note saved to staff profile.',
        ]);
    }

    public function render()
    {
        $this->authorize('view', $this->staff);

        $this->staff->load([
            'department',
            'subjects',
            'classes.divisions',
            'classAssignments.schoolClass',
            'documents.uploader',
            'notes.author',
            'tasks.assigner',
            'evaluations.evaluator',
            'schedules.schoolClass',
            'schedules.subject',
        ]);

        $classIds = $this->staff->classes->pluck('id')->all();
        $totalStudentsCount = Student::whereIn('class_id', $classIds)->count();

        $canViewConfidential = Auth::user() ? Auth::user()->can('viewConfidential', $this->staff) : false;

        return view('livewire.staff.show360', [
            'staff' => $this->staff,
            'totalStudentsCount' => $totalStudentsCount,
            'canViewConfidential' => $canViewConfidential,
        ])->layout('components.layouts.admin', ['title' => "Teacher 360° Profile - {$this->staff->full_name}"]);
    }
}
