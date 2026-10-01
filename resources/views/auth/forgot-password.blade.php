<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password - {{ config('app.name', 'SmartSchool CRM') }}</title>

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
            <h2 class="text-2xl font-extrabold text-white tracking-tight">Password Recovery</h2>
            <p class="text-xs text-indigo-300 mt-1">Enter your email to receive a secure password reset link.</p>
        </div>

        <div class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200/20 dark:border-slate-800 rounded-3xl p-8 shadow-2xl">
            @if (session('status'))
                <x-ui.alert type="success" class="mb-5">
                    {{ session('status') }}
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <x-form.group label="Email Address" name="email" required>
                    <x-form.input
                        name="email"
                        type="email"
                        :value="old('email')"
                        placeholder="yourname@smartschool.edu"
                        required
                        autofocus
                    />
                </x-form.group>

                <x-ui.button type="submit" variant="primary" class="w-full py-2.5">
                    Email Password Reset Link
                </x-ui.button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                    ← Back to Sign In
                </a>
            </div>
        </div>
    </div>
</body>
</html>
