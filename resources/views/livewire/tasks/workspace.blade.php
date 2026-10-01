<div class="space-y-6">
    <!-- Top Nav / Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                <a href="/tasks/dashboard" class="hover:text-indigo-600 transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Command Center
                </a>
                <span>/</span>
                <span>Task #{{ $task->id }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $task->title }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $task->priority_badge_color }}">
                    {{ ucfirst($task->priority) }} Priority
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $task->status_badge_color }}">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button wire:click="$set('showReminderModal', true)" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 font-medium text-xs rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Schedule Reminder
            </button>
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

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Details & Comments -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Card -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-3">Task Specification & Overview</h2>
                
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mt-1 whitespace-pre-line">{{ $task->description ?: 'No additional description provided.' }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-slate-800">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Due Timestamp</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-0.5 {{ $task->is_overdue ? 'text-rose-600 dark:text-rose-400' : '' }}">
                            {{ $task->due_at->format('M d, Y H:i') }}
                        </p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-slate-800">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Created By</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $task->creator?->name ?? 'System' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-slate-800">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Recurrence Pattern</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-0.5">
                            {{ $task->is_recurring ? ucfirst($task->recurrence_pattern) : 'Non-recurring' }}
                        </p>
                    </div>
                </div>

                <!-- Entity Linkage Box -->
                @if($task->taskable)
                    <div class="p-4 bg-indigo-50/60 dark:bg-indigo-950/30 rounded-xl border border-indigo-200/60 dark:border-indigo-900/50 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase text-indigo-700 dark:text-indigo-300">Linked Entity 360 Context</span>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white mt-0.5">
                                {{ class_basename($task->taskable_type) }} #{{ $task->taskable_id }}
                            </h4>
                        </div>
                        <span class="px-3 py-1 bg-indigo-600 text-white font-medium text-xs rounded-lg shadow-sm">
                            Polymorphic Attached
                        </span>
                    </div>
                @endif
            </div>

            <!-- Comments & Attachments Thread -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <span>Contextual Comments & Attachments ({{ $task->comments->count() }})</span>
                </h2>

                <!-- Post Comment Form -->
                <form wire:submit.prevent="postComment" class="space-y-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div>
                        <textarea wire:model="newComment" rows="3" placeholder="Add an operational update or comment..." class="w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"></textarea>
                        @error('newComment') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-xs text-slate-500">
                            <input type="file" wire:model="attachment" class="text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950 dark:file:text-indigo-300" />
                            @error('attachment') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-xl shadow-md transition">
                            Post Comment
                        </button>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-3 mt-4">
                    @forelse($task->comments as $comment)
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/60 dark:border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">
                                        {{ substr($comment->user?->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $comment->user?->name ?? 'System' }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-sm text-slate-700 dark:text-slate-300 pl-9">{{ $comment->comment }}</p>

                            @if($comment->attachment_path)
                                <div class="pl-9 pt-1">
                                    <a href="{{ Storage::url($comment->attachment_path) }}" target="_blank" class="inline-flex items-center text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        {{ $comment->attachment_name ?: 'Download Attachment' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No comments posted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Controls, Audit Trail, Reminders -->
        <div class="space-y-6">
            <!-- Action Controls Card -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 pb-2">Status & Ownership</h3>

                <!-- Status Buttons -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1.5">Change Status</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="changeStatus('in_progress')" class="px-3 py-2 text-xs font-medium rounded-xl border transition {{ $task->status === 'in_progress' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                            In Progress
                        </button>
                        <button wire:click="changeStatus('completed')" class="px-3 py-2 text-xs font-medium rounded-xl border transition {{ $task->status === 'completed' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                            Completed
                        </button>
                        <button wire:click="changeStatus('pending')" class="px-3 py-2 text-xs font-medium rounded-xl border transition {{ $task->status === 'pending' ? 'bg-amber-600 text-white border-amber-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                            Pending
                        </button>
                        <button wire:click="changeStatus('cancelled')" class="px-3 py-2 text-xs font-medium rounded-xl border transition {{ $task->status === 'cancelled' ? 'bg-rose-600 text-white border-rose-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                            Cancelled
                        </button>
                    </div>
                </div>

                <!-- Priority Selector -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Priority</label>
                    <div class="flex items-center space-x-2">
                        <select wire:model="updatePriority" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <button wire:click="changePriority" class="px-3 py-1.5 bg-slate-900 text-white hover:bg-slate-800 text-xs font-medium rounded-xl transition dark:bg-indigo-600 dark:hover:bg-indigo-500">
                            Update
                        </button>
                    </div>
                </div>

                <!-- Assignee Selector -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Assigned Staff</label>
                    <div class="flex items-center space-x-2">
                        <select wire:model="updateAssignedTo" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                            <option value="">Unassigned</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="reassign" class="px-3 py-1.5 bg-slate-900 text-white hover:bg-slate-800 text-xs font-medium rounded-xl transition dark:bg-indigo-600 dark:hover:bg-indigo-500">
                            Reassign
                        </button>
                    </div>
                </div>
            </div>

            <!-- Scheduled Reminders List -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center justify-between">
                    <span>Reminders Schedule</span>
                    <span class="text-xs font-normal text-indigo-600 dark:text-indigo-400">({{ $task->reminders->count() }})</span>
                </h3>

                <div class="space-y-2">
                    @forelse($task->reminders as $reminder)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ $reminder->remind_at->format('M d, H:i') }}</p>
                                <span class="uppercase text-[10px] text-slate-400 font-bold">{{ $reminder->channel }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $reminder->is_sent ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                {{ $reminder->is_sent ? 'Sent' : 'Pending' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-2">No active reminders scheduled.</p>
                    @endforelse
                </div>
            </div>

            <!-- Audit Trail / Activity History -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 pb-2">
                    Audit Trail & History Log
                </h3>

                <div class="relative border-l border-slate-200 dark:border-slate-800 ml-2 space-y-4 py-1">
                    @foreach($task->activities as $act)
                        <div class="ml-4 relative">
                            <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-indigo-600 ring-4 ring-white dark:ring-slate-900"></span>
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $act->description }}</p>
                            <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                <span>{{ $act->user?->name ?? 'System Engine' }}</span>
                                <span>•</span>
                                <span>{{ $act->created_at->format('M d, H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Add Reminder Modal -->
    @if($showReminderModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Schedule Task Reminder</h3>
                    <button wire:click="$set('showReminderModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="addReminder" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Reminder Timestamp *</label>
                        <input type="datetime-local" wire:model="newReminderAt" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Channel *</label>
                        <select wire:model="newReminderChannel" class="w-full px-3.5 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                            <option value="in_app">In-App Notification</option>
                            <option value="email">Email Notification</option>
                            <option value="sms">SMS Text</option>
                            <option value="whatsapp">WhatsApp Message</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" wire:click="$set('showReminderModal', false)" class="px-3.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-medium bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md transition">
                            Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
