<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Sampling & Pertumbuhan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.samplings.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    + Input Sampling
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-sm p-4 rounded-lg">
                Jadwal otomatis: sampling pertama di DOC 30, lalu tiap 7 hari.
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/40">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">DOC</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">MBW (gr)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">ADG</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Size (ekor/kg)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Populasi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">SR%</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Biomass (kg)</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($rows as $row)
                            @php $s = $row['sampling']; @endphp
                            <tr>
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 font-medium">{{ $s->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $s->doc }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($s->mbw, 2) }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $row['adg'] !== null ? number_format($row['adg'], 3) : '—' }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $row['size'] > 0 ? number_format($row['size'], 1) : '—' }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($s->populasi) }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($row['sr'], 1) }}%</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ number_format($row['biomass'], 1) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.samplings.edit', [$stocking, $s]) }}" class="text-teal-600 dark:text-teal-400 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">Belum ada sampling.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
