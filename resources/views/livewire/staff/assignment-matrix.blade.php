<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Class & Subject Assignment Matrix</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Visual breakdown of faculty workloads, assigned subjects, and head class teacher responsibilities across departments.</p>
        </div>
        <div>
            <a href="{{ route('staff.index') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/40 px-4 py-2.5 text-sm font-semibold text-indigo-700 dark:text-indigo-300 shadow-sm hover:bg-indigo-100 transition">
                &larr; Return to Staff Directory
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2 relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search teacher by name, code, or subject..." class="w-full pl-4 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <select wire:model.live="department_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Faculty Member</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Department</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Assigned Subjects</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Head Class Teacher</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Subject Teaching Classes</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                        {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <a href="{{ route('staff.show', $teacher) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 text-sm">
                                            {{ $teacher->full_name }}
                                        </a>
                                        <div class="text-[11px] font-mono text-indigo-600 dark:text-indigo-400">{{ $teacher->employee_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-700 dark:text-slate-300 font-medium">
                                {{ $teacher->department->name ?? 'General Science' }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($teacher->subjects as $sub)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                            {{ $sub->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic">None</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @php
                                    $classTeacherAssignment = $teacher->classAssignments->firstWhere('is_class_teacher', true);
                                @endphp
                                @if($classTeacherAssignment && $classTeacherAssignment->schoolClass)
                                    <span class="px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold">
                                        {{ $classTeacherAssignment->schoolClass->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">No head class</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700 dark:text-slate-300">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($teacher->classes as $class)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-medium text-[11px]">
                                            {{ $class->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic">None</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                <a href="{{ route('staff.show', $teacher) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">View 360° Dashboard &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">No faculty members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
