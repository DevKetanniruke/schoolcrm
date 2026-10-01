@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'disabled' => false,
])

<label class="inline-flex items-center space-x-2.5 cursor-pointer">
    <input
        type="checkbox"
        {{ $name ? "name={$name} id={$name}" : '' }}
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-900 transition'
        ]) }}
    />
    @if ($label || $slot->isNotEmpty())
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label ?? $slot }}
        </span>
    @endif
</label>
