@props([
    'name' => null,
    'accept' => '*/*',
    'disabled' => false,
])

<div class="relative">
    <input
        type="file"
        {{ $name ? "name={$name} id={$name}" : '' }}
        accept="{{ $accept }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900 cursor-pointer transition'
        ]) }}
    />
</div>
