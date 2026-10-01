<div class="space-y-8">
    <!-- Top Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-purple-950 p-6 md:p-8 text-white shadow-2xl border border-indigo-900/40">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        ⚡ Event-Driven Workflow Engine
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        🔒 RBAC Permission Enforced
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">CRM Automation Engine</h1>
                <p class="text-sm text-slate-300 mt-1 max-w-3xl">
                    Configure reusable triggers, conditions, and automated actions with real-time audit logs while strictly enforcing authorization policies.
                </p>
            </div>
        </div>
    </div>

    <!-- Tab Bar (Rules vs Audit Logs) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-2 shadow-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
        <div class="flex space-x-2">
            <button type="button" wire:click="$set('active_tab', 'rules')" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 {{ $active_tab === 'rules' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>⚙️ Configurable Automation Rules</span>
            </button>

            <button type="button" wire:click="$set('active_tab', 'audit_logs')" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-2 {{ $active_tab === 'audit_logs' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>📋 Execution Audit Logs</span>
            </button>
        </div>
    </div>

    @if($active_tab === 'rules')
        <!-- Rules Tab: Builder & Table Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Panel: Create Rule Builder (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Automation Rule
                    </h2>

                    <form wire:submit.prevent="createRule" class="space-y-4">
                        <!-- Rule Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Rule Name <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="name" placeholder="e.g. Lead Followup Escalation" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500" required />
                            @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Reusable Trigger Selection -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Trigger Event (WHEN) <span class="text-rose-500">*</span></label>
                            <select wire:model="trigger_event" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm focus:ring-2 focus:ring-indigo-500">
                                @foreach($triggerOptions as $tKey => $tLabel)
                                    <option value="{{ $tKey }}">{{ $tLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reusable Conditions Builder -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Conditions (IF / WHERE)</label>
                            
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" wire:model="condition_field" placeholder="field (e.g. stage)" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-xs" />
                                <select wire:model="condition_operator" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-xs">
                                    <option value="equals">equals</option>
                                    <option value="not_equals">not equals</option>
                                    <option value="contains">contains</option>
                                </select>
                                <input type="text" wire:model="condition_value" placeholder="value" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-xs" />
                            </div>

                            <button type="button" wire:click="addCondition" class="w-full py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 font-bold text-xs hover:bg-indigo-100 transition border border-indigo-200 dark:border-indigo-800">
                                + Add Condition Rule
                            </button>

                            @if(!empty($conditions))
                                <div class="space-y-1 pt-1">
                                    @foreach($conditions as $idx => $c)
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs">
                                            <span class="font-mono text-slate-700 dark:text-slate-300">{{ $c['field'] }} {{ $c['operator'] }} "{{ $c['value'] }}"</span>
                                            <button type="button" wire:click="removeCondition({{ $idx }})" class="text-rose-500 hover:text-rose-700 font-bold">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Reusable Actions Selection -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Automated Actions (THEN) <span class="text-rose-500">*</span></label>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($actionOptions as $aKey => $aLabel)
                                    <label class="flex items-center p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        <input type="checkbox" wire:model="selected_actions" value="{{ $aKey }}" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-2.5" />
                                        <span>{{ $aLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Save Button -->
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 hover:from-indigo-500 hover:to-purple-500 transition">
                            Save Automation Rule
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Panel: Rules Directory Table (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Active Automation Rules</h2>
                        <span class="text-xs text-slate-400">{{ $rules->total() }} rules configured</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50 dark:bg-slate-950/50">
                                    <th class="p-3.5 rounded-l-xl">Rule Name & Creator</th>
                                    <th class="p-3.5">Trigger (WHEN)</th>
                                    <th class="p-3.5">Conditions & Actions (THEN)</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5 text-right rounded-r-xl">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @forelse($rules as $rule)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                        <td class="p-3.5">
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $rule->name }}</div>
                                            <div class="text-xs text-slate-400">By {{ $rule->creator?->name ?? 'System' }}</div>
                                        </td>

                                        <td class="p-3.5">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                                                {{ $rule->trigger_event }}
                                            </span>
                                        </td>

                                        <td class="p-3.5">
                                            <div class="text-xs text-slate-700 dark:text-slate-300">
                                                <strong>{{ count($rule->conditions_json ?? []) }}</strong> condition(s), 
                                                <strong>{{ count($rule->actions_json ?? []) }}</strong> action(s)
                                            </div>
                                            <div class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5">
                                                Actions: {{ implode(', ', array_column($rule->actions_json ?? [], 'action')) }}
                                            </div>
                                        </td>

                                        <td class="p-3.5">
                                            @if($rule->status === 'active')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-500/20 text-slate-400 border border-slate-500/30">Inactive</span>
                                            @endif
                                        </td>

                                        <td class="p-3.5 text-right space-x-2">
                                            <button type="button" wire:click="toggleRuleStatus({{ $rule->id }})" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition">
                                                Toggle
                                            </button>

                                            <button type="button" wire:click="deleteRule({{ $rule->id }})" class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold hover:bg-rose-500/30 transition">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400">
                                            No automation rules configured yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-2">
                        {{ $rules->links() }}
                    </div>
                </div>
            </div>

        </div>
    @else
        <!-- Audit Logs Tab: Real-Time Execution Audit Explorer -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Automation Execution Audit Logs</h2>
                    <p class="text-xs text-slate-400">Complete audit trail of trigger evaluations, authorization checks, and automated actions.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50 dark:bg-slate-950/50">
                            <th class="p-3.5 rounded-l-xl">Timestamp</th>
                            <th class="p-3.5">Trigger Event & Rule</th>
                            <th class="p-3.5">Target Entity</th>
                            <th class="p-3.5">Execution Status</th>
                            <th class="p-3.5 rounded-r-xl">Actions Executed & Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3.5 font-mono text-xs text-slate-400">
                                    {{ $log->executed_at ? $log->executed_at->format('M d, Y h:i:s A') : $log->created_at->format('M d, h:i A') }}
                                </td>

                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $log->rule?->name ?? 'System Automation' }}</div>
                                    <div class="text-[10px] font-mono text-indigo-400">{{ $log->trigger_event }}</div>
                                </td>

                                <td class="p-3.5 text-xs text-slate-300 font-mono">
                                    {{ $log->entity_type }} #{{ $log->entity_id }}
                                </td>

                                <td class="p-3.5">
                                    @if($log->execution_status === 'success')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">🟢 Success</span>
                                    @elseif($log->execution_status === 'blocked_by_auth')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">🔒 Blocked by Auth</span>
                                    @elseif($log->execution_status === 'condition_unmet')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">🟡 Condition Unmet</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">❌ Failed</span>
                                    @endif
                                </td>

                                <td class="p-3.5 text-xs font-mono text-slate-300">
                                    @if(!empty($log->actions_executed_json))
                                        <div class="space-y-1">
                                            @foreach($log->actions_executed_json as $actRes)
                                                <div class="text-[11px] text-slate-300">• {{ $actRes['action'] ?? 'action' }}: {{ $actRes['result'] ?? 'executed' }}</div>
                                            @endforeach
                                        </div>
                                    @elseif($log->error_message)
                                        <div class="text-rose-400 text-xs font-sans">{{ $log->error_message }}</div>
                                    @else
                                        <div class="text-slate-500 text-xs">No actions executed</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">
                                    No automation execution logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $auditLogs->links() }}
            </div>
        </div>
    @endif
</div>
