<header class="sticky top-0 z-30 h-16 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 lg:px-8 flex items-center justify-between transition-colors">
    <!-- Left Section: Sidebar Toggle & Search -->
    <div class="flex items-center space-x-4">
        <!-- Sidebar Toggle -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <!-- Search Input -->
        <div class="hidden md:flex items-center relative w-72">
            <svg class="w-4 h-4 absolute left-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input
                type="text"
                placeholder="{{ __('messages.search_placeholder') }}"
                class="w-full pl-9 pr-12 py-1.5 bg-slate-100 dark:bg-slate-800 border-0 rounded-lg text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 placeholder-slate-400 transition"
            />
            <kbd class="absolute right-2 px-1.5 py-0.5 text-[10px] font-mono text-slate-400 bg-slate-200 dark:bg-slate-700 rounded">⌘K</kbd>
        </div>
    </div>

    <!-- Right Section: Actions & Dropdowns -->
    <div class="flex items-center space-x-3">
        <!-- Language Translator Selector -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700 transition"
            >
                <span>🌐</span>
                <span>{{ app()->getLocale() === 'mr' ? 'मराठी' : 'English' }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 space-y-1"
            >
                <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.language') }} / भाषा</div>
                <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700/60 {{ app()->getLocale() === 'en' ? 'font-bold text-indigo-600 dark:text-indigo-400' : '' }}">
                    <span>🇬🇧 English</span>
                    @if(app()->getLocale() === 'en') <span class="text-indigo-600 font-bold">✓</span> @endif
                </a>
                <a href="{{ route('lang.switch', 'mr') }}" class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700/60 {{ app()->getLocale() === 'mr' ? 'font-bold text-indigo-600 dark:text-indigo-400' : '' }}">
                    <span>🇮🇳 मराठी (Marathi)</span>
                    @if(app()->getLocale() === 'mr') <span class="text-indigo-600 font-bold">✓</span> @endif
                </a>
            </div>
        </div>

        <!-- Quick Create Button -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="hidden sm:flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-md shadow-indigo-500/20 transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>{{ __('messages.quick_add') }}</span>
            </button>
            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50"
            >
                <a href="{{ route('students.index') }}" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60">+ {{ __('messages.enroll_new_student') }}</a>
                <a href="{{ route('staff.index') }}" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60">+ {{ __('messages.add_staff_member') }}</a>
                <a href="#" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60">+ {{ __('messages.generate_fee_invoice') }}</a>
            </div>
        </div>

        <!-- Notifications Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="relative p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900"></span>
            </button>

            <!-- Dropdown content -->
            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-2 z-50"
            >
                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Notifications</span>
                    <span class="text-[10px] text-indigo-500 font-semibold cursor-pointer">Mark all read</span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50 max-h-64 overflow-y-auto">
                    <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                        <p class="text-xs font-medium text-slate-800 dark:text-slate-200">New Admission Request</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Parent Submitted application for Grade 9.</p>
                        <span class="text-[10px] text-slate-400 mt-1 block">5 mins ago</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button
                @click="open = !open"
                class="flex items-center space-x-2 p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold text-xs flex items-center justify-center">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                </div>
                <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <!-- Profile Menu -->
            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-2 z-50"
            >
                <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@smartschool.edu' }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60">{{ __('messages.my_profile') }}</a>
                <a href="#" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60">{{ __('messages.audit_logs') }}</a>
                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                <form method="POST" action="{{ route('logout') ?? '#' }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40">{{ __('messages.logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</header>
