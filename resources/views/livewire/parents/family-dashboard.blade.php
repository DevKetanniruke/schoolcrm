<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Family Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Visual family hierarchy (`Parent &rarr; Children`), child profile switcher, and consolidated academic overview.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @if($parent)
                <a href="{{ route('parents.show', $parent) }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/40 px-4 py-2.5 text-sm font-semibold text-indigo-700 dark:text-indigo-300 shadow-sm hover:bg-indigo-100 transition">
                    View Full Parent 360° Profile
                </a>
            @endif
        </div>
    </div>

    @if($parent)
        <!-- Visual Hierarchy Card: Parent -> Child 1, Child 2, Child 3 -->
        <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <!-- Parent Root node -->
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center font-bold text-xl shadow-lg ring-4 ring-indigo-400/20">
                        {{ strtoupper(substr($parent->first_name, 0, 1) . substr($parent->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase tracking-wider text-indigo-300 font-semibold">Head of Household / Parent</span>
                            <span class="bg-indigo-500/30 text-indigo-200 text-[10px] font-mono px-2 py-0.5 rounded border border-indigo-400/30">{{ $parent->parent_code }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ $parent->full_name }}</h2>
                        <p class="text-xs text-slate-300">{{ $parent::class ? $parent->email : '' }} • Phone: {{ $parent->phone }}</p>
                    </div>
                </div>

                <!-- Tree Branch Connector & Child Switcher Cards -->
                <div class="pt-4 border-t border-slate-800">
                    <div class="flex items-center gap-2 mb-3 text-xs font-semibold uppercase tracking-wider text-indigo-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Linked Children Hierarchy (`Parent &rarr; Linked Students`) — Click to Switch Profile
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($children as $child)
                            <button
                                wire:click="selectChild({{ $child->id }})"
                                class="p-4 rounded-2xl text-left transition-all duration-200 border flex items-center justify-between group
                                {{ $selectedChild && $selectedChild->id === $child->id ? 'bg-indigo-600/90 border-indigo-400 shadow-lg shadow-indigo-600/30 ring-2 ring-indigo-400/50' : 'bg-slate-800/80 border-slate-700/80 hover:bg-slate-800 hover:border-slate-600' }}"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-slate-700/80 group-hover:bg-slate-600 flex items-center justify-center font-bold text-sm text-indigo-200">
                                        {{ strtoupper(substr($child->first_name, 0, 1) . substr($child->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-sm">{{ $child->full_name }}</h3>
                                        <p class="text-xs text-slate-300">Grade / Class: {{ $child->schoolClass->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-mono font-semibold block text-indigo-300">{{ $child->admission_number }}</span>
                                    @if($selectedChild && $selectedChild->id === $child->id)
                                        <span class="text-[10px] bg-emerald-500 text-white font-bold px-2 py-0.5 rounded-full inline-block mt-1">Active View</span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Child Profile Dashboard Section -->
        @if($selectedChild)
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
                <!-- Selected Child Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200/60 dark:border-slate-700/60 pb-6">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-2xl flex items-center justify-center">
                            {{ strtoupper(substr($selectedChild->first_name, 0, 1) . substr($selectedChild->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $selectedChild->full_name }}</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 capitalize">
                                    {{ $selectedChild->status }}
                                </span>
                            </div>
                            <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-3">
                                <span>Admission #: <strong class="font-mono text-indigo-600 dark:text-indigo-400">{{ $selectedChild->admission_number }}</strong></span>
                                <span>•</span>
                                <span>Class: <strong class="text-slate-800 dark:text-slate-200">{{ $selectedChild->schoolClass->name ?? 'N/A' }}</strong></span>
                                <span>•</span>
                                <span>DOB: {{ $selectedChild->date_of_birth ? $selectedChild->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('students.show', $selectedChild) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-500 transition">
                        View Student 360° Profile &rarr;
                    </a>
                </div>

                <!-- Metrics Grid for Selected Child -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <span class="text-xs text-slate-400 block font-medium">Attendance Rate</span>
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">96.4%</div>
                        <span class="text-[11px] text-slate-500">142 Days Present, 3 Absent</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <span class="text-xs text-slate-400 block font-medium">Academic Average</span>
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">A (93.5%)</div>
                        <span class="text-[11px] text-slate-500">Class Rank: 4th / 32 Students</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <span class="text-xs text-slate-400 block font-medium">Pending Tasks</span>
                        <div class="text-2xl font-bold text-amber-500">2 Tasks</div>
                        <span class="text-[11px] text-slate-500">Medical immunization upload</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <span class="text-xs text-slate-400 block font-medium">Upcoming PTM</span>
                        <div class="text-lg font-bold text-purple-600 dark:text-purple-400 truncate">Oct 05, 10:00 AM</div>
                        <span class="text-[11px] text-slate-500">Conference Room A</span>
                    </div>
                </div>

                <!-- Recent Activity & Notes for Selected Child -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Recent Teacher Annotations</h4>
                        <div class="space-y-2 text-xs">
                            @forelse($selectedChild->notes->take(3) as $cNote)
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl space-y-1 border border-slate-200/60 dark:border-slate-700/60">
                                    <div class="flex justify-between font-semibold text-slate-800 dark:text-slate-200">
                                        <span class="capitalize">{{ $cNote->category }}</span>
                                        <span class="text-slate-400">{{ $cNote->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-300">{{ $cNote->note }}</p>
                                </div>
                            @empty
                                <div class="text-slate-400 italic">No notes recorded for this child.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Attached Documents & Forms</h4>
                        <div class="space-y-2 text-xs">
                            @forelse($selectedChild->documents as $cDoc)
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl flex justify-between items-center border border-slate-200/60 dark:border-slate-700/60">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $cDoc->title }}</span>
                                    <span class="text-slate-400 uppercase text-[10px] font-mono">{{ $cDoc->document_type }}</span>
                                </div>
                            @empty
                                <div class="text-slate-400 italic">No documents attached for this child.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="p-12 text-center text-slate-400 bg-white dark:bg-slate-800 rounded-3xl">
            No parent profile selected.
        </div>
    @endif
</div>
