@props([
    'name' => null,
    'rows' => 3,
    'placeholder' => '',
    'disabled' => false,
])

<textarea
    {{ $name ? "name={$name} id={$name}" : '' }}
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => 'w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800 transition'
    ]) }}
>{{ $slot }}</textarea>
