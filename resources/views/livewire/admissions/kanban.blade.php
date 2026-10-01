<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Admission Pipeline Visual Kanban</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage 11 stage transitions: New Lead &rarr; Contacted &rarr; Counselling &rarr; Campus Visit &rarr; App Started &rarr; Docs Pending &rarr; App Submitted &rarr; Under Review &rarr; Approved &rarr; Confirmed &rarr; Lost.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('admissions.dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/40 px-4 py-2.5 text-sm font-semibold text-indigo-700 dark:text-indigo-300 shadow-sm hover:bg-indigo-100 transition">
                &larr; Executive Dashboard
            </a>
            <a href="{{ route('admissions.index') }}" class="inline-flex items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-900/40 px-4 py-2.5 text-sm font-semibold text-purple-700 dark:text-purple-300 shadow-sm hover:bg-purple-100 transition">
                Lead Directory
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by student, parent, or lead code..." class="w-full pl-4 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <select wire:model.live="counselor_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Counselors</option>
                    @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="source_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Lead Channels</option>
                    @foreach($sources as $src)
                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- 11 Horizontal Kanban Columns Board -->
    <div class="flex space-x-4 overflow-x-auto pb-6 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-700">
        @foreach($stages as $stageKey => $stageMeta)
            @php
                $stageLeads = $groupedLeads->get($stageKey, collect());
            @endphp
            <div class="w-72 flex-shrink-0 bg-slate-100 dark:bg-slate-900/60 rounded-3xl p-3 border border-slate-200/80 dark:border-slate-800 flex flex-col max-h-[calc(100vh-16rem)]">
                <!-- Column Header -->
                <div class="flex items-center justify-between px-3 py-2 border-b border-slate-200 dark:border-slate-800 mb-3">
                    <div class="flex items-center space-x-2 truncate">
                        <span class="w-2.5 h-2.5 rounded-full {{ $stageMeta['color'] }}"></span>
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $stageMeta['label'] }}</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                        {{ $stageLeads->count() }}
                    </span>
                </div>

                <!-- Cards Container -->
                <div class="flex-1 overflow-y-auto space-y-3 px-1">
                    @forelse($stageLeads as $lead)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-3 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[10px] font-mono font-semibold text-indigo-600 dark:text-indigo-400 block">{{ $lead->lead_code }}</span>
                                    <a href="{{ route('admissions.leads.show', $lead) }}" class="font-bold text-slate-900 dark:text-white text-sm hover:text-indigo-600 block leading-snug">
                                        {{ $lead->student_name }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Parent: {{ $lead->parent_name }}</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    Score: {{ $lead->score }}
                                </span>
                            </div>

                            <div class="text-[11px] text-slate-500 dark:text-slate-400 space-y-1 border-t border-slate-100 dark:border-slate-700/60 pt-2">
                                <div>Grade: <strong>{{ $lead->gradeApplyingFor->name ?? 'Grade 5' }}</strong></div>
                                <div>Channel: {{ $lead->source->name ?? 'Direct' }}</div>
                                <div>Counselor: <strong class="text-slate-700 dark:text-slate-300">{{ $lead->counselor->name ?? 'Unassigned' }}</strong></div>
                            </div>

                            <!-- Stage Action Controls -->
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs">
                                <a href="{{ route('admissions.leads.show', $lead) }}" class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                    View 360° &rarr;
                                </a>

                                <!-- Stage Quick Selector Dropdown -->
                                <select
                                    wire:change="updateLeadStage({{ $lead->id }}, $event.target.value)"
                                    class="text-[10px] py-1 px-2 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-1 focus:ring-indigo-500"
                                >
                                    @foreach($stages as $sk => $sm)
                                        <option value="{{ $sk }}" {{ $lead->stage === $sk ? 'selected' : '' }}>
                                            Move: {{ $sm['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400 italic bg-white/40 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                            No leads in {{ $stageMeta['label'] }}
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
