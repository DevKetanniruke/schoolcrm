<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Communication Delivery Logs & Audit Tracker</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Detailed per-recipient delivery statuses across Email, SMS, WhatsApp, and In-App channels.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('communication.dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition">
                Command Center
            </a>
            <a href="{{ route('communication.composer') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                + New Composer
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-200/60 dark:border-slate-700/60 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search email, phone, or provider ID..." class="w-full pl-4 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
            </div>
            <div>
                <select wire:model.live="channel" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                    <option value="">All Delivery Channels</option>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="in_app">In-App</option>
                </select>
            </div>
            <div>
                <select wire:model.live="delivery_status" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm">
                    <option value="">All Delivery Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="sent">Sent</option>
                    <option value="delivered">Delivered</option>
                    <option value="read">Read</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-xs text-slate-400 font-semibold uppercase">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Broadcast Title</th>
                        <th class="px-6 py-3.5 text-left">Recipient Destination</th>
                        <th class="px-6 py-3.5 text-left">Channel & Provider</th>
                        <th class="px-6 py-3.5 text-left">Delivery Status</th>
                        <th class="px-6 py-3.5 text-right">Timestamp / Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-xs bg-white dark:bg-slate-800">
                    @forelse($logs as $lg)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                {{ $lg->communication?->title ?? 'Direct Message' }}
                                <span class="block text-[10px] text-slate-400 font-normal">Audience: {{ strtoupper($lg->recipient_type) }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                <div>{{ $lg->recipient_email ?: 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $lg->recipient_phone ?: '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $lg->channel === 'email' ? 'bg-blue-100 text-blue-800' : ($lg->channel === 'whatsapp' ? 'bg-emerald-100 text-emerald-800' : ($lg->channel === 'sms' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800')) }}">
                                    {{ $lg->channel }}
                                </span>
                                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Provider: {{ $lg->provider_name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ in_array($lg->delivery_status, ['delivered', 'read']) ? 'bg-emerald-100 text-emerald-800' : ($lg->delivery_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $lg->delivery_status }}
                                </span>
                                @if($lg->failure_reason)
                                    <p class="text-[10px] text-red-600 mt-1 max-w-xs">{{ $lg->failure_reason }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-[11px] text-slate-500 mb-1">
                                    {{ $lg->delivered_at ? $lg->delivered_at->format('M d, Y h:i A') : $lg->created_at->format('M d, Y h:i A') }}
                                </div>
                                @if($lg->delivery_status === 'failed')
                                    <button wire:click="resendLog({{ $lg->id }})" class="px-2.5 py-1 bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 rounded font-semibold text-[10px]">
                                        Retry Dispatch
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">No communication logs recorded matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-700">
            {{ $logs->links() }}
        </div>
    </div>
</div>
