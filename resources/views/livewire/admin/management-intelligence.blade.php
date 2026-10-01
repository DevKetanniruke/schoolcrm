<div class="space-y-8">
    <!-- Top Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-purple-950 to-indigo-950 p-6 md:p-8 text-white shadow-2xl border border-purple-900/40">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        🛡️ Controlled Query Layer
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Zero Raw SQL Execution Security
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Management Intelligence Dashboard</h1>
                <p class="text-sm text-slate-300 mt-1 max-w-3xl">
                    Ask natural-language questions to query authorized CRM metrics across admissions, attendance, complaints, tasks, and executive performance with full drill-down records.
                </p>
            </div>
        </div>
    </div>

    <!-- Natural Language Query Search Bar & Sample Question Chips -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-4">
        <form wire:submit.prevent="runQuery" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-purple-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model="prompt" placeholder="Ask a question (e.g. How many admission leads are pending follow-up?)..." class="w-full pl-11 pr-4 py-3.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm md:text-base focus:ring-2 focus:ring-purple-500 font-medium" required />
            </div>

            <button type="submit" wire:loading.attr="disabled" class="py-3.5 px-6 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-purple-600/30 transition flex items-center justify-center space-x-2 shrink-0">
                <svg wire:loading.remove class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove>Query Intelligence</span>
                <span wire:loading>Analyzing Query...</span>
            </button>
        </form>

        <!-- Sample Question Chips -->
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Sample Executive Questions:</div>
            <div class="flex flex-wrap gap-2">
                @foreach($sampleQuestions as $q)
                    <button type="button" wire:click="runQuery('{{ addslashes($q) }}')" class="text-xs py-1.5 px-3 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900 transition flex items-center space-x-1.5">
                        <span class="text-purple-500">💬</span>
                        <span>{{ $q }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Active Query Result Sections -->
    @if($activeResult)
        <div class="space-y-6">
            
            <!-- Section 1: Executive Answer -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-purple-500 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Executive Answer
                    </h2>
                    <span class="text-xs text-slate-400">Validated Query Intent Result</span>
                </div>
                <div class="text-base md:text-lg font-medium text-slate-900 dark:text-slate-100 leading-relaxed bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                    {!! Str::markdown($activeResult['answer'] ?? '') !!}
                </div>
            </div>

            <!-- Section 2: Supporting Metrics Grid -->
            @if(!empty($activeResult['supporting_metrics']))
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Supporting Metrics & KPIs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($activeResult['supporting_metrics'] as $metric)
                            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xl border border-slate-200 dark:border-slate-800">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $metric['label'] }}</div>
                                <div class="text-3xl font-black text-{{ $metric['color'] ?? 'indigo' }}-600 dark:text-{{ $metric['color'] ?? 'indigo' }}-400 mt-1">
                                    {{ $metric['value'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Section 3: Relevant Records & Underlying Record Drill-Down -->
            @if(!empty($activeResult['relevant_records']))
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center">
                            <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Relevant Records (Drill-Down Available)
                        </h3>
                        <span class="text-xs text-slate-400">{{ count($activeResult['relevant_records']) }} records returned</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($activeResult['relevant_records'] as $record)
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition flex flex-col md:flex-row md:items-center justify-between gap-3">
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $record['title'] }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $record['subtitle'] }}</div>
                                </div>

                                <div class="flex items-center space-x-3 shrink-0">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $record['status_color'] ?? 'indigo' }}-500/20 text-{{ $record['status_color'] ?? 'indigo' }}-600 dark:text-{{ $record['status_color'] ?? 'indigo' }}-400 border border-{{ $record['status_color'] ?? 'indigo' }}-500/30">
                                        {{ $record['status'] }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono">{{ $record['date'] }}</span>
                                    
                                    @if(!empty($record['drill_down_url']))
                                        <a href="{{ $record['drill_down_url'] }}" class="px-3 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-300 font-bold text-xs hover:bg-purple-100 transition border border-purple-200 dark:border-purple-800 flex items-center space-x-1">
                                            <span>Drill Down</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Section 4 & 5: Date Range & Filters Used Meta Bar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Date Range -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xl border border-slate-200 dark:border-slate-800">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Date Range Applied</div>
                    <div class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center">
                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $activeResult['date_range'] ?? 'All Time' }}
                    </div>
                </div>

                <!-- Filters Used -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xl border border-slate-200 dark:border-slate-800">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Filters Used (Controlled Query Layer)</div>
                    <div class="flex flex-wrap gap-1.5">
                        @if(!empty($activeResult['filters_used']))
                            @foreach($activeResult['filters_used'] as $fKey => $fVal)
                                <span class="px-2.5 py-1 rounded-md text-xs font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $fKey }}: <strong>{{ $fVal }}</strong>
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>

        </div>
    @endif
</div>
