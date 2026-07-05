<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Manajemen Dasar Tambak" subtitle="Kolam {{ $stocking->pond->kode_kolam }} — Siphon, lumpur, kincir" :back="route('stockings.show', $stocking)">
            <x-slot name="actions">
                <a href="{{ route('stockings.pond-maintenance-logs.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Catat Hari Ini
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <x-card :padded="false">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                <thead class="bg-slate-50/60 dark:bg-slate-800/40">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Siphon</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kondisi Lumpur</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Jumlah Kincir</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Jam Nyala</th>
                        <th class="px-5 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="px-5 py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $log->tgl->format('d M Y') }}</td>
                            <td class="px-5 py-2.5">
                                @if ($log->siphon === null)
                                    <span class="text-slate-400">—</span>
                                @else
                                    <x-badge :tone="$log->siphon ? 'emerald' : 'slate'">{{ $log->siphon ? 'Ya' : 'Tidak' }}</x-badge>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-slate-600 dark:text-slate-300">{{ $log->kondisi_lumpur ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-slate-600 dark:text-slate-300">{{ $log->jumlah_kincir ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-slate-600 dark:text-slate-300">{{ $log->jam_nyala_kincir ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('stockings.pond-maintenance-logs.edit', [$stocking, $log]) }}" class="text-slate-400 hover:text-teal-600 dark:hover:text-teal-400">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-slate-500">Belum ada catatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </x-card>

    </div>
</x-app-layout>
