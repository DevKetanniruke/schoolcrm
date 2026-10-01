<div class="space-y-6">
    <!-- Header Banner -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Admissions CRM Executive Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Real-time enrollment funnel performance, conversion rates, counselor leaderboards, and marketing source metrics.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('admissions.pipeline') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                Visual Pipeline Kanban
            </a>
            <a href="{{ route('admissions.index') }}" class="inline-flex items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-900/40 px-4 py-2.5 text-sm font-semibold text-purple-700 dark:text-purple-300 shadow-sm hover:bg-purple-100 transition">
                Lead Directory
            </a>
        </div>
    </div>

    <!-- Executive KPI Grid (5 Key Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- New Leads -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>New Leads</span>
                <span class="p-1.5 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $newLeads }}</div>
            <div class="text-xs text-slate-500">Out of {{ $totalLeads }} total leads</div>
        </div>

        <!-- Follow-ups Today -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Follow-ups Today</span>
                <span class="p-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $todayFollowUpsCount }}</div>
            <div class="text-xs text-slate-500">Scheduled for today</div>
        </div>

        <!-- Overdue Follow-ups -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Overdue Activities</span>
                <span class="p-1.5 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $overdueFollowUpsCount }}</div>
            <div class="text-xs text-slate-500">Requires counselor action</div>
        </div>

        <!-- Applications in Progress -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Applications Active</span>
                <span class="p-1.5 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $applicationsInProgress }}</div>
            <div class="text-xs text-slate-500">Submitted & under review</div>
        </div>

        <!-- Conversion Rate % -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Conversion Rate</span>
                <span class="p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $conversionRate }}%</div>
            <div class="text-xs text-slate-500">{{ $confirmedAdmissions }} Confirmed admissions</div>
        </div>
    </div>

    <!-- Pipeline Stage Distribution Overview -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Admission Pipeline Funnel Distribution</h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-11 gap-2 text-center text-xs">
            @foreach([
                'new_lead' => 'New Lead',
                'contacted' => 'Contacted',
                'counselling' => 'Counselling',
                'campus_visit' => 'Campus Visit',
                'application_started' => 'App Started',
                'documents_pending' => 'Docs Pending',
                'application_submitted' => 'App Submitted',
                'under_review' => 'Under Review',
                'approved' => 'Approved',
                'admission_confirmed' => 'Confirmed',
                'lost' => 'Lost',
            ] as $stageKey => $stageLabel)
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block truncate">{{ $stageLabel }}</span>
                    <span class="text-lg font-bold {{ $stageKey === 'admission_confirmed' ? 'text-emerald-600' : ($stageKey === 'lost' ? 'text-slate-400' : 'text-slate-800 dark:text-slate-200') }}">
                        {{ $stageCounts[$stageKey] ?? 0 }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Leaderboards & Attribution Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Counselor Performance Leaderboard -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Counselor Performance Leaderboard</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                    <thead>
                        <tr>
                            <th class="py-2 text-left text-slate-400 font-semibold uppercase">Counselor</th>
                            <th class="py-2 text-center text-slate-400 font-semibold uppercase">Assigned</th>
                            <th class="py-2 text-center text-slate-400 font-semibold uppercase">Admissions</th>
                            <th class="py-2 text-right text-slate-400 font-semibold uppercase">Conversion %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($counselors as $counselor)
                            @php
                                $cConvRate = $counselor->assigned_leads_count > 0 ? round(($counselor->converted_leads_count / $counselor->assigned_leads_count) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $counselor->name }}</td>
                                <td class="py-3 text-center text-slate-600 dark:text-slate-300 font-bold">{{ $counselor->assigned_leads_count }}</td>
                                <td class="py-3 text-center text-emerald-600 font-bold">{{ $counselor->converted_leads_count }}</td>
                                <td class="py-3 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $cConvRate }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 italic">No counselor records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lead Source & Campaign Attribution -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Marketing Source & Campaign Attribution</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                    <thead>
                        <tr>
                            <th class="py-2 text-left text-slate-400 font-semibold uppercase">Lead Source Channel</th>
                            <th class="py-2 text-center text-slate-400 font-semibold uppercase">Leads Generated</th>
                            <th class="py-2 text-center text-slate-400 font-semibold uppercase">Admissions</th>
                            <th class="py-2 text-right text-slate-400 font-semibold uppercase">Conversion %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($sourceMetrics as $src)
                            @php
                                $sConvRate = $src->leads_count > 0 ? round(($src->converted_count / $src->leads_count) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $src->name }}</td>
                                <td class="py-3 text-center text-slate-600 dark:text-slate-300 font-bold">{{ $src->leads_count }}</td>
                                <td class="py-3 text-center text-emerald-600 font-bold">{{ $src->converted_count }}</td>
                                <td class="py-3 text-right font-mono font-bold text-purple-600 dark:text-purple-400">{{ $sConvRate }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 italic">No lead sources recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
