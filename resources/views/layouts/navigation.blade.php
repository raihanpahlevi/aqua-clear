@php
    $navItems = [
        ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'ponds.index', 'pattern' => 'ponds.*', 'icon' => 'pond', 'label' => 'Data Kolam'],
        ['route' => 'cycles.index', 'pattern' => 'cycles.*', 'icon' => 'cycle', 'label' => 'Siklus'],
        ['route' => 'reports.index', 'pattern' => 'reports.*', 'icon' => 'report', 'label' => 'Laporan'],
    ];
@endphp

<div class="flex flex-col h-full">
    <!-- Brand -->
    <div class="flex items-center px-5 h-16 shrink-0 border-b border-slate-100 dark:border-slate-800">
        <span class="text-lg font-bold text-teal-700 tracking-tight">Aquaclear</span>
    </div>

    <!-- Nav items -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach ($navItems as $item)
            <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['pattern'])">
                <x-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
                {{ $item['label'] }}
            </x-nav-link>
        @endforeach
    </nav>

    <!-- User -->
    <div class="p-3 border-t border-slate-100 dark:border-slate-800">
        <x-dropdown align="top" width="60">
            <x-slot name="trigger">
                <button class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition text-left">
                    <div class="w-9 h-9 rounded-full bg-teal-100 dark:bg-teal-500/20 text-teal-700 dark:text-teal-400 flex items-center justify-center font-semibold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 capitalize truncate">{{ Auth::user()->getRoleNames()->first() ?? '—' }}</div>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 shrink-0" />
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</div>
