<div class="p-6 bg-slate-950 text-slate-100 min-h-screen relative overflow-hidden">
    <!-- Ambient Background Glow Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header -->
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 pb-6 border-b border-sky-900/30">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-gradient-to-tr from-sky-500 to-cyan-400 rounded-2xl shadow-lg shadow-sky-500/25 text-slate-950">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-cyan-200 to-indigo-300 tracking-tight">
                    Weekly Test & Assessment Manager
                </h1>
                <p class="text-xs text-sky-300/70 mt-1 font-medium">Design, review, approve, and publish weekly class-wise and subject-wise tests.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 bg-slate-900/90 p-1.5 rounded-2xl border border-sky-500/20 backdrop-blur-xl shadow-xl shadow-sky-950/40">
            <button wire:click="$set('activeTab', 'list')" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeTab === 'list' ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 shadow-lg shadow-sky-500/30' : 'text-slate-400 hover:text-sky-300' }}">
                📋 All Assessments
            </button>
            <button wire:click="openCreateTab" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeTab === 'create' ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 shadow-lg shadow-sky-500/30' : 'text-slate-400 hover:text-sky-300' }}">
                ➕ Create Weekly Test
            </button>
        </div>
    </div>

    <!-- TAB 1: Assessment List -->
    @if($activeTab === 'list')
        <!-- Filters Bar -->
        <div class="relative z-10 bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-4 mb-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs shadow-xl">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search test title..." class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200 placeholder-slate-500">

            <select wire:model.live="filterClassId" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
                <option value="">All Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterSubjectId" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
                <option value="">All Subjects</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="review_required">Review Required</option>
                <option value="published">Published</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <!-- Table -->
        <div class="relative z-10 bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl overflow-hidden shadow-2xl mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/90 text-sky-300 uppercase text-[10px] tracking-wider font-extrabold border-b border-sky-900/40">
                        <tr>
                            <th class="p-4">Test Details</th>
                            <th class="p-4">Class / Subject</th>
                            <th class="p-4">Duration & Marks</th>
                            <th class="p-4">Questions</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 bg-slate-900/40">
                        @forelse($assessments as $a)
                            <tr class="hover:bg-sky-500/5 transition">
                                <td class="p-4 font-bold text-white">
                                    <div class="font-extrabold text-sm text-slate-100">{{ $a->title }}</div>
                                    <div class="text-[10px] text-sky-400/80 font-mono mt-0.5">Code: {{ $a->code }} • Type: {{ str_replace('_', ' ', strtoupper($a->type)) }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-200">{{ $a->schoolClass->name ?? 'Class' }} {{ $a->division ? '('.$a->division->name.')' : '' }}</div>
                                    <div class="text-[11px] text-sky-400 font-semibold">{{ $a->subject->name ?? 'Subject' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-200 font-medium">⏱ {{ $a->duration_minutes }} mins</div>
                                    <div class="text-[11px] text-slate-400 font-mono">Pass: {{ $a->passing_marks }} / Total: {{ $a->total_marks }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 bg-sky-500/20 text-sky-300 border border-sky-500/30 font-extrabold rounded-lg">
                                        {{ $a->questions->count() }} Questions
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg uppercase tracking-wider
                                        {{ in_array($a->status, ['active', 'published']) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($a->status === 'review_required' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 animate-pulse' : 'bg-slate-800 text-slate-300 border border-slate-700') }}">
                                        {{ str_replace('_', ' ', $a->status) }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(in_array($a->status, ['draft', 'review_required']))
                                            <button wire:click="publishAssessment({{ $a->id }})" class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-[11px] rounded-lg shadow-md transition">
                                                Publish & Assign
                                            </button>
                                        @endif
                                        <button wire:click="archiveAssessment({{ $a->id }})" class="text-rose-400 hover:text-rose-300 font-bold transition text-xs">Archive</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-slate-500 font-medium">
                                    No weekly tests created yet. Click "+ Create Weekly Test" to start.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="relative z-10">
            {{ $assessments->links() }}
        </div>

    <!-- TAB 2: Create / Edit Test Form -->
    @elseif($activeTab === 'create')
        <div class="relative z-10 bg-slate-900/90 backdrop-blur-xl border border-sky-500/30 rounded-2xl p-6 shadow-2xl max-w-4xl">
            <h2 class="text-lg font-bold text-white mb-5 pb-3 border-b border-sky-900/30">Create New Weekly Assessment Test</h2>

            <form wire:submit.prevent="saveAssessment" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Test Title *</label>
                        <input type="text" wire:model="title" placeholder="e.g. Mathematics Week 3 Test - Algebra Basics" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Assessment Type *</label>
                        <select wire:model="type" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                            <option value="weekly_test">Weekly Test</option>
                            <option value="practice_test">Practice Test</option>
                            <option value="revision_test">Revision Test</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Class *</label>
                        <select wire:model.live="class_id" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                            <option value="">Select Class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Division (Optional)</label>
                        <select wire:model="division_id" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                            <option value="">All Divisions</option>
                            @foreach($divisions as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Subject *</label>
                        <select wire:model.live="subject_id" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Duration (Minutes) *</label>
                        <input type="number" wire:model="duration_minutes" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400 font-mono">
                    </div>
                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Passing Marks *</label>
                        <input type="number" step="0.5" wire:model="passing_marks" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400 font-mono">
                    </div>
                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Overall Difficulty</label>
                        <select wire:model="difficulty" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                </div>

                <!-- Select Questions from Question Bank -->
                <div class="pt-4 border-t border-slate-800">
                    <label class="block text-sky-300 font-extrabold mb-2 text-sm">Select Questions from Question Bank (Subject: {{ $subjects->firstWhere('id', $subject_id)?->name ?? 'None' }})</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 bg-slate-950 p-3.5 rounded-xl border border-sky-900/40">
                        @forelse($bankQuestions as $bq)
                            <label class="flex items-start gap-3 p-3 bg-slate-900/90 rounded-xl border border-sky-900/30 hover:border-sky-400/40 cursor-pointer transition">
                                <input type="checkbox" wire:model="selected_question_ids" value="{{ $bq->id }}" class="mt-1 rounded bg-slate-950 border-sky-700 text-sky-500 focus:ring-sky-400">
                                <div class="text-xs">
                                    <div class="font-bold text-slate-100">{{ $bq->question_text }}</div>
                                    <div class="text-[10px] text-sky-400/80 font-mono mt-0.5">
                                        Type: {{ strtoupper($bq->question_type) }} • Marks: {{ $bq->marks }} • Difficulty: {{ $bq->difficulty }}
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="text-slate-500 text-center py-6 text-xs font-medium">
                                No approved questions available in Question Bank for this subject.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" wire:click="$set('activeTab', 'list')" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-cyan-400 hover:from-sky-300 hover:to-cyan-300 text-slate-950 font-black rounded-xl shadow-lg shadow-sky-500/25 transition">Save Assessment Draft</button>
                </div>
            </form>
        </div>
    @endif
</div>
