<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In - {{ config('app.name', 'SmartSchool CRM') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-2xl font-extrabold shadow-xl shadow-indigo-500/30 mb-3">
                S
            </div>
            <h2 class="text-2xl font-extrabold text-white tracking-tight">SmartSchool CRM</h2>
            <p class="text-xs text-indigo-300 mt-1">Enterprise School Authentication & Management Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200/20 dark:border-slate-800 rounded-3xl p-8 shadow-2xl">
            <!-- Session status alert -->
            @if (session('status'))
                <x-ui.alert type="info" class="mb-5">
                    {{ session('status') }}
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <x-form.group label="Email Address" name="email" required>
                    <x-form.input
                        name="email"
                        type="email"
                        :value="old('email')"
                        placeholder="admin@smartschool.edu"
                        required
                        autofocus
                    />
                </x-form.group>

                <!-- Password Input -->
                <div x-data="{ show: false }">
                    <x-form.group label="Password" name="password" required>
                        <div class="relative">
                            <input
                                :type="show ? 'text' : 'password'"
                                name="password"
                                id="password"
                                required
                                placeholder="••••••••"
                                class="w-full px-3.5 py-2 pr-10 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            />
                            <button
                                type="button"
                                @click="show = !show"
                                class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            >
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 012.122-.063c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </x-form.group>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs">
                    <x-form.checkbox name="remember" label="Remember me" />

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <x-ui.button type="submit" variant="primary" class="w-full py-2.5 mt-2">
                    Sign In to Portal
                </x-ui.button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-500 mt-6">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
