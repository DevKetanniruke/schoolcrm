<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Message Templates Library</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pre-built communication formats with dynamic variable placeholders for fees, attendance, emergencies, and events.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('communication.composer') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition">
                Composer
            </a>
            <button wire:click="createTemplate" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                + Create New Template
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search template title, code..." class="w-full pl-4 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
            </div>
            <div>
                <select wire:model.live="category" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                    <option value="">All Categories</option>
                    <option value="academic">Academic & Report Cards</option>
                    <option value="attendance">Attendance & Absences</option>
                    <option value="fees">Fee Payments & Billing</option>
                    <option value="events">Events & Open House</option>
                    <option value="admissions">Admission Intake</option>
                    <option value="emergency">Emergency Alerts</option>
                    <option value="general">General Notices</option>
                </select>
            </div>
            <div>
                <select wire:model.live="channel" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                    <option value="">All Channels</option>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="in_app">In-App</option>
                    <option value="multi_channel">Multi-Channel</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Template Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $tpl)
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                            {{ $tpl->category }}
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-400">
                            {{ $tpl->code }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $tpl->title }}</h3>

                    @if($tpl->subject)
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 border-l-2 border-indigo-400 pl-2">
                            Subject: {{ $tpl->subject }}
                        </p>
                    @endif

                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-3 bg-slate-50 dark:bg-slate-900/40 p-3 rounded-xl">
                        {{ $tpl->content_body }}
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                        {{ $tpl->channel }}
                    </span>
                    <button wire:click="editTemplate({{ $tpl->id }})" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">
                        Edit Template &rarr;
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-400 italic">No message templates found matching filter criteria.</div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $editingTemplateId ? 'Edit Message Template' : 'Create Message Template' }}</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Template Title *</label>
                        <input type="text" wire:model="title" class="w-full rounded-xl border-slate-200 dark:border-slate-700 text-sm">
                        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Template Code *</label>
                        <input type="text" wire:model="code" class="w-full rounded-xl border-slate-200 dark:border-slate-700 text-sm" placeholder="TPL-NOTICE">
                        @error('code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Channel</label>
                        <select wire:model="tpl_channel" class="w-full rounded-xl border-slate-200 dark:border-slate-700 text-sm">
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="in_app">In-App</option>
                            <option value="multi_channel">Multi-Channel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <select wire:model="tpl_category" class="w-full rounded-xl border-slate-200 dark:border-slate-700 text-sm">
                            <option value="general">General</option>
                            <option value="academic">Academic</option>
                            <option value="attendance">Attendance</option>
                            <option value="fees">Fees</option>
                            <option value="events">Events</option>
                            <option value="admissions">Admissions</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subject Header</label>
                    <input type="text" wire:model="subject" class="w-full rounded-xl border-slate-200 dark:border-slate-700 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Content Body *</label>
                    <textarea wire:model="content_body" rows="4" class="w-full rounded-xl border-slate-200 dark:border-slate-700 text-sm" placeholder="Use {student_name}, {parent_name}, {school_name}"></textarea>
                    @error('content_body') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 border rounded-xl text-sm text-slate-600 dark:text-slate-300">Cancel</button>
                    <button wire:click="saveTemplate" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700">Save Template</button>
                </div>
            </div>
        </div>
    @endif
</div>
