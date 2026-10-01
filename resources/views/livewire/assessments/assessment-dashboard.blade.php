<div class="p-6 bg-slate-950 text-slate-100 min-h-screen relative overflow-hidden">
    <!-- Ambient Background Glow Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Header -->
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 pb-6 border-b border-sky-900/30">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-gradient-to-tr from-sky-500 to-cyan-400 rounded-2xl shadow-lg shadow-sky-500/25 text-slate-950">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-cyan-200 to-indigo-300 tracking-tight">
                        AI Weekly Assessment Intelligence
                    </h1>
                    <p class="text-xs text-sky-300/70 mt-1 font-medium">Class-wise & subject-wise test command center, topic mastery analytics & automated grading.</p>
                </div>
            </div>
        </div>

        <!-- Role Tabs with Sky Blue Glow -->
        <div class="flex items-center gap-1.5 bg-slate-900/90 p-1.5 rounded-2xl border border-sky-500/20 backdrop-blur-xl shadow-xl shadow-sky-950/40">
            <button wire:click="$set('activeRoleTab', 'teacher')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $activeRoleTab === 'teacher' ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 shadow-lg shadow-sky-500/30' : 'text-slate-400 hover:text-sky-300 hover:bg-sky-500/10' }}">
                👩‍🏫 Teacher View
            </button>
            <button wire:click="$set('activeRoleTab', 'student')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $activeRoleTab === 'student' ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 shadow-lg shadow-sky-500/30' : 'text-slate-400 hover:text-sky-300 hover:bg-sky-500/10' }}">
                🎓 Student Portal
            </button>
            <button wire:click="$set('activeRoleTab', 'parent')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $activeRoleTab === 'parent' ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 shadow-lg shadow-sky-500/30' : 'text-slate-400 hover:text-sky-300 hover:bg-sky-500/10' }}">
                👪 Parent Insights
            </button>
            <button wire:click="$set('activeRoleTab', 'admin')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $activeRoleTab === 'admin' ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 shadow-lg shadow-sky-500/30' : 'text-slate-400 hover:text-sky-300 hover:bg-sky-500/10' }}">
                🏛️ School Overview
            </button>
        </div>
    </div>

    <!-- 1. TEACHER DASHBOARD VIEW -->
    @if($activeRoleTab === 'teacher')
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 mb-8 relative z-10">
            <div class="bg-gradient-to-br from-slate-900/90 to-sky-950/30 border border-sky-500/20 hover:border-sky-400/50 rounded-2xl p-5 shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-sky-300/80 uppercase tracking-wider">Total Tests Created</span>
                    <span class="p-2 rounded-xl bg-sky-500/10 text-sky-400 font-bold">📋</span>
                </div>
                <div class="text-3xl font-black text-white font-mono mt-3">{{ $teacherTotalCreated }}</div>
            </div>

            <div class="bg-gradient-to-br from-slate-900/90 to-amber-950/20 border border-amber-500/20 hover:border-amber-400/50 rounded-2xl p-5 shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-300/80 uppercase tracking-wider">Pending Review</span>
                    <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 font-bold">⌛</span>
                </div>
                <div class="text-3xl font-black text-amber-400 font-mono mt-3">{{ $teacherPendingReview }}</div>
            </div>

            <div class="bg-gradient-to-br from-slate-900/90 to-emerald-950/20 border border-emerald-500/20 hover:border-emerald-400/50 rounded-2xl p-5 shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-300/80 uppercase tracking-wider">Active Tests</span>
                    <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold">⚡</span>
                </div>
                <div class="text-3xl font-black text-emerald-400 font-mono mt-3">{{ $teacherActive }}</div>
            </div>

            <div class="bg-gradient-to-br from-slate-900/90 to-cyan-950/30 border border-cyan-500/20 hover:border-cyan-400/50 rounded-2xl p-5 shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-cyan-300/80 uppercase tracking-wider">Completed Tests</span>
                    <span class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400 font-bold">🏆</span>
                </div>
                <div class="text-3xl font-black text-cyan-300 font-mono mt-3">{{ $teacherCompleted }}</div>
            </div>
        </div>

        <!-- Topic Performance Tracking Table -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-6 shadow-2xl mb-8 relative z-10">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-400 animate-pulse"></span>
                    <h2 class="text-base font-bold text-white tracking-wide">Topic Mastery & Student Weak Spot Intelligence</h2>
                </div>
                <span class="text-xs text-sky-300/70 font-semibold bg-sky-500/10 px-3 py-1 rounded-full border border-sky-500/20">Live Performance Sync</span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/90 text-sky-300 uppercase text-[10px] tracking-wider font-bold border-b border-sky-900/40">
                        <tr>
                            <th class="p-4">Student Name</th>
                            <th class="p-4">Subject & Topic</th>
                            <th class="p-4">Questions Attempted</th>
                            <th class="p-4">Accuracy %</th>
                            <th class="p-4">Mastery Level</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 bg-slate-900/40">
                        @forelse($topicPerformances as $tp)
                            <tr class="hover:bg-sky-500/5 transition duration-150">
                                <td class="p-4 font-bold text-white flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 border border-sky-500/30 text-sky-300 flex items-center justify-center font-mono font-bold text-xs">
                                        {{ strtoupper(substr($tp->student->first_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <span>{{ $tp->student->first_name ?? 'Student' }} {{ $tp->student->last_name ?? '' }}</span>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-200 font-semibold">{{ $tp->subject->name ?? 'Subject' }}</div>
                                    <div class="text-[10px] text-sky-400/80">Topic: {{ $tp->topic->name ?? 'General' }}</div>
                                </td>
                                <td class="p-4 font-mono text-slate-300 font-semibold">{{ $tp->correct_questions_count }} / {{ $tp->total_questions_attempted }}</td>
                                <td class="p-4 font-mono font-black text-sky-400 text-sm">{{ $tp->accuracy_percentage }}%</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wide
                                        {{ $tp->mastery_level === 'mastered' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($tp->mastery_level === 'needs_improvement' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30') }}">
                                        {{ str_replace('_', ' ', $tp->mastery_level) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No topic performance records recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- 2. STUDENT DASHBOARD VIEW -->
    @elseif($activeRoleTab === 'student')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 relative z-10">
            <!-- Active & Assigned Tests -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center gap-2 mb-5">
                    <span class="w-3 h-3 rounded-full bg-cyan-400 animate-pulse"></span>
                    <h2 class="text-base font-bold text-white">Assigned Weekly Tests</h2>
                </div>
                <div class="space-y-3.5">
                    @forelse($studentAssignments as $assign)
                        <div class="p-4 bg-gradient-to-r from-slate-950 to-sky-950/30 rounded-xl border border-sky-500/20 hover:border-sky-400/50 flex items-center justify-between transition">
                            <div>
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-md uppercase font-mono">
                                    {{ $assign->assessment->subject->name ?? 'Subject' }}
                                </span>
                                <h3 class="text-sm font-extrabold text-white mt-1.5">{{ $assign->assessment->title }}</h3>
                                <div class="text-[11px] text-slate-400 mt-0.5">⏱ {{ $assign->assessment->duration_minutes }} mins • Pass Marks: <strong class="text-sky-300 font-mono">{{ $assign->assessment->passing_marks }}</strong></div>
                            </div>
                            <button wire:click="startStudentTest({{ $assign->assessment->id }})" class="px-4 py-2.5 bg-gradient-to-r from-sky-400 to-cyan-400 hover:from-sky-300 hover:to-cyan-300 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-sky-500/25 transition">
                                🚀 Start Test
                            </button>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-xs bg-slate-950/40 rounded-xl border border-slate-800">
                            No active weekly tests assigned to your class right now.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Student History & Completed Attempts -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-6 shadow-2xl">
                <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                    <span>🏆 Completed Tests & Scores</span>
                </h2>
                <div class="space-y-3.5">
                    @forelse($studentAttempts as $att)
                        <div class="p-4 bg-slate-950/90 rounded-xl border border-slate-800 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-slate-200">{{ $att->assessment->title }}</h3>
                                <div class="text-[10px] text-sky-400/70 mt-1">{{ $att->submitted_at?->format('d M Y, h:i A') }}</div>
                            </div>

                            @if($att->result)
                                <div class="text-right">
                                    <div class="text-sm font-black font-mono text-sky-300">{{ $att->result->score }} Marks</div>
                                    <div class="text-[10px] font-mono font-bold {{ $att->result->is_passed ? 'text-emerald-400' : 'text-rose-400' }}">
                                        Grade {{ $att->result->grade }} ({{ $att->result->percentage }}%)
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-xs bg-slate-950/40 rounded-xl border border-slate-800">
                            No completed test attempts yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <!-- 3. PARENT DASHBOARD VIEW -->
    @elseif($activeRoleTab === 'parent')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 relative z-10">
            <!-- Child Test Results -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-6 shadow-2xl">
                <h2 class="text-base font-bold text-white mb-5">Child's Weekly Test Progress</h2>
                <div class="space-y-3 text-xs">
                    @forelse($parentCompletedResults as $res)
                        <div class="p-4 bg-slate-950/90 rounded-xl border border-sky-900/30 flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-slate-100">{{ $res->student->first_name ?? 'Student' }} - {{ $res->assessment->title }}</div>
                                <div class="text-[10px] text-sky-400/70 mt-0.5">{{ $res->published_at?->format('d M Y') }}</div>
                            </div>
                            <div class="text-right font-mono">
                                <div class="font-black text-sky-300 text-sm">{{ $res->score }} Marks</div>
                                <div class="text-[10px] text-cyan-300 font-bold">{{ $res->percentage }}% (Grade {{ $res->grade }})</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 bg-slate-950/40 rounded-xl border border-slate-800">
                            No child test results published yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Weak Topic Indicators -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-rose-500/20 rounded-2xl p-6 shadow-2xl">
                <h2 class="text-base font-bold text-white mb-5">Topics Requiring Revision & Attention</h2>
                <div class="space-y-3 text-xs">
                    @forelse($weakTopics as $wt)
                        <div class="p-4 bg-rose-950/30 border border-rose-500/30 rounded-xl flex items-center justify-between">
                            <div>
                                <div class="font-bold text-rose-200">{{ $wt->student->first_name ?? 'Child' }}</div>
                                <div class="text-[11px] text-slate-300 mt-0.5">Subject: {{ $wt->subject->name ?? '' }} • Topic: <strong class="text-rose-300">{{ $wt->topic->name ?? '' }}</strong></div>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-md">
                                    Accuracy: {{ $wt->accuracy_percentage }}%
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 bg-slate-950/40 rounded-xl border border-slate-800">
                            All topic accuracy indicators are healthy! No weak spots detected.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <!-- 4. ADMIN DASHBOARD VIEW -->
    @elseif($activeRoleTab === 'admin')
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 mb-8 relative z-10">
            <div class="bg-gradient-to-br from-slate-900/90 to-sky-950/30 border border-sky-500/20 rounded-2xl p-5 shadow-xl">
                <div class="text-sky-300/80 text-xs font-bold uppercase tracking-wider mb-1">School-wide Weekly Tests</div>
                <div class="text-3xl font-black text-white font-mono mt-2">{{ $teacherTotalCreated }}</div>
            </div>

            <div class="bg-gradient-to-br from-slate-900/90 to-amber-950/20 border border-amber-500/20 rounded-2xl p-5 shadow-xl">
                <div class="text-amber-300/80 text-xs font-bold uppercase tracking-wider mb-1">Pending Teacher Reviews</div>
                <div class="text-3xl font-black text-amber-400 font-mono mt-2">{{ $teacherPendingReview }}</div>
            </div>

            <div class="bg-gradient-to-br from-slate-900/90 to-emerald-950/20 border border-emerald-500/20 rounded-2xl p-5 shadow-xl">
                <div class="text-emerald-300/80 text-xs font-bold uppercase tracking-wider mb-1">Active Class Assessments</div>
                <div class="text-3xl font-black text-emerald-400 font-mono mt-2">{{ $teacherActive }}</div>
            </div>

            <div class="bg-gradient-to-br from-slate-900/90 to-cyan-950/30 border border-cyan-500/20 rounded-2xl p-5 shadow-xl">
                <div class="text-cyan-300/80 text-xs font-bold uppercase tracking-wider mb-1">Topics Assessed</div>
                <div class="text-3xl font-black text-cyan-300 font-mono mt-2">{{ $topicPerformances->count() }}</div>
            </div>
        </div>
    @endif
</div>
