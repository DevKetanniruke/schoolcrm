<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Communication CRM Command Center</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Multi-channel messaging hub for In-App, Email, SMS, and WhatsApp broadcasts with delivery status tracking.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('communication.composer') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                New Broadcast Composer
            </a>
            <a href="{{ route('communication.templates') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition">
                Templates Manager
            </a>
            <a href="{{ route('communication.logs') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition">
                Delivery Logs
            </a>
        </div>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Dispatches -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Total Dispatches</span>
                <span class="p-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalDispatches }}</div>
            <div class="text-xs text-slate-500">Sent & queued broadcasts</div>
        </div>

        <!-- Success Delivery Rate -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Delivery Success</span>
                <span class="p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $successRate }}%</div>
            <div class="text-xs text-slate-500">{{ $deliveredCount }} delivered / read</div>
        </div>

        <!-- Failed Dispatches -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Failed Deliveries</span>
                <span class="p-1.5 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $failedCount }}</div>
            <div class="text-xs text-slate-500">Requires retry / email fix</div>
        </div>

        <!-- Scheduled Queued -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Scheduled Queued</span>
                <span class="p-1.5 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $queuedScheduled }}</div>
            <div class="text-xs text-slate-500">Delayed queue dispatches</div>
        </div>

        <!-- Active Templates -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider">
                <span>Active Templates</span>
                <span class="p-1.5 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $activeTemplatesCount }}</div>
            <div class="text-xs text-slate-500">Reusable message formats</div>
        </div>
    </div>

    <!-- Channel Breakdown & Recent Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Channel Distribution Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Multi-Channel Volume</h3>
            <div class="space-y-3">
                <!-- Email -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center font-bold text-xs">
                            EMAIL
                        </div>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Email Broadcasts</span>
                    </div>
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $channelStats['email'] }}</span>
                </div>
                <!-- SMS -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 flex items-center justify-center font-bold text-xs">
                            SMS
                        </div>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">SMS Text Alerts</span>
                    </div>
                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ $channelStats['sms'] }}</span>
                </div>
                <!-- WhatsApp -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">
                            WA
                        </div>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">WhatsApp Messages</span>
                    </div>
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $channelStats['whatsapp'] }}</span>
                </div>
                <!-- In-App -->
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 flex items-center justify-center font-bold text-xs">
                            APP
                        </div>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">In-App Notifications</span>
                    </div>
                    <span class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ $channelStats['in_app'] }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Communications Table -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Communication Broadcasts</h3>
                <a href="{{ route('communication.logs') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">View All Logs &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                    <thead>
                        <tr>
                            <th class="py-2.5 text-left text-slate-400 font-semibold uppercase">Broadcast Title</th>
                            <th class="py-2.5 text-left text-slate-400 font-semibold uppercase">Channel</th>
                            <th class="py-2.5 text-left text-slate-400 font-semibold uppercase">Audience</th>
                            <th class="py-2.5 text-center text-slate-400 font-semibold uppercase">Recipients</th>
                            <th class="py-2.5 text-right text-slate-400 font-semibold uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @forelse($recentDispatches as $comm)
                            <tr>
                                <td class="py-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $comm->title }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $comm->created_at->format('M d, Y h:i A') }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $comm->channel === 'email' ? 'bg-blue-100 text-blue-800' : ($comm->channel === 'whatsapp' ? 'bg-emerald-100 text-emerald-800' : ($comm->channel === 'sms' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800')) }}">
                                        {{ $comm->channel }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-600 dark:text-slate-300 font-medium uppercase text-[10px]">
                                    {{ str_replace('_', ' ', $comm->recipient_type) }}
                                </td>
                                <td class="py-3 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $comm->total_recipients }}
                                </td>
                                <td class="py-3 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $comm->status === 'sent' ? 'bg-emerald-100 text-emerald-800' : ($comm->status === 'queued' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $comm->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400 italic">No communication broadcasts sent yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
