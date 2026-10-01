<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <!-- Header & Dashboard Selector -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl text-white shadow-lg shadow-indigo-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-wide">
                        {{ __('messages.school_crm_analytics') ?? 'School CRM Analytics' }}
                    </h1>
                    <p class="text-sm text-slate-400">
                        {{ __('messages.analytics_subtitle') ?? 'High-performance aggregated real-time & cached intelligence engine' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action & Cache controls -->
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-slate-800 text-emerald-400 border border-emerald-500/20">
                <span class="w-2 h-2 mr-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Cached: {{ $metrics['calculated_at'] ?? now()->toTimeString() }}
            </span>
            <button 
                wire:click="refreshCache" 
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm rounded-lg transition-all shadow-md flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>{{ __('messages.refresh_cache') ?? 'Refresh Cache' }}</span>
            </button>
        </div>
    </div>

    <!-- Filter Toolbar Bar -->
    <div class="bg-slate-800/80 backdrop-blur-md border border-slate-700/60 rounded-xl p-4 mb-6 shadow-xl">
        <div class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-3 flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <span>Global Analytics Filters</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- School Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">School</label>
                <select wire:model.live="schoolId" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200 focus:ring-1 focus:ring-indigo-500">
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Preset -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Date Range</label>
                <select wire:model.live="datePreset" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200 focus:ring-1 focus:ring-indigo-500">
                    <option value="today">Today</option>
                    <option value="this_month">This Month</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Date</option>
                </select>
            </div>

            <!-- Class Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Class / Grade</label>
                <select wire:model.live="classId" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Source Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Lead Source</label>
                <select wire:model.live="sourceId" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Sources</option>
                    @foreach($sources as $src)
                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Counselor Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Counselor / Staff</label>
                <select wire:model.live="counselorId" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200 focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Counselors</option>
                    @foreach($counselors as $cns)
                        <option value="{{ $cns->id }}">{{ $cns->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Button -->
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs py-2 px-3 rounded-lg font-medium transition-colors">
                    Reset Filters
                </button>
            </div>
        </div>

        @if($datePreset === 'custom')
            <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-slate-700/50">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Start Date</label>
                    <input type="date" wire:model.live="startDate" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-3 text-slate-200">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">End Date</label>
                    <input type="date" wire:model.live="endDate" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-3 text-slate-200">
                </div>
            </div>
        @endif
    </div>

    <!-- 6 Dashboard Tabs Switcher -->
    <div class="flex flex-wrap gap-2 border-b border-slate-800 mb-6 pb-2">
        @php
            $tabs = [
                'executive' => 'Executive Overview',
                'admissions' => 'Admissions & Leads',
                'communication' => 'Communication & Messaging',
                'student_engagement' => 'Student Engagement',
                'parent_engagement' => 'Parent Engagement',
                'staff_tasks' => 'Staff Tasks & SLA',
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <button 
                wire:click="setDashboard('{{ $key }}')"
                class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 flex items-center space-x-2 {{ $selectedDashboard === $key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>{{ $label }}</span>
            </button>
        @endforeach
    </div>

    <!-- Active Dashboard Body View -->
    <div>
        @if($selectedDashboard === 'executive')
            <!-- Executive Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Lead Volume</span>
                        <span class="px-2 py-0.5 text-xs bg-indigo-500/10 text-indigo-400 rounded-full font-bold">Total</span>
                    </div>
                    <div class="text-3xl font-extrabold text-white mb-2">{{ $metrics['summary']['lead_volume'] ?? 0 }}</div>
                    <p class="text-xs text-slate-400">Total new prospective inquiry leads registered</p>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Lead Conversion</span>
                        <span class="px-2 py-0.5 text-xs bg-emerald-500/10 text-emerald-400 rounded-full font-bold">Rate</span>
                    </div>
                    <div class="text-3xl font-extrabold text-emerald-400 mb-2">{{ $metrics['summary']['lead_conversion_rate'] ?? 0 }}%</div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $metrics['summary']['lead_conversion_rate'] ?? 0) }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Application Conversion</span>
                        <span class="px-2 py-0.5 text-xs bg-purple-500/10 text-purple-400 rounded-full font-bold">Rate</span>
                    </div>
                    <div class="text-3xl font-extrabold text-purple-400 mb-2">{{ $metrics['summary']['app_conversion_rate'] ?? 0 }}%</div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ min(100, $metrics['summary']['app_conversion_rate'] ?? 0) }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Follow-up Completion</span>
                        <span class="px-2 py-0.5 text-xs bg-amber-500/10 text-amber-400 rounded-full font-bold">SLA</span>
                    </div>
                    <div class="text-3xl font-extrabold text-amber-400 mb-2">{{ $metrics['summary']['followup_completion_rate'] ?? 0 }}%</div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min(100, $metrics['summary']['followup_completion_rate'] ?? 0) }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Communication Delivery</span>
                        <span class="px-2 py-0.5 text-xs bg-blue-500/10 text-blue-400 rounded-full font-bold">Outreach</span>
                    </div>
                    <div class="text-3xl font-extrabold text-blue-400 mb-2">{{ $metrics['summary']['communication_delivery_rate'] ?? 0 }}%</div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, $metrics['summary']['communication_delivery_rate'] ?? 0) }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Task Completion</span>
                        <span class="px-2 py-0.5 text-xs bg-cyan-500/10 text-cyan-400 rounded-full font-bold">Productivity</span>
                    </div>
                    <div class="text-3xl font-extrabold text-cyan-400 mb-2">{{ $metrics['summary']['task_completion_rate'] ?? 0 }}%</div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-cyan-500 h-2 rounded-full" style="width: {{ min(100, $metrics['summary']['task_completion_rate'] ?? 0) }}%"></div>
                    </div>
                </div>
            </div>

        @elseif($selectedDashboard === 'admissions')
            <!-- Admissions Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Lead Volume</div>
                    <div class="text-3xl font-extrabold text-indigo-400 mt-2">{{ $metrics['lead_volume'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Conversion Rate</div>
                    <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $metrics['lead_conversion_rate'] ?? 0 }}%</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Avg Response Time</div>
                    <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $metrics['avg_response_time_hours'] ?? 0 }} hrs</div>
                </div>
            </div>

            <!-- Breakdown Grids -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Lead Sources Breakdown -->
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Lead Volume by Source</h3>
                    <div class="space-y-3">
                        @forelse($metrics['source_breakdown'] ?? [] as $src)
                            <div>
                                <div class="flex justify-between text-xs mb-1 text-slate-300">
                                    <span>{{ $src['source_name'] }}</span>
                                    <span class="font-bold">{{ $src['count'] }} leads</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ min(100, $src['count'] * 10) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 italic">No source breakdown data available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Counselor Performance Breakdown -->
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Lead Assignment by Counselor</h3>
                    <div class="space-y-3">
                        @forelse($metrics['counselor_breakdown'] ?? [] as $cns)
                            <div>
                                <div class="flex justify-between text-xs mb-1 text-slate-300">
                                    <span>{{ $cns['counselor_name'] }}</span>
                                    <span class="font-bold">{{ $cns['count'] }} leads</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ min(100, $cns['count'] * 10) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 italic">No counselor breakdown data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        @elseif($selectedDashboard === 'communication')
            <!-- Communication Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Total Dispatched Messages</div>
                    <div class="text-3xl font-extrabold text-blue-400 mt-2">{{ $metrics['total_communications'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Delivery Rate</div>
                    <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $metrics['delivery_rate'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5">
                <h3 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Channel Distribution</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($metrics['channel_breakdown'] ?? [] as $channel => $cnt)
                        <div class="bg-slate-900/60 p-4 rounded-lg text-center border border-slate-700/40">
                            <span class="text-xs text-slate-400 uppercase font-semibold block">{{ $channel }}</span>
                            <span class="text-2xl font-bold text-indigo-400 mt-1 block">{{ $cnt }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif($selectedDashboard === 'student_engagement')
            <!-- Student Engagement Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Tracked Students</div>
                    <div class="text-3xl font-extrabold text-indigo-400 mt-2">{{ $metrics['total_tracked_students'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Attendance Trends Rate</div>
                    <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $metrics['avg_attendance_percentage'] ?? 0 }}%</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Assignment Completion</div>
                    <div class="text-3xl font-extrabold text-cyan-400 mt-2">{{ $metrics['avg_assignment_completion'] ?? 0 }}%</div>
                </div>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5">
                <h3 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Student Engagement Status Distribution</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-slate-900/60 p-4 rounded-lg border border-emerald-500/20 text-center">
                        <span class="text-xs font-bold text-emerald-400 uppercase">🟢 Normal</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">{{ $metrics['overall_status_counts']['normal'] ?? 0 }}</span>
                    </div>

                    <div class="bg-slate-900/60 p-4 rounded-lg border border-amber-500/20 text-center">
                        <span class="text-xs font-bold text-amber-400 uppercase">🟡 Needs Attention</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">{{ $metrics['overall_status_counts']['needs_attention'] ?? 0 }}</span>
                    </div>

                    <div class="bg-slate-900/60 p-4 rounded-lg border border-blue-500/20 text-center">
                        <span class="text-xs font-bold text-blue-400 uppercase">🔵 Follow-up Recommended</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">{{ $metrics['overall_status_counts']['followup_recommended'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

        @elseif($selectedDashboard === 'parent_engagement')
            <!-- Parent Engagement Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Total Parent Interactions</div>
                    <div class="text-3xl font-extrabold text-purple-400 mt-2">{{ $metrics['total_interactions'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Total Complaints Logged</div>
                    <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $metrics['total_complaints'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Complaint Resolution Rate</div>
                    <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $metrics['complaint_resolution_rate'] ?? 0 }}%</div>
                </div>
            </div>

        @elseif($selectedDashboard === 'staff_tasks')
            <!-- Staff Tasks Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Total Assigned Tasks</div>
                    <div class="text-3xl font-extrabold text-indigo-400 mt-2">{{ $metrics['total_tasks'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Overdue SLA Tasks</div>
                    <div class="text-3xl font-extrabold text-rose-400 mt-2">{{ $metrics['overdue_tasks'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow-lg">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Task Completion Rate</div>
                    <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $metrics['task_completion_rate'] ?? 0 }}%</div>
                </div>
            </div>
        @endif
    </div>
</div>
