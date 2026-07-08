<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ink leading-tight">
                Panen — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.harvests.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    + Catat Panen
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

            <div class="bg-sand/40 p-4 rounded-2xl border border-lumpur/20">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-ink/50">Progres vs Target {{ $progres['target_min'] }}–{{ $progres['target_max'] }} kg/kolam (akumulasi semua tahap)</span>
                    <span class="font-semibold text-ink/80">{{ number_format($progres['biomass_kg'], 1) }} kg</span>
                </div>
                <div class="w-full bg-sand/40 rounded-full h-2">
                    <div class="bg-teal-mid/100 h-2 rounded-full" style="width: {{ $progres['persen_dari_target_min'] }}%"></div>
                </div>
            </div>

            <div class="bg-teal-mid/10 text-teal-mid text-sm p-4 rounded-lg">
                Referensi tahap (Vaname):
                @foreach ($referensiTahap as $ref)
                    <span class="inline-block mr-4">{{ $ref['label'] }}: MBW {{ $ref['mbw'] }}, size {{ $ref['size'] }}</span>
                @endforeach
            </div>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-x-auto">
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tahap</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Berat (kg)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Size</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Harga/kg</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Pendapatan</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($harvests as $harvest)
                            <tr>
                                <td class="px-4 py-2 text-ink/80 font-medium">{{ $referensiTahap[$harvest->tahap]['label'] }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $harvest->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($harvest->berat_kg, 1) }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $harvest->size ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($harvest->harga_per_kg, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($harvest->pendapatan, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.harvests.edit', [$stocking, $harvest]) }}" class="text-teal-mid hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-ink/50">Belum ada panen tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
