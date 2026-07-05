<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Persiapan Tambak & Air" subtitle="Kolam {{ $pond->kode_kolam }}" :back="route('ponds.show', $pond)">
            <x-slot name="actions">
                <a href="{{ route('ponds.prep-logs.create', $pond) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> Catat Progres
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

        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs rounded-lg">
            Checklist bebas — belum ada standar mutu baku dari klien, jadi belum ada validasi ketat di sini.
        </div>

        <x-card :padded="false">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                <thead class="bg-slate-50/60 dark:bg-slate-800/40">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tanggal</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Jenis</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Siklus</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Checklist Selesai</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Biaya</th>
                        <th class="px-5 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($prepLogs as $log)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="px-5 py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $log->tgl->format('d M Y') }}</td>
                            <td class="px-5 py-2.5"><x-badge :tone="$log->jenis === 'tambak' ? 'teal' : 'sky'">{{ $log->jenis }}</x-badge></td>
                            <td class="px-5 py-2.5 text-slate-600 dark:text-slate-300">{{ $log->cycle->nama ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-slate-600 dark:text-slate-300">
                                @if (empty($log->checklist))
                                    <span class="text-slate-400">Belum ada</span>
                                @else
                                    {{ implode(', ', $log->checklist) }}
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-slate-600 dark:text-slate-300">{{ $log->biaya !== null ? 'Rp '.number_format($log->biaya, 0, ',', '.') : '—' }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('ponds.prep-logs.edit', [$pond, $log]) }}" class="text-slate-400 hover:text-teal-600 dark:hover:text-teal-400">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-slate-500">Belum ada progres persiapan tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </x-card>

    </div>
</x-app-layout>
