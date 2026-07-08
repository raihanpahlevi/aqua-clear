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
        <h2 class="font-semibold text-xl text-ink leading-tight">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 bg-perhatian/10 text-perhatian text-sm rounded-lg">
                Ringkasan lintas semua kolam. Estimasi, bukan angka pasti — dipengaruhi harga jual aktual saat panen dan kondisi lapangan.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Total Biaya (semua kolam)</div>
                    <div class="font-mono text-xl font-semibold text-ink">Rp {{ number_format($ringkasan['totalBiaya'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Total Pendapatan Panen</div>
                    <div class="font-mono text-xl font-semibold text-ink">Rp {{ number_format($ringkasan['totalPendapatan'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Total Estimasi Laba/Rugi</div>
                    <div class="font-mono text-xl font-semibold {{ $ringkasan['totalLabaRugi'] >= 0 ? 'text-sehat' : 'text-kritis' }}">
                        Rp {{ number_format($ringkasan['totalLabaRugi'], 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-hidden">
                <div class="p-4 border-b border-lumpur/20 font-display font-semibold text-ink">
                    Detail per Kolam
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-lumpur/10">
                        <thead class="bg-sand/30">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Kolam</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Siklus</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-ink/50 uppercase">Total Biaya</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-ink/50 uppercase">Total Pendapatan</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-ink/50 uppercase">HPP/kg</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-ink/50 uppercase">Laba/Rugi</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-lumpur/10">
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-mono font-medium text-ink/80">{{ $row['stocking']->pond->kode_kolam }}</td>
                                    <td class="px-4 py-2 text-sm text-ink/60">{{ $row['stocking']->cycle->nama }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <x-badge :tone="$statusTone($row['stocking']->pond->status)">{{ str_replace('_', ' ', $row['stocking']->pond->status) }}</x-badge>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right font-mono text-ink/80">Rp {{ number_format($row['totalBiaya'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-mono text-ink/80">Rp {{ number_format($row['totalPendapatan'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-mono text-ink/80">{{ $row['hppPerKg'] !== null ? 'Rp '.number_format($row['hppPerKg'], 0, ',', '.') : '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-mono font-semibold {{ $row['labaRugi'] >= 0 ? 'text-sehat' : 'text-kritis' }}">
                                        Rp {{ number_format($row['labaRugi'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right">
                                        <a href="{{ route('stockings.report.show', $row['stocking']) }}" class="text-teal-mid hover:underline">Detail →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-6 text-center text-ink/50 text-sm">Belum ada data stocking.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
