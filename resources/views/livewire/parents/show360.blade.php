<div class="space-y-6">
    <!-- Top Flash Alert -->
    @if(session()->has('message'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 p-4 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Parent Profile Header Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-purple-50 dark:bg-purple-950/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-5">
                <!-- Profile Avatar -->
                <div class="relative">
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                        {{ strtoupper(substr($parent->first_name, 0, 1) . substr($parent->last_name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full ring-2 ring-white dark:ring-slate-800 bg-emerald-500"></span>
                </div>

                <!-- Basic Info Header -->
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $parent->full_name }}</h1>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 capitalize">
                            {{ $parent->status }}
                        </span>
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-y-1 gap-x-4 text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1 font-mono text-indigo-600 dark:text-indigo-400 font-medium">
                            Code: {{ $parent->parent_code }}
                        </div>
                        <div>•</div>
                        <div>Phone: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $parent->phone ?? 'N/A' }}</span></div>
                        <div>•</div>
                        <div>Email: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $parent->email }}</span></div>
                        <div>•</div>
                        <div>Occupation: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $parent->occupation ?? 'N/A' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Action Quick Buttons -->
            @can('parents.edit')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('parents.family-dashboard', ['parent_id' => $parent->id]) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-purple-50 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 hover:bg-purple-100 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Family Dashboard
                    </a>
                    <button wire:click="$set('showCommModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition">
                        + Log Comm
                    </button>
                    <button wire:click="$set('showComplaintModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-50 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 transition">
                        + Log Complaint
                    </button>
                    <button wire:click="$set('showApptModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100 transition">
                        + Book Appt
                    </button>
                    <button wire:click="$set('showDocModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition">
                        + Upload Doc
                    </button>
                </div>
            @endcan
        </div>

        <!-- Navigation Tabs -->
        <div class="mt-8 border-b border-slate-200 dark:border-slate-700 flex items-center gap-6 overflow-x-auto text-sm font-medium">
            <button wire:click="setTab('overview')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'overview' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Overview & Children ({{ $parent->students->count() }})
            </button>
            <button wire:click="setTab('communications')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'communications' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Communication History ({{ $parent->communications->count() }})
            </button>
            <button wire:click="setTab('complaints')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'complaints' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Complaints & Tickets ({{ $parent->complaints->count() }})
            </button>
            <button wire:click="setTab('appointments')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'appointments' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Appointments ({{ $parent->appointments->count() }})
            </button>
            <button wire:click="setTab('tasks')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'tasks' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Tasks ({{ $parent->tasks->count() }})
            </button>
            <button wire:click="setTab('notes')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'notes' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Notes ({{ $parent->notes->count() }})
            </button>
            <button wire:click="setTab('documents')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'documents' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Documents ({{ $parent->documents->count() }})
            </button>
        </div>
    </div>

    <!-- TAB 1: OVERVIEW & CHILDREN LINKAGE -->
    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Associated Children Cards & Profile Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Associated Children Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Associated Children (Students)
                        </h3>
                        <button wire:click="$set('showLinkStudentModal', true)" class="text-xs text-indigo-600 hover:underline font-semibold">+ Link Child</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($parent->students as $child)
                            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 font-bold flex items-center justify-center text-sm">
                                            {{ strtoupper(substr($child->first_name, 0, 1) . substr($child->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('students.show', $child) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 text-sm">
                                                {{ $child->full_name }}
                                            </a>
                                            <div class="text-xs text-slate-400">Class: {{ $child->schoolClass->name ?? 'Unassigned' }}</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 font-bold">
                                        {{ $child->admission_number }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                                    <span>Relationship: <strong class="capitalize text-slate-700 dark:text-slate-300">{{ $child->pivot->relationship_type ?? 'Mother' }}</strong></span>
                                    <a href="{{ route('parents.family-dashboard', ['parent_id' => $parent->id, 'student_id' => $child->id]) }}" class="text-indigo-600 font-semibold hover:underline">Switch to Profile &rarr;</a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-6 text-slate-400 text-sm">No children linked to this parent yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Basic Profile Details -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Parent Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Full Name</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $parent->full_name }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Email</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $parent->email }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Primary Phone</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $parent->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Occupation & Employer</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $parent->occupation ?? 'N/A' }} {{ $parent->employer ? "at {$parent->employer}" : '' }}</span>
                        </div>
                    </div>

                    @if($parent->address_line1)
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl text-sm">
                            <span class="text-xs text-slate-400 block mb-0.5">Residential Address</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">
                                {{ $parent->address_line1 }}, {{ $parent->city }}, {{ $parent->state }} {{ $parent->postal_code }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right 1 Col: Quick Communications & Recent Activity -->
            <div class="space-y-6">
                <!-- Admissions & Applications Summary -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Admissions Data
                    </h3>
                    <div class="space-y-2 text-xs">
                        @foreach($parent->students as $st)
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $st->full_name }}</span>
                                    <span class="text-slate-400">Admitted: {{ $st->admission_date ? $st->admission_date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 capitalize">
                                    {{ $st->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Communication History Preview (Required Sample Data Check) -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Recent Communications
                        </h3>
                        <button wire:click="setTab('communications')" class="text-xs text-indigo-600 hover:underline font-semibold">View All</button>
                    </div>

                    <div class="space-y-3">
                        @forelse($parent->communications->take(3) as $comm)
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl text-xs space-y-1">
                                <div class="flex justify-between font-semibold text-slate-800 dark:text-slate-200">
                                    <span>{{ $comm->subject }}</span>
                                    <span class="text-slate-400">{{ $comm->interaction_date ? $comm->interaction_date->format('Y-m-d') : '' }}</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300">{{ $comm->summary }}</p>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 text-center py-3">No communications recorded.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: COMMUNICATIONS LOG -->
    @if($activeTab === 'communications')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Communication History Log</h2>
                    <p class="text-xs text-slate-500">Emails, phone calls, meetings, and SMS interactions with {{ $parent->full_name }}</p>
                </div>
                <button wire:click="$set('showCommModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500">
                    + Log New Interaction
                </button>
            </div>

            <div class="space-y-3">
                @forelse($parent->communications as $c)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 capitalize">{{ str_replace('_', ' ', $c->type) }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 capitalize">{{ $c->direction }}</span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono">{{ $c->interaction_date ? $c->interaction_date->format('Y-m-d H:i') : $c->created_at->format('Y-m-d') }}</span>
                        </div>

                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $c->subject }}</h4>
                        <p class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $c->summary }}</p>

                        <div class="text-[11px] text-slate-400 pt-1 flex justify-between">
                            <span>Logged by: <strong class="text-slate-600 dark:text-slate-300">{{ $c->staff->name ?? 'System Staff' }}</strong></span>
                            @if($c->student)
                                <span>Regarding Child: <strong class="text-slate-600 dark:text-slate-300">{{ $c->student->full_name }}</strong></span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">No communication logs recorded yet.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 3: COMPLAINTS & TICKETS -->
    @if($activeTab === 'complaints')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Complaints & Concerns Tickets</h2>
                    <p class="text-xs text-slate-500">Track and resolve issues submitted by parent</p>
                </div>
                <button wire:click="$set('showComplaintModal', true)" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-semibold hover:bg-rose-500">
                    + Submit Complaint Ticket
                </button>
            </div>

            <div class="space-y-4">
                @forelse($parent->complaints as $ticket)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-rose-600 dark:text-rose-400">{{ $ticket->ticket_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 capitalize">{{ $ticket->category }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 uppercase">{{ $ticket->priority }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize
                                @if($ticket->status === 'resolved') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40
                                @else bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-300 @endif">
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </span>
                        </div>

                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $ticket->subject }}</h4>
                        <p class="text-xs text-slate-700 dark:text-slate-300">{{ $ticket->description }}</p>

                        @if($ticket->resolution_notes)
                            <div class="mt-2 p-2.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl text-xs text-emerald-800 dark:text-emerald-300">
                                <strong>Resolution:</strong> {{ $ticket->resolution_notes }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">No complaints or concern tickets recorded.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 4: APPOINTMENTS -->
    @if($activeTab === 'appointments')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Scheduled Appointments</h2>
                    <p class="text-xs text-slate-500">Parent-teacher meetings, counseling sessions, and admission interviews</p>
                </div>
                <button wire:click="$set('showApptModal', true)" class="px-4 py-2 bg-amber-600 text-white rounded-xl text-xs font-semibold hover:bg-amber-500">
                    + Schedule Appointment
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($parent->appointments as $appt)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 capitalize">{{ str_replace('_', ' ', $appt->type) }}</span>
                            <span class="text-xs text-slate-400">{{ $appt->scheduled_at ? $appt->scheduled_at->format('M d, Y h:i A') : '' }}</span>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $appt->title }}</h4>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div>Location/Link: <strong class="text-slate-700 dark:text-slate-300">{{ $appt->location_or_link ?? 'In School' }}</strong></div>
                            <div>Duration: {{ $appt->duration_minutes }} mins</div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-slate-400 text-sm">No scheduled appointments.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 5: TASKS -->
    @if($activeTab === 'tasks')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Assigned Tasks</h2>
                    <p class="text-xs text-slate-500">Pending items and document submission tasks</p>
                </div>
                <button wire:click="$set('showTaskModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500">
                    + Assign Task
                </button>
            </div>

            <div class="space-y-3">
                @forelse($parent->tasks as $t)
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <h4 class="font-bold text-slate-900 dark:text-white">{{ $t->title }}</h4>
                            <p class="text-slate-500">{{ $t->description }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 uppercase">{{ $t->priority }}</span>
                            <div class="text-slate-400 text-[10px] mt-1">Due: {{ $t->due_date ? $t->due_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">No tasks assigned.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 6: NOTES -->
    @if($activeTab === 'notes')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Parent Notes & Annotations</h2>
                    <p class="text-xs text-slate-500">Staff notes and confidential annotations</p>
                </div>
                <button wire:click="$set('showNoteModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500">
                    + Add Note
                </button>
            </div>

            <div class="space-y-3">
                @forelse($parent->notes as $n)
                    @if($n->is_confidential && !$canViewConfidentialNotes)
                        @continue
                    @endif
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 capitalize">{{ $n->category }}</span>
                                @if($n->is_confidential)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/40">Confidential</span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-400">{{ $n->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-sm text-slate-800 dark:text-slate-200">{{ $n->note }}</p>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">No notes recorded.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 7: DOCUMENTS (Exact required sample entries rendering) -->
    @if($activeTab === 'documents')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Uploaded Documents</h2>
                    <p class="text-xs text-slate-500">Attached ID proofs, consent forms, and custody records</p>
                </div>
                <button wire:click="$set('showDocModal', true)" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-500">
                    + Upload Document
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($parent->documents as $doc)
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $doc->title }}</h4>
                                <span class="text-xs text-slate-400 uppercase">{{ $doc->document_type }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-slate-500">
                            <span>{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 font-semibold hover:underline">View Document</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-400 text-sm">No documents uploaded.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- MODAL: ADD COMMUNICATION -->
    @if($showCommModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Log Communication</h3>
                <form wire:submit.prevent="addCommunication" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Interaction Type</label>
                            <select wire:model="commType" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                <option value="phone_call">Phone Call</option>
                                <option value="email">Email</option>
                                <option value="in_person_meeting">In-Person Meeting</option>
                                <option value="sms">SMS</option>
                                <option value="portal_message">Portal Message</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Direction</label>
                            <select wire:model="commDirection" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                <option value="outbound">Outbound (School -> Parent)</option>
                                <option value="inbound">Inbound (Parent -> School)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Subject *</label>
                        <input wire:model="commSubject" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                        @error('commSubject') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Summary *</label>
                        <textarea wire:model="commSummary" rows="3" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm"></textarea>
                        @error('commSummary') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showCommModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Save Communication</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD COMPLAINT -->
    @if($showComplaintModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Submit Complaint Ticket</h3>
                <form wire:submit.prevent="addComplaint" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Category</label>
                            <select wire:model="complaintCategory" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="behavior">Behavior</option>
                                <option value="facility">Facility</option>
                                <option value="fee_billing">Fee & Billing</option>
                                <option value="transport">Transport</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                            <select wire:model="complaintPriority" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Subject *</label>
                        <input wire:model="complaintSubject" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Description *</label>
                        <textarea wire:model="complaintDescription" rows="4" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showComplaintModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl">Log Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: SCHEDULE APPOINTMENT -->
    @if($showApptModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Schedule Appointment</h3>
                <form wire:submit.prevent="scheduleAppointment" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Title *</label>
                        <input wire:model="apptTitle" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="e.g. Parent-Teacher Progress Discussion">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Date & Time *</label>
                            <input wire:model="scheduledAt" type="datetime-local" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Duration (Mins)</label>
                            <input wire:model="durationMinutes" type="number" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Location or Virtual Link</label>
                        <input wire:model="locationLink" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="e.g. Room 204 or Zoom URL">
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showApptModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-xl">Save Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD NOTE -->
    @if($showNoteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add Note</h3>
                <form wire:submit.prevent="addNote" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Note Content *</label>
                        <textarea wire:model="noteContent" rows="4" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="isConfidential" id="note_conf_check" class="rounded text-indigo-600">
                        <label for="note_conf_check" class="text-xs font-medium text-slate-700 dark:text-slate-300">Mark as Confidential</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showNoteModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: UPLOAD DOCUMENT -->
    @if($showDocModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Upload Document</h3>
                <form wire:submit.prevent="uploadDocument" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Title *</label>
                        <input wire:model="docTitle" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="e.g. ID proof or Consent form">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Document File *</label>
                        <input wire:model="docFile" type="file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showDocModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: LINK STUDENT -->
    @if($showLinkStudentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Link Child (Student)</h3>
                <form wire:submit.prevent="linkStudent" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Select Student *</label>
                        <select wire:model="linkStudentId" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="">Select Child</option>
                            @foreach($allStudents as $stItem)
                                <option value="{{ $stItem->id }}">{{ $stItem->full_name }} ({{ $stItem->admission_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Relationship Type</label>
                        <select wire:model="linkRelationship" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="mother">Mother</option>
                            <option value="father">Father</option>
                            <option value="legal_guardian">Legal Guardian</option>
                            <option value="step_father">Step Father</option>
                            <option value="step_mother">Step Mother</option>
                            <option value="other">Other Relative</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showLinkStudentModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Link Child</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
