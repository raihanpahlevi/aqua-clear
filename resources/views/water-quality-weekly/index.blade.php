<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-semibold text-xl text-ink leading-tight">
                Kualitas Air Mingguan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-ink/50 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.water-quality-weekly.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-mid border border-transparent rounded-lg font-semibold text-xs text-paper uppercase tracking-wide hover:bg-teal-deep">
                    + Input Uji Air
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
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">TAN</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Ammonia</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Nitrit</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Nitrat</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-ink/50 uppercase">Rasio V/B</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @forelse ($logs as $log)
                            @php
                                $ratio = $ratios[$log->id] ?? null;
                                $violations = $waterQualityService->weeklyViolations($log);
                            @endphp
                            <tr title="{{ implode(', ', $violations) }}">
                                <td class="px-4 py-2 font-mono text-ink/80 font-medium">
                                    {{ $log->tgl->format('d M Y') }}
                                    @if (! empty($violations))
                                        <x-icon name="alert" class="w-3.5 h-3.5 inline text-kritis" />
                                    @endif
                                </td>
                                <td class="px-4 py-2 font-mono {{ $waterQualityService->isTanHigh($log->tan) ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->tan ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $waterQualityService->isAmmoniaHigh($log->ammonia) ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->ammonia ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $waterQualityService->isNitritHigh($log->nitrit) ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->nitrit ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono {{ $waterQualityService->isNitratHigh($log->nitrat) ? 'text-kritis font-semibold' : 'text-ink/70' }}">{{ $log->nitrat ?? '—' }}</td>
                                <td class="px-4 py-2 font-mono">
                                    @if ($ratio !== null)
                                        <span class="{{ $ratio > 10 ? 'text-kritis font-semibold' : 'text-ink/70' }}">
                                            {{ number_format($ratio, 1) }}% {{ $ratio > 10 ? '⚠' : '' }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.water-quality-weekly.edit', [$stocking, $log]) }}" class="text-teal-mid hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-ink/50">Belum ada data kualitas air mingguan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-ink/40">Angka merah = di luar standar mutu (TAN &lt;2ppm, Ammonia &lt;0,1ppm, Nitrit &lt;0,1ppm, Nitrat &lt;50ppm, Rasio V/B &lt;10%).</p>

        </div>
    </div>
</x-app-layout>
