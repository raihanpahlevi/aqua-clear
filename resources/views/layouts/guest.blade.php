<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aquaclear') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12 bg-teal-50/60">

            <div class="w-full sm:max-w-sm">
                <div class="text-center mb-6">
                    <span class="text-xl font-bold text-teal-700">Aquaclear</span>
                    <p class="text-xs text-slate-400 mt-0.5">Sistem Manajemen Tambak Udang</p>
                </div>

                <div class="bg-white px-8 py-8 rounded-xl border border-slate-200 shadow-sm shadow-slate-900/5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
