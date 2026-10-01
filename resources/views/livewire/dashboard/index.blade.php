<div>
    <!-- Page Header & Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>CRM Core</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Dashboard Overview</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Enterprise Overview</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Real-time school performance metrics & audit telemetry.</p>
        </div>

        <!-- Action Bar & Demo Controls -->
        <div class="flex items-center space-x-3">
            <x-ui.button variant="outline" size="sm" wire:click="triggerToast('info')">
                Test Info Toast
            </x-ui.button>
            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-modal', 'demo-modal')">
                <x-slot:icon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </x-slot:icon>
                Modal Demo
            </x-ui.button>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-ui.stat-card
            title="Total Students"
            value="{{ number_format($stats['total_students']) }}"
            change="+4.2%"
            changeType="increase"
            iconBg="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>

        <x-ui.stat-card
            title="Active Faculty & Staff"
            value="{{ $stats['active_staff'] }}"
            change="+2"
            changeType="increase"
            iconBg="bg-purple-500/10 text-purple-600 dark:text-purple-400"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>

        <x-ui.stat-card
            title="Fee Collections (MTD)"
            value="{{ $stats['monthly_revenue'] }}"
            change="+12.5%"
            changeType="increase"
            iconBg="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>

        <x-ui.stat-card
            title="Avg Attendance Rate"
            value="{{ $stats['attendance_rate'] }}"
            change="-0.3%"
            changeType="decrease"
            iconBg="bg-sky-500/10 text-sky-600 dark:text-sky-400"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-ui.stat-card>
    </div>

    <!-- Main Content Area: Chart Placeholder & Action Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Analytics Graphic Container -->
        <div class="lg:col-span-2">
            <x-ui.card title="Enrollment & Fee Telemetry" subtitle="Monthly collection vs student enrollment velocity">
                <div class="h-64 flex flex-col justify-between pt-4">
                    <!-- Visual Chart Canvas Mockup -->
                    <div class="w-full flex-1 flex items-end justify-between space-x-3 px-2">
                        <div class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 rounded-t-lg transition-all h-[40%] relative group">
                            <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-600 opacity-0 group-hover:opacity-100 transition">Jan ($95k)</span>
                        </div>
                        <div class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 rounded-t-lg transition-all h-[55%] relative group">
                            <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-600 opacity-0 group-hover:opacity-100 transition">Feb ($110k)</span>
                        </div>
                        <div class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 rounded-t-lg transition-all h-[70%] relative group">
                            <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-600 opacity-0 group-hover:opacity-100 transition">Mar ($128k)</span>
                        </div>
                        <div class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 rounded-t-lg transition-all h-[60%] relative group">
                            <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-600 opacity-0 group-hover:opacity-100 transition">Apr ($115k)</span>
                        </div>
                        <div class="w-full bg-indigo-500/20 hover:bg-indigo-500/30 rounded-t-lg transition-all h-[85%] relative group">
                            <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-600 opacity-0 group-hover:opacity-100 transition">May ($142k)</span>
                        </div>
                        <div class="w-full bg-indigo-600 rounded-t-lg transition-all h-[95%] relative group shadow-lg shadow-indigo-600/30">
                            <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-600 opacity-100">Jun ($158k)</span>
                        </div>
                    </div>
                    <!-- Month Labels -->
                    <div class="flex justify-between text-[11px] font-semibold text-slate-400 mt-4 border-t border-slate-100 dark:border-slate-800 pt-2 px-2">
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                        <span>May</span>
                        <span class="text-indigo-600 font-bold">Jun</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- System Architecture Status Panel -->
        <div>
            <x-ui.card title="System Readiness & Scoping" subtitle="Production-grade core parameters">
                <div class="space-y-4 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="font-medium text-slate-600 dark:text-slate-300">Active School Tenant</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $school->name ?? 'Oakridge Intl.' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="font-medium text-slate-600 dark:text-slate-300">Sanctum API Engine</span>
                        <x-ui.badge variant="success" size="sm">Active</x-ui.badge>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="font-medium text-slate-600 dark:text-slate-300">Queue Architecture</span>
                        <span class="font-mono text-slate-500 text-[11px]">Database / Redis</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <span class="font-medium text-slate-600 dark:text-slate-300">Audit Telemetry</span>
                        <x-ui.badge variant="primary" size="sm">Enabled</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <!-- Recent Audit Logs Table Component -->
    <x-ui.card title="System Audit Logs" subtitle="Recent administrative & security activities">
        <x-table.table>
            <x-table.header>
                <x-table.cell header>User</x-table.cell>
                <x-table.cell header>Action Event</x-table.cell>
                <x-table.cell header>IP Address</x-table.cell>
                <x-table.cell header>Timestamp</x-table.cell>
            </x-table.header>
            <x-table.body>
                @forelse ($activities as $log)
                    <x-table.row>
                        <x-table.cell>
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($log->user->name ?? 'Sys', 0, 2)) }}
                                </div>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400 font-mono">{{ $log->action }}</code>
                        </x-table.cell>
                        <x-table.cell>
                            <span class="text-xs font-mono text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                        </x-table.cell>
                        <x-table.cell>
                            <span class="text-xs text-slate-500">{{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}</span>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="4">
                            <x-ui.empty-state title="No Audit Records" description="No activity logs have been recorded yet." />
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-ui.card>

    <!-- Demo Modal Component -->
    <x-ui.modal name="demo-modal" title="Reusable Modal Component Demo">
        <form class="space-y-4">
            <x-form.group label="Student Full Name" name="student_name" required help="Enter primary legal name">
                <x-form.input name="student_name" placeholder="e.g. Alexander Pierce" />
            </x-form.group>

            <x-form.group label="Assigned Grade Level" name="grade">
                <x-form.select name="grade" :options="['g1' => 'Grade 1', 'g2' => 'Grade 2', 'g9' => 'Grade 9', 'g12' => 'Grade 12']" />
            </x-form.group>

            <x-form.group label="Send Parent Notification">
                <x-form.toggle name="notify_parent" enabled="true" label="Send Instant SMS Alert" />
            </x-form.group>
        </form>

        <x-slot:footer>
            <x-ui.button variant="ghost" @click="$dispatch('close-modal', 'demo-modal')">
                Cancel
            </x-ui.button>
            <x-ui.button variant="primary" wire:click="triggerToast('success')" @click="$dispatch('close-modal', 'demo-modal')">
                Save & Dispatch Event
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
