@php
    $statusTone = fn ($status) => match ($status) {
        'kosong' => 'slate',
        'siap_tebar' => 'amber',
        'aktif' => 'emerald',
        'panen' => 'sky',
        'maintenance' => 'rose',
        default => 'slate',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Data Kolam" subtitle="Master {{ $ponds->flatten()->count() }} kolam terdaftar.">
            <x-slot name="actions">
                <a href="{{ route('ponds.bulk-create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-wide shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Banyak Sekaligus
                </a>
                <a href="{{ route('ponds.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Tambah Kolam
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @forelse ($ponds as $namaBlok => $kolamDiBlok)
            <x-card :padded="false">
                <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 font-semibold text-slate-700 dark:text-slate-200 text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 flex items-center justify-center text-xs font-bold">{{ $namaBlok }}</span>
                    Blok {{ $namaBlok }}
                    <span class="text-slate-400 font-normal">· {{ $kolamDiBlok->count() }} kolam</span>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/60 dark:bg-slate-800/40">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kode Kolam</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Luas (m²)</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kapasitas</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($kolamDiBlok as $pond)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                <td class="px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $pond->kode_kolam }}
                                </td>
                                <td class="px-5 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ $pond->luas ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ $pond->kapasitas ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-sm">
                                    <x-badge :tone="$statusTone($pond->status)">{{ str_replace('_', ' ', $pond->status) }}</x-badge>
                                </td>
                                <td class="px-5 py-2.5 text-sm text-right space-x-3">
                                    <a href="{{ route('ponds.show', $pond) }}" class="inline-flex items-center gap-1 text-teal-600 dark:text-teal-400 hover:underline font-medium">
                                        Detail <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                    </a>
                                    <a href="{{ route('ponds.edit', $pond) }}" class="inline-flex items-center text-slate-400 hover:text-teal-600 dark:hover:text-teal-400">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </x-card>
        @empty
            <x-card class="text-center py-12">
                <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="pond" class="w-6 h-6" />
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada kolam. Klik "Tambah Kolam" untuk mulai input data 76 kolam.</p>
            </x-card>
        @endforelse

    </div>
</x-app-layout>
