<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Hierarchy & Structure</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Organization & School Hierarchy</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Multi-tenant foundation entities: Organizations, Schools, Campuses, Departments, Classes, Divisions & Subjects.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 mb-6 overflow-x-auto pb-1 scrollbar-none">
        <button
            wire:click="selectTab('organizations')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'organizations' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Organizations ({{ count($organizations) }})
        </button>
        <button
            wire:click="selectTab('schools')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'schools' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Schools ({{ count($schools) }})
        </button>
        <button
            wire:click="selectTab('campuses')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'campuses' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Campuses ({{ count($campuses) }})
        </button>
        <button
            wire:click="selectTab('academicYears')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'academicYears' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Academic Years ({{ count($academicYears) }})
        </button>
        <button
            wire:click="selectTab('departments')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'departments' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Departments ({{ count($departments) }})
        </button>
        <button
            wire:click="selectTab('classes')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'classes' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Classes ({{ count($classes) }})
        </button>
        <button
            wire:click="selectTab('divisions')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'divisions' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Divisions ({{ count($divisions) }})
        </button>
        <button
            wire:click="selectTab('subjects')"
            class="px-4 py-2 text-xs font-bold rounded-t-xl transition border-b-2 {{ $activeTab === 'subjects' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
        >
            Subjects ({{ count($subjects) }})
        </button>
    </div>

    <!-- Tab Contents -->
    @if ($activeTab === 'organizations')
        <x-ui.card title="Organizations (Tenant Root)">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Organization Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Contact Email</x-table.cell>
                    <x-table.cell header>Managed Schools</x-table.cell>
                    <x-table.cell header>Status</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($organizations as $org)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $org->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $org->code }}</code></x-table.cell>
                            <x-table.cell><span class="text-xs text-slate-500">{{ $org->contact_email ?? 'N/A' }}</span></x-table.cell>
                            <x-table.cell><x-ui.badge variant="primary" size="sm">{{ $org->schools_count }} Schools</x-ui.badge></x-table.cell>
                            <x-table.cell><x-ui.badge variant="success" size="sm">{{ ucfirst($org->status) }}</x-ui.badge></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'schools')
        <x-ui.card title="Schools Directory">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>School Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Parent Organization</x-table.cell>
                    <x-table.cell header>Campuses</x-table.cell>
                    <x-table.cell header>Departments</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($schools as $school)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $school->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $school->code }}</code></x-table.cell>
                            <x-table.cell><span class="text-xs text-slate-600 dark:text-slate-300 font-semibold">{{ $school->organization->name ?? 'None' }}</span></x-table.cell>
                            <x-table.cell><x-ui.badge variant="info" size="sm">{{ $school->campuses_count }} Campuses</x-ui.badge></x-table.cell>
                            <x-table.cell><x-ui.badge variant="neutral" size="sm">{{ $school->departments_count }} Departments</x-ui.badge></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'campuses')
        <x-ui.card title="Campuses">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Campus Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Location</x-table.cell>
                    <x-table.cell header>School</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($campuses as $campus)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $campus->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $campus->code }}</code></x-table.cell>
                            <x-table.cell><span class="text-xs text-slate-500">{{ $campus->location ?? 'Main' }}</span></x-table.cell>
                            <x-table.cell><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $campus->school->name }}</span></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'academicYears')
        <x-ui.card title="Academic Years">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Session Name</x-table.cell>
                    <x-table.cell header>Start Date</x-table.cell>
                    <x-table.cell header>End Date</x-table.cell>
                    <x-table.cell header>Status</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($academicYears as $ay)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $ay->name }}</span></x-table.cell>
                            <x-table.cell><span class="text-xs font-mono text-slate-500">{{ $ay->start_date ? $ay->start_date->format('Y-m-d') : '' }}</span></x-table.cell>
                            <x-table.cell><span class="text-xs font-mono text-slate-500">{{ $ay->end_date ? $ay->end_date->format('Y-m-d') : '' }}</span></x-table.cell>
                            <x-table.cell>
                                @if ($ay->is_current)
                                    <x-ui.badge variant="success" size="sm">Current Active Session</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral" size="sm">{{ ucfirst($ay->status) }}</x-ui.badge>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'departments')
        <x-ui.card title="Departments">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Department Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Classes</x-table.cell>
                    <x-table.cell header>Subjects</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($departments as $dept)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $dept->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $dept->code }}</code></x-table.cell>
                            <x-table.cell><x-ui.badge variant="primary" size="sm">{{ $dept->classes_count }} Classes</x-ui.badge></x-table.cell>
                            <x-table.cell><x-ui.badge variant="info" size="sm">{{ $dept->subjects_count }} Subjects</x-ui.badge></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'classes')
        <x-ui.card title="Classes">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Class Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Department</x-table.cell>
                    <x-table.cell header>Divisions</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($classes as $cls)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $cls->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $cls->code }}</code></x-table.cell>
                            <x-table.cell><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $cls->department->name }}</span></x-table.cell>
                            <x-table.cell><x-ui.badge variant="success" size="sm">{{ $cls->divisions_count }} Sections</x-ui.badge></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'divisions')
        <x-ui.card title="Divisions (Sections)">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Division Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Class</x-table.cell>
                    <x-table.cell header>Capacity</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($divisions as $div)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $div->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $div->code }}</code></x-table.cell>
                            <x-table.cell><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $div->schoolClass->name }}</span></x-table.cell>
                            <x-table.cell><span class="text-xs text-slate-500 font-mono">{{ $div->capacity }} students</span></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>

    @elseif ($activeTab === 'subjects')
        <x-ui.card title="Subjects Directory">
            <x-table.table>
                <x-table.header>
                    <x-table.cell header>Subject Name</x-table.cell>
                    <x-table.cell header>Code</x-table.cell>
                    <x-table.cell header>Department</x-table.cell>
                    <x-table.cell header>Credits</x-table.cell>
                    <x-table.cell header>Type</x-table.cell>
                </x-table.header>
                <x-table.body>
                    @foreach ($subjects as $sub)
                        <x-table.row>
                            <x-table.cell><span class="font-bold text-slate-900 dark:text-white">{{ $sub->name }}</span></x-table.cell>
                            <x-table.cell><code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-indigo-600 dark:text-indigo-400">{{ $sub->code }}</code></x-table.cell>
                            <x-table.cell><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $sub->department->name }}</span></x-table.cell>
                            <x-table.cell><span class="text-xs font-mono text-slate-500">{{ $sub->credits }} Credits</span></x-table.cell>
                            <x-table.cell><x-ui.badge variant="primary" size="sm">{{ ucfirst($sub->type) }}</x-ui.badge></x-table.cell>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </x-ui.card>
    @endif
</div>
