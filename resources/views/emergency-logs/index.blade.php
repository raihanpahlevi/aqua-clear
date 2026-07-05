<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Emergency & Kesehatan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.emergency-logs.create', $stocking) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide shadow-sm hover:bg-rose-700">
                    + Catat Kejadian
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="p-4 bg-slate-50 dark:bg-slate-900/40 text-slate-600 dark:text-slate-300 text-sm rounded-lg">
                Flush-out berlaku bila DOC &lt; 30 dan kondisi kritis (SR turun tajam / serangan penyakit), lalu restocking.
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/40">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Jenis</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tindakan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 font-medium">{{ $log->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $log->jenis }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $log->tindakan ?? '—' }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $log->keputusan ? str_replace('_', ' ', $log->keputusan) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">Belum ada kejadian tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
