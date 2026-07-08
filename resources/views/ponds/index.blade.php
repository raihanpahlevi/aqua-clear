@php
    $statusTone = fn ($status) => match ($status) {
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
        <x-page-header title="Data Kolam" subtitle="Master {{ $ponds->flatten()->count() }} kolam terdaftar.">
            <x-slot name="actions">
                <a href="{{ route('ponds.bulk-create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-paper border border-lumpur/40 rounded-lg font-semibold text-xs text-ink/80 uppercase tracking-wide shadow-sm hover:bg-sand/30">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Banyak Sekaligus
                </a>
                <a href="{{ route('ponds.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Tambah Kolam
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-sehat/10 text-sehat rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-kritis/10 text-kritis rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @forelse ($ponds as $namaBlok => $kolamDiBlok)
            <x-card :padded="false">
                <div class="px-5 py-3.5 border-b border-lumpur/20 font-display font-semibold text-ink text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-teal-mid/10 text-teal-mid flex items-center justify-center text-xs font-bold">{{ $namaBlok }}</span>
                    Blok {{ $namaBlok }}
                    <span class="text-ink/40 font-normal">· {{ $kolamDiBlok->count() }} kolam</span>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-lumpur/10">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Kode Kolam</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Luas (m²)</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Kapasitas</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @foreach ($kolamDiBlok as $pond)
                            <tr class="hover:bg-sand/30">
                                <td class="px-5 py-2.5 text-sm font-mono font-semibold text-ink/80">
                                    {{ $pond->kode_kolam }}
                                </td>
                                <td class="px-5 py-2.5 text-sm font-mono text-ink/70">{{ $pond->luas ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-sm font-mono text-ink/70">{{ $pond->kapasitas ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-sm">
                                    <x-badge :tone="$statusTone($pond->status)">{{ str_replace('_', ' ', $pond->status) }}</x-badge>
                                </td>
                                <td class="px-5 py-2.5 text-sm text-right space-x-3">
                                    <a href="{{ route('ponds.show', $pond) }}" class="inline-flex items-center gap-1 text-teal-mid hover:underline font-medium">
                                        Detail <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                    </a>
                                    <a href="{{ route('ponds.edit', $pond) }}" class="inline-flex items-center text-ink/40 hover:text-teal-mid">
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
                <div class="w-12 h-12 rounded-xl bg-teal-mid/10 text-teal-mid flex items-center justify-center mx-auto mb-3">
                    <x-icon name="pond" class="w-6 h-6" />
                </div>
                <p class="text-ink/50 text-sm">Belum ada kolam. Klik "Tambah Kolam" untuk mulai input data 76 kolam.</p>
            </x-card>
        @endforelse

    </div>
</x-app-layout>
