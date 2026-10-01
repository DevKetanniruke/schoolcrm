@props(['name'])

@error($name)
    <p {{ $attributes->merge(['class' => 'text-xs font-semibold text-rose-600 dark:text-rose-400 mt-1 flex items-center space-x-1']) }}>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM12 3a9 9 0 100 18 9 9 0 000-18z" />
        </svg>
        <span>{{ $message }}</span>
    </p>
@enderror
