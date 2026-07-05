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
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-sm rounded-lg">
                Ringkasan lintas semua kolam. Estimasi, bukan angka pasti — dipengaruhi harga jual aktual saat panen dan kondisi lapangan.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Total Biaya (semua kolam)</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($ringkasan['totalBiaya'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Total Pendapatan Panen</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($ringkasan['totalPendapatan'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Total Estimasi Laba/Rugi</div>
                    <div class="text-xl font-semibold {{ $ringkasan['totalLabaRugi'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        Rp {{ number_format($ringkasan['totalLabaRugi'], 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 font-semibold text-slate-700 dark:text-slate-200">
                    Detail per Kolam
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900/40">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Kolam</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Siklus</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Total Biaya</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Total Pendapatan</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">HPP/kg</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Laba/Rugi</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200">{{ $row['stocking']->pond->kode_kolam }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400">{{ $row['stocking']->cycle->nama }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <x-badge :tone="$statusTone($row['stocking']->pond->status)">{{ str_replace('_', ' ', $row['stocking']->pond->status) }}</x-badge>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right text-slate-700 dark:text-slate-200">Rp {{ number_format($row['totalBiaya'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-slate-700 dark:text-slate-200">Rp {{ number_format($row['totalPendapatan'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-slate-700 dark:text-slate-200">{{ $row['hppPerKg'] !== null ? 'Rp '.number_format($row['hppPerKg'], 0, ',', '.') : '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-semibold {{ $row['labaRugi'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        Rp {{ number_format($row['labaRugi'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right">
                                        <a href="{{ route('stockings.report.show', $row['stocking']) }}" class="text-teal-600 dark:text-teal-400 hover:underline">Detail →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400 text-sm">Belum ada data stocking.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
