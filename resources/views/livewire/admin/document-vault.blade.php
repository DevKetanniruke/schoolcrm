<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <!-- Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-blue-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-wide">
                        Digital Document Vault
                    </h1>
                    <p class="text-sm text-slate-400">
                        Secure non-public private storage, version control, retention policies & access audit trail
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <button 
                wire:click="openPolicyModal"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium text-sm rounded-lg transition-all flex items-center space-x-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Retention Policies</span>
            </button>

            <button 
                wire:click="openUploadModal"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-lg shadow-lg shadow-indigo-600/30 transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Upload Document</span>
            </button>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-slate-800/80 backdrop-blur-md border border-slate-700/60 rounded-xl p-4 mb-6 shadow-xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <!-- Search -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Search Documents</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title, file name..." class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-3 text-slate-200 focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Document Category</label>
                <select wire:model.live="categoryFilter" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200">
                    <option value="">All Categories</option>
                    @foreach($categories as $catKey => $catLabel)
                        <option value="{{ $catKey }}">{{ $catLabel }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Verification Status Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Verification Status</label>
                <select wire:model.live="statusFilter" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200">
                    <option value="">All Statuses</option>
                    <option value="pending">🟡 Pending Verification</option>
                    <option value="verified">🟢 Verified</option>
                    <option value="rejected">🔴 Rejected</option>
                    <option value="expired">⚪ Expired</option>
                </select>
            </div>

            <!-- Entity Owner Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Belongs To (Entity)</label>
                <select wire:model.live="ownerTypeFilter" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200">
                    <option value="">All Belongings</option>
                    @foreach($ownerTypes as $oKey => $oLabel)
                        <option value="{{ $oKey }}">{{ $oLabel }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Confidential Filter -->
            <div>
                <label class="block text-xs text-slate-400 mb-1">Confidentiality</label>
                <select wire:model.live="confidentialFilter" class="w-full bg-slate-900 border border-slate-700 rounded-lg text-xs py-2 px-2.5 text-slate-200">
                    <option value="">All Documents</option>
                    <option value="1">🔒 Confidential Only</option>
                    <option value="0">🔓 Normal Only</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Document Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
        @forelse($documents as $doc)
            <div class="bg-slate-800/90 border border-slate-700/60 rounded-xl p-5 shadow-lg flex flex-col justify-between hover:border-indigo-500/50 transition-all duration-200">
                <div>
                    <!-- Top Status Row -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ 
                            $doc->verification_status === 'verified' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : (
                            $doc->verification_status === 'rejected' ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : (
                            $doc->verification_status === 'expired' ? 'bg-slate-700 text-slate-300 border-slate-600' : 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                            )) }}">
                            {{ ucfirst($doc->verification_status) }}
                        </span>

                        <div class="flex items-center space-x-1.5">
                            @if($doc->is_confidential)
                                <span class="px-2 py-0.5 text-[10px] font-mono bg-purple-500/20 text-purple-300 border border-purple-500/30 rounded">
                                    🔒 Confidential
                                </span>
                            @endif
                            <span class="px-2 py-0.5 text-[10px] font-mono bg-slate-900 text-indigo-400 border border-slate-700 rounded">
                                v{{ $doc->version }}
                            </span>
                        </div>
                    </div>

                    <!-- Title & File Name -->
                    <h3 class="text-base font-bold text-white tracking-wide truncate mb-1" title="{{ $doc->title }}">
                        {{ $doc->title }}
                    </h3>
                    <p class="text-xs font-mono text-slate-400 truncate mb-3" title="{{ $doc->file_name }}">
                        {{ $doc->file_name }}
                    </p>

                    <!-- Meta Tags -->
                    <div class="space-y-1.5 text-xs text-slate-300 mb-4 bg-slate-900/60 p-3 rounded-lg border border-slate-700/40">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Category:</span>
                            <span class="font-medium text-slate-200">{{ $categories[$doc->category] ?? $doc->category }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Belongs To:</span>
                            <span class="font-medium text-indigo-400 truncate max-w-[140px]">
                                {{ class_basename($doc->documentable_type) ?: 'School' }} #{{ $doc->documentable_id ?: 'Root' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Size:</span>
                            <span class="font-mono text-slate-300">{{ round($doc->file_size / 1024, 1) }} KB</span>
                        </div>
                        @if($doc->expiry_date)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Expires:</span>
                                <span class="font-medium text-rose-400">{{ $doc->expiry_date->toDateString() }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons Footer -->
                <div class="pt-3 border-t border-slate-700/50 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Preview Route -->
                        <a href="{{ route('documents.preview', $doc->id) }}" target="_blank" class="px-2.5 py-1.5 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 text-xs font-semibold rounded-lg border border-indigo-500/30 transition text-center flex items-center justify-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Preview</span>
                        </a>

                        <!-- Download Route -->
                        <a href="{{ route('documents.download', $doc->id) }}" class="px-2.5 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition text-center flex items-center justify-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Download</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5">
                        <button wire:click="openVersionModal({{ $doc->id }})" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-300 text-[11px] rounded transition" title="Add Version">
                            + Version
                        </button>
                        <button wire:click="openVerificationModal({{ $doc->id }})" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-300 text-[11px] rounded transition" title="Verify Status">
                            Verify
                        </button>
                        <button wire:click="openAuditModal({{ $doc->id }})" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-300 text-[11px] rounded transition" title="Audit Trail">
                            Audit
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-800/40 border border-slate-700/50 rounded-xl p-12 text-center">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-base font-bold text-slate-300 mb-1">No Digital Documents Found</h3>
                <p class="text-xs text-slate-500">Upload new documents into private storage or clear active search filters.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $documents->links() }}
    </div>

    <!-- 1. Upload Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h2 class="text-lg font-bold text-white">Upload Private Digital Document</h2>
                    <button wire:click="$set('showUploadModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="storeDocument" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Document Title *</label>
                        <input type="text" wire:model="title" placeholder="e.g. Birth Certificate - Rohan Sharma" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Category *</label>
                            <select wire:model="category" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-2.5 text-slate-200">
                                @foreach($categories as $catKey => $catLabel)
                                    <option value="{{ $catKey }}">{{ $catLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Belongs To Entity</label>
                            <select wire:model="documentable_type" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-2.5 text-slate-200">
                                <option value="">School Institutional</option>
                                @foreach($ownerTypes as $oKey => $oLabel)
                                    <option value="{{ $oKey }}">{{ $oLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($documentable_type)
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">{{ $documentable_type }} ID #</label>
                            <input type="number" wire:model="documentable_id" placeholder="Enter entity Record ID (e.g. 1)" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Expiry Date (Optional)</label>
                            <input type="date" wire:model="expiry_date" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_confidential" class="rounded bg-slate-800 border-slate-700 text-purple-600 focus:ring-purple-500">
                                <span class="text-slate-300 font-semibold">🔒 Mark Confidential</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Select File (PDF, PNG, JPG, DOCX - Private Storage) *</label>
                        <input type="file" wire:model="file" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showUploadModal', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg">Save Private Document</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 2. New Version Modal -->
    @if($showVersionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h2 class="text-lg font-bold text-white">Upload New Document Version</h2>
                    <button wire:click="$set('showVersionModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="storeNewVersion" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">New File *</label>
                        <input type="file" wire:model="newVersionFile" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Version Change Notes</label>
                        <textarea wire:model="versionNotes" rows="3" placeholder="Reason for uploading new version..." class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showVersionModal', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg">Upload Version</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 3. Verification Modal -->
    @if($showVerificationModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h2 class="text-lg font-bold text-white">Document Verification Control</h2>
                    <button wire:click="$set('showVerificationModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="updateVerification" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Verification Status *</label>
                        <select wire:model="verificationStatus" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                            <option value="pending">🟡 Pending</option>
                            <option value="verified">🟢 Verified</option>
                            <option value="rejected">🔴 Rejected</option>
                            <option value="expired">⚪ Expired</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Verification Remarks / Reason</label>
                        <textarea wire:model="verificationNotes" rows="3" placeholder="Notes explaining verification status..." class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showVerificationModal', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg">Save Verification</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 4. Retention Policy Modal -->
    @if($showPolicyModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h2 class="text-lg font-bold text-white">Configurable Retention Policies</h2>
                    <button wire:click="$set('showPolicyModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="saveRetentionPolicy" class="space-y-4 text-xs border-b border-slate-800 pb-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Category</label>
                            <select wire:model="policyCategory" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-2.5 text-slate-200">
                                @foreach($categories as $catKey => $catLabel)
                                    <option value="{{ $catKey }}">{{ $catLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Retention Period (Months)</label>
                            <input type="number" wire:model="policyMonths" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:model="policyAutoArchive" class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                            <span class="text-slate-300">Auto Archive</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:model="policyAutoDelete" class="rounded bg-slate-800 border-slate-700 text-rose-600">
                            <span class="text-slate-300">Auto Delete On Expiry</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg">Update Retention Policy</button>
                </form>

                <!-- Active Policy List -->
                <div class="text-xs space-y-2 max-h-48 overflow-y-auto">
                    <h4 class="font-bold text-slate-300 uppercase tracking-wider">Active Category Retention Rules</h4>
                    @foreach($policies as $pol)
                        <div class="flex justify-between items-center bg-slate-800/60 p-2.5 rounded-lg border border-slate-700/40">
                            <span class="text-slate-200 font-medium">{{ $categories[$pol->document_category] ?? $pol->document_category }}</span>
                            <span class="font-mono text-indigo-400 font-bold">{{ $pol->retention_period_months }} Months</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- 5. Audit Trail & Detail Modal -->
    @if($showAuditModal && $activeDocument)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-2xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ $activeDocument->title }}</h2>
                        <p class="text-xs text-slate-400 font-mono">File: {{ $activeDocument->file_name }} | Version: v{{ $activeDocument->version }}</p>
                    </div>
                    <button wire:click="$set('showAuditModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <!-- Version History List -->
                <div class="text-xs space-y-2">
                    <h3 class="font-bold text-indigo-400 uppercase tracking-wider">Version History ({{ $activeDocument->versions->count() }} versions)</h3>
                    <div class="space-y-1.5">
                        @foreach($activeDocument->versions as $ver)
                            <div class="flex justify-between items-center bg-slate-800/70 p-2.5 rounded-lg border border-slate-700/50">
                                <div>
                                    <span class="font-bold text-white">v{{ $ver->version_number }}</span> - <span class="font-mono text-slate-300">{{ $ver->file_name }}</span>
                                    <p class="text-[11px] text-slate-400">{{ $ver->notes ?? 'No notes' }}</p>
                                </div>
                                <div class="text-right text-[11px] text-slate-400">
                                    <span>{{ $ver->created_at->toDateTimeString() }}</span>
                                    <p>By {{ $ver->uploader?->name ?? 'User' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Activity Audit Trail Timeline -->
                <div class="text-xs space-y-2 pt-3 border-t border-slate-800">
                    <h3 class="font-bold text-emerald-400 uppercase tracking-wider">Access Activity Audit Trail</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($activeDocument->activityLogs as $log)
                            <div class="flex items-start space-x-3 bg-slate-800/40 p-2.5 rounded-lg border border-slate-700/30">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                    {{ $log->activity_type }}
                                </span>
                                <div class="flex-1">
                                    <p class="text-slate-200 font-medium">{{ $log->description }}</p>
                                    <p class="text-[10px] text-slate-500">By: {{ $log->user?->name ?? 'System' }} | IP: {{ $log->ip_address }} | {{ $log->created_at->toDateTimeString() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
