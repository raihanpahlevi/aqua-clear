@php
    $statusTone = match ($pond->status) {
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
        <x-page-header title="Kolam {{ $pond->kode_kolam }}" subtitle="Blok {{ $pond->block->nama }}" :back="route('ponds.index')" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <x-card class="flex flex-wrap items-center gap-8">
            <div>
                <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Luas</div>
                <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $pond->luas ?? '—' }} m²</div>
            </div>
            <div>
                <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kapasitas</div>
                <div class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $pond->kapasitas ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Status</div>
                <x-badge :tone="$statusTone">{{ str_replace('_', ' ', $pond->status) }}</x-badge>
            </div>
            <a href="{{ route('ponds.prep-logs.index', $pond) }}" class="ms-auto inline-flex items-center gap-1.5 text-sm font-medium text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300">
                <x-icon name="feed" class="w-4 h-4" /> Persiapan Tambak & Air
            </a>
            <a href="{{ route('ponds.edit', $pond) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300">
                <x-icon name="pencil" class="w-4 h-4" /> Edit Data Kolam
            </a>
        </x-card>

        <x-card :padded="false">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Riwayat Siklus / Stocking</h3>
                <a href="{{ route('ponds.stockings.create', $pond) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Mulai Siklus Baru
                </a>
            </div>

            @if ($stockings->isEmpty())
                <div class="p-8 text-center text-slate-400 dark:text-slate-500 text-sm">Belum ada riwayat siklus di kolam ini.</div>
            @else
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/60 dark:bg-slate-800/40">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Siklus</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tgl Tebar</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tgl Pakan Pertama</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Jumlah Tebar</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($stockings as $stocking)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                <td class="px-5 py-2.5 text-sm text-slate-600 dark:text-slate-300 font-medium">{{ $stocking->cycle->nama }}</td>
                                <td class="px-5 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ $stocking->tgl_tebar->format('d M Y') }}</td>
                                <td class="px-5 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ $stocking->tgl_pakan_pertama?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ number_format($stocking->jumlah_tebar) }} ekor</td>
                                <td class="px-5 py-2.5 text-sm text-right">
                                    <a href="{{ route('stockings.show', $stocking) }}" class="inline-flex items-center gap-1 text-teal-600 dark:text-teal-400 hover:underline font-medium">
                                        Detail <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-card>

    </div>
</x-app-layout>
