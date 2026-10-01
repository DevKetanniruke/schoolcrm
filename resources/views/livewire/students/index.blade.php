<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('messages.student_directory') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('messages.student_directory_subtitle') }}</p>
        </div>
        @can('students.create')
            <div class="mt-4 sm:mt-0">
                <button wire:click="openCreateModal" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    {{ __('messages.enroll_new_student') }}
                </button>
            </div>
        @endcan
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('messages.search_students_placeholder') }}" class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model.live="statusFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    <option value="active">{{ __('messages.active') }}</option>
                    <option value="inactive">{{ __('messages.inactive') }}</option>
                    <option value="suspended">{{ __('messages.suspended') }}</option>
                    <option value="transferred">{{ __('messages.transferred') }}</option>
                    <option value="graduated">{{ __('messages.graduated') }}</option>
                </select>
            </div>

            <!-- Class Filter -->
            <div>
                <select wire:model.live="classFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.all_classes') }}</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Division Filter -->
            <div>
                <select wire:model.live="divisionFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.all_divisions') }}</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Student Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('messages.th_student') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('messages.th_adm_id') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('messages.th_class_division') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('messages.th_primary_guardian') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('messages.th_status') }}</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('messages.th_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($student->profile_photo_path)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($student->profile_photo_path) }}" alt="{{ $student->full_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('students.show', $student) }}" class="font-medium text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ __("messages.{$student->gender}") }} • {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                <div class="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ $student->admission_number }}</div>
                                <div class="text-xs text-slate-400 dark:text-slate-500">ID: {{ $student->student_id_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                @if($student->schoolClass)
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $student->schoolClass->name }}</span>
                                    @if($student->division)
                                        <span class="text-slate-500">({{ $student->division->name }})</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Unassigned</span>
                                @endif
                                <div class="text-xs text-slate-400">Roll: {{ $student->roll_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                @php $primaryGuardian = $student->primaryGuardian; @endphp
                                @if($primaryGuardian)
                                    <div class="font-medium text-slate-800 dark:text-slate-200">{{ $primaryGuardian->first_name ?? $primaryGuardian->name }} {{ $primaryGuardian->last_name ?? '' }}</div>
                                    <div class="text-xs text-slate-400">{{ $primaryGuardian->phone ?? $primaryGuardian->email ?? 'No contact' }}</div>
                                @else
                                    <span class="text-slate-400 italic">No Guardian</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($student->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                    @elseif($student->status === 'graduated') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($student->status === 'suspended') bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400
                                    @else bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 @endif">
                                    {{ __("messages.{$student->status}") }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('students.show', $student) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300 dark:hover:bg-indigo-900/60 text-xs font-semibold transition">
                                    {{ __('messages.view_360_profile') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="text-base font-medium">{{ __('messages.no_students_found') }}</p>
                                <p class="text-sm mt-1 text-slate-400">{{ __('messages.no_students_sub') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Create Student Drawer / Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex justify-end">
            <div class="w-full max-w-2xl bg-white dark:bg-slate-800 shadow-2xl min-h-screen p-6 overflow-y-auto space-y-6 animate-slide-in-right">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ __('messages.enroll_new_student') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('messages.enroll_subtitle') }}</p>
                    </div>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveStudent" class="space-y-6">
                    <!-- Personal Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3">{{ __('messages.personal_info') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.first_name') }} *</label>
                                <input wire:model="first_name" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                @error('first_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.middle_name') }}</label>
                                <input wire:model="middle_name" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.last_name') }} *</label>
                                <input wire:model="last_name" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                @error('last_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.dob') }} *</label>
                                <input wire:model="date_of_birth" type="date" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                @error('date_of_birth') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.gender') }} *</label>
                                <select wire:model="gender" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                    <option value="male">{{ __('messages.male') }}</option>
                                    <option value="female">{{ __('messages.female') }}</option>
                                    <option value="other">{{ __('messages.other') }}</option>
                                </select>
                                @error('gender') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.admission_no') }} *</label>
                                <input wire:model="admission_number" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm font-mono">
                                @error('admission_number') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.email') }}</label>
                                <input wire:model="email" type="email" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.phone') }}</label>
                                <input wire:model="phone" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Academic Info -->
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                        <h3 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3">{{ __('messages.academic_assignment') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.academic_year') }}</label>
                                <select wire:model="academic_year_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                    <option value="">{{ __('messages.select_academic_year') }}</option>
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.class') }}</label>
                                <select wire:model="class_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                    <option value="">{{ __('messages.select_class') }}</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.division') }}</label>
                                <select wire:model="division_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                    <option value="">{{ __('messages.select_division') }}</option>
                                    @foreach($divisions as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Guardian -->
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                        <h3 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3">{{ __('messages.primary_guardian_info') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.guardian_name') }} *</label>
                                <input wire:model="guardian_name" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                @error('guardian_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.relationship') }} *</label>
                                <select wire:model="guardian_relationship" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                    <option value="father">{{ __('messages.father') }}</option>
                                    <option value="mother">{{ __('messages.mother') }}</option>
                                    <option value="guardian">{{ __('messages.legal_guardian') }}</option>
                                    <option value="other">{{ __('messages.other') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.guardian_phone') }} *</label>
                                <input wire:model="guardian_phone" type="text" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                                @error('guardian_phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ __('messages.guardian_email') }}</label>
                                <input wire:model="guardian_email" type="email" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm transition">
                            {{ __('messages.save_student_record') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
