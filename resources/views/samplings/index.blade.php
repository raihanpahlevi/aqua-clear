<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-semibold text-xl text-ink leading-tight">
                Sampling & Pertumbuhan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.samplings.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    + Input Sampling
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-sehat/10 text-sehat rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-teal-mid/10 text-teal-mid text-sm p-4 rounded-lg">
                Jadwal otomatis: sampling pertama di DOC 30, lalu tiap 7 hari.
            </div>

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-x-auto">
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">DOC</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">MBW (gr)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">ADG</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Size (ekor/kg)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Populasi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">SR%</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Biomass (kg)</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($rows as $row)
                            @php $s = $row['sampling']; @endphp
                            <tr>
                                <td class="px-4 py-2 font-mono text-ink/80 font-medium">{{ $s->tgl->format('d M Y') }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $s->doc }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($s->mbw, 2) }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $row['adg'] !== null ? number_format($row['adg'], 3) : '—' }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $row['size'] > 0 ? number_format($row['size'], 1) : '—' }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($s->populasi) }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($row['sr'], 1) }}%</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ number_format($row['biomass'], 1) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.samplings.edit', [$stocking, $s]) }}" class="text-teal-mid hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-6 text-center text-ink/50">Belum ada sampling.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
