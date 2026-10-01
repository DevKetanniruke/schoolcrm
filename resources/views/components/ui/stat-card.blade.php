@props([
    'title' => '',
    'value' => '0',
    'change' => null,
    'changeType' => 'increase', // 'increase' | 'decrease' | 'neutral'
    'icon' => null,
    'iconBg' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all']) }}>
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ $title }}</span>
        @if ($icon)
            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $iconBg }}">
                {{ $icon }}
            </div>
        @endif
    </div>

    <div class="mt-3 flex items-baseline justify-between">
        <span class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $value }}</span>
        @if ($change)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $changeType === 'increase' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $changeType === 'decrease' ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400' : '' }}
                {{ $changeType === 'neutral' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : '' }}
            ">
                @if ($changeType === 'increase')
                    ↑
                @elseif ($changeType === 'decrease')
                    ↓
                @endif
                {{ $change }}
            </span>
        @endif
    </div>
</div>
