<div class="space-y-8">
    <!-- Top Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 md:p-8 text-white shadow-2xl border border-indigo-900/40">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        📊 Data-Driven CRM Insights
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Neutral Non-Stigmatizing Categories
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Student Engagement Analytics</h1>
                <p class="text-sm text-slate-300 mt-1 max-w-3xl">
                    Comprehensive engagement tracking across attendance, assignment activity, academic trends, communication response, participation, and support requests with full underlying data evidence.
                </p>
            </div>

            <button type="button" wire:click="recalculateAll" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Recalculate Indicators</span>
            </button>
        </div>
    </div>

    <!-- Executive Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Analyzed</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalEvaluated }}</div>
            <div class="text-[10px] text-indigo-400 mt-1">Active Students</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xl border border-emerald-500/20">
            <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                Normal
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $normalCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">{{ $totalEvaluated > 0 ? round(($normalCount / $totalEvaluated) * 100, 1) : 0 }}% of total</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xl border border-amber-500/20">
            <div class="text-xs font-semibold text-amber-600 dark:text-amber-400 flex items-center">
                <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span>
                Needs Attention
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $attentionCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">{{ $totalEvaluated > 0 ? round(($attentionCount / $totalEvaluated) * 100, 1) : 0 }}% of total</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xl border border-blue-500/20">
            <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center">
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-1.5"></span>
                Follow-up Rec.
            </div>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $followupCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">{{ $totalEvaluated > 0 ? round(($followupCount / $totalEvaluated) * 100, 1) : 0 }}% of total</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Avg Attendance</div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $avgAttendance }}%</div>
            <div class="text-[10px] text-slate-500 mt-1">Across all classes</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Avg Completion</div>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $avgAssignment }}%</div>
            <div class="text-[10px] text-slate-500 mt-1">Assignments</div>
        </div>

    </div>

    <!-- Filter Bar & Table Section -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-6">
        
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Search -->
            <div class="w-full md:w-72">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search student by name or adm #..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500" />
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <select wire:model.live="statusFilter" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="all">All Neutral Statuses</option>
                    <option value="normal">🟢 Normal</option>
                    <option value="needs_attention">🟡 Needs Attention</option>
                    <option value="followup_recommended">🔵 Follow-up Recommended</option>
                </select>

                <select wire:model.live="classFilter" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Student Engagement Analytics Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50 dark:bg-slate-950/50">
                        <th class="p-3.5 rounded-l-xl">Student Name</th>
                        <th class="p-3.5">Attendance</th>
                        <th class="p-3.5">Assignments</th>
                        <th class="p-3.5">Academics</th>
                        <th class="p-3.5">Comm. Response</th>
                        <th class="p-3.5">Participation</th>
                        <th class="p-3.5">Neutral Status</th>
                        <th class="p-3.5 text-right rounded-r-xl">Underlying Evidence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($engagementRecords as $record)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $record->student?->full_name }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ $record->student?->schoolClass?->name ?? 'Unassigned' }} | Adm #{{ $record->student?->admission_number }}
                                </div>
                            </td>

                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $record->attendance_percentage }}%</div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $record->attendance_days_present }}P / {{ $record->attendance_days_absent }}A
                                    @if($record->attendance_trend === 'improving')
                                        <span class="text-emerald-500 font-bold ml-1">↑ Improving</span>
                                    @elseif($record->attendance_trend === 'declining')
                                        <span class="text-rose-500 font-bold ml-1">↓ Declining</span>
                                    @else
                                        <span class="text-slate-400 ml-1">→ Stable</span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $record->assignment_completion_rate }}%</div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $record->assignment_submissions_count }} submitted, {{ $record->assignment_pending_count }} pending
                                </div>
                            </td>

                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $record->academic_gpa_score }}%</div>
                                <div class="text-[10px] text-slate-400 capitalize">Trend: {{ $record->academic_trend }}</div>
                            </td>

                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $record->communication_response_rate }}%</div>
                                <div class="text-[10px] text-slate-400">{{ $record->communication_interactions_count }} interactions</div>
                            </td>

                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $record->participation_score }}/100</div>
                                <div class="text-[10px] text-slate-400">{{ $record->extracurricular_activities_count }} clubs</div>
                            </td>

                            <td class="p-3.5">
                                @if($record->overall_status === 'normal')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Normal
                                    </span>
                                @elseif($record->overall_status === 'needs_attention')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span>
                                        Needs Attention
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/30 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-1.5"></span>
                                        Follow-up Recommended
                                    </span>
                                @endif
                            </td>

                            <td class="p-3.5 text-right">
                                <button type="button" wire:click="viewUnderlyingEvidence({{ $record->student_id }})" class="px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 font-bold text-xs hover:bg-indigo-100 dark:hover:bg-indigo-900 transition border border-indigo-200 dark:border-indigo-800">
                                    🔍 View Underlying Data
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                No engagement records found matching current filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $engagementRecords->links() }}
        </div>

    </div>

    <!-- Underlying Data Evidence Drawer / Modal -->
    @if($showUnderlyingDrawer && $activeRecord)
        <div class="fixed inset-0 z-50 overflow-hidden bg-slate-950/70 backdrop-blur-sm flex justify-end">
            <div class="w-full max-w-2xl bg-white dark:bg-slate-900 h-full shadow-2xl overflow-y-auto border-l border-slate-200 dark:border-slate-800 p-6 md:p-8 space-y-6">
                
                <!-- Drawer Header -->
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                    <div>
                        <div class="text-xs text-indigo-500 font-bold uppercase tracking-wider">Granular Underlying Evidence</div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ $activeStudentName }}</h2>
                        <div class="text-xs text-slate-400">Student ID #{{ $activeRecord->student?->admission_number }}</div>
                    </div>
                    <button type="button" wire:click="closeDrawer" class="text-slate-400 hover:text-white p-2 rounded-lg bg-slate-100 dark:bg-slate-800">
                        ✕
                    </button>
                </div>

                <!-- Neutral Status Summary Banner -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-500 block">Overall Engagement Score</span>
                        <span class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $activeRecord->engagement_score }}/100</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block text-right">Neutral Indicator Category</span>
                        @if($activeRecord->overall_status === 'normal')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">🟢 Normal</span>
                        @elseif($activeRecord->overall_status === 'needs_attention')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">🟡 Needs Attention</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">🔵 Follow-up Recommended</span>
                        @endif
                    </div>
                </div>

                <!-- Granular Indicator Evidence Cards -->
                @php $evidence = $activeRecord->underlying_data_json ?? []; @endphp

                <!-- 1. Attendance Evidence -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        📅 Attendance Trend Evidence
                    </h3>
                    <div class="grid grid-cols-3 gap-2 text-xs text-slate-600 dark:text-slate-300 pt-1">
                        <div>Present Days: <strong class="text-emerald-500">{{ $evidence['attendance']['present_days'] ?? $activeRecord->attendance_days_present }}</strong></div>
                        <div>Absent Days: <strong class="text-rose-500">{{ $evidence['attendance']['absent_days'] ?? $activeRecord->attendance_days_absent }}</strong></div>
                        <div>Unexcused: <strong>{{ $evidence['attendance']['unexcused_absences'] ?? 0 }}</strong></div>
                    </div>
                </div>

                <!-- 2. Assignment Activity Evidence -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        📚 Assignment Activity Evidence
                    </h3>
                    <div class="text-xs text-slate-600 dark:text-slate-300">
                        Completion Rate: <strong class="text-indigo-400">{{ $activeRecord->assignment_completion_rate }}%</strong> ({{ $activeRecord->assignment_submissions_count }} submitted, {{ $activeRecord->assignment_pending_count }} pending)
                    </div>
                </div>

                <!-- 3. Communication Response Evidence -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        💬 Communication Response Evidence
                    </h3>
                    <div class="text-xs text-slate-600 dark:text-slate-300">
                        Parent Responsiveness Rate: <strong class="text-emerald-400">{{ $activeRecord->communication_response_rate }}%</strong> across {{ $activeRecord->communication_interactions_count }} recorded CRM log entries.
                    </div>
                </div>

                <!-- 4. Academic Trends Evidence -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                        🎓 Academic Trend Breakdown
                    </h3>
                    <div class="text-xs text-slate-600 dark:text-slate-300">
                        Current GPA Score: <strong class="text-purple-400">{{ $activeRecord->academic_gpa_score }}%</strong> | Trend: <strong class="capitalize">{{ $activeRecord->academic_trend }}</strong>
                    </div>
                </div>

                <!-- Staff Action Bar (Strictly Non-Autonomous) -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Human Oversight Required: High-impact actions are never automated.</span>
                    <button type="button" wire:click="closeDrawer" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold">
                        Close Evidence Drawer
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
