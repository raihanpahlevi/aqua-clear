<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aquaclear') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|public-sans:400,500,600,700|jetbrains-mono:500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12 bg-paper">

            <div class="w-full sm:max-w-sm">
                <div class="text-center mb-6">
                    <span class="font-display text-xl font-semibold text-teal-deep">Aquaclear</span>
                    <p class="text-xs text-ink/40 mt-0.5">Sistem Manajemen Tambak Udang</p>
                </div>

                <div class="bg-sand/40 px-8 py-8 rounded-2xl border border-lumpur/20">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
