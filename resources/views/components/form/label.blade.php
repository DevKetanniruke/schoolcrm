@props([
    'for' => null,
    'required' => false,
])

<label {{ $for ? "for={$for}" : '' }} {{ $attributes->merge(['class' => 'block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-rose-500 font-bold ml-0.5">*</span>
    @endif
</label>
