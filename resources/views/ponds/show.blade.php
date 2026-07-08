@php
    $statusTone = match ($pond->status) {
        'kosong' => 'slate',
        'siap_tebar' => 'perhatian',
        'aktif' => 'sehat',
        'panen' => 'teal',
        'maintenance' => 'kritis',
        default => 'slate',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Kolam {{ $pond->kode_kolam }}" subtitle="Blok {{ $pond->block->nama }}" :back="route('ponds.index')" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-sehat/10 text-sehat rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <x-card class="flex flex-wrap items-center gap-8">
            <div>
                <div class="text-xs text-ink/50 uppercase tracking-wide">Luas</div>
                <div class="text-lg font-bold text-ink">{{ $pond->luas ?? '—' }} m²</div>
            </div>
            <div>
                <div class="text-xs text-ink/50 uppercase tracking-wide">Kapasitas</div>
                <div class="text-lg font-bold text-ink">{{ $pond->kapasitas ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-ink/50 uppercase tracking-wide mb-1">Status</div>
                <x-badge :tone="$statusTone">{{ str_replace('_', ' ', $pond->status) }}</x-badge>
            </div>
            <a href="{{ route('ponds.prep-logs.index', $pond) }}" class="ms-auto inline-flex items-center gap-1.5 text-sm font-medium text-teal-mid hover:text-teal-deep">
                <x-icon name="feed" class="w-4 h-4" /> Persiapan Tambak & Air
            </a>
            <a href="{{ route('ponds.edit', $pond) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-teal-mid hover:text-teal-deep">
                <x-icon name="pencil" class="w-4 h-4" /> Edit Data Kolam
            </a>
        </x-card>

        <x-card :padded="false">
            <div class="px-5 py-4 border-b border-lumpur/20 flex justify-between items-center">
                <h3 class="font-display font-semibold text-ink text-sm">Riwayat Siklus / Stocking</h3>
                <a href="{{ route('ponds.stockings.create', $pond) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Mulai Siklus Baru
                </a>
            </div>

            @if ($stockings->isEmpty())
                <div class="p-8 text-center text-ink/40 text-sm">Belum ada riwayat siklus di kolam ini.</div>
            @else
                <table class="min-w-full divide-y divide-lumpur/10">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Siklus</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Tgl Tebar</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Tgl Pakan Pertama</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Jumlah Tebar</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @foreach ($stockings as $stocking)
                            <tr class="hover:bg-sand/30">
                                <td class="px-5 py-2.5 text-sm text-ink/70 font-medium">{{ $stocking->cycle->nama }}</td>
                                <td class="px-5 py-2.5 text-sm font-mono text-ink/70">{{ $stocking->tgl_tebar->format('d M Y') }}</td>
                                <td class="px-5 py-2.5 text-sm font-mono text-ink/70">{{ $stocking->tgl_pakan_pertama?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-sm font-mono text-ink/70">{{ number_format($stocking->jumlah_tebar) }} ekor</td>
                                <td class="px-5 py-2.5 text-sm text-right">
                                    <a href="{{ route('stockings.show', $stocking) }}" class="inline-flex items-center gap-1 text-teal-mid hover:underline font-medium">
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
