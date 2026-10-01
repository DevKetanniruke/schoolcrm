<div class="space-y-6">
    <!-- Top Flash Alert -->
    @if(session()->has('message'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 p-4 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Profile Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="h-20 w-20 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-3xl shadow-xl ring-4 ring-indigo-400/20">
                    {{ strtoupper(substr($staff->first_name, 0, 1) . substr($staff->last_name, 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <span class="text-xs uppercase tracking-wider text-indigo-300 font-semibold">{{ $staff->designation ?: 'Staff Member' }}</span>
                        <span class="bg-indigo-500/30 text-indigo-200 text-[10px] font-mono px-2 py-0.5 rounded border border-indigo-400/30">{{ $staff->employee_code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider
                            {{ $staff->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700 text-slate-300' }}">
                            {{ str_replace('_', ' ', $staff->status) }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $staff->full_name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300">
                        <span>Department: <strong class="text-indigo-200">{{ $staff->department->name ?? 'General Staff' }}</strong></span>
                        <span>•</span>
                        <span>Qualification: <strong class="text-white">{{ $staff->qualification ?: 'Degree Unspecified' }}</strong></span>
                        <span>•</span>
                        <span>Experience: <strong class="text-white">{{ $staff->experience_years }} Years</strong></span>
                    </div>
                </div>
            </div>

            <!-- Header Metrics Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 w-full md:w-auto">
                <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-2xl text-center">
                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Assigned Classes</span>
                    <span class="text-xl font-bold text-indigo-400">{{ $staff->classes->count() }}</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-2xl text-center">
                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Total Students</span>
                    <span class="text-xl font-bold text-emerald-400">{{ $totalStudentsCount }}</span>
                </div>
                <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-2xl text-center col-span-2 sm:col-span-1">
                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Pending Tasks</span>
                    <span class="text-xl font-bold text-amber-400">{{ $staff->tasks->where('status', 'pending')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-200 dark:border-slate-700">
        <nav class="-mb-px flex space-x-6 overflow-x-auto scrollbar-none">
            @foreach([
                'overview' => 'Overview',
                'classes' => 'Assigned Classes',
                'subjects' => 'Subjects Taught',
                'schedule' => 'Timetable Schedule',
                'tasks' => 'Tasks & Action Items',
                'evaluations' => 'Evaluations & Performance',
                'documents' => 'Documents & Forms',
                'notes' => 'Staff Administrative Notes',
            ] as $key => $label)
                <button
                    wire:click="$set('activeTab', '{{ $key }}')"
                    class="py-3 px-1 text-xs sm:text-sm font-semibold border-b-2 whitespace-nowrap transition
                    {{ $activeTab === $key
                        ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                        : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}"
                >
                    {{ $label }}
                    @if($key === 'tasks' && $staff->tasks->where('status', 'pending')->count() > 0)
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                            {{ $staff->tasks->where('status', 'pending')->count() }}
                        </span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Tab Panels Content -->

    <!-- 1. OVERVIEW TAB -->
    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Personal Details Card -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3">Professional & Employment Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Full Legal Name</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Employee Code</span>
                        <span class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $staff->employee_code }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Email Address</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $staff->email }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Phone Number</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $staff->phone ?: 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Role Category</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ str_replace('_', ' ', $staff->role_type) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Designation</span>
                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $staff->designation }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Department</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->department->name ?? 'Unassigned' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Academic Qualification</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->qualification ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Joining Date</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->joining_date ? $staff->joining_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Employment Type</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ str_replace('_', ' ', $staff->employment_type) }}</span>
                    </div>
                </div>
            </div>

            <!-- Side Card: Quick Statistics & Contact Info -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3">Contact Information</h3>
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 block">Primary Email</span>
                            <a href="mailto:{{ $staff->email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">{{ $staff->email }}</a>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Phone</span>
                            <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $staff->phone ?: 'None' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Residential Address</span>
                            <span class="text-slate-800 dark:text-slate-200 font-medium">
                                {{ implode(', ', array_filter([$staff->address_line1, $staff->city, $staff->state, $staff->postal_code])) ?: 'Address not on record' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Portal Account Card -->
                <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-3xl p-6 border border-indigo-200/60 dark:border-indigo-800/60 space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-300 block">System Portal Access</span>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-700 dark:text-slate-300 font-medium">User Account Status:</span>
                        @if($staff->user)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold">Active Account</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 font-bold">No Portal User</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. ASSIGNED CLASSES TAB -->
    @if($activeTab === 'classes')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Assigned Classes & Classroom Roster</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Classes where {{ $staff->first_name }} is designated as Class Teacher or Subject Teacher.</p>
                </div>
                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-bold text-xs rounded-xl">
                    {{ $staff->classes->count() }} Active Classes
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($staff->classAssignments as $assignment)
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white text-base">{{ $assignment->schoolClass->name ?? 'Class N/A' }}</h4>
                            @if($assignment->is_class_teacher)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                    Head Class Teacher
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 capitalize">
                                    {{ str_replace('_', ' ', $assignment->assignment_role) }}
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                            <div>Division: <strong class="text-slate-800 dark:text-slate-200">{{ $assignment->division->name ?? 'All Sections' }}</strong></div>
                            <div>Enrolled Students: <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ \App\Models\Student::where('class_id', $assignment->class_id)->count() }} Students</strong></div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 p-8 text-center text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                        No class assignments linked to this teacher profile.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- 3. SUBJECTS TAUGHT TAB -->
    @if($activeTab === 'subjects')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Subject Mastery & Curricula</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($staff->subjectAssignments as $ts)
                    <div class="p-5 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200/60 dark:border-purple-800/40 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase text-purple-600 dark:text-purple-400 block">{{ $ts->subject->code ?? 'SUB-101' }}</span>
                            <h4 class="font-bold text-slate-900 dark:text-white text-base mt-0.5">{{ $ts->subject->name ?? 'Subject Name' }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Department: {{ $ts->subject->department->name ?? 'Science' }} • Credits: {{ $ts->subject->credits ?? 4 }}</p>
                        </div>
                        <div>
                            @if($ts->is_primary)
                                <span class="px-3 py-1 bg-purple-600 text-white font-bold text-xs rounded-xl shadow-sm">Primary Subject</span>
                            @else
                                <span class="px-3 py-1 bg-slate-200 text-slate-700 font-medium text-xs rounded-xl">Secondary Subject</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-8 text-center text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                        No subject assignments linked to this staff profile.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- 4. TIMETABLE SCHEDULE TAB -->
    @if($activeTab === 'schedule')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Weekly Teaching Schedule & Class Slots</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($staff->schedules as $sched)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                            <span>{{ $sched->day_of_week }}</span>
                            <span>{{ $sched->start_time }} - {{ $sched->end_time }}</span>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $sched->subject->name ?? 'Lecture' }}</h4>
                        <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400">
                            <span>Class: <strong>{{ $sched->schoolClass->name ?? 'General' }}</strong></span>
                            <span class="font-mono bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded text-[10px] text-slate-700 dark:text-slate-300">Room: {{ $sched->room_number ?: 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 p-8 text-center text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                        No timetable schedule slots published for this staff member.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- 5. TASKS & ACTION ITEMS TAB -->
    @if($activeTab === 'tasks')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Assigned Staff Tasks & Deliverables</h3>
                @can('update', $staff)
                    <button wire:click="$set('showTaskModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500 transition">
                        + Assign New Task
                    </button>
                @endcan
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                @forelse($staff->tasks as $task)
                    <div class="py-4 flex items-start justify-between gap-4">
                        <div class="flex items-start space-x-3">
                            <button wire:click="toggleTaskStatus({{ $task->id }})" class="mt-0.5 focus:outline-none">
                                @if($task->status === 'completed')
                                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-300 hover:border-indigo-500"></div>
                                @endif
                            </button>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-900 dark:text-white' }}">
                                    {{ $task->title }}
                                </h4>
                                @if($task->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $task->description }}</p>
                                @endif
                                <div class="flex items-center gap-3 text-[11px] text-slate-400 pt-0.5">
                                    <span>Due: <strong>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No deadline' }}</strong></span>
                                    <span>Assigned By: {{ $task->assigner->name ?? 'Admin' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize
                                {{ $task->priority === 'urgent' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300' :
                                   ($task->priority === 'high' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-slate-100 text-slate-700') }}">
                                {{ $task->priority }} Priority
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 italic">No tasks recorded for this staff member.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- 6. EVALUATIONS TAB -->
    @if($activeTab === 'evaluations')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Performance Evaluation & Administrative Reviews</h3>
                @can('update', $staff)
                    <button wire:click="$set('showEvalModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500 transition">
                        + Log Performance Evaluation
                    </button>
                @endcan
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse($staff->evaluations as $eval)
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 text-xs uppercase tracking-wider">{{ $eval->review_period }}</span>
                            <div class="flex items-center text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $eval->rating ? 'fill-current' : 'text-slate-300 dark:text-slate-700' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $eval->feedback }}</p>
                        @if($eval->goals)
                            <div class="text-[11px] text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-xl">
                                <strong>Target Goals:</strong> {{ $eval->goals }}
                            </div>
                        @endif
                        <div class="text-[10px] text-slate-400 flex justify-between pt-1">
                            <span>Evaluated by: {{ $eval->evaluator->name ?? 'HR / Admin' }}</span>
                            <span>Date: {{ $eval->evaluation_date ? $eval->evaluation_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-8 text-center text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                        No performance evaluation reviews recorded for this staff member.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- 7. DOCUMENTS TAB -->
    @if($activeTab === 'documents')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Staff Certificates, Contracts, & Forms</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($staff->documents as $doc)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono font-semibold text-indigo-600 dark:text-indigo-400 uppercase block">{{ str_replace('_', ' ', $doc->document_type) }}</span>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $doc->title }}</h4>
                            <span class="text-[10px] text-slate-400 block">{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 capitalize">
                            {{ $doc->verification_status }}
                        </span>
                    </div>
                @empty
                    <div class="col-span-3 p-8 text-center text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                        No staff documents uploaded.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- 8. NOTES TAB -->
    @if($activeTab === 'notes')
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Staff Administrative Notes</h3>
                @can('update', $staff)
                    <button wire:click="$set('showNoteModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500 transition">
                        + Add Note
                    </button>
                @endcan
            </div>

            <div class="space-y-3">
                @forelse($staff->notes as $note)
                    @if(!$note->is_confidential || $canViewConfidential)
                        <div class="p-4 rounded-2xl border transition
                            {{ $note->is_confidential ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200/60 dark:border-rose-900/40' : 'bg-slate-50 dark:bg-slate-900/50 border-slate-200/60 dark:border-slate-700/60' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold capitalize text-slate-800 dark:text-slate-200">{{ $note->category }} Note</span>
                                <div class="flex items-center gap-2">
                                    @if($note->is_confidential)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            Confidential Admin Note
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400">{{ $note->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $note->note }}</p>
                            <div class="text-[10px] text-slate-400 mt-2">Author: {{ $note->author->name ?? 'Admin User' }}</div>
                        </div>
                    @endif
                @empty
                    <div class="p-8 text-center text-slate-400 italic bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                        No administrative notes recorded for this staff member.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Add Task Modal -->
    @if($showTaskModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Assign Task to Staff Member</h3>
                <form wire:submit.prevent="addTask" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Task Title *</label>
                        <input wire:model="taskTitle" type="text" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                        <textarea wire:model="taskDescription" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Due Date</label>
                            <input wire:model="taskDueDate" type="date" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                            <select wire:model="taskPriority" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showTaskModal', false)" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Add Evaluation Modal -->
    @if($showEvalModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Log Performance Evaluation Review</h3>
                <form wire:submit.prevent="addEvaluation" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Review Period *</label>
                            <input wire:model="evalReviewPeriod" type="text" placeholder="e.g. Q3 2026" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rating (1 to 5) *</label>
                            <select wire:model="evalRating" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                <option value="5">5 - Outstanding / Exceeds Expectations</option>
                                <option value="4">4 - Meets All Expectations</option>
                                <option value="3">3 - Satisfactory Performance</option>
                                <option value="2">2 - Needs Improvement</option>
                                <option value="1">1 - Unsatisfactory</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Feedback Review Notes *</label>
                        <textarea wire:model="evalFeedback" rows="3" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Future Goals / Action Plan</label>
                        <textarea wire:model="evalGoals" rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showEvalModal', false)" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500">Save Review</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Add Note Modal -->
    @if($showNoteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add Staff Administrative Note</h3>
                <form wire:submit.prevent="addNote" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <select wire:model="noteCategory" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                            <option value="general">General Note</option>
                            <option value="performance">Performance Tracking</option>
                            <option value="commendation">Commendation / Award</option>
                            <option value="disciplinary">Disciplinary Action</option>
                            <option value="training">Professional Training</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Note Content *</label>
                        <textarea wire:model="noteContent" rows="4" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input wire:model="noteIsConfidential" type="checkbox" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="ml-2 font-semibold text-rose-600 dark:text-rose-400">Mark as confidential (Restricted to HR / Admin)</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showNoteModal', false)" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
