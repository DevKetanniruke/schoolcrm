@props([
    'name' => 'confirm-action',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed with this action? This action cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger', // danger | primary
])

<x-ui.modal :name="$name" :title="$title" maxWidth="md">
    <div class="flex items-start space-x-4">
        <div class="shrink-0 w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <div>
            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ $message }}</p>
        </div>
    </div>

    <x-slot:footer>
        <x-ui.button variant="ghost" @click="$dispatch('close-modal', '{{ $name }}')">
            {{ $cancelText }}
        </x-ui.button>
        <x-ui.button :variant="$variant" @click="$dispatch('confirmed-{{ $name }}'); $dispatch('close-modal', '{{ $name }}')">
            {{ $confirmText }}
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
