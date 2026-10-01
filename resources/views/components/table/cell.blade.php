@props([
    'header' => false,
])

@if ($header)
    <th {{ $attributes->merge(['class' => 'px-6 py-3.5 text-left font-bold']) }}>
        {{ $slot }}
    </th>
@else
    <td {{ $attributes->merge(['class' => 'px-6 py-4 align-middle']) }}>
        {{ $slot }}
    </td>
@endif
