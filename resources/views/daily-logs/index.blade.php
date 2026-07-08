<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-semibold text-xl text-ink leading-tight">
                Pakan & Kualitas Air Harian — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.daily-logs.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    + Input Hari Ini
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

            <div class="bg-sand/40 rounded-2xl border border-lumpur/20 overflow-x-auto">
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Pakan 07/11/15/19 (kg)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Kode Pakan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">DO Pagi/Sore</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">pH Pagi/Sore</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Suhu Pagi/Sore</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Salinitas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Mortalitas</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($dailyLogs as $log)
                            @php
                                $violations = $waterQualityService->dailyViolations($log);
                                $doBad = $waterQualityService->isDoLow($log->do_pagi) || $waterQualityService->isDoLow($log->do_sore);
                                $phBad = $waterQualityService->isPhOut($log->ph_pagi) || $waterQualityService->isPhOut($log->ph_sore);
                                $suhuBad = $waterQualityService->isSuhuOut($log->suhu_pagi) || $waterQualityService->isSuhuOut($log->suhu_sore);
                                $salinitasBad = $waterQualityService->isSalinitasOut($log->salinitas);
                            @endphp
                            <tr title="{{ implode(', ', $violations) }}">
                                <td class="px-4 py-2 font-mono text-ink/80 font-medium">
                                    {{ $log->tgl->format('d M Y') }}
                                    @if (! empty($violations))
                                        <x-icon name="alert" class="w-3.5 h-3.5 inline text-kritis" />
                                    @endif
                                </td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $log->pakan_07_kg ?? '—' }} / {{ $log->pakan_11_kg ?? '—' }} / {{ $log->pakan_15_kg ?? '—' }} / {{ $log->pakan_19_kg ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">{{ $log->kode_pakan ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $doBad ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->do_pagi ?? '—' }} / {{ $log->do_sore ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $phBad ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->ph_pagi ?? '—' }} / {{ $log->ph_sore ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $suhuBad ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->suhu_pagi ?? '—' }} / {{ $log->suhu_sore ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $salinitasBad ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->salinitas ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono text-ink/70">
                                    {{ $log->mortalitas !== null ? $growthService->correctedMortality($log->mortalitas) : '—' }}
                                    @if ($log->mortalitas !== null)
                                        <span class="text-xs text-ink/40">(obs. {{ $log->mortalitas }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.daily-logs.edit', [$stocking, $log]) }}" class="text-teal-mid hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-6 text-center text-ink/50">Belum ada input harian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-ink/40">Angka merah = di luar standar mutu (DO >4ppm, pH 7,5–8,5, Suhu 28–32°C, Salinitas 25–30ppt).</p>

            {{ $dailyLogs->links() }}

        </div>
    </div>
</x-app-layout>
