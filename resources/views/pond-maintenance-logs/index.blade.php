<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Manajemen Dasar Tambak" subtitle="Kolam {{ $stocking->pond->kode_kolam }} — Siphon, lumpur, kincir" :back="route('stockings.show', $stocking)">
            <x-slot name="actions">
                <a href="{{ route('stockings.pond-maintenance-logs.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Catat Hari Ini
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

        <x-card :padded="false">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                <thead class="bg-sand/30">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Siphon</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Kondisi Lumpur</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Jumlah Kincir</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Jam Nyala</th>
                        <th class="px-5 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lumpur/10">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-sand/30">
                            <td class="px-5 py-2.5 font-mono text-ink/80 font-medium">{{ $log->tgl->format('d M Y') }}</td>
                            <td class="px-5 py-2.5">
                                @if ($log->siphon === null)
                                    <span class="text-ink/40">—</span>
                                @else
                                    <x-badge :tone="$log->siphon ? 'emerald' : 'slate'">{{ $log->siphon ? 'Ya' : 'Tidak' }}</x-badge>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-ink/70">{{ $log->kondisi_lumpur ?? '—' }}</td>
                            <td class="px-5 py-2.5 font-mono text-ink/70">{{ $log->jumlah_kincir ?? '—' }}</td>
                            <td class="px-5 py-2.5 font-mono text-ink/70">{{ $log->jam_nyala_kincir ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('stockings.pond-maintenance-logs.edit', [$stocking, $log]) }}" class="text-ink/40 hover:text-teal-mid">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-ink/40">Belum ada catatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </x-card>

    </div>
</x-app-layout>
