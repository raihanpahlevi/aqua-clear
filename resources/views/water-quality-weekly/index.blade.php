<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                Kualitas Air Mingguan — Kolam {{ $stocking->pond->kode_kolam }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('stockings.show', $stocking) }}" class="text-sm text-slate-500 dark:text-slate-400 hover:underline self-center">← Kembali</a>
                <a href="{{ route('stockings.water-quality-weekly.create', $stocking) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wide hover:bg-teal-700">
                    + Input Uji Air
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/70 dark:border-slate-800 shadow-sm shadow-slate-900/5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/40">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">TAN</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Ammonia</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Nitrit</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Nitrat</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Rasio V/B</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($logs as $log)
                            @php
                                $ratio = $ratios[$log->id] ?? null;
                                $violations = $waterQualityService->weeklyViolations($log);
                            @endphp
                            <tr title="{{ implode(', ', $violations) }}">
                                <td class="px-4 py-2 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $log->tgl->format('d M Y') }}
                                    @if (! empty($violations))
                                        <x-icon name="alert" class="w-3.5 h-3.5 inline text-rose-500" />
                                    @endif
                                </td>
                                <td class="px-4 py-2 {{ $waterQualityService->isTanHigh($log->tan) ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">{{ $log->tan ?? '—' }}</td>
                                <td class="px-4 py-2 {{ $waterQualityService->isAmmoniaHigh($log->ammonia) ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">{{ $log->ammonia ?? '—' }}</td>
                                <td class="px-4 py-2 {{ $waterQualityService->isNitritHigh($log->nitrit) ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">{{ $log->nitrit ?? '—' }}</td>
                                <td class="px-4 py-2 {{ $waterQualityService->isNitratHigh($log->nitrat) ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">{{ $log->nitrat ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    @if ($ratio !== null)
                                        <span class="{{ $ratio > 10 ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">
                                            {{ number_format($ratio, 1) }}% {{ $ratio > 10 ? '⚠' : '' }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('stockings.water-quality-weekly.edit', [$stocking, $log]) }}" class="text-teal-600 dark:text-teal-400 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">Belum ada data kualitas air mingguan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-slate-400">Angka merah = di luar standar mutu (TAN &lt;2ppm, Ammonia &lt;0,1ppm, Nitrit &lt;0,1ppm, Nitrat &lt;50ppm, Rasio V/B &lt;10%).</p>

        </div>
    </div>
</x-app-layout>
