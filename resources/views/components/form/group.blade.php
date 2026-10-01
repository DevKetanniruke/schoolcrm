@props([
    'name' => null,
    'label' => null,
    'required' => false,
    'help' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <x-form.label :for="$name" :required="$required">{{ $label }}</x-form.label>
    @endif

    {{ $slot }}

    @if ($help)
        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif

    @if ($name)
        <x-form.error :name="$name" />
    @endif
</div>
