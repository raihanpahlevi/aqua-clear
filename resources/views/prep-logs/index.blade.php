<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Persiapan Tambak & Air" subtitle="Kolam {{ $pond->kode_kolam }}" :back="route('ponds.show', $pond)">
            <x-slot name="actions">
                <a href="{{ route('ponds.prep-logs.create', $pond) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Catat Progres
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-sehat/10 text-sehat rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="p-4 bg-sand/30 text-ink/60 text-xs rounded-lg">
            Checklist bebas — belum ada standar mutu baku dari klien, jadi belum ada validasi ketat di sini.
        </div>

        <x-card :padded="false">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                <thead class="bg-sand/30">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Jenis</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Siklus</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Checklist Selesai</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Biaya</th>
                        <th class="px-5 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lumpur/10">
                    @forelse ($prepLogs as $log)
                        <tr class="hover:bg-sand/30">
                            <td class="px-5 py-2.5 font-mono text-ink/80 font-medium">{{ $log->tgl->format('d M Y') }}</td>
                            <td class="px-5 py-2.5"><x-badge :tone="$log->jenis === 'tambak' ? 'teal' : 'sky'">{{ $log->jenis }}</x-badge></td>
                            <td class="px-5 py-2.5 text-ink/70">{{ $log->cycle->nama ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-ink/70">
                                @if (empty($log->checklist))
                                    <span class="text-ink/40">Belum ada</span>
                                @else
                                    {{ implode(', ', $log->checklist) }}
                                @endif
                            </td>
                            <td class="px-5 py-2.5 font-mono text-ink/70">{{ $log->biaya !== null ? 'Rp '.number_format($log->biaya, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('ponds.prep-logs.edit', [$pond, $log]) }}" class="text-ink/40 hover:text-teal-mid">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-ink/40">Belum ada progres persiapan tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </x-card>

    </div>
</x-app-layout>
