<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ink leading-tight">
                Biaya & Laporan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 bg-perhatian/10 text-perhatian text-sm rounded-lg">
                Estimasi, bukan angka pasti — dipengaruhi harga jual aktual saat panen dan kondisi lapangan.
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Total Biaya</div>
                    <div class="font-mono text-xl font-semibold text-ink">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Total Pendapatan Panen</div>
                    <div class="font-mono text-xl font-semibold text-ink">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">HPP / kg</div>
                    <div class="font-mono text-xl font-semibold text-ink">{{ $hppPerKg !== null ? 'Rp '.number_format($hppPerKg, 0, ',', '.') : '—' }}</div>
                </div>
                <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                    <div class="text-xs text-ink/50 uppercase">Estimasi Laba/Rugi</div>
                    <div class="font-mono text-xl font-semibold {{ $labaRugi >= 0 ? 'text-sehat' : 'text-kritis' }}">
                        Rp {{ number_format($labaRugi, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-hidden">
                <div class="p-4 border-b border-lumpur/20 font-display font-semibold text-ink">
                    Biaya per Kategori
                </div>
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <tbody class="divide-y divide-lumpur/10">
                        @foreach ($biayaPerKategori as $kategori => $nilai)
                            <tr>
                                <td class="px-4 py-2 text-ink/70 capitalize">{{ $kategori }}</td>
                                <td class="px-4 py-2 text-right font-mono text-ink/80">Rp {{ number_format($nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-sand/30 font-semibold">
                            <td class="px-4 py-2 text-ink/80">% Biaya Pakan dari Total</td>
                            <td class="px-4 py-2 text-right text-ink/80">{{ $persenBiayaPakan !== null ? number_format($persenBiayaPakan, 1).'%' : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="px-4 py-2 text-xs text-ink/50 border-t border-lumpur/20">
                    Acuan historis biaya pakan: 60–68% dari total biaya operasional.
                </div>
            </div>

            <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-ink/50">Progres Panen vs Target {{ $progresPanen['target_min'] }}–{{ $progresPanen['target_max'] }} kg/kolam</span>
                    <span class="font-semibold text-ink/80">{{ number_format($totalBiomassPanen, 1) }} kg</span>
                </div>
                <div class="w-full bg-sand/40 rounded-full h-2">
                    <div class="bg-teal-mid/100 h-2 rounded-full" style="width: {{ $progresPanen['persen_dari_target_min'] }}%"></div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
