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
    <div class="p-3 border-t border-slate-100 dark:border-slate-800 space-y-1">
        <div class="flex items-center gap-3 px-3 py-2.5">
            <div class="w-9 h-9 rounded-full bg-teal-100 dark:bg-teal-500/20 text-teal-700 dark:text-teal-400 flex items-center justify-center font-semibold text-sm shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-400 capitalize truncate">{{ Auth::user()->getRoleNames()->first() ?? '—' }}</div>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 transition">
            <x-icon name="user" class="w-4 h-4 shrink-0" />
            {{ __('Profil') }}
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 transition text-left">
                <x-icon name="logout" class="w-4 h-4 shrink-0" />
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</div>
