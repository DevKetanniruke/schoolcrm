<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-indigo-900 via-slate-900 to-purple-950 p-6 rounded-2xl text-white shadow-xl border border-indigo-500/20">
        <div>
            <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-indigo-500/20 rounded-xl backdrop-blur-md border border-indigo-400/30">
                    <svg class="w-7 h-7 text-indigo-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">SmartSchool AI Copilot Layer ({{ app()->getLocale() === 'mr' ? 'मराठी' : 'English' }})</h1>
                    <p class="text-sm text-slate-300">Decoupled AI intelligence engine respecting RBAC permissions, audit logging, and safe non-destructive action.</p>
                </div>
            </div>
        </div>

        <!-- Usage Quota Meter -->
        <div class="flex items-center space-x-4 bg-slate-800/80 p-3 rounded-xl border border-slate-700/60 backdrop-blur-sm text-xs">
            <div>
                <p class="text-slate-400 font-medium">{{ __('messages.daily_quota') }}</p>
                <p class="text-sm font-bold text-indigo-300 mt-0.5">{{ $dailyRequests }} / {{ $dailyLimit }} requests</p>
            </div>
            <div class="h-8 w-px bg-slate-700"></div>
            <div>
                <p class="text-slate-400 font-medium">{{ __('messages.tokens_used') }}</p>
                <p class="text-sm font-bold text-purple-300 mt-0.5">{{ number_format($dailyTokens) }} tokens</p>
            </div>
        </div>
    </div>

    <!-- Capabilities Selector Tabs -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Select AI Capability Domain</label>
        <div class="flex flex-wrap items-center gap-2">
            @foreach($capabilitiesMap as $key => $labelKey)
                <button wire:click="setCapability('{{ $key }}')" class="px-3.5 py-2 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 {{ $selectedCapability === $key ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                    <span>{{ __("messages.{$key}") }}</span>
                </button>
            @endforeach
        </div>

        <!-- Context Entity Binders -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-200 dark:border-slate-800">
            @can('viewAny', App\Models\Student::class)
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Bind Student Context</label>
                    <select wire:model="selectedStudentId" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                        <option value="">None Selected</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->full_name }} (ID #{{ $s->student_id_number }})</option>
                        @endforeach
                    </select>
                </div>
            @endcan

            @can('viewAny', App\Models\AdmissionLead::class)
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Bind Admission Lead</label>
                    <select wire:model="selectedLeadId" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                        <option value="">None Selected</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}">{{ $l->parent_name }} (Child: {{ $l->student_name }})</option>
                        @endforeach
                    </select>
                </div>
            @endcan

            @can('viewAny', App\Models\ParentProfile::class)
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Bind Parent Context</label>
                    <select wire:model="selectedParentId" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white">
                        <option value="">None Selected</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->email }})</option>
                        @endforeach
                    </select>
                </div>
            @endcan
        </div>
    </div>

    <!-- Interactive Quick Prompt Chips -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('messages.preset_prompts') }}</p>
        <div class="flex flex-wrap gap-2">
            <button wire:click="usePresetPrompt('{{ __('messages.preset_student_summary') }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 text-xs font-medium rounded-xl border border-indigo-200/60 dark:border-indigo-900/50 transition">
                ⚡ {{ __('messages.preset_student_summary') }}
            </button>
            <button wire:click="usePresetPrompt('{{ __('messages.preset_lead_followup') }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 text-xs font-medium rounded-xl border border-indigo-200/60 dark:border-indigo-900/50 transition">
                ⚡ {{ __('messages.preset_lead_followup') }}
            </button>
            <button wire:click="usePresetPrompt('{{ __('messages.preset_overdue_tasks') }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 text-xs font-medium rounded-xl border border-indigo-200/60 dark:border-indigo-900/50 transition">
                ⚡ {{ __('messages.preset_overdue_tasks') }}
            </button>
            <button wire:click="usePresetPrompt('{{ __('messages.preset_event_announcement') }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 text-xs font-medium rounded-xl border border-indigo-200/60 dark:border-indigo-900/50 transition">
                ⚡ {{ __('messages.preset_event_announcement') }}
            </button>
            <button wire:click="usePresetPrompt('{{ __('messages.preset_admin_followup') }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 text-xs font-medium rounded-xl border border-indigo-200/60 dark:border-indigo-900/50 transition">
                ⚡ {{ __('messages.preset_admin_followup') }}
            </button>
        </div>
    </div>

    <!-- Main Chat Workspace -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col min-h-[500px]">
        <!-- Stream Header -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">Active Session: {{ __("messages.{$selectedCapability}") }}</h2>
            </div>
            <div class="flex items-center space-x-3">
                <select wire:model="preferredDriver" class="px-2.5 py-1 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg dark:text-white">
                    <option value="local_heuristic">Local AI Driver (Heuristic)</option>
                    <option value="openai">OpenAI (GPT-4o Mini)</option>
                    <option value="anthropic">Anthropic (Claude 3.5)</option>
                </select>
                <button wire:click="clearChat" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    {{ __('messages.clear_stream') }}
                </button>
            </div>
        </div>

        <!-- Chat Stream Box -->
        <div class="p-6 flex-1 overflow-y-auto space-y-4 max-h-[600px] scrollbar-thin">
            @foreach($chatMessages as $msg)
                <div class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                    <div class="flex items-center space-x-2 mb-1 text-[11px] text-slate-400">
                        <span>{{ $msg['role'] === 'user' ? 'You' : 'SmartSchool AI' }}</span>
                        <span>•</span>
                        <span>{{ $msg['timestamp'] }}</span>
                    </div>

                    <div class="max-w-3xl p-4 rounded-2xl shadow-sm text-sm space-y-2 {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700 rounded-bl-none' }}">
                        <div class="whitespace-pre-line leading-relaxed">
                            {!! Str::markdown($msg['content']) !!}
                        </div>

                        @if(!empty($msg['suggestedActions']))
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-700/60 flex flex-wrap gap-2">
                                @foreach($msg['suggestedActions'] as $act)
                                    <button onclick="alert('Action {{ $act['action'] }} confirmed by user.')" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                        ✓ {{ $act['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Box -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            <form wire:submit.prevent="submitPrompt" class="flex items-center space-x-3">
                <input type="text" wire:model="prompt" placeholder="{{ __('messages.prompt_placeholder') }}" class="flex-1 px-4 py-3 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                <button type="submit" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl transition shadow-lg shadow-indigo-600/30">
                    {{ __('messages.send_query') }}
                </button>
            </form>
            @error('prompt') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
