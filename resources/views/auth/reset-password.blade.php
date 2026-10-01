<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Password - {{ config('app.name', 'SmartSchool CRM') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white text-2xl font-extrabold shadow-xl shadow-indigo-500/30 mb-3">
                S
            </div>
            <h2 class="text-2xl font-extrabold text-white tracking-tight">Set New Password</h2>
            <p class="text-xs text-indigo-300 mt-1">Please enter your new password details below.</p>
        </div>

        <div class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200/20 dark:border-slate-800 rounded-3xl p-8 shadow-2xl">
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <x-form.group label="Email Address" name="email" required>
                    <x-form.input
                        name="email"
                        type="email"
                        :value="old('email', $request->email)"
                        required
                        readonly
                    />
                </x-form.group>

                <x-form.group label="New Password" name="password" required>
                    <x-form.input
                        name="password"
                        type="password"
                        required
                        placeholder="••••••••"
                    />
                </x-form.group>

                <x-form.group label="Confirm New Password" name="password_confirmation" required>
                    <x-form.input
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="••••••••"
                    />
                </x-form.group>

                <x-ui.button type="submit" variant="primary" class="w-full py-2.5">
                    Reset Password
                </x-ui.button>
            </form>
        </div>
    </div>
</body>
</html>
