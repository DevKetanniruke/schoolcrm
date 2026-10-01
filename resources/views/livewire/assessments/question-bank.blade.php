<div class="p-6 bg-slate-950 text-slate-100 min-h-screen relative overflow-hidden">
    <!-- Ambient Background Glow Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header -->
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 pb-6 border-b border-sky-900/30">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-gradient-to-tr from-sky-500 to-cyan-400 rounded-2xl shadow-lg shadow-sky-500/25 text-slate-950">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-cyan-200 to-indigo-300 tracking-tight">
                    Question Bank & Review Studio
                </h1>
                <p class="text-xs text-sky-300/70 mt-1 font-medium">Create, manage, review, and filter reusable questions for weekly class & subject tests.</p>
            </div>
        </div>
        <button wire:click="openCreateModal" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-cyan-400 hover:from-sky-300 hover:to-cyan-300 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-sky-500/25 transition duration-200 flex items-center gap-2">
            <span>➕ Create Question</span>
        </button>
    </div>

    <!-- Filters Bar with Sky Blue accents -->
    <div class="relative z-10 bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-4 mb-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 text-xs shadow-xl">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search question text..." class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200 placeholder-slate-500">

        <select wire:model.live="selectedSubjectId" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
            <option value="">All Subjects</option>
            @foreach($subjects as $sub)
                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="selectedTopicId" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
            <option value="">All Topics</option>
            @foreach($topics as $top)
                <option value="{{ $top->id }}">{{ $top->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="selectedQuestionType" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
            <option value="">All Question Types</option>
            <option value="mcq">MCQ</option>
            <option value="true_false">True / False</option>
            <option value="single_answer">Single Answer</option>
            <option value="multiple_answer">Multiple Answer</option>
            <option value="short_answer">Short Answer</option>
        </select>

        <select wire:model.live="selectedDifficulty" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
            <option value="">All Difficulties</option>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>

        <select wire:model.live="selectedReviewStatus" class="bg-slate-950 border border-sky-900/50 focus:border-sky-400 rounded-xl py-2 px-3 text-slate-200">
            <option value="">All Review Statuses</option>
            <option value="pending">Pending Review</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <!-- Questions Grid -->
    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        @forelse($questions as $q)
            <div class="bg-gradient-to-br from-slate-900/90 to-sky-950/20 backdrop-blur-xl border border-sky-500/20 hover:border-sky-400/50 rounded-2xl p-5 shadow-xl transition-all duration-300 flex flex-col justify-between hover:-translate-y-0.5">
                <div>
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2.5 py-0.5 text-[10px] font-mono bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-md uppercase font-bold">
                                {{ strtoupper($q->question_type) }}
                            </span>
                            <span class="px-2.5 py-0.5 text-[10px] font-mono bg-slate-800 text-slate-300 rounded-md capitalize font-semibold border border-slate-700">
                                {{ $q->difficulty }}
                            </span>
                            <span class="px-2.5 py-0.5 text-[10px] font-mono bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 rounded-md font-extrabold">
                                {{ $q->marks }} Marks
                            </span>
                        </div>

                        <!-- Review status -->
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-md uppercase tracking-wider
                            {{ $q->review_status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($q->review_status === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40 animate-pulse') }}">
                            {{ $q->review_status }}
                        </span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-100 mb-2 leading-relaxed">{{ $q->question_text }}</h3>

                    <!-- Subject / Topic -->
                    <div class="text-[11px] text-sky-300/70 mb-3.5 flex flex-wrap items-center gap-2 font-medium">
                        <span>📚 {{ $q->subject->name ?? 'General' }}</span>
                        @if($q->topic)
                            <span>• 📌 Topic: {{ $q->topic->name }}</span>
                        @endif
                        @if($q->is_ai_generated)
                            <span class="bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md text-[9px] font-black">🤖 AI Generated</span>
                        @endif
                    </div>

                    <!-- Options Preview -->
                    @if(in_array($q->question_type, ['mcq', 'true_false', 'single_answer', 'multiple_answer']))
                        <div class="space-y-1.5 mb-3.5">
                            @foreach($q->options as $opt)
                                <div class="text-xs p-2.5 rounded-xl border flex items-center justify-between transition {{ $opt->is_correct ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-200 font-bold' : 'bg-slate-950/60 border-slate-800 text-slate-300' }}">
                                    <span><strong class="font-mono text-sky-400 mr-1.5">{{ $opt->option_key }}.</strong> {{ $opt->option_text }}</span>
                                    @if($opt->is_correct)
                                        <span class="text-[10px] text-emerald-400 font-black">✓ Correct</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($q->question_type === 'short_answer')
                        <div class="text-xs p-3 bg-slate-950/80 border border-sky-900/40 rounded-xl text-slate-300 mb-3.5">
                            <strong class="text-sky-300">Expected Answer:</strong> {{ $q->correct_answer }}
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-[10px] text-slate-500 font-medium">By {{ $q->creator->name ?? 'System' }} • {{ $q->created_at->diffForHumans() }}</span>
                    <div class="flex items-center gap-2">
                        @if($q->review_status === 'pending')
                            <button wire:click="approveQuestion({{ $q->id }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold rounded-lg transition">Approve</button>
                            <button wire:click="rejectQuestion({{ $q->id }})" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white text-[11px] font-bold rounded-lg transition">Reject</button>
                        @endif
                        <button wire:click="deleteQuestion({{ $q->id }})" onclick="return confirm('Delete this question?')" class="text-rose-400 hover:text-rose-300 font-bold transition">Delete</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-900/40 border border-slate-800 p-10 rounded-2xl text-center text-slate-500 text-xs">
                No questions found matching your filters. Click "+ Create Question" to add new items.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="relative z-10">
        {{ $questions->links() }}
    </div>

    <!-- Create / Edit Question Modal -->
    @if($showQuestionModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-sky-500/30 rounded-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl relative z-10">
                <div class="flex justify-between items-center mb-5 pb-3 border-b border-sky-900/30">
                    <h2 class="text-lg font-bold text-white">Create New Assessment Question</h2>
                    <button wire:click="$set('showQuestionModal', false)" class="text-slate-400 hover:text-white text-lg">✕</button>
                </div>

                <form wire:submit.prevent="saveQuestion" class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sky-300/80 mb-1 font-bold">Subject *</label>
                            <select wire:model.live="subject_id" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sky-300/80 mb-1 font-bold">Topic / Chapter</label>
                            <select wire:model="topic_id" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                                <option value="">Select Topic</option>
                                @foreach($topics as $top)
                                    <option value="{{ $top->id }}">{{ $top->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sky-300/80 mb-1 font-bold">Question Type *</label>
                            <select wire:model.live="question_type" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                                <option value="mcq">MCQ</option>
                                <option value="true_false">True / False</option>
                                <option value="single_answer">Single Answer</option>
                                <option value="multiple_answer">Multiple Answer</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sky-300/80 mb-1 font-bold">Difficulty *</label>
                            <select wire:model="difficulty" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sky-300/80 mb-1 font-bold">Marks *</label>
                            <input type="number" step="0.5" wire:model="marks" class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Question Text *</label>
                        <textarea wire:model="question_text" rows="3" placeholder="Enter full question content..." class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 placeholder-slate-500 focus:border-sky-400"></textarea>
                    </div>

                    @if(in_array($question_type, ['mcq', 'true_false', 'single_answer', 'multiple_answer']))
                        <div>
                            <label class="block text-sky-300/80 mb-2 font-bold">Options & Correct Answer Selection</label>
                            <div class="space-y-2">
                                @foreach($options as $idx => $opt)
                                    <div class="flex items-center gap-2 bg-slate-950 p-2.5 rounded-xl border border-sky-900/40">
                                        <span class="font-mono text-sky-400 font-bold w-6 text-center">{{ $opt['key'] }}</span>
                                        <input type="text" wire:model="options.{{ $idx }}.text" placeholder="Option text..." class="flex-1 bg-slate-900 border border-slate-800 rounded-lg py-1.5 px-3 text-slate-200 focus:border-sky-400">
                                        <label class="flex items-center gap-1.5 cursor-pointer text-slate-300 bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-800">
                                            <input type="checkbox" wire:model="options.{{ $idx }}.is_correct" class="rounded bg-slate-950 border-slate-700 text-sky-500 focus:ring-sky-400">
                                            <span class="text-[11px] font-bold">Correct</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($question_type === 'short_answer')
                        <div>
                            <label class="block text-sky-300/80 mb-1 font-bold">Expected Answer Key</label>
                            <input type="text" wire:model="correct_answer" placeholder="Exact answer string..." class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400">
                        </div>
                    @endif

                    <div>
                        <label class="block text-sky-300/80 mb-1 font-bold">Explanation / Solution Notes (Optional)</label>
                        <textarea wire:model="explanation" rows="2" placeholder="Provide solution rationale for students after test completion..." class="w-full bg-slate-950 border border-sky-900/50 rounded-xl py-2.5 px-3 text-slate-200 focus:border-sky-400"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showQuestionModal', false)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-cyan-400 hover:from-sky-300 hover:to-cyan-300 text-slate-950 font-black rounded-xl shadow-lg shadow-sky-500/25 transition">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
