<div class="space-y-6">
    <!-- Top Flash Alert -->
    @if(session()->has('message'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 p-4 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Teacher & Staff Directory</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage faculty members, academic assignments, qualifications, staff profiles, and administrative roles.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('staff.matrix') }}" class="inline-flex items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-900/40 px-4 py-2.5 text-sm font-semibold text-purple-700 dark:text-purple-300 shadow-sm hover:bg-purple-100 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Class & Subject Matrix
            </a>
            @can('create', App\Models\StaffProfile::class)
                <button wire:click="createStaff" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Enroll Staff Member
                </button>
            @endcan
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, employee code, qualification, subject..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Department Filter -->
            <div>
                <select wire:model.live="department_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role Type Filter -->
            <div>
                <select wire:model.live="role_type" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Staff Roles</option>
                    <option value="teacher">Teacher / Faculty</option>
                    <option value="principal">Principal / Exec</option>
                    <option value="counselor">Counselor</option>
                    <option value="hr">HR Staff</option>
                    <option value="accountant">Accountant</option>
                    <option value="admin_staff">Admin Staff</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model.live="status" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="on_leave">On Leave</option>
                    <option value="inactive">Inactive</option>
                    <option value="terminated">Terminated</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Staff Directory Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Staff Member</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Emp Code</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Department & Qualification</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Assigned Subjects / Classes</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Contact & Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($staffMembers as $staff)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow">
                                            {{ strtoupper(substr($staff->first_name, 0, 1) . substr($staff->last_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('staff.show', $staff) }}" class="font-medium text-slate-900 dark:text-white hover:text-indigo-600">
                                            {{ $staff->full_name }}
                                        </a>
                                        <div class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                                            {{ $staff->designation }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded-lg">
                                    {{ $staff->employee_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $staff->department->name ?? 'General Staff' }}</div>
                                <div class="text-xs text-slate-400">{{ $staff->qualification ?: 'Degree Unspecified' }} • {{ $staff->experience_years }} yrs exp</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($staff->subjects as $subject)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                                {{ $subject->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">No subject assigned</span>
                                        @endforelse
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Classes: {{ $staff->classes->pluck('name')->implode(', ') ?: 'None' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-slate-700 dark:text-slate-300">{{ $staff->phone ?: 'No Phone' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $staff->email }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold mt-1 capitalize
                                    {{ $staff->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-800' }}">
                                    {{ str_replace('_', ' ', $staff->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('staff.show', $staff) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300 text-xs font-semibold transition">
                                    View 360° Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                No staff records found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffMembers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>

    <!-- Enroll Staff Modal Drawer -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex justify-end">
            <div class="w-full max-w-2xl bg-white dark:bg-slate-800 h-full min-h-screen p-6 shadow-2xl flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Enroll Staff / Faculty Member</h2>
                        <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveStaff" class="mt-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">First Name *</label>
                                <input wire:model="first_name" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                                @error('first_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Middle Name</label>
                                <input wire:model="middle_name" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Last Name *</label>
                                <input wire:model="last_name" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                                @error('last_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address *</label>
                                <input wire:model="email" type="email" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                                @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
                                <input wire:model="phone" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Staff Role Type *</label>
                                <select wire:model="role_type_input" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                                    <option value="teacher">Teacher / Faculty</option>
                                    <option value="principal">Principal / Executive</option>
                                    <option value="counselor">Counselor</option>
                                    <option value="hr">HR Manager</option>
                                    <option value="accountant">Accountant</option>
                                    <option value="admin_staff">Administrative Staff</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Designation Title</label>
                                <input wire:model="designation" type="text" placeholder="e.g. Senior Physics Teacher" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Department</label>
                                <select wire:model="new_department_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Qualification</label>
                                <input wire:model="qualification" type="text" placeholder="e.g. M.Sc Physics, B.Ed" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Years Experience</label>
                                <input wire:model="experience_years" type="number" min="0" max="50" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Joining Date</label>
                                <input wire:model="joining_date" type="date" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Employment Type</label>
                                <select wire:model="employment_type_input" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="visiting">Visiting Faculty</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center">
                                <input wire:model="create_user_account" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-xs font-medium text-slate-700 dark:text-slate-300">Create system user portal account with default password</span>
                            </label>
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                            <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500 shadow-md">Create Staff Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
