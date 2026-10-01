<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    {{ strtoupper(substr($lead->student_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lead->student_name }}</h1>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $lead->lead_code }}
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $lead->status === 'converted' ? 'bg-emerald-100 text-emerald-800' : ($lead->status === 'lost' ? 'bg-slate-200 text-slate-700' : 'bg-blue-100 text-blue-800') }}">
                            {{ strtoupper($lead->status) }}
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300 flex flex-wrap gap-x-6 gap-y-1">
                        <span><strong class="text-gray-900 dark:text-white">Parent:</strong> {{ $lead->parent_name }}</span>
                        <span><strong class="text-gray-900 dark:text-white">Phone:</strong> {{ $lead->parent_phone }}</span>
                        <span><strong class="text-gray-900 dark:text-white">Email:</strong> {{ $lead->parent_email }}</span>
                        <span><strong class="text-gray-900 dark:text-white">Applying For:</strong> {{ $lead->gradeApplyingFor?->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="text-right">
                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Lead Score</span>
                    <span class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $lead->score }}/100</span>
                </div>
                <button wire:click="$set('showStageModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    Move Stage
                </button>
                <a href="{{ route('admissions.pipeline') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg text-sm font-medium transition">
                    Kanban Board
                </a>
            </div>
        </div>

        <!-- 11-Stage Pipeline Progress Stepper -->
        <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-6">
            <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">11-Stage Pipeline Progress</div>
            <div class="overflow-x-auto pb-2">
                <div class="flex items-center min-w-max space-x-1">
                    @php
                        $stageKeys = array_keys($stages);
                        $currentIdx = array_search($lead->stage, $stageKeys);
                    @endphp
                    @foreach($stages as $key => $info)
                        @php
                            $loopIdx = array_search($key, $stageKeys);
                            $isPassed = $loopIdx < $currentIdx;
                            $isCurrent = $key === $lead->stage;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex flex-col items-center">
                                <div class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5
                                    {{ $isCurrent ? 'bg-indigo-600 text-white ring-2 ring-indigo-300 shadow' : ($isPassed ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400') }}">
                                    @if($isPassed)
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                    {{ $info['label'] }}
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="w-4 h-0.5 {{ $isPassed ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 px-6 overflow-x-auto" aria-label="Tabs">
                @foreach([
                    'overview' => 'Overview & Details',
                    'ai_assistant' => '🤖 AI Admission Assistant',
                    'enquiries' => 'Inquiries (' . $lead->enquiries->count() . ')',
                    'followups' => 'Follow-ups (' . $lead->followups->count() . ')',
                    'application' => 'Application',
                    'documents' => 'Documents (' . $lead->documents->count() . ')',
                    'timeline' => 'Audit Activity Feed'
                ] as $tabKey => $tabLabel)
                    <button wire:click="$set('activeTab', '{{ $tabKey }}')"
                        class="py-4 px-1 inline-flex items-center gap-2 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === $tabKey ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                        {{ $tabLabel }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            <!-- AI ADMISSION ASSISTANT TAB -->
            @if($activeTab === 'ai_assistant')
                <div class="space-y-6">
                    <!-- Header Banner -->
                    <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-purple-950 p-6 rounded-2xl text-white shadow-xl border border-indigo-500/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-indigo-500/20 rounded-xl backdrop-blur-md border border-indigo-400/30">
                                    <svg class="w-8 h-8 text-indigo-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold tracking-tight">AI Admission Assistant</h2>
                                    <p class="text-xs text-slate-300 mt-0.5">Supportive intelligence layer for admission counselors. Human oversight enforced; no autonomous decisions.</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-mono uppercase bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30">
                                Human Oversight Active
                            </span>
                        </div>

                        <!-- 8 Core Capabilities Chips -->
                        <div class="mt-6 pt-4 border-t border-indigo-500/30">
                            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wider mb-3">Select Admission Assistant Capability:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($aiCapabilities as $key => $cap)
                                    <button wire:click="runAiAssistant('{{ $key }}')" 
                                        class="px-3.5 py-2 text-xs font-medium rounded-xl border transition flex items-center space-x-1.5 {{ $activeAiCapability === $key ? 'bg-indigo-600 text-white border-indigo-400 shadow-lg shadow-indigo-600/30 font-bold' : 'bg-slate-800/80 text-slate-200 border-slate-700 hover:bg-slate-700' }}">
                                        <span>{{ $cap['icon'] }}</span>
                                        <span>{{ $cap['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- AI Output Display with 4 Labeled Sections -->
                    @if($aiOutput)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md p-6 space-y-6">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4">
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full bg-indigo-600 animate-ping"></span>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                        Generated Result: {{ $aiCapabilities[$activeAiCapability]['label'] ?? 'Admission Insight' }}
                                    </h3>
                                </div>
                                <span class="text-xs text-slate-400 font-mono">Structured 4-Part Standard Format</span>
                            </div>

                            <div class="prose dark:prose-invert max-w-none text-sm text-slate-800 dark:text-slate-100 whitespace-pre-line leading-relaxed">
                                {!! Str::markdown($aiOutput) !!}
                            </div>

                            <!-- Interactive User Confirmation & Staff Review Action Banner -->
                            <div class="p-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-bold text-indigo-900 dark:text-indigo-200 uppercase tracking-wider">User Confirmation & Staff Approval</p>
                                    <p class="text-xs text-indigo-700 dark:text-indigo-300 mt-0.5">
                                        Please review, edit, or confirm the AI suggestion before taking action. No automated admission decisions are made without counselor approval.
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button onclick="navigator.clipboard.writeText(`{{ addslashes($aiOutput) }}`); alert('AI content copied to clipboard.');" 
                                        class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 text-xs font-semibold rounded-lg shadow-sm transition">
                                        📋 Copy Output
                                    </button>
                                    <button wire:click="$set('showFollowUpModal', true)" 
                                        class="px-3 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-md transition">
                                        🗓️ Schedule Follow-Up Task
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Placeholder state -->
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-12 text-center text-slate-500 dark:text-slate-400">
                            <span class="text-4xl block mb-3">🤖</span>
                            <p class="text-base font-semibold text-slate-800 dark:text-slate-200">AI Admission Assistant Ready</p>
                            <p class="text-xs mt-1 max-w-md mx-auto">Select one of the 8 capabilities above to summarize lead history, generate call notes, draft outreach messages, or identify overdue tasks for {{ $lead->student_name }}.</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- OVERVIEW TAB -->
            @if($activeTab === 'overview')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Student & Parent Card -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl space-y-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">Student & Family Information</h3>
                        <div class="text-sm space-y-2 text-gray-700 dark:text-gray-300">
                            <p><span class="text-gray-500">Student Name:</span> <strong>{{ $lead->student_name }}</strong></p>
                            <p><span class="text-gray-500">DOB:</span> {{ $lead->student_dob ? $lead->student_dob->format('M d, Y') : 'N/A' }}</p>
                            <p><span class="text-gray-500">Gender:</span> {{ ucfirst($lead->gender) }}</p>
                            <p><span class="text-gray-500">Parent Name:</span> {{ $lead->parent_name }}</p>
                            <p><span class="text-gray-500">Primary Phone:</span> {{ $lead->parent_phone }}</p>
                            <p><span class="text-gray-500">Alt Phone:</span> {{ $lead->alternate_phone ?: 'N/A' }}</p>
                            <p><span class="text-gray-500">Email:</span> {{ $lead->parent_email }}</p>
                            <p><span class="text-gray-500">Address:</span> {{ implode(', ', array_filter([$lead->address, $lead->city, $lead->state])) ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Attribution & Counselor Card -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl space-y-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">Attribution & Counselor</h3>
                        <div class="text-sm space-y-2 text-gray-700 dark:text-gray-300">
                            <p><span class="text-gray-500">Lead Source:</span> <strong>{{ $lead->source?->name ?? 'Direct Inquiry' }}</strong></p>
                            <p><span class="text-gray-500">Campaign:</span> {{ $lead->campaign?->name ?? 'Standard Intake' }}</p>
                            <p><span class="text-gray-500">Assigned Counselor:</span> <strong>{{ $lead->counselor?->name ?? 'Unassigned' }}</strong></p>
                            <p><span class="text-gray-500">Academic Year:</span> {{ $lead->academicYear?->name ?? 'N/A' }}</p>
                            <p><span class="text-gray-500">Created Date:</span> {{ $lead->created_at->format('M d, Y h:i A') }}</p>
                            @if($lead->loss_reason)
                                <div class="mt-3 p-3 bg-red-50 text-red-700 rounded-lg text-xs">
                                    <strong>Loss Reason:</strong> {{ $lead->loss_reason }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Highlights Card -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl space-y-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">Quick Stats & Action</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <span class="text-xs text-gray-500">Pending Follow-ups</span>
                                <span class="text-sm font-bold text-amber-600">{{ $lead->followups->where('status', 'pending')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <span class="text-xs text-gray-500">Total Inquiries</span>
                                <span class="text-sm font-bold text-indigo-600">{{ $lead->enquiries->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <span class="text-xs text-gray-500">Documents Vault</span>
                                <span class="text-sm font-bold text-teal-600">{{ $lead->documents->count() }}</span>
                            </div>
                            <button wire:click="$set('showEnquiryModal', true)" class="w-full py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 font-semibold text-xs rounded-lg transition">
                                + Log Quick Inquiry
                            </button>
                            <button wire:click="$set('showFollowUpModal', true)" class="w-full py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 font-semibold text-xs rounded-lg transition">
                                + Schedule Follow-up
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ENQUIRIES TAB -->
            @if($activeTab === 'enquiries')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Communication & Inquiries Log</h3>
                        <button wire:click="$set('showEnquiryModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
                            + Log New Inquiry
                        </button>
                    </div>

                    <div class="space-y-3">
                        @forelse($lead->enquiries as $enquiry)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 uppercase">
                                        {{ str_replace('_', ' ', $enquiry->inquiry_type) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $enquiry->inquiry_date ? \Carbon\Carbon::parse($enquiry->inquiry_date)->format('M d, Y h:i A') : $enquiry->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Q: {{ $enquiry->question }}</p>
                                @if($enquiry->response)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-700 mt-2">
                                        <strong>Response:</strong> {{ $enquiry->response }}
                                    </p>
                                @endif
                                <div class="text-xs text-gray-400 mt-2">Logged by: {{ $enquiry->answeredBy?->name ?? 'System' }}</div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">No inquiries recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- FOLLOW-UPS TAB -->
            @if($activeTab === 'followups')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Scheduled Follow-ups & Tasks</h3>
                        <button wire:click="$set('showFollowUpModal', true)" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">
                            + Schedule Follow-up
                        </button>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($lead->followups as $task)
                            <div class="py-4 flex items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase {{ $task->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($task->scheduled_at < now() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $task->status }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-mono">{{ str_replace('_', ' ', $task->type) }}</span>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $task->subject }}</h4>
                                    </div>
                                    @if($task->notes)
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $task->notes }}</p>
                                    @endif
                                    <div class="text-xs text-gray-400 flex gap-4">
                                        <span>Scheduled: {{ \Carbon\Carbon::parse($task->scheduled_at)->format('M d, Y h:i A') }}</span>
                                        <span>Assigned To: {{ $task->assignedTo?->name ?? 'Counselor' }}</span>
                                    </div>
                                </div>

                                @if($task->status !== 'completed')
                                    <button wire:click="markFollowUpComplete({{ $task->id }})" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 whitespace-nowrap">
                                        Mark Done
                                    </button>
                                @else
                                    <span class="text-xs text-emerald-600 font-semibold">Done {{ \Carbon\Carbon::parse($task->completed_at)->format('M d') }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">No scheduled follow-up tasks.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- APPLICATION TAB -->
            @if($activeTab === 'application')
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Admission Application Form</h3>
                        <button wire:click="$set('showApplicationModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
                            {{ $lead->application ? 'Edit Application' : 'Create Application' }}
                        </button>
                    </div>

                    @if($lead->application)
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-xl space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-gray-200 dark:border-gray-600 pb-4">
                                <div>
                                    <span class="text-xs text-gray-500 block">Application #</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $lead->application->application_number }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Application Stage</span>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 uppercase">
                                        {{ str_replace('_', ' ', $lead->application->application_stage) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Fee Payment Status</span>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $lead->application->fee_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} uppercase">
                                        {{ $lead->application->fee_status }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                                <p><span class="text-gray-500">Submission Date:</span> {{ $lead->application->submission_date ? $lead->application->submission_date->format('M d, Y') : 'N/A' }}</p>
                                <p><span class="text-gray-500">Decision Date:</span> {{ $lead->application->decision_date ? $lead->application->decision_date->format('M d, Y') : 'Pending' }}</p>
                            </div>

                            @if($lead->application->reviewer_notes)
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <span class="text-xs font-bold text-gray-500 block mb-1">Reviewer Notes</span>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $lead->application->reviewer_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                            <p class="text-gray-500 mb-3">No application started yet for this lead.</p>
                            <button wire:click="$set('showApplicationModal', true)" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                                Initialize Application
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- DOCUMENTS TAB -->
            @if($activeTab === 'documents')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Lead Documents Vault</h3>
                        <button wire:click="$set('showDocumentModal', true)" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700">
                            + Add Document
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($lead->documents as $doc)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300 flex items-center justify-center font-bold text-xs uppercase">
                                        DOC
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $doc->title }}</h4>
                                        <div class="text-xs text-gray-500 space-x-2">
                                            <span class="uppercase font-semibold">{{ str_replace('_', ' ', $doc->document_type) }}</span>
                                            <span>• {{ $doc->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                                    Uploaded
                                </span>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8 text-gray-500">No documents uploaded.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- TIMELINE TAB -->
            @if($activeTab === 'timeline')
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Stage & Activity Audit Trail</h3>
                    <div class="relative border-l-2 border-indigo-200 dark:border-indigo-900 ml-4 space-y-6">
                        @forelse($lead->statusHistories as $history)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-indigo-600 ring-4 ring-white dark:ring-gray-800"></div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                            Stage Changed: {{ strtoupper(str_replace('_', ' ', $history->from_stage ?? 'New')) }} → {{ strtoupper(str_replace('_', ' ', $history->to_stage)) }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $history->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    @if($history->reason)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $history->reason }}</p>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-2">Changed by: {{ $history->changedBy?->name ?? 'System' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="pl-6 text-gray-500 text-sm">No stage transition history logged yet.</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Move Stage Modal -->
    @if($showStageModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Update Pipeline Stage</h3>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Select New Stage</label>
                    <select wire:model="selectedStage" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        @foreach($stages as $key => $info)
                            <option value="{{ $key }}">{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                @if($selectedStage === 'lost')
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Reason for Loss</label>
                        <textarea wire:model="stageReason" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" rows="2" placeholder="e.g. Chose another institution due to distance"></textarea>
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Notes / Reason (Optional)</label>
                    <input type="text" wire:model="stageReason" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="Notes for stage change">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showStageModal', false)" class="px-4 py-2 border rounded-lg text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="changeStage" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Update Stage</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Log Enquiry Modal -->
    @if($showEnquiryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Log Parent Inquiry</h3>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Inquiry Category</label>
                    <select wire:model="inquiryType" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        <option value="general">General Inquiry</option>
                        <option value="fee_structure">Fee Structure & Discounts</option>
                        <option value="curriculum">Curriculum & Subjects</option>
                        <option value="transport">School Bus & Transport</option>
                        <option value="hostel">Hostel & Boarding</option>
                        <option value="scholarship">Scholarships & Grants</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Parent Question / Topic</label>
                    <textarea wire:model="question" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="What did the parent ask?"></textarea>
                    @error('question') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Response Given</label>
                    <textarea wire:model="response" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="Details of explanation provided"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showEnquiryModal', false)" class="px-4 py-2 border rounded-lg text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveEnquiry" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Save Inquiry</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Schedule Follow-up Modal -->
    @if($showFollowUpModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Schedule Follow-up Activity</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Interaction Type</label>
                        <select wire:model="followUpType" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="phone_call">Phone Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Counseling Meeting</option>
                            <option value="campus_visit">Campus Visit</option>
                            <option value="whatsapp">WhatsApp Message</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Scheduled Date & Time</label>
                        <input type="datetime-local" wire:model="followUpScheduledAt" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        @error('followUpScheduledAt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                    <input type="text" wire:model="followUpSubject" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="e.g. Follow up on fee structure inquiry">
                    @error('followUpSubject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Notes / Instructions</label>
                    <textarea wire:model="followUpNotes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showFollowUpModal', false)" class="px-4 py-2 border rounded-lg text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveFollowUp" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">Schedule Task</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Application Modal -->
    @if($showApplicationModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Manage Admission Application</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Application Stage</label>
                        <select wire:model="appStage" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="under_review">Under Review</option>
                            <option value="interview_scheduled">Interview Scheduled</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="enrolled">Enrolled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Fee Payment Status</label>
                        <select wire:model="appFeeStatus" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="waived">Waived</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Reviewer Notes</label>
                    <textarea wire:model="appReviewerNotes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="Internal remarks regarding application eligibility or interview results"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showApplicationModal', false)" class="px-4 py-2 border rounded-lg text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveApplication" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Save Application</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Document Modal -->
    @if($showDocumentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upload Lead Document</h3>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Document Title</label>
                    <input type="text" wire:model="docTitle" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="e.g. Birth Certificate Copy">
                    @error('docTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Document Type</label>
                    <select wire:model="docType" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        <option value="birth_certificate">Birth Certificate</option>
                        <option value="report_card">Previous Report Card</option>
                        <option value="passport_photo">Passport Photograph</option>
                        <option value="transfer_certificate">Transfer Certificate</option>
                        <option value="medical_form">Medical Form</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">File Upload (PDF/Image)</label>
                    <input type="file" wire:model="docFile" class="w-full text-xs text-gray-500 border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showDocumentModal', false)" class="px-4 py-2 border rounded-lg text-sm text-gray-600 dark:text-gray-300">Cancel</button>
                    <button wire:click="saveDocument" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700">Upload</button>
                </div>
            </div>
        </div>
    @endif
</div>
