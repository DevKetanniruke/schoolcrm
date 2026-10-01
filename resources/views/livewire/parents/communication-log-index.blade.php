<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Communication Log Center</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Centralized log of all parent-school staff interactions (Emails, Phone Calls, Meetings, SMS).</p>
        </div>
        <div>
            <button wire:click="resetFilters" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">
                Reset All Filters
            </button>
        </div>
    </div>

    <!-- Multi-Filter Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Search Keywords</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search subject, summary, parent or child name..." class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
            </div>

            <!-- Interaction Type Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Type</label>
                <select wire:model.live="typeFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
                    <option value="">All Types</option>
                    <option value="email">Email</option>
                    <option value="phone_call">Phone Call</option>
                    <option value="in_person_meeting">In-Person Meeting</option>
                    <option value="sms">SMS</option>
                    <option value="portal_message">Portal Message</option>
                </select>
            </div>

            <!-- Direction Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Direction</label>
                <select wire:model.live="directionFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
                    <option value="">All Directions</option>
                    <option value="outbound">Outbound (School -> Parent)</option>
                    <option value="inbound">Inbound (Parent -> School)</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">From Date</label>
                <input wire:model.live="dateFrom" type="date" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">To Date</label>
                <input wire:model.live="dateTo" type="date" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100 dark:border-slate-700/60 text-xs">
            <!-- Parent Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Filter by Parent</label>
                <select wire:model.live="parentFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
                    <option value="">All Parents</option>
                    @foreach($parentsList as $pItem)
                        <option value="{{ $pItem->id }}">{{ $pItem->full_name }} ({{ $pItem->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Student Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Filter by Student/Child</label>
                <select wire:model.live="studentFilter" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-xs">
                    <option value="">All Students</option>
                    @foreach($studentsList as $sItem)
                        <option value="{{ $sItem->id }}">{{ $sItem->full_name }} ({{ $sItem->admission_number }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Communication Log Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Interaction Date</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Type & Direction</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Parent</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Child / Student</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Subject & Summary</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Logged Staff</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($communications as $comm)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300 font-mono">
                                {{ $comm->interaction_date ? $comm->interaction_date->format('Y-m-d H:i') : '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 capitalize">
                                    {{ str_replace('_', ' ', $comm->type) }}
                                </span>
                                <span class="text-[10px] block mt-0.5 text-slate-400 capitalize">{{ $comm->direction }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($comm->parent)
                                    <a href="{{ route('parents.show', $comm->parent) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600">
                                        {{ $comm->parent->full_name }}
                                    </a>
                                    <div class="text-xs text-slate-400">{{ $comm->parent->phone ?? $comm->parent->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($comm->student)
                                    <a href="{{ route('students.show', $comm->student) }}" class="font-medium text-slate-800 dark:text-slate-200 hover:text-indigo-600">
                                        {{ $comm->student->full_name }}
                                    </a>
                                    <div class="text-xs text-slate-400 font-mono">{{ $comm->student->admission_number }}</div>
                                @else
                                    <span class="text-xs text-slate-400 italic">General Parent Interaction</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700 dark:text-slate-300">
                                <div class="font-bold text-slate-900 dark:text-white mb-0.5 text-sm">{{ $comm->subject }}</div>
                                <div class="line-clamp-2 text-slate-600 dark:text-slate-300">{{ $comm->summary }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $comm->staff->name ?? 'System Staff' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                No communication logs found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($communications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                {{ $communications->links() }}
            </div>
        @endif
    </div>
</div>
