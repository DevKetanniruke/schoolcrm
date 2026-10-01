<div class="p-6 bg-slate-900 min-h-screen text-slate-100">
    <!-- Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-purple-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-wide">
                        Centralized Notification Engine
                    </h1>
                    <p class="text-sm text-slate-400">
                        Multi-channel queued messaging, templates, preferences & automated retry mechanism
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-slate-800 text-indigo-400 border border-indigo-500/30">
                ⚡ Asynchronous Laravel Queue Worker Active
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-800 mb-6 pb-2">
        <button 
            wire:click="$set('activeTab', 'broadcast')"
            class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-all flex items-center space-x-2 {{ $activeTab === 'broadcast' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>📢 Multi-Channel Broadcast</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'templates')"
            class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-all flex items-center space-x-2 {{ $activeTab === 'templates' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>📜 Templates Library ({{ $templates->count() }})</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'preferences')"
            class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-all flex items-center space-x-2 {{ $activeTab === 'preferences' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>⚙ Notification Preferences</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'logs')"
            class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-all flex items-center space-x-2 {{ $activeTab === 'logs' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>📊 Delivery Logs & Retries</span>
        </button>
    </div>

    <!-- Tab 1: Broadcast Composer -->
    @if($activeTab === 'broadcast')
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-6 shadow-xl max-w-3xl">
            <h2 class="text-base font-bold text-white mb-4 flex items-center space-x-2">
                <span>Queue Asynchronous Broadcast Notification</span>
            </h2>

            <form wire:submit.prevent="sendBroadcastNotification" class="space-y-5 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Select Pre-Defined Template (Optional)</label>
                    <select wire:model.live="selected_template_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 px-3 text-slate-200">
                        <option value="">-- Custom Notification (No Template) --</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">{{ $tpl->title }} [{{ strtoupper($tpl->code) }}]</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Notification Title *</label>
                        <input type="text" wire:model="title" placeholder="e.g. Fee Reminder Notice" class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 px-3 text-slate-200">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Subject (Email/Push)</label>
                        <input type="text" wire:model="subject" placeholder="e.g. Important Update regarding Fee Payment" class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 px-3 text-slate-200">
                    </div>
                </div>

                <!-- Channels Checkboxes -->
                <div>
                    <label class="block text-slate-400 mb-2 font-semibold">Delivery Channels (Multi-Channel Dispatch) *</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($availableChannels as $chKey => $chLabel)
                            <label class="flex items-center space-x-2 bg-slate-900 p-2.5 rounded-lg border border-slate-700/60 cursor-pointer hover:border-indigo-500/50">
                                <input type="checkbox" wire:model="selected_channels" value="{{ $chKey }}" class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-slate-200 font-medium text-[11px]">{{ $chLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Message Body -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-slate-400 font-semibold">Message Content Body *</label>
                        <span class="text-[11px] text-indigo-400 font-mono">Placeholders: &#123;&#123;user_name&#125;&#125;, &#123;&#123;student_name&#125;&#125;, &#123;&#123;school_name&#125;&#125;, &#123;&#123;date&#125;&#125;</span>
                    </div>
                    <textarea wire:model="message_body" rows="5" placeholder="Dear &#123;&#123;user_name&#125;&#125;, please note that..." class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 px-3 text-slate-200 font-mono text-xs"></textarea>
                </div>

                <!-- Scheduled At -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Schedule Dispatch (Optional Future Date)</label>
                        <input type="datetime-local" wire:model="scheduled_at" class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 px-3 text-slate-200">
                        <span class="text-[10px] text-slate-500">Leave blank to dispatch immediately to queue.</span>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Target Audience</label>
                        <select wire:model="recipient_type" class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 px-3 text-slate-200">
                            <option value="all_users">All Registered System Users</option>
                            <option value="parents">All Parents</option>
                            <option value="staff">All Teachers & Staff</option>
                            <option value="leads">All Admission Leads</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-700/50 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg shadow-lg shadow-indigo-600/30 transition text-xs flex items-center space-x-2">
                        <span>Dispatch to Queue</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>

    <!-- Tab 2: Templates Library -->
    @elseif($activeTab === 'templates')
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-white">Pre-Defined Notification Templates</h2>
                <button wire:click="$set('showTemplateModal', true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow">
                    + Create Template
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($templates as $tpl)
                    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 shadow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 text-[10px] font-mono bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded uppercase font-bold">
                                    {{ $tpl->code }}
                                </span>
                                <span class="text-xs text-slate-400 capitalize">{{ $tpl->category }}</span>
                            </div>

                            <h3 class="text-sm font-bold text-white mb-2">{{ $tpl->title }}</h3>
                            <p class="text-xs text-slate-300 font-mono bg-slate-900/60 p-3 rounded-lg border border-slate-700/40 whitespace-pre-wrap">
                                {{ Str::limit($tpl->content_body, 120) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-slate-800/40 p-8 rounded-xl text-center text-slate-500 text-xs">
                        No notification templates defined yet. Click "+ Create Template" to add one.
                    </div>
                @endforelse
            </div>
        </div>

    <!-- Tab 3: Notification Preferences -->
    @elseif($activeTab === 'preferences')
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-6 shadow-xl max-w-xl">
            <h2 class="text-base font-bold text-white mb-4">Notification Channel Preferences</h2>
            <p class="text-xs text-slate-400 mb-6">Manage which communication channels you wish to receive notifications through.</p>

            <form wire:submit.prevent="saveMyPreferences" class="space-y-4 text-xs">
                <div class="space-y-3">
                    <label class="flex items-center justify-between bg-slate-900 p-3 rounded-lg border border-slate-700">
                        <span class="text-slate-200 font-semibold">📥 Database (In-App Notifications)</span>
                        <input type="checkbox" wire:model="pref_in_app" class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                    </label>

                    <label class="flex items-center justify-between bg-slate-900 p-3 rounded-lg border border-slate-700">
                        <span class="text-slate-200 font-semibold">✉️ Email Alerts</span>
                        <input type="checkbox" wire:model="pref_email" class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                    </label>

                    <label class="flex items-center justify-between bg-slate-900 p-3 rounded-lg border border-slate-700">
                        <span class="text-slate-200 font-semibold">📱 SMS Messages</span>
                        <input type="checkbox" wire:model="pref_sms" class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                    </label>

                    <label class="flex items-center justify-between bg-slate-900 p-3 rounded-lg border border-slate-700">
                        <span class="text-slate-200 font-semibold">💬 WhatsApp Notifications</span>
                        <input type="checkbox" wire:model="pref_whatsapp" class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                    </label>

                    <label class="flex items-center justify-between bg-slate-900 p-3 rounded-lg border border-slate-700">
                        <span class="text-slate-200 font-semibold">🔔 Mobile Push Notifications</span>
                        <input type="checkbox" wire:model="pref_push" class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-700/50 flex justify-end">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-xs">
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>

    <!-- Tab 4: Delivery Logs & Retries -->
    @elseif($activeTab === 'logs')
        <div class="space-y-4">
            <!-- Log Filters -->
            <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-4 shadow flex flex-wrap gap-3 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1">Filter Channel</label>
                    <select wire:model.live="logChannelFilter" class="bg-slate-900 border border-slate-700 rounded-lg py-1.5 px-3 text-slate-200">
                        <option value="">All Channels</option>
                        <option value="database">Database (In-App)</option>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="push">Push</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Filter Delivery Status</label>
                    <select wire:model.live="logStatusFilter" class="bg-slate-900 border border-slate-700 rounded-lg py-1.5 px-3 text-slate-200">
                        <option value="">All Statuses</option>
                        <option value="delivered">🟢 Delivered</option>
                        <option value="pending">🟡 Pending / Queued</option>
                        <option value="failed">🔴 Failed</option>
                        <option value="skipped_by_preference">⚪ Skipped By Preference</option>
                    </select>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Recipient</th>
                            <th class="py-3 px-4">Channel</th>
                            <th class="py-3 px-4">Provider ID</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Retries</th>
                            <th class="py-3 px-4">Last Attempt</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($deliveryLogs as $log)
                            <tr class="hover:bg-slate-800/40">
                                <td class="py-3 px-4">
                                    <span class="font-medium text-white block">{{ $log->recipient_email ?: $log->recipient_phone ?: 'User #' . $log->recipient_id }}</span>
                                    <span class="text-[10px] text-slate-500 uppercase">{{ $log->recipient_type }}</span>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-indigo-400">
                                    {{ strtoupper($log->channel) }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">
                                    {{ $log->provider_message_id ?: 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ 
                                        $log->delivery_status === 'delivered' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : (
                                        $log->delivery_status === 'failed' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : (
                                        $log->delivery_status === 'skipped_by_preference' ? 'bg-slate-700 text-slate-300 border border-slate-600' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30'
                                        )) }}">
                                        {{ $log->delivery_status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    {{ $log->retry_count ?? 0 }} / {{ $log->max_retries ?? 3 }}
                                </td>
                                <td class="py-3 px-4 text-slate-400 text-[11px]">
                                    {{ $log->last_attempt_at ? $log->last_attempt_at->toTimeString() : 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($log->delivery_status === 'failed' && ($log->retry_count < $log->max_retries))
                                        <button wire:click="retryLog({{ $log->id }})" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-[11px] rounded shadow">
                                            🔄 Retry
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">
                                    No delivery log records matching filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $deliveryLogs->links() }}
            </div>
        </div>
    @endif

    <!-- Create Template Modal -->
    @if($showTemplateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <h2 class="text-lg font-bold text-white">Create Notification Template</h2>
                    <button wire:click="$set('showTemplateModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="createTemplate" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Template Title *</label>
                        <input type="text" wire:model="tpl_title" placeholder="e.g. Exam Schedule Release" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Template Code *</label>
                            <input type="text" wire:model="tpl_code" placeholder="e.g. TPL-EXAM-NOTICE" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200 uppercase font-mono">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1 font-semibold">Category</label>
                            <select wire:model="tpl_category" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-2.5 text-slate-200">
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="attendance">Attendance</option>
                                <option value="fees">Fees</option>
                                <option value="events">Events</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Content Body (with {{user_name}}, {{student_name}}, {{school_name}}) *</label>
                        <textarea wire:model="tpl_content_body" rows="4" placeholder="Dear {{user_name}}, please find the exam schedule attached..." class="w-full bg-slate-800 border border-slate-700 rounded-lg py-2 px-3 text-slate-200 font-mono"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showTemplateModal', false)" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
