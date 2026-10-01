<?php

namespace App\Livewire\Tasks;

use App\Core\Tasks\Services\TaskService;
use App\Models\AdmissionApplication;
use App\Models\AdmissionLead;
use App\Models\CrmTask;
use App\Models\ParentComplaint;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $activeTab = 'my_tasks'; // todays_tasks, my_tasks, overdue_tasks, upcoming_tasks, all_tasks
    public string $search = '';
    public string $selectedPriority = '';
    public string $selectedStatus = '';
    public string $selectedEntityType = '';

    // Create Modal Fields
    public bool $showCreateModal = false;
    public string $title = '';
    public string $description = '';
    public string $priority = 'medium';
    public ?int $assigned_to = null;
    public string $due_at = '';
    public string $taskable_type = '';
    public ?int $taskable_id = null;

    // Reminder options
    public bool $enable_reminder = false;
    public string $reminder_at = '';
    public string $reminder_channel = 'in_app';

    // Recurrence options
    public bool $is_recurring = false;
    public string $recurrence_pattern = 'weekly';
    public string $recurrence_end_date = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'due_at' => 'required|date',
        'assigned_to' => 'nullable|exists:users,id',
        'taskable_type' => 'nullable|string',
        'taskable_id' => 'nullable|integer',
    ];

    public function mount()
    {
        $this->authorize('viewAny', CrmTask::class);
        $this->due_at = now()->addDay()->format('Y-m-d\TH:i');
        $this->reminder_at = now()->addDay()->subHours(2)->format('Y-m-d\TH:i');
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedPriority()
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->authorize('create', CrmTask::class);
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm()
    {
        $this->reset([
            'title',
            'description',
            'priority',
            'assigned_to',
            'taskable_type',
            'taskable_id',
            'enable_reminder',
            'is_recurring',
        ]);
        $this->due_at = now()->addDay()->format('Y-m-d\TH:i');
        $this->reminder_at = now()->addDay()->subHours(2)->format('Y-m-d\TH:i');
    }

    public function saveTask(TaskService $taskService)
    {
        $this->authorize('create', CrmTask::class);
        $this->validate();

        $user = auth()->user();

        $data = [
            'school_id' => $user->school_id ?? 1,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'pending',
            'due_at' => $this->due_at,
            'assigned_to' => $this->assigned_to,
            'taskable_type' => $this->taskable_type ?: null,
            'taskable_id' => $this->taskable_id ?: null,
            'is_recurring' => $this->is_recurring,
            'recurrence_pattern' => $this->is_recurring ? $this->recurrence_pattern : null,
            'recurrence_end_date' => ($this->is_recurring && $this->recurrence_end_date) ? $this->recurrence_end_date : null,
        ];

        if ($this->enable_reminder && $this->reminder_at) {
            $data['reminder_at'] = $this->reminder_at;
            $data['reminder_channel'] = $this->reminder_channel;
        }

        $taskService->createTask($data, $user);

        session()->flash('success', 'Task created successfully.');
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function markCompleted(int $taskId, TaskService $taskService)
    {
        $task = CrmTask::findOrFail($taskId);
        $this->authorize('update', $task);

        $taskService->updateStatus($task, 'completed', auth()->user());
        session()->flash('success', "Task #{$task->id} marked as Completed.");
    }

    public function markInProgress(int $taskId, TaskService $taskService)
    {
        $task = CrmTask::findOrFail($taskId);
        $this->authorize('update', $task);

        $taskService->updateStatus($task, 'in_progress', auth()->user());
        session()->flash('success', "Task #{$task->id} moved to In Progress.");
    }

    public function render(TaskService $taskService)
    {
        $currentUser = auth()->user();
        $schoolId = $currentUser->school_id ?? 1;

        // Auto check overdue tasks on dashboard load
        $taskService->checkAndFlagOverdueTasks($schoolId);

        // Overall KPIs
        $baseQuery = CrmTask::where('school_id', $schoolId);

        $kpis = [
            'total' => (clone $baseQuery)->count(),
            'due_today' => (clone $baseQuery)->whereDate('due_at', now()->toDateString())->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'my_pending' => (clone $baseQuery)->where('assigned_to', $currentUser->id)->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'overdue' => (clone $baseQuery)->where('status', 'overdue')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
        ];

        // Filtered tasks query
        $query = CrmTask::with(['assignee', 'creator', 'taskable'])
            ->where('school_id', $schoolId);

        // Filter by Tab
        match ($this->activeTab) {
            'todays_tasks' => $query->whereDate('due_at', now()->toDateString())->whereNotIn('status', ['completed', 'cancelled']),
            'my_tasks' => $query->where('assigned_to', $currentUser->id),
            'overdue_tasks' => $query->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($q2) {
                      $q2->whereNotIn('status', ['completed', 'cancelled'])->where('due_at', '<', now());
                  });
            }),
            'upcoming_tasks' => $query->where('due_at', '>', now())->whereNotIn('status', ['completed', 'cancelled']),
            default => null, // all_tasks
        };

        // Filter by Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Filter by Priority
        if ($this->selectedPriority) {
            $query->where('priority', $this->selectedPriority);
        }

        // Filter by Status
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        // Filter by Entity Type
        if ($this->selectedEntityType) {
            $query->where('taskable_type', $this->selectedEntityType);
        }

        $tasks = $query->orderBy('due_at', 'asc')->paginate(12);

        // User dropdown options
        $users = User::where('school_id', $schoolId)->orderBy('name')->get();

        // Entity options for create modal
        $entityTypes = [
            Student::class => 'Student',
            ParentProfile::class => 'Parent / Family',
            AdmissionLead::class => 'Admission Lead',
            StaffProfile::class => 'Teacher / Staff',
            School::class => 'School',
            AdmissionApplication::class => 'Admission Application',
            ParentComplaint::class => 'Parent Complaint Ticket',
        ];

        return view('livewire.tasks.dashboard', [
            'tasks' => $tasks,
            'kpis' => $kpis,
            'users' => $users,
            'entityTypes' => $entityTypes,
        ])->layout('components.layouts.admin', ['title' => 'CRM Task Command Center']);
    }
}
