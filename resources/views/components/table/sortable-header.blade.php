@props([
    'field' => '',
    'currentSort' => '',
    'direction' => 'asc',
])

<th {{ $attributes->merge(['class' => 'px-6 py-3.5 text-left font-bold cursor-pointer select-none hover:text-indigo-600 dark:hover:text-indigo-400 transition']) }}>
    <div class="flex items-center space-x-1.5">
        <span>{{ $slot }}</span>
        <span class="text-slate-400">
            @if ($currentSort === $field)
                @if ($direction === 'asc')
                    ↑
                @else
                    ↓
                @endif
            @else
                ↕
            @endif
        </span>
    </div>
</th>
