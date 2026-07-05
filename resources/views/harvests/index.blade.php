<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Panen — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.harvests.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    + Catat Panen
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-500 dark:text-slate-400">Progres vs Target {{ $progres['target_min'] }}–{{ $progres['target_max'] }} kg/kolam (akumulasi semua tahap)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($progres['biomass_kg'], 1) }} kg</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $progres['persen_dari_target_min'] }}%"></div>
                </div>
            </div>

            <div class="bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-sm p-4 rounded-lg">
                Referensi tahap (Vaname):
                @foreach ($referensiTahap as $ref)
                    <span class="inline-block mr-4">{{ $ref['label'] }}: MBW {{ $ref['mbw'] }}, size {{ $ref['size'] }}</span>
                @endforeach
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/40">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tahap</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Berat (kg)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Size</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Harga/kg</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Pendapatan</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($harvests as $harvest)
                            <tr>
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 font-medium">{{ $referensiTahap[$harvest->tahap]['label'] }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $harvest->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($harvest->berat_kg, 1) }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $harvest->size ?? '—' }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($harvest->harga_per_kg, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($harvest->pendapatan, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.harvests.edit', [$stocking, $harvest]) }}" class="text-teal-600 dark:text-teal-400 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">Belum ada panen tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
