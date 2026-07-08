<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ink leading-tight">
                Aplikasi Kimia & Biologi — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.inventory-usage.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    + Catat Pemakaian
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-sehat/10 text-sehat rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <p class="text-sm text-ink/50">Termasuk pembelian pakan (biaya) — terpisah dari input kg pakan harian di modul Pakan & Kualitas Air.</p>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-x-auto">
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Kategori</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Item</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Qty</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Biaya (Rp)</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($usages as $usage)
                            <tr>
                                <td class="px-4 py-2 font-mono text-ink/80 font-medium">{{ $usage->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-ink/70 capitalize">{{ $usage->kategori }}</td>
                                <td class="px-4 py-2 text-ink/70">{{ $usage->item }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $usage->qty }} {{ $usage->satuan }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $usage->harga !== null ? number_format($usage->harga, 0, ',', '.') : '— (belum ada data harga)' }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('stockings.inventory-usage.edit', [$stocking, $usage]) }}" class="text-teal-mid hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('stockings.inventory-usage.destroy', [$stocking, $usage]) }}" class="inline" onsubmit="return confirm('Hapus catatan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-kritis hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-ink/50">Belum ada pemakaian tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
