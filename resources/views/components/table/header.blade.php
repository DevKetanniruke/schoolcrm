<thead {{ $attributes->merge(['class' => 'bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800']) }}>
    <tr>
        {{ $slot }}
    </tr>
</thead>
