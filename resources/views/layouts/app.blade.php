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
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-paper">

            <!-- Desktop sidebar -->
            <aside class="hidden lg:flex lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:flex-col bg-teal-deep border-r border-black/10 z-30">
                @include('layouts.navigation')
            </aside>

            <!-- Mobile sidebar drawer -->
            <div x-show="sidebarOpen" x-cloak class="lg:hidden fixed inset-0 z-40">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @click="sidebarOpen = false" class="fixed inset-0 bg-ink/50"></div>

                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                     class="fixed inset-y-0 left-0 w-72 bg-teal-deep shadow-xl">
                    @include('layouts.navigation')
                </div>
            </div>

            <!-- Main column -->
            <div class="lg:pl-64 flex flex-col min-h-screen">

                <!-- Mobile topbar -->
                <div class="lg:hidden sticky top-0 z-20 flex items-center gap-3 h-14 px-4 bg-teal-deep border-b border-black/10">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 text-sand/70">
                        <x-icon name="menu" class="w-5 h-5" />
                    </button>
                    <span class="font-display font-semibold text-sand">Aquaclear</span>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-paper border-b border-lumpur/20">
                        <div class="px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
