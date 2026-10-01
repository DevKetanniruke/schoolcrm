<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out flex flex-col justify-between border-r border-slate-800 shadow-2xl"
>
    <!-- Brand / Header -->
    <div>
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-indigo-500/30">
                    S
                </div>
                <div>
                    <span class="font-bold text-white tracking-wide text-base block leading-tight">SmartSchool</span>
                    <span class="text-xs text-indigo-400 font-medium">{{ __('messages.app_name') }}</span>
                </div>
            </a>

            <!-- Close Mobile Button -->
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- School Selector Badge -->
        <div class="px-4 py-3 border-b border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 truncate">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-semibold text-slate-200 truncate">{{ auth()->user()->school->name ?? 'Oakridge Intl. Academy' }}</span>
                </div>
                <span class="text-[10px] font-mono uppercase bg-indigo-500/20 text-indigo-300 px-1.5 py-0.5 rounded border border-indigo-500/30">{{ auth()->user()->school->code ?? 'OIA-01' }}</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-3 space-y-1 overflow-y-auto max-h-[calc(100vh-10rem)] scrollbar-thin scrollbar-thumb-slate-800">
            <!-- Dashboard link -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span>{{ __('messages.dashboard') }}</span>
            </a>

            <!-- Section Label -->
            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">{{ __('messages.crm_modules') }}</div>

            <!-- Students Menu -->
            <div x-data="{ open: {{ request()->routeIs('students.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('students.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.436 60.436 0 01-.491-6.347A48.627 48.627 0 0112 3c4.229 0 8.084 1.258 11.23 3.421.196 2.062.03 4.19-.491 6.347m-15.482 0a50.57 50.57 0 00-2.658 8.14A59.905 59.905 0 0112 19.5c3.67 0 7.185-.56 10.428-1.592a50.567 50.567 0 00-2.658-8.14M12 12v.01" />
                        </svg>
                        <span>{{ __('messages.students') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('students.index') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('students.index') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.all_students_directory') }}</a>
                    <a href="{{ route('students.engagement-analytics') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('students.engagement-analytics') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">📊 {{ __('messages.engagement_analytics') }}</a>
                </div>
            </div>

            <!-- Parent & Family Menu -->
            <div x-data="{ open: {{ request()->routeIs('parents.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('parents.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ __('messages.parents') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('parents.index') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('parents.index') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.parent_directory') }}</a>
                    <a href="{{ route('parents.family-dashboard') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('parents.family-dashboard') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.family_dashboard') }}</a>
                    <a href="{{ route('parents.communication-log') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('parents.communication-log') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.communication_log_center') }}</a>
                </div>
            </div>

            <!-- Teacher & Staff Menu -->
            <div x-data="{ open: {{ request()->routeIs('staff.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('staff.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ __('messages.staff') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('staff.index') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('staff.index') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.staff_directory') }}</a>
                    <a href="{{ route('staff.matrix') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('staff.matrix') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.class_subject_matrix') }}</a>
                </div>
            </div>

            <!-- Admission CRM Menu -->
            <div x-data="{ open: {{ request()->routeIs('admissions.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admissions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ __('messages.admissions') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('admissions.dashboard') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('admissions.dashboard') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.executive_dashboard') }}</a>
                    <a href="{{ route('admissions.pipeline') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('admissions.pipeline') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.kanban_board') }}</a>
                    <a href="{{ route('admissions.index') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('admissions.index') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.leads_directory') }}</a>
                </div>
            </div>

            <!-- Multi-Channel Communication CRM Menu -->
            <div x-data="{ open: {{ request()->routeIs('communication.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('communication.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span>{{ __('messages.communications') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('communication.dashboard') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('communication.dashboard') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.command_center') }}</a>
                    <a href="{{ route('communication.ai-assistant') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('communication.ai-assistant') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">🤖 {{ __('messages.ai_parent_communication') }}</a>
                    <a href="{{ route('communication.composer') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('communication.composer') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.broadcast_composer') }}</a>
                    <a href="{{ route('communication.templates') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('communication.templates') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.message_templates') }}</a>
                    <a href="{{ route('communication.logs') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('communication.logs') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.delivery_logs') }}</a>
                </div>
            </div>

            <!-- CRM Task & Follow-up Engine Menu -->
            <div x-data="{ open: {{ request()->routeIs('tasks.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('tasks.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>{{ __('messages.tasks') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('tasks.dashboard') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('tasks.dashboard') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.task_command_center') }}</a>
                </div>
            </div>

            <!-- AI Weekly Assessment System Menu -->
            <div x-data="{ open: {{ request()->routeIs('assessments.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('assessments.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} transition group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition group-hover:scale-110 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('messages.weekly_assessments') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                    <a href="{{ route('assessments.dashboard') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('assessments.dashboard') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.assessment_dashboard') }}</a>
                    <a href="{{ route('assessments.questions') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('assessments.questions') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.question_bank') }}</a>
                    <a href="{{ route('assessments.manage') }}" class="block px-3 py-1.5 rounded-md text-xs font-medium {{ request()->routeIs('assessments.manage') ? 'text-indigo-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">{{ __('messages.test_manager') }}</a>
                </div>
            </div>

            <!-- AI Intelligence Layer -->
            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold text-indigo-400 uppercase tracking-wider flex items-center justify-between">
                <span>{{ __('messages.ai_intelligence') }}</span>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-mono uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Copilot</span>
            </div>

            <a href="{{ route('ai.assistant') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('ai.assistant') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <div class="p-1 rounded-md bg-indigo-500/20 mr-3 group-hover:scale-110 transition">
                    <svg class="w-4 h-4 text-indigo-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span>{{ __('messages.ai_copilot') }}</span>
            </a>

            <!-- Administration & User Management Section -->
            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">{{ __('messages.administration') }}</div>

            <!-- Management Intelligence -->
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('admin.management-intelligence') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.management-intelligence') ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 text-purple-400 group-hover:text-white transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 012 2h2a2 2 0 012-2z" />
                    </svg>
                    <span>🛡️ {{ __('messages.management_intelligence') }}</span>
                </a>
                <a href="{{ route('admin.automation-rules') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.automation-rules') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 text-indigo-400 group-hover:text-white transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>⚡ {{ __('messages.crm_automation') }}</span>
                </a>
                <a href="{{ route('admin.crm-analytics') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.crm-analytics') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 text-emerald-400 group-hover:text-white transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>📈 {{ __('messages.school_crm_analytics') ?? 'School CRM Analytics' }}</span>
                </a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.notifications') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 text-purple-400 group-hover:text-white transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>🔔 {{ __('messages.notification_engine') ?? 'Notification Engine' }}</span>
                </a>
                <a href="{{ route('documents.vault') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('documents.vault') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 text-blue-400 group-hover:text-white transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    <span>📁 {{ __('messages.digital_document_vault') ?? 'Digital Document Vault' }}</span>
                </a>
            @endcan

            <!-- School Hierarchy -->
            <a href="{{ route('admin.hierarchy.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.hierarchy.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>{{ __('messages.school_hierarchy') }}</span>
            </a>

            <!-- User Directory -->
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>{{ __('messages.user_management') }}</span>
                </a>
            @endcan

            <!-- RBAC Roles -->
            @can('viewAny', App\Models\Role::class)
                <a href="{{ route('admin.roles.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>{{ __('messages.rbac_roles') }}</span>
                </a>
            @endcan

            <!-- System Permissions -->
            @can('viewAny', App\Models\Permission::class)
                <a href="{{ route('admin.permissions.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition group {{ request()->routeIs('admin.permissions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                    <span>{{ __('messages.permissions_matrix') }}</span>
                </a>
            @endcan
        </nav>
    </div>

    <!-- Footer Profile summary -->
    <div class="p-3 border-t border-slate-800 bg-slate-950/60">
        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-2 py-1.5 hover:bg-slate-800/60 rounded-xl transition">
            <div class="w-9 h-9 rounded-full bg-slate-700 ring-2 ring-indigo-500/50 flex items-center justify-center font-bold text-white text-xs">
                {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
            </div>
            <div class="flex-1 truncate">
                <p class="text-xs font-semibold text-slate-100 truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@smartschool.edu' }}</p>
            </div>
        </a>
    </div>
</aside>
