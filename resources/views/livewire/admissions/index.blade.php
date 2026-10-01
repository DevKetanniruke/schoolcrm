<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Admission Leads Directory</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Search and manage student leads, counselor assignments, and multi-channel attribution.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('admissions.pipeline') }}" class="inline-flex items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-900/40 px-4 py-2.5 text-sm font-semibold text-purple-700 dark:text-purple-300 shadow-sm hover:bg-purple-100 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                Visual Pipeline Kanban
            </a>
            @can('create', App\Models\AdmissionLead::class)
                <button wire:click="createLead" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create Admission Lead
                </button>
            @endcan
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search lead code, student, parent, email, phone..." class="w-full pl-4 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Stage Filter -->
            <div>
                <select wire:model.live="stage" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Pipeline Stages</option>
                    <option value="new_lead">New Lead</option>
                    <option value="contacted">Contacted</option>
                    <option value="counselling">Counselling</option>
                    <option value="campus_visit">Campus Visit</option>
                    <option value="application_started">Application Started</option>
                    <option value="documents_pending">Documents Pending</option>
                    <option value="application_submitted">Application Submitted</option>
                    <option value="under_review">Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="admission_confirmed">Admission Confirmed</option>
                    <option value="lost">Lost</option>
                </select>
            </div>

            <!-- Channel Source Filter -->
            <div>
                <select wire:model.live="source_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Channels</option>
                    @foreach($sources as $src)
                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Counselor Filter -->
            <div>
                <select wire:model.live="counselor_id" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Counselors</option>
                    @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Applicant & Parent</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Lead Code</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Grade Level</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Channel Source</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Counselor</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Pipeline Stage</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">
                                    <a href="{{ route('admissions.leads.show', $lead) }}" class="hover:text-indigo-600">
                                        {{ $lead->student_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Parent: {{ $lead->parent_name }} ({{ $lead->parent_phone }})</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $lead->lead_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ $lead->gradeApplyingFor->name ?? 'Grade 5' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                                <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 font-medium">
                                    {{ $lead->source->name ?? 'Direct' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-700 dark:text-slate-300">
                                {{ $lead->counselor->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $lead->stage === 'admission_confirmed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                       ($lead->stage === 'lost' ? 'bg-slate-200 text-slate-700' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300') }}">
                                    {{ str_replace('_', ' ', $lead->stage) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                <a href="{{ route('admissions.leads.show', $lead) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-300 transition">
                                    View 360° Workspace
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">No admission leads found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

    <!-- Create Lead Drawer -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex justify-end">
            <div class="w-full max-w-xl bg-white dark:bg-slate-800 h-full min-h-screen p-6 shadow-2xl flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Register Admission Lead</h2>
                        <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveLead" class="mt-6 space-y-4 text-xs">
                        <div class="space-y-3">
                            <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-indigo-600">Parent / Guardian Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Parent Full Name *</label>
                                    <input wire:model="parent_name" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                    @error('parent_name') <span class="text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Parent Phone Number *</label>
                                    <input wire:model="parent_phone" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                    @error('parent_phone') <span class="text-rose-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Parent Email Address *</label>
                                <input wire:model="parent_email" type="email" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                @error('parent_email') <span class="text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-indigo-600">Student Applicant Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Student Applicant Name *</label>
                                    <input wire:model="student_name" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                    @error('student_name') <span class="text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Applying Grade Level</label>
                                    <select wire:model="new_grade_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                        <option value="">Select Grade Level</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-indigo-600">Attribution & Counselor Assignment</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lead Channel Source</label>
                                    <select wire:model="new_source_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                        <option value="">Select Channel</option>
                                        @foreach($sources as $src)
                                            <option value="{{ $src->id }}">{{ $src->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Campaign</label>
                                    <select wire:model="new_campaign_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                        <option value="">Select Campaign</option>
                                        @foreach($campaigns as $camp)
                                            <option value="{{ $camp->id }}">{{ $camp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Assigned Counselor</label>
                                    <select wire:model="new_counselor_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 py-2 px-3 text-slate-900 dark:text-white">
                                        <option value="">Select Counselor</option>
                                        @foreach($counselors as $counselor)
                                            <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                            <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-500 shadow-md">Register Lead</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
