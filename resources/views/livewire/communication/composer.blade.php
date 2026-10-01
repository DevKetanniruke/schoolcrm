<div class="space-y-6">
    <!-- Top Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Multi-Channel Broadcast Composer</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Compose, format, target, and schedule communication messages for students, parents, staff, or admission leads.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('communication.dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition">
                &larr; Back to Command Center
            </a>
        </div>
    </div>

    <!-- Main Composer Form Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
        <form wire:submit.prevent="dispatchBroadcast" class="space-y-6">
            <!-- Row 1: Title & Template Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Broadcast Title / Campaign Reference *</label>
                    <input type="text" wire:model="title" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Term 2 Fee Payment Due Reminder">
                    @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Load Pre-built Template</label>
                    <select wire:model.live="template_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Custom Message (No Template) --</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">{{ $tpl->title }} ({{ strtoupper($tpl->channel) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: Channel & Target Audience -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Delivery Channel *</label>
                    <select wire:model="channel" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="email">Email Notification</option>
                        <option value="sms">SMS Text Alert</option>
                        <option value="whatsapp">WhatsApp Message</option>
                        <option value="in_app">In-App Notification</option>
                        <option value="multi_channel">Multi-Channel Omni Broadcast (All Channels)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Target Audience Group *</label>
                    <select wire:model.live="recipient_type" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="parent_group">All Parents & Guardians</option>
                        <option value="class">Specific Academic Class / Roster</option>
                        <option value="staff_group">All Teachers & Faculty Staff</option>
                        <option value="admission_leads">All Prospective Admission Leads</option>
                        <option value="school_wide">School-Wide Broadcast (Everyone)</option>
                        <option value="individual">Individual Recipient</option>
                    </select>
                </div>
            </div>

            <!-- Conditional Filters based on Target Audience -->
            @if($recipient_type === 'class')
                <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800">
                    <label class="block text-xs font-bold text-indigo-900 dark:text-indigo-200 mb-1">Select Target Class</label>
                    <select wire:model="target_class_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                        <option value="">-- Choose Class --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }} (Grade {{ $cls->year_level }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($recipient_type === 'individual')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Parent Target</label>
                        <select wire:model="target_parent_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="">-- Select Parent --</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Student Target</label>
                        <select wire:model="target_student_id" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="">-- Select Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_number }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <!-- Subject (Email & Multi-channel) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Subject Heading</label>
                <input type="text" wire:model="subject" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Message subject header line">
            </div>

            <!-- Variable Chips & Message Content Body -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Message Body *</label>
                    <div class="flex items-center gap-1">
                        <span class="text-[10px] text-slate-400">Insert Variable:</span>
                        @foreach($availableVariables as $varKey => $varLabel)
                            <button type="button" wire:click="insertVariable('{{ $varKey }}')" class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 rounded text-[10px] font-mono font-semibold transition">
                                +{{ '{' . $varKey . '}' }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <textarea wire:model="message_body" rows="6" class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-sans" placeholder="Type your message content body here... Use {student_name}, {parent_name}, or {school_name} as placeholders."></textarea>
                @error('message_body') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <!-- Schedule Options -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model.live="is_scheduled" id="is_scheduled" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <label for="is_scheduled" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                        Schedule for Future Release
                    </label>
                </div>

                @if($is_scheduled)
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-slate-500 font-medium">Release Time:</label>
                        <input type="datetime-local" wire:model="scheduled_at" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                        @error('scheduled_at') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('communication.dashboard') }}" class="px-5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    {{ $is_scheduled ? 'Schedule Broadcast' : 'Send Broadcast Now' }}
                </button>
            </div>
        </form>
    </div>
</div>
