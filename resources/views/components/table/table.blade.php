<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm']) }}>
    <table class="w-full text-left border-collapse">
        {{ $slot }}
    </table>
</div>
