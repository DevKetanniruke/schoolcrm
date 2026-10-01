<div class="space-y-8">
    <!-- Top Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-purple-950 p-6 md:p-8 text-white shadow-2xl border border-indigo-900/40">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        🤖 AI Communication Assistant
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Configurable Staff Approval Required
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">AI-Assisted Parent Communication Studio</h1>
                <p class="text-sm text-slate-300 mt-1 max-w-3xl">
                    Generate, refine, preview, and approve multi-channel communication across Email, SMS, WhatsApp, and Portal Notifications in English, Marathi, and Hindi.
                </p>
            </div>
            @if($draft_id)
                <div class="flex flex-col items-end">
                    <span class="text-xs text-slate-400">Draft #{{ $draft_id }}</span>
                    @if($approval_status === 'approved')
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Approved by {{ $approved_by_name ?? 'Staff' }}
                        </span>
                    @elseif($approval_status === 'sent')
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/40 flex items-center mt-1">
                            🚀 Broadcast Dispatched
                        </span>
                    @elseif($approval_status === 'rejected')
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 flex items-center mt-1">
                            ❌ Rejected
                        </span>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center mt-1 animate-pulse">
                            ⏳ Pending Approval
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Main Workspace Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Panel: Input Parameters (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Communication Parameters
                </h2>

                <form wire:submit.prevent="generate" class="space-y-4">
                    <!-- Quick Topic Presets -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Quick Presets</label>
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <button type="button" wire:click="$set('topic', 'Annual Sports Day 2026')" class="text-[11px] px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition">🏆 Sports Day</button>
                            <button type="button" wire:click="$set('topic', 'Term 2 Examination Schedule')" class="text-[11px] px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition">📚 Exams</button>
                            <button type="button" wire:click="$set('topic', 'Parent-Teacher Meeting')" class="text-[11px] px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition">🤝 PTM Meet</button>
                            <button type="button" wire:click="$set('topic', 'School Fee Quarter 3 Reminder')" class="text-[11px] px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition">💳 Fee Reminder</button>
                        </div>
                    </div>

                    <!-- Topic -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Topic / Subject Title <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="topic" placeholder="e.g. Science Exhibition & Field Trip" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500" required />
                        @error('topic') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Audience -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Target Audience <span class="text-rose-500">*</span></label>
                        <select wire:model="audience" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach($audienceOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tone -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tone & Voice <span class="text-rose-500">*</span></label>
                        <select wire:model="tone" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach($toneOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Language Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Output Language <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($languageOptions as $langKey => $langLabel)
                                <button type="button" wire:click="$set('language', '{{ $langKey }}')" class="py-2 px-3 rounded-xl border text-xs font-bold transition {{ $language === $langKey ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/30' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                                    {{ $langLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Important Info -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Important Information / Key Details</label>
                        <textarea wire:model="important_info" rows="3" placeholder="Enter key dates, deadlines, time, venue, guidelines, or specific instructions..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Approval Requirement Rule Toggle -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Require Staff Approval</span>
                                <span class="text-[11px] text-slate-500">Must be approved before sending</span>
                            </div>
                            <input type="checkbox" wire:model="require_approval" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" />
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 hover:from-indigo-500 hover:to-purple-500 transition flex items-center justify-center space-x-2">
                        <svg wire:loading.remove class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Generate 5 Formats with AI</span>
                        <span wire:loading>Generating Content...</span>
                    </button>
                </form>
            </div>

            <!-- Recent Drafts List -->
            @if($recentDrafts->count() > 0)
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Recent AI Drafts</h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($recentDrafts as $rDraft)
                            <div wire:click="loadDraft({{ $rDraft->id }})" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition flex items-center justify-between {{ $draft_id === $rDraft->id ? 'bg-indigo-50/50 dark:bg-indigo-950/40 border-indigo-500/50' : '' }}">
                                <div class="truncate mr-2">
                                    <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $rDraft->topic }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $rDraft->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="flex items-center space-x-1.5 shrink-0">
                                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $rDraft->language }}</span>
                                    @if($rDraft->approval_status === 'approved')
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold">Approved</span>
                                    @elseif($rDraft->approval_status === 'sent')
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-600 dark:text-purple-300 font-bold">Sent</span>
                                    @else
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Panel: Multi-Channel Editors & Interactive Previews (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Channel Navigation Tabs -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 shadow-xl border border-slate-200 dark:border-slate-800">
                <div class="flex overflow-x-auto space-x-1 p-1 scrollbar-none">
                    <button type="button" wire:click="$set('active_preview_channel', 'email')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 {{ $active_preview_channel === 'email' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>✉️ Email Circular</span>
                    </button>

                    <button type="button" wire:click="$set('active_preview_channel', 'whatsapp')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 {{ $active_preview_channel === 'whatsapp' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>🟢 WhatsApp Ready</span>
                    </button>

                    <button type="button" wire:click="$set('active_preview_channel', 'sms')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 {{ $active_preview_channel === 'sms' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>📱 SMS Version</span>
                    </button>

                    <button type="button" wire:click="$set('active_preview_channel', 'short_notification')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 {{ $active_preview_channel === 'short_notification' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span>💬 Short Notification</span>
                    </button>

                    <button type="button" wire:click="$set('active_preview_channel', 'detailed_message')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 {{ $active_preview_channel === 'detailed_message' ? 'bg-slate-800 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>📜 Portal Circular</span>
                    </button>
                </div>
            </div>

            <!-- Content Editor & Live Simulator Area -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-6">
                
                @if($active_preview_channel === 'email')
                    <!-- Email Editor & Preview -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">✉️ Email Content Editor</h3>
                            <span class="text-xs text-slate-400">Staff Editable</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Email Subject Line</label>
                            <input type="text" wire:model="email_subject" placeholder="Email subject line..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm font-semibold focus:ring-2 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Email Body Content</label>
                            <textarea wire:model="email_body" rows="8" placeholder="Email body content..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm font-mono focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>

                        <!-- Live Desktop Email Preview Card -->
                        <div class="border border-slate-300 dark:border-slate-700 rounded-xl overflow-hidden shadow-inner bg-slate-50 dark:bg-slate-950">
                            <div class="bg-slate-200 dark:bg-slate-800 px-4 py-2 flex items-center space-x-2 border-b border-slate-300 dark:border-slate-700">
                                <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <span class="text-xs font-mono text-slate-600 dark:text-slate-400 ml-2">Email Live Client Simulator</span>
                            </div>
                            <div class="p-5 space-y-3 text-slate-800 dark:text-slate-200">
                                <div class="border-b border-slate-200 dark:border-slate-800 pb-2">
                                    <div class="text-xs text-slate-400">Subject:</div>
                                    <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $email_subject ?: 'No subject entered yet' }}</div>
                                </div>
                                <div class="text-xs text-slate-400">From: SmartSchool Academy &lt;notifications@smartschool.edu&gt;</div>
                                <div class="text-xs text-slate-400">To: {{ $audienceOptions[$audience] ?? $audience }}</div>
                                <div class="prose prose-sm dark:prose-invert max-w-none pt-2 whitespace-pre-line text-sm">
                                    {{ $email_body ?: 'Generate AI content to preview email body here.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($active_preview_channel === 'whatsapp')
                    <!-- WhatsApp Editor & Simulator -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">🟢 WhatsApp Ready Message</h3>
                            <span class="text-xs text-slate-400">Supports *bold*, _italic_, emojis</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">WhatsApp Text Copy</label>
                            <textarea wire:model="whatsapp_version" rows="7" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm font-mono focus:ring-2 focus:ring-emerald-500"></textarea>
                        </div>

                        <!-- WhatsApp Smartphone Simulator -->
                        <div class="max-w-md mx-auto rounded-3xl overflow-hidden border-4 border-slate-800 shadow-2xl bg-[#0b141a] text-slate-100 font-sans">
                            <div class="bg-[#202c33] p-3 flex items-center space-x-3 border-b border-slate-700">
                                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-white text-xs">S</div>
                                <div>
                                    <div class="text-xs font-bold text-white">SmartSchool Parent Broadcast</div>
                                    <div class="text-[10px] text-emerald-400">Official Verified Account</div>
                                </div>
                            </div>
                            <div class="p-4 bg-[radial-gradient(#1f2c34_1px,transparent_1px)] [background-size:16px_16px] min-h-[200px]">
                                <div class="bg-[#005c4b] p-3 rounded-2xl rounded-tl-none max-w-[90%] text-xs shadow-md space-y-1 text-emerald-50 whitespace-pre-line">
                                    {{ $whatsapp_version ?: 'Generate content to view WhatsApp message bubble preview.' }}
                                    <div class="text-[9px] text-emerald-200/70 text-right mt-1">11:42 AM ✓✓</div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($active_preview_channel === 'sms')
                    <!-- SMS Editor & Phone Simulator -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">📱 SMS Version Editor</h3>
                            <span class="text-xs font-mono text-indigo-400">{{ strlen($sms_version) }} characters</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">SMS Message Text (160 char limit suggested)</label>
                            <textarea wire:model="sms_version" rows="4" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <!-- Mobile SMS Bubble Simulator -->
                        <div class="max-w-md mx-auto rounded-3xl overflow-hidden border-4 border-slate-800 shadow-2xl bg-slate-900 text-white p-4 space-y-2">
                            <div class="text-center text-[10px] text-slate-500 uppercase tracking-widest mb-2">Today SMS Notification</div>
                            <div class="bg-blue-600 p-3.5 rounded-2xl rounded-tr-none text-xs text-white shadow-lg whitespace-pre-line">
                                {{ $sms_version ?: 'Generate content to view SMS text preview.' }}
                            </div>
                        </div>
                    </div>

                @elseif($active_preview_channel === 'short_notification')
                    <!-- Short Notification Editor -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">💬 Short Push Notification</h3>
                            <span class="text-xs text-slate-400">In-App & Mobile Push Digest</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Notification Banner Alert Text</label>
                            <textarea wire:model="short_notification" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>

                        <!-- Floating Push Banner Simulator -->
                        <div class="p-4 rounded-2xl bg-slate-900/90 text-white border border-slate-700 shadow-xl max-w-md mx-auto flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-600 flex items-center justify-center font-bold text-white shrink-0">🔔</div>
                            <div>
                                <div class="text-xs font-bold text-slate-100">SmartSchool Parent App</div>
                                <div class="text-xs text-slate-300 mt-0.5 whitespace-pre-line">{{ $short_notification ?: 'Short push alert preview will render here.' }}</div>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- Detailed Circular Message Editor -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">📜 Detailed Portal Circular</h3>
                            <span class="text-xs text-slate-400">Parent Dashboard & Notice Board</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Full Detailed Notice Text</label>
                            <textarea wire:model="detailed_message" rows="8" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-slate-500"></textarea>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                            <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mb-2">Notice Board Preview</h4>
                            <div class="text-sm whitespace-pre-line text-slate-800 dark:text-slate-200">
                                {{ $detailed_message ?: 'Generate content to view full detailed circular.' }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Workflow Action Bar (Edit, Save, Approve, Send) -->
                <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
                    
                    <button type="button" wire:click="saveEdit" class="w-full md:w-auto px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        <span>Save Content Edits</span>
                    </button>

                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        @if($approval_status !== 'approved' && $approval_status !== 'sent')
                            <button type="button" wire:click="approve" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 hover:bg-emerald-500 transition flex items-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Approve Communication</span>
                            </button>

                            <button type="button" wire:click="reject" class="px-4 py-2.5 rounded-xl bg-rose-600/20 text-rose-300 border border-rose-500/40 font-bold text-xs hover:bg-rose-600/30 transition">
                                Reject Draft
                            </button>
                        @endif

                        <!-- Dispatch Send Button (With strict approval check enforcement) -->
                        <button type="button" wire:click="send" class="w-full md:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs shadow-lg shadow-purple-600/30 hover:from-purple-500 hover:to-indigo-500 transition flex items-center justify-center space-x-2 {{ ($require_approval && $approval_status !== 'approved' && $approval_status !== 'sent') ? 'opacity-80' : '' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>
                                @if($require_approval && $approval_status !== 'approved' && $approval_status !== 'sent')
                                    🔒 Send (Requires Approval)
                                @else
                                    🚀 Dispatch & Send Now
                                @endif
                            </span>
                        </button>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
