<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Aplikasi Kimia & Biologi — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.inventory-usage.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    + Catat Pemakaian
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

            <p class="text-sm text-slate-500 dark:text-slate-400">Termasuk pembelian pakan (biaya) — terpisah dari input kg pakan harian di modul Pakan & Kualitas Air.</p>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/40">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Kategori</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Item</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Qty</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Biaya (Rp)</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($usages as $usage)
                            <tr>
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 font-medium">{{ $usage->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300 capitalize">{{ $usage->kategori }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $usage->item }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $usage->qty }} {{ $usage->satuan }}</td>
                                <td class="px-4 py-2 text-slate-600 dark:text-slate-300">{{ $usage->harga !== null ? number_format($usage->harga, 0, ',', '.') : '— (belum ada data harga)' }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('stockings.inventory-usage.edit', [$stocking, $usage]) }}" class="text-teal-600 dark:text-teal-400 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('stockings.inventory-usage.destroy', [$stocking, $usage]) }}" class="inline" onsubmit="return confirm('Hapus catatan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">Belum ada pemakaian tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
