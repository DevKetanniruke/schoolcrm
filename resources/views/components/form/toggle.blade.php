@props([
    'name' => null,
    'label' => null,
    'enabled' => false,
])

<div x-data="{ state: {{ $enabled ? 'true' : 'false' }} }" class="flex items-center space-x-3">
    <button
        type="button"
        @click="state = !state"
        :class="state ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'"
        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
        <span
            :class="state ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
        ></span>
    </button>
    @if ($label || $slot->isNotEmpty())
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300" @click="state = !state">
            {{ $label ?? $slot }}
        </span>
    @endif
</div>
