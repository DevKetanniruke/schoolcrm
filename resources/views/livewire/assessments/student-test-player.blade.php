<div class="p-6 bg-slate-950 text-slate-100 min-h-screen relative overflow-hidden">
    <!-- Ambient Background Glow Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Test Player Header -->
    <div class="relative z-10 bg-slate-900/80 backdrop-blur-xl border border-sky-500/30 rounded-2xl p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-2xl">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-gradient-to-tr from-sky-500 to-cyan-400 rounded-2xl shadow-lg shadow-sky-500/25 text-slate-950">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-md uppercase font-mono">
                        {{ str_replace('_', ' ', $assessment->type ?? 'Test') }}
                    </span>
                    <span class="text-xs font-semibold text-sky-400/80">Class: {{ $assessment->schoolClass->name ?? '' }}</span>
                </div>
                <h1 class="text-xl md:text-2xl font-black text-white mt-1 tracking-tight">{{ $assessment->title }}</h1>
            </div>
        </div>

        @if(! $isSubmitted)
            <div class="flex items-center gap-4 text-xs">
                <div class="bg-slate-950 border border-sky-900/40 rounded-xl py-2 px-4 flex items-center gap-2 shadow-inner">
                    <span class="text-slate-400 font-medium">⏱ Time Allowed:</span>
                    <span class="font-mono text-sky-300 font-black text-sm">{{ $assessment->duration_minutes }} mins</span>
                </div>
                <button wire:click="$set('showSubmitConfirmModal', true)" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition">
                    🏁 Submit Test
                </button>
            </div>
        @else
            <div class="px-4 py-2 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-extrabold text-xs rounded-xl shadow-lg">
                ✓ Test Completed & Graded
            </div>
        @endif
    </div>

    @if($isSubmitted && $result)
        <!-- Test Result Card Summary -->
        <div class="relative z-10 bg-slate-900/90 backdrop-blur-xl border border-emerald-500/40 rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-800 pb-4 mb-4">
                <div>
                    <h2 class="text-lg font-black text-white">Your Test Assessment Result</h2>
                    <p class="text-xs text-sky-300/70 mt-0.5 font-medium">Submitted at {{ $attempt->submitted_at?->format('d M Y, h:i A') }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-center bg-slate-950 p-3 rounded-xl border border-slate-800 shadow-inner">
                        <div class="text-[10px] text-slate-400 uppercase font-bold">Total Score</div>
                        <div class="text-lg font-black text-sky-300 font-mono">{{ $result->score }} / {{ $attempt->total_marks_possible }}</div>
                    </div>

                    <div class="text-center bg-slate-950 p-3 rounded-xl border border-slate-800 shadow-inner">
                        <div class="text-[10px] text-slate-400 uppercase font-bold">Percentage</div>
                        <div class="text-lg font-black text-cyan-300 font-mono">{{ $result->percentage }}%</div>
                    </div>

                    <div class="text-center bg-slate-950 p-3 rounded-xl border border-slate-800 shadow-inner">
                        <div class="text-[10px] text-slate-400 uppercase font-bold">Grade & Status</div>
                        <div class="text-lg font-black font-mono {{ $result->is_passed ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $result->grade }} ({{ $result->is_passed ? 'PASSED' : 'FAILED' }})
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-xs text-slate-300 font-medium">
                You answered <strong class="text-sky-300 font-mono">{{ $attempt->answered_questions }}</strong> out of <strong class="text-sky-300 font-mono">{{ $attempt->total_questions }}</strong> questions. Student topic performance records have been updated automatically.
            </div>
        </div>
    @endif

    <!-- Main Test Taking Body -->
    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Question Column (3 cols) -->
        <div class="lg:col-span-3 space-y-4">
            @if($currentQuestion)
                <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-6 shadow-2xl">
                    <!-- Question Info Bar -->
                    <div class="flex justify-between items-center mb-5 pb-3 border-b border-sky-900/30 text-xs">
                        <span class="font-black text-sky-300 text-sm">Question {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}</span>
                        <span class="px-2.5 py-1 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-lg font-mono font-bold">{{ $currentQuestion['marks'] }} Marks</span>
                    </div>

                    <!-- Question Text -->
                    <h2 class="text-base md:text-lg font-bold text-white mb-6 leading-relaxed">
                        {{ $currentQuestion['text'] }}
                    </h2>

                    <!-- Question Options / Answers -->
                    <div class="space-y-3 mb-6">
                        @if(in_array($currentQuestion['type'], ['mcq', 'true_false', 'single_answer', 'multiple_answer']))
                            @foreach($currentQuestion['options'] as $opt)
                                @php
                                    $qId = $currentQuestion['id'];
                                    $selectedOpts = $selectedOptions[$qId] ?? [];
                                    $isSelected = in_array($opt['id'], $selectedOpts);
                                @endphp
                                <button wire:click="selectOption({{ $qId }}, {{ $opt['id'] }}, '{{ $currentQuestion['type'] }}')" 
                                    @disabled($isSubmitted)
                                    class="w-full text-left p-4 rounded-2xl border transition duration-200 flex items-center justify-between text-xs 
                                    {{ $isSelected ? 'bg-gradient-to-r from-sky-950/80 to-cyan-950/60 border-sky-400 text-sky-100 font-bold shadow-lg shadow-sky-500/10 ring-1 ring-sky-400/40' : 'bg-slate-950/80 border-slate-800/80 text-slate-300 hover:bg-slate-900 hover:border-sky-900/50' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-xl border flex items-center justify-center font-mono font-black text-xs
                                            {{ $isSelected ? 'bg-gradient-to-r from-sky-400 to-cyan-400 text-slate-950 border-sky-300 shadow-md' : 'bg-slate-900 border-slate-700 text-slate-400' }}">
                                            {{ $opt['key'] }}
                                        </span>
                                        <span class="text-slate-100 text-sm font-medium">{{ $opt['text'] }}</span>
                                    </div>
                                    @if($isSelected)
                                        <span class="text-sky-400 font-black flex items-center gap-1">✓ Selected</span>
                                    @endif
                                </button>
                            @endforeach
                        @elseif($currentQuestion['type'] === 'short_answer')
                            @php $qId = $currentQuestion['id']; @endphp
                            <div>
                                <label class="block text-sky-300/80 text-xs mb-2 font-bold">Type your answer below:</label>
                                <textarea wire:model.live.debounce.500ms="textAnswers.{{ $qId }}" 
                                    @disabled($isSubmitted)
                                    rows="4" placeholder="Enter short answer response..." class="w-full bg-slate-950 border border-sky-900/50 rounded-2xl p-4 text-slate-200 text-xs focus:border-sky-400 placeholder-slate-500"></textarea>
                            </div>
                        @endif
                    </div>

                    <!-- Navigation Footer -->
                    <div class="flex justify-between items-center pt-4 border-t border-slate-800">
                        <button wire:click="previousQuestion" @disabled($currentQuestionIndex === 0) class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 disabled:opacity-40 text-slate-300 text-xs font-bold rounded-xl transition">
                            ← Previous
                        </button>

                        <button wire:click="nextQuestion" @disabled($currentQuestionIndex === $totalQuestions - 1) class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-cyan-400 hover:from-sky-300 hover:to-cyan-300 disabled:opacity-40 text-slate-950 text-xs font-black rounded-xl shadow-lg shadow-sky-500/25 transition">
                            Next →
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Navigation Grid & Counters (1 col) -->
        <div class="space-y-4">
            <!-- Counters -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-4 shadow-xl text-xs space-y-2.5">
                <h3 class="font-extrabold text-white mb-2 tracking-wide">Test Progress Status</h3>
                <div class="flex justify-between items-center bg-slate-950 p-3 rounded-xl border border-slate-800">
                    <span class="text-slate-400 font-medium">Answered:</span>
                    <span class="font-mono text-emerald-400 font-black text-sm">{{ $answeredCount }}</span>
                </div>
                <div class="flex justify-between items-center bg-slate-950 p-3 rounded-xl border border-slate-800">
                    <span class="text-slate-400 font-medium">Unanswered:</span>
                    <span class="font-mono text-amber-400 font-black text-sm">{{ $unansweredCount }}</span>
                </div>
            </div>

            <!-- Question Grid Navigation -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-sky-500/20 rounded-2xl p-4 shadow-xl">
                <h3 class="font-extrabold text-white text-xs mb-3 tracking-wide">Question Navigation</h3>
                <div class="grid grid-cols-5 gap-2">
                    @foreach($questionsData as $idx => $qd)
                        @php
                            $qId = $qd['id'];
                            $hasAns = ! empty($selectedOptions[$qId]) || ! empty($textAnswers[$qId]);
                            $isCurrent = $currentQuestionIndex === $idx;
                        @endphp
                        <button wire:click="goToQuestion({{ $idx }})" 
                            class="h-10 rounded-xl font-mono text-xs font-black border transition duration-200 flex items-center justify-center 
                            {{ $isCurrent ? 'ring-2 ring-sky-400 ring-offset-2 ring-offset-slate-950 shadow-lg shadow-sky-500/30' : '' }}
                            {{ $hasAns ? 'bg-gradient-to-tr from-sky-500/30 to-cyan-500/20 border-sky-400/50 text-sky-200' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white hover:border-sky-900' }}">
                            {{ $idx + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if($showSubmitConfirmModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-sky-500/30 rounded-2xl p-6 w-full max-w-md shadow-2xl text-xs relative z-10">
                <h2 class="text-base font-bold text-white mb-2">Submit Weekly Test?</h2>
                <p class="text-slate-300 mb-4 font-medium leading-relaxed">
                    You have answered <strong class="text-sky-300 font-mono text-sm">{{ $answeredCount }}</strong> of <strong class="text-sky-300 font-mono text-sm">{{ $totalQuestions }}</strong> questions. Once submitted, your answers will be auto-graded and saved.
                </p>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button wire:click="$set('showSubmitConfirmModal', false)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition">Return to Test</button>
                    <button wire:click="submitTest" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black rounded-xl shadow-lg shadow-emerald-500/20 transition">Confirm & Submit</button>
                </div>
            </div>
        </div>
    @endif
</div>
