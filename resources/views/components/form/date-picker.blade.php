@props([
    'name' => null,
    'placeholder' => 'YYYY-MM-DD',
    'disabled' => false,
])

<div class="relative">
    <input
        type="date"
        {{ $name ? "name={$name} id={$name}" : '' }}
        placeholder="{{ $placeholder }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition'
        ]) }}
    />
</div>
