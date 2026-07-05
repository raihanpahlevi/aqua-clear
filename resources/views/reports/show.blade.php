<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Biaya & Laporan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 hover:underline">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-sm rounded-lg">
                Estimasi, bukan angka pasti — dipengaruhi harga jual aktual saat panen dan kondisi lapangan.
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Total Biaya</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Total Pendapatan Panen</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">HPP / kg</div>
                    <div class="text-xl font-semibold text-slate-800 dark:text-slate-200">{{ $hppPerKg !== null ? 'Rp '.number_format($hppPerKg, 0, ',', '.') : '—' }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase">Estimasi Laba/Rugi</div>
                    <div class="text-xl font-semibold {{ $labaRugi >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        Rp {{ number_format($labaRugi, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 font-semibold text-slate-700 dark:text-slate-200">
                    Biaya per Kategori
                </div>
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($biayaPerKategori as $kategori => $nilai)
                            <tr>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300 capitalize">{{ $kategori }}</td>
                                <td class="px-4 py-2 text-right text-slate-700 dark:text-slate-200">Rp {{ number_format($nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-slate-50 dark:bg-slate-900/40 font-semibold">
                            <td class="px-4 py-2 text-slate-700 dark:text-slate-200">% Biaya Pakan dari Total</td>
                            <td class="px-4 py-2 text-right text-slate-700 dark:text-slate-200">{{ $persenBiayaPakan !== null ? number_format($persenBiayaPakan, 1).'%' : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="px-4 py-2 text-xs text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700">
                    Acuan historis biaya pakan: 60–68% dari total biaya operasional.
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-500 dark:text-slate-400">Progres Panen vs Target {{ $progresPanen['target_min'] }}–{{ $progresPanen['target_max'] }} kg/kolam</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($totalBiomassPanen, 1) }} kg</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $progresPanen['persen_dari_target_min'] }}%"></div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
