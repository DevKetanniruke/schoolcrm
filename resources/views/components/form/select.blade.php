@props([
    'name' => null,
    'options' => [], // ['val' => 'Label'] or simple array
    'placeholder' => 'Select an option',
    'disabled' => false,
])

<select
    {{ $name ? "name={$name} id={$name}" : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => 'w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800 transition'
    ]) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @if (is_array($options) || is_iterable($options))
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    @else
        {{ $slot }}
    @endif
</select>
