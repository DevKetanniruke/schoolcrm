<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random();
            const newToast = {
                id: id,
                type: toast.type || 'info',
                message: toast.message || '',
                title: toast.title || '',
                duration: toast.duration || 4000
            };
            this.toasts.push(newToast);
            setTimeout(() => {
                this.remove(id);
            }, newToast.duration);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-on:notify.window="add($event.detail)"
    x-init="
        @if (session()->has('success'))
            add({ type: 'success', message: '{{ session('success') }}', title: 'Success' });
        @endif
        @if (session()->has('error'))
            add({ type: 'error', message: '{{ session('error') }}', title: 'Error' });
        @endif
        @if (session()->has('warning'))
            add({ type: 'warning', message: '{{ session('warning') }}', title: 'Warning' });
        @endif
        @if (session()->has('info'))
            add({ type: 'info', message: '{{ session('info') }}', title: 'Info' });
        @endif
    "
    class="fixed bottom-5 right-5 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300 transform translate-y-2 opacity-0"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform translate-y-2 opacity-0"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-2 opacity-0"
            class="pointer-events-auto rounded-xl p-4 shadow-xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 flex items-start space-x-3 text-slate-800 dark:text-slate-100"
            :class="{
                'border-l-4 border-l-emerald-500': toast.type === 'success',
                'border-l-4 border-l-rose-500': toast.type === 'error',
                'border-l-4 border-l-amber-500': toast.type === 'warning',
                'border-l-4 border-l-sky-500': toast.type === 'info'
            }"
        >
            <!-- Icon -->
            <div class="shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12v-.008z" />
                    </svg>
                </template>
            </div>

            <!-- Text Content -->
            <div class="flex-1 text-sm min-w-0">
                <p x-show="toast.title" class="font-semibold text-slate-900 dark:text-white" x-text="toast.title"></p>
                <p class="text-slate-600 dark:text-slate-300 leading-snug" x-text="toast.message"></p>
            </div>

            <!-- Close button -->
            <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
