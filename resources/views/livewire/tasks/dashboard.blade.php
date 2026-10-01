<div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-2xl text-white shadow-xl">
        <div>
            <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-indigo-600/30 rounded-xl backdrop-blur-md border border-indigo-400/20">
                    <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">CRM Task & Follow-up Command Center</h1>
                    <p class="text-sm text-slate-300">Centralized task management, follow-up automation, and audit logging across SmartSchool entities.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @can('create', App\Models\CrmTask::class)
                <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm rounded-xl transition shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/40">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Task
                </button>
            @endcan
        </div>
    </div>

    <!-- Alert Flash -->
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between dark:bg-emerald-950/40 dark:border-emerald-800/50 dark:text-emerald-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Executive KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Tasks -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Tasks</p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ number_format($kpis['total']) }}</h3>
            </div>
            <div class="p-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>

        <!-- Due Today -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">Due Today</p>
                <h3 class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ number_format($kpis['due_today']) }}</h3>
            </div>
            <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- My Pending Tasks -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">My Assigned</p>
                <h3 class="text-2xl font-bold text-indigo-700 dark:text-indigo-300 mt-1">{{ number_format($kpis['my_pending']) }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        <!-- Overdue Tasks -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Overdue Follow-ups</p>
                <h3 class="text-2xl font-bold text-rose-700 dark:text-rose-300 mt-1">{{ number_format($kpis['overdue']) }}</h3>
            </div>
            <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Completed Rate -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Completed</p>
                <h3 class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">{{ number_format($kpis['completed']) }}</h3>
            </div>
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <!-- Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <button wire:click="setTab('my_tasks')" class="px-4 py-2 text-sm font-medium rounded-xl transition {{ $activeTab === 'my_tasks' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                My Tasks
            </button>
            <button wire:click="setTab('todays_tasks')" class="px-4 py-2 text-sm font-medium rounded-xl transition {{ $activeTab === 'todays_tasks' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                Today's Tasks
            </button>
            <button wire:click="setTab('overdue_tasks')" class="px-4 py-2 text-sm font-medium rounded-xl transition {{ $activeTab === 'overdue_tasks' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                Overdue Tasks
            </button>
            <button wire:click="setTab('upcoming_tasks')" class="px-4 py-2 text-sm font-medium rounded-xl transition {{ $activeTab === 'upcoming_tasks' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                Upcoming Tasks
            </button>
            <button wire:click="setTab('all_tasks')" class="px-4 py-2 text-sm font-medium rounded-xl transition {{ $activeTab === 'all_tasks' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                All Tasks
            </button>
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Search -->
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tasks by title..." class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" />
            </div>

            <!-- Priority filter -->
            <div>
                <select wire:model.live="selectedPriority" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <!-- Status filter -->
            <div>
                <select wire:model.live="selectedStatus" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="overdue">Overdue</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Entity filter -->
            <div>
                <select wire:model.live="selectedEntityType" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                    <option value="">All Dynamic Entities</option>
                    @foreach($entityTypes as $class => $label)
                        <option value="{{ $class }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Task Cards / Table View -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                Tasks Directory ({{ $tasks->total() }})
            </h2>
        </div>

        @if($tasks->isEmpty())
            <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-base font-medium">No tasks found matching your active filter.</p>
                <p class="text-sm mt-1">Try resetting search filters or create a new task.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-200 font-semibold uppercase text-xs tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4">Task / Title</th>
                            <th class="py-3.5 px-4">Associated Entity</th>
                            <th class="py-3.5 px-4">Assignee</th>
                            <th class="py-3.5 px-4">Priority</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Due Date</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($tasks as $t)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        @if($t->is_recurring)
                                            <span title="Recurring Task ({{ ucfirst($t->recurrence_pattern) }})" class="p-1 text-indigo-600 bg-indigo-50 rounded dark:bg-indigo-900/30 dark:text-indigo-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </span>
                                        @endif
                                        <a href="/tasks/{{ $t->id }}" class="font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                            {{ $t->title }}
                                        </a>
                                    </div>
                                    @if($t->description)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ $t->description }}</p>
                                    @endif
                                </td>

                                <td class="py-4 px-4 text-xs font-medium">
                                    @if($t->taskable)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ class_basename($t->taskable_type) }} #{{ $t->taskable_id }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Standalone</span>
                                    @endif
                                </td>

                                <td class="py-4 px-4">
                                    @if($t->assignee)
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">
                                                {{ substr($t->assignee->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $t->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Unassigned</span>
                                    @endif
                                </td>

                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $t->priority_badge_color }}">
                                        {{ ucfirst($t->priority) }}
                                    </span>
                                </td>

                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $t->status_badge_color }}">
                                        @if($t->status === 'overdue')
                                            <svg class="w-3 h-3 mr-1 animate-pulse text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" />
                                            </svg>
                                        @endif
                                        {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                                    </span>
                                </td>

                                <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-400">
                                    <div class="{{ $t->is_overdue ? 'text-rose-600 dark:text-rose-400 font-bold' : '' }}">
                                        {{ $t->due_at->format('M d, Y H:i') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $t->due_at->diffForHumans() }}</div>
                                </td>

                                <td class="py-4 px-4 text-right space-x-1">
                                    @if($t->status !== 'completed')
                                        <button wire:click="markCompleted({{ $t->id }})" title="Mark Complete" class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg dark:bg-emerald-950/40 dark:text-emerald-400 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                    @if($t->status === 'pending')
                                        <button wire:click="markInProgress({{ $t->id }})" title="Start In Progress" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg dark:bg-indigo-950/40 dark:text-indigo-400 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            </svg>
                                        </button>
                                    @endif
                                    <a href="/tasks/{{ $t->id }}" class="inline-flex items-center px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-lg dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 transition">
                                        View 360°
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>

    <!-- Create Task Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Create New CRM Task</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveTask" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Task Title *</label>
                        <input type="text" wire:model="title" placeholder="e.g. Schedule follow-up call with parent" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                        @error('title') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" placeholder="Contextual background and actionable details..." class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Priority *</label>
                            <select wire:model="priority" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Due Date & Time *</label>
                            <input type="datetime-local" wire:model="due_at" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                            @error('due_at') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Assigned User</label>
                            <select wire:model="assigned_to" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                                <option value="">Assign Later</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Entity Association -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Link to CRM Entity</label>
                            <select wire:model.live="taskable_type" class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white">
                                <option value="">Standalone Task</option>
                                @foreach($entityTypes as $class => $label)
                                    <option value="{{ $class }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Entity Primary Key ID</label>
                            <input type="number" wire:model="taskable_id" placeholder="e.g. 1" class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                        </div>
                    </div>

                    <!-- Reminder Option -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <label class="flex items-center space-x-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model.live="enable_reminder" class="rounded text-indigo-600 focus:ring-indigo-500" />
                            <span>Schedule Notification Reminder</span>
                        </label>

                        @if($enable_reminder)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Remind At</label>
                                    <input type="datetime-local" wire:model="reminder_at" class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Channel</label>
                                    <select wire:model="reminder_channel" class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                                        <option value="in_app">In-App Notification</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Recurrence Option -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <label class="flex items-center space-x-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model.live="is_recurring" class="rounded text-indigo-600 focus:ring-indigo-500" />
                            <span>Set Recurrence Pattern</span>
                        </label>

                        @if($is_recurring)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Pattern</label>
                                    <select wire:model="recurrence_pattern" class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">End Date (Optional)</label>
                                    <input type="date" wire:model="recurrence_end_date" class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" />
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end space-x-3 border-t border-slate-200 dark:border-slate-800 pt-4">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/30 transition">
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
