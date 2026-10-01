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

    <!-- Student Profile Header Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-indigo-50 dark:bg-indigo-950/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-5">
                <!-- Profile Avatar -->
                <div class="relative">
                    @if($student->profile_photo_path)
                        <img class="h-20 w-20 rounded-2xl object-cover ring-4 ring-indigo-50 dark:ring-indigo-900/50 shadow-md" src="{{ Storage::url($student->profile_photo_path) }}" alt="{{ $student->full_name }}">
                    @else
                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="absolute -bottom-1 -right-1 block h-4 w-4 rounded-full ring-2 ring-white dark:ring-slate-800
                        @if($student->status === 'active') bg-emerald-500 @else bg-slate-400 @endif"></span>
                </div>

                <!-- Basic Info Header -->
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $student->full_name }}</h1>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold
                            @if($student->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                            @elseif($student->status === 'suspended') bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400
                            @elseif($student->status === 'graduated') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                            @else bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 @endif">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-y-1 gap-x-4 text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1 font-mono text-indigo-600 dark:text-indigo-400 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg>
                            Adm: {{ $student->admission_number }}
                        </div>
                        <div>•</div>
                        <div>Class: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $student->schoolClass->name ?? 'N/A' }}</span> {{ $student->division ? "({$student->division->name})" : '' }}</div>
                        <div>•</div>
                        <div>School: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $student->school->name ?? 'N/A' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Action Quick Buttons -->
            @can('students.edit')
                <div class="flex flex-wrap items-center gap-2.5">
                    <button wire:click="$set('showStatusModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition">
                        Change Status
                    </button>
                    <button wire:click="$set('showNoteModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Note
                    </button>
                    <button wire:click="$set('showDocumentModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload Doc
                    </button>
                    <button wire:click="$set('showGuardianModal', true)" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-purple-50 dark:bg-purple-900/40 hover:bg-purple-100 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Add Guardian
                    </button>
                </div>
            @endcan
        </div>

        <!-- Navigation Tabs -->
        <div class="mt-8 border-b border-slate-200 dark:border-slate-700 flex items-center gap-6 overflow-x-auto text-sm font-medium">
            <button wire:click="setTab('overview')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'overview' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                Overview & 360° Summary
            </button>
            <button wire:click="setTab('guardians')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'guardians' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                Guardians ({{ $student->guardians->count() }})
            </button>
            <button wire:click="setTab('timeline')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'timeline' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                Unified Activity Timeline ({{ $timeline->count() }})
            </button>
            <button wire:click="setTab('notes')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'notes' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                Notes ({{ $student->notes->count() }})
            </button>
            <button wire:click="setTab('documents')" class="pb-3 border-b-2 transition whitespace-nowrap {{ $activeTab === 'documents' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
                Documents ({{ $student->documents->count() }})
            </button>
        </div>
    </div>

    <!-- TAB 1: OVERVIEW & 360° SUMMARY -->
    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Core Panels -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Panel 1: Basic Information -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Student Basic Profile
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Full Name</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->full_name }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Date of Birth & Age</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') . ' (' . $student->date_of_birth->age . ' yrs)' : 'N/A' }}
                            </span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Gender</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ $student->gender }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Admission Date</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Contact Email</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->email ?? 'N/A' }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl">
                            <span class="text-xs text-slate-400 block mb-0.5">Roll Number</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $student->roll_number ?? 'Unassigned' }}</span>
                        </div>
                    </div>

                    @if($student->addresses->isNotEmpty())
                        <div class="mt-3 bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-2xl text-sm">
                            <span class="text-xs text-slate-400 block mb-0.5">Residential Address</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">
                                @php $addr = $student->addresses->first(); @endphp
                                {{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}, {{ $addr->country }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Panel 3 & 4 Grid: Attendance & Academic Summary -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Panel 3: Attendance Summary Widget -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Attendance
                            </h3>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/40 px-2.5 py-1 rounded-full">95.2% Rate</span>
                        </div>

                        <div class="space-y-2">
                            <div class="w-full bg-slate-100 dark:bg-slate-700 h-3 rounded-full overflow-hidden flex">
                                <div class="bg-emerald-500 h-full" style="width: 90%"></div>
                                <div class="bg-amber-400 h-full" style="width: 5%"></div>
                                <div class="bg-rose-500 h-full" style="width: 5%"></div>
                            </div>
                            <div class="grid grid-cols-3 text-center text-xs pt-1">
                                <div><span class="font-bold text-emerald-600">144</span> <span class="text-slate-400">Present</span></div>
                                <div><span class="font-bold text-amber-500">4</span> <span class="text-slate-400">Late</span></div>
                                <div><span class="font-bold text-rose-500">3</span> <span class="text-slate-400">Absent</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel 4: Academic Summary Widget -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                Academic Performance
                            </h3>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/40 px-2.5 py-1 rounded-full">GPA 3.85 / 4.0</span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl">
                                <span class="text-slate-600 dark:text-slate-300">Mathematics</span>
                                <span class="font-bold text-slate-900 dark:text-white">A (94%)</span>
                            </div>
                            <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl">
                                <span class="text-slate-600 dark:text-slate-300">Science & Chemistry</span>
                                <span class="font-bold text-slate-900 dark:text-white">A- (91%)</span>
                            </div>
                            <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl">
                                <span class="text-slate-600 dark:text-slate-300">English Literature</span>
                                <span class="font-bold text-slate-900 dark:text-white">A+ (98%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 5 & 6: Communication History & Tasks -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Panel 5: Communication History -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Communication History
                        </h3>
                        <div class="space-y-3 text-xs">
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl space-y-1">
                                <div class="flex justify-between font-semibold text-slate-800 dark:text-slate-200">
                                    <span>Parent Consultation Call</span>
                                    <span class="text-slate-400">2 days ago</span>
                                </div>
                                <p class="text-slate-500">Discussed upcoming science fair project and progress.</p>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl space-y-1">
                                <div class="flex justify-between font-semibold text-slate-800 dark:text-slate-200">
                                    <span>Automated Attendance SMS</span>
                                    <span class="text-slate-400">Sep 15, 2026</span>
                                </div>
                                <p class="text-slate-500">Sent to primary guardian: Student arrived on time.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Panel 6: Tasks Widget -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Action Tasks
                        </h3>
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center gap-3 p-2.5 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <input type="checkbox" checked class="rounded text-indigo-600">
                                <span class="text-slate-600 dark:text-slate-300 line-through">Verify medical certificate doc</span>
                            </div>
                            <div class="flex items-center gap-3 p-2.5 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <input type="checkbox" class="rounded text-indigo-600">
                                <span class="text-slate-800 dark:text-slate-200 font-medium">Collect fee clearance form</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Column: Primary Guardian & Recent Notes -->
            <div class="space-y-6">
                <!-- Panel 2: Parent / Guardian Summary Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Primary Guardian
                        </h3>
                        <button wire:click="setTab('guardians')" class="text-xs text-indigo-600 hover:underline">View All ({{ $student->guardians->count() }})</button>
                    </div>

                    @php $primary = $student->primaryGuardian; @endphp
                    @if($primary)
                        <div class="bg-gradient-to-br from-indigo-50/50 to-purple-50/50 dark:from-slate-900 dark:to-slate-900/60 p-4 rounded-2xl border border-indigo-100 dark:border-slate-700 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $primary->first_name }} {{ $primary->last_name }}</span>
                                <span class="text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 font-semibold px-2 py-0.5 rounded-md capitalize">{{ $primary->relationship }}</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h32a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/></svg>
                                    <span>{{ $primary->phone }}</span>
                                </div>
                                @if($primary->email)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>{{ $primary->email }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-slate-400 italic text-center py-4">No primary guardian linked yet.</div>
                    @endif
                </div>

                <!-- Panel 7: Notes Preview -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 7.172a2 2 0 010 2.828l-8.485 8.485M7 17h.01"/></svg>
                            Recent Notes
                        </h3>
                        <button wire:click="setTab('notes')" class="text-xs text-indigo-600 hover:underline">View All</button>
                    </div>

                    <div class="space-y-3">
                        @forelse($student->notes->take(3) as $note)
                            @if($note->is_confidential && !$canViewConfidentialNotes)
                                @continue
                            @endif
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-2xl space-y-1 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ $note->type }}</span>
                                    @if($note->is_confidential)
                                        <span class="bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 font-semibold px-2 py-0.5 rounded text-[10px]">Confidential</span>
                                    @endif
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 line-clamp-2">{{ $note->content }}</p>
                                <div class="text-[10px] text-slate-400 pt-1">
                                    By {{ $note->author->name ?? 'System' }} • {{ $note->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 text-center py-4">No notes recorded yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Panel 9 & 10: Applications & Activities -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Co-Curricular & Clubs
                    </h3>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium">Science Olympiad Club</span>
                        <span class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">School Basketball Team</span>
                        <span class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-medium">Robotics Society</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: GUARDIANS -->
    @if($activeTab === 'guardians')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Parents & Guardians</h2>
                    <p class="text-xs text-slate-500">Linked primary and secondary emergency contacts for {{ $student->full_name }}</p>
                </div>
                <button wire:click="$set('showGuardianModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500">
                    + Add New Guardian
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($student->guardians as $g)
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $g->first_name }} {{ $g->last_name }}</h4>
                                <span class="text-xs text-slate-500 capitalize">{{ $g->relationship }} {{ $g->occupation ? "• {$g->occupation}" : '' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($g->is_primary)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">Primary</span>
                                @endif
                                @if($g->is_emergency_contact)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">Emergency</span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
                            <div><span class="text-slate-400">Phone:</span> {{ $g->phone }}</div>
                            <div><span class="text-slate-400">Email:</span> {{ $g->email ?? 'N/A' }}</div>
                            <div><span class="text-slate-400">Portal Access:</span> {{ $g->has_portal_access ? 'Enabled' : 'Disabled' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-slate-400 text-sm">
                        No guardians added for this student yet.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 3: UNIFIED ACTIVITY TIMELINE -->
    @if($activeTab === 'timeline')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Unified Student Activity Timeline</h2>
                <p class="text-xs text-slate-500">Chronological history of status updates, uploaded documents, notes, and profile changes</p>
            </div>

            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700 space-y-8">
                @forelse($timeline as $item)
                    <div class="relative group">
                        <!-- Icon node -->
                        <div class="absolute -left-[31px] top-0.5 h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs shadow-md">
                            @if($item['type'] === 'status_change')
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            @elseif($item['type'] === 'note')
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 7.172a2 2 0 010 2.828l-8.485 8.485M7 17h.01"/></svg>
                            @elseif($item['type'] === 'document')
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @endif
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $item['title'] }}</h4>
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($item['timestamp'])->format('M d, Y • h:i A') }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">{{ $item['description'] }}</p>
                            <div class="text-[11px] text-slate-400 pt-1">
                                By: <span class="font-medium text-slate-600 dark:text-slate-300">{{ $item['author'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-400 py-6">No recorded activity timeline entries yet.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 4: NOTES -->
    @if($activeTab === 'notes')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Student Profile Notes</h2>
                    <p class="text-xs text-slate-500">Internal teacher, counselor, and admin notes</p>
                </div>
                <button wire:click="$set('showNoteModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500">
                    + Add New Note
                </button>
            </div>

            <div class="space-y-4">
                @forelse($student->notes as $note)
                    @if($note->is_confidential && !$canViewConfidentialNotes)
                        @continue
                    @endif
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 capitalize">{{ $note->type }}</span>
                                @if($note->is_confidential)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Confidential Note</span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-400">{{ $note->created_at->format('M d, Y h:i A') }}</span>
                        </div>

                        <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line">{{ $note->content }}</p>

                        <div class="text-xs text-slate-400 pt-1">
                            Recorded by <span class="font-medium text-slate-700 dark:text-slate-300">{{ $note->author->name ?? 'System' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm">No notes available for this student.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 5: DOCUMENTS -->
    @if($activeTab === 'documents')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Uploaded Documents</h2>
                    <p class="text-xs text-slate-500">Birth certificates, medical records, past marksheets</p>
                </div>
                <button wire:click="$set('showDocumentModal', true)" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-500">
                    + Upload Document
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($student->documents as $doc)
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 flex flex-col justify-between space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $doc->title }}</h4>
                                <span class="text-xs text-slate-400 uppercase tracking-wider">{{ $doc->type }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs border-t border-slate-200/60 dark:border-slate-700/60 pt-2.5 text-slate-500">
                            <span>{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                View / Download
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-400 text-sm">No documents uploaded yet.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- MODAL: ADD NOTE -->
    @if($showNoteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add Note for {{ $student->first_name }}</h3>
                <form wire:submit.prevent="addNote" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Note Type</label>
                        <select wire:model="noteType" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="general">General</option>
                            <option value="academic">Academic</option>
                            <option value="behavioral">Behavioral</option>
                            <option value="medical">Medical</option>
                            <option value="counseling">Counseling</option>
                            <option value="financial">Financial</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Content *</label>
                        <textarea wire:model="noteContent" rows="4" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="Write detailed note..."></textarea>
                        @error('noteContent') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="isConfidential" id="confidential_check" class="rounded text-indigo-600">
                        <label for="confidential_check" class="text-xs font-medium text-slate-700 dark:text-slate-300">Mark as Confidential (Restricted access)</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showNoteModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD GUARDIAN -->
    @if($showGuardianModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add Guardian</h3>
                <form wire:submit.prevent="addGuardian" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">First Name *</label>
                            <input wire:model="guardianFirstName" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            @error('guardianFirstName') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Last Name *</label>
                            <input wire:model="guardianLastName" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Relationship *</label>
                            <select wire:model="guardianRelationship" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Legal Guardian</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Phone *</label>
                            <input wire:model="guardianPhone" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            @error('guardianPhone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input wire:model="guardianEmail" type="email" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                    </div>

                    <div class="flex items-center gap-4 text-xs">
                        <label class="flex items-center gap-1.5">
                            <input type="checkbox" wire:model="isPrimaryGuardian" class="rounded text-indigo-600">
                            Primary Guardian
                        </label>
                        <label class="flex items-center gap-1.5">
                            <input type="checkbox" wire:model="isEmergencyContact" class="rounded text-indigo-600">
                            Emergency Contact
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showGuardianModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Add Guardian</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: UPLOAD DOCUMENT -->
    @if($showDocumentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Upload Document</h3>
                <form wire:submit.prevent="uploadDocument" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Document Title *</label>
                        <input wire:model="documentTitle" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="e.g. Birth Certificate">
                        @error('documentTitle') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Document Type *</label>
                        <select wire:model="documentType" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="birth_certificate">Birth Certificate</option>
                            <option value="medical_record">Medical Record</option>
                            <option value="previous_marksheet">Previous Marksheet</option>
                            <option value="identity_proof">Identity Proof</option>
                            <option value="transfer_certificate">Transfer Certificate</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">File *</label>
                        <input wire:model="documentFile" type="file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/40 dark:file:text-indigo-300">
                        @error('documentFile') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showDocumentModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: CHANGE STATUS -->
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Update Student Status</h3>
                <form wire:submit.prevent="updateStatus" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">New Status *</label>
                        <select wire:model="newStatus" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                            <option value="transferred">Transferred</option>
                            <option value="graduated">Graduated</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Reason for Status Change *</label>
                        <input wire:model="statusReason" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="e.g. Completed Grade 12 Graduation">
                        @error('statusReason') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
                        <textarea wire:model="statusNotes" rows="3" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm" placeholder="Additional details..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" wire:click="$set('showStatusModal', false)" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
