@php
    $modules = [
        ['route' => 'stockings.daily-logs.index', 'icon' => 'feed', 'label' => 'Pakan & Kualitas Air Harian'],
        ['route' => 'stockings.water-quality-weekly.index', 'icon' => 'flask', 'label' => 'Kualitas Air Mingguan'],
        ['route' => 'stockings.samplings.index', 'icon' => 'sampling', 'label' => 'Sampling & Pertumbuhan'],
        ['route' => 'stockings.inventory-usage.index', 'icon' => 'droplet', 'label' => 'Aplikasi Kimia & Biologi'],
        ['route' => 'stockings.pond-maintenance-logs.index', 'icon' => 'cycle', 'label' => 'Manajemen Dasar Tambak'],
        ['route' => 'stockings.harvests.index', 'icon' => 'harvest', 'label' => 'Panen'],
        ['route' => 'stockings.emergency-logs.index', 'icon' => 'alert', 'label' => 'Emergency & Kesehatan'],
        ['route' => 'stockings.report.show', 'icon' => 'report', 'label' => 'Biaya & Laporan'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Kolam {{ $stocking->pond->kode_kolam }}" subtitle="{{ $stocking->cycle->nama }}" :back="route('ponds.show', $stocking->pond)" />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-5">

        @if (session('status'))
            <div class="p-4 bg-sehat/10 text-sehat rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($anchoAlert)
            <div class="flex items-start gap-2.5 p-4 bg-kritis/10 text-kritis rounded-lg text-sm">
                <x-icon name="alert" class="w-4 h-4 shrink-0 mt-0.5" />
                <span><strong>Alert:</strong> 2x berturut-turut hasil ancho "sisa banyak" — cek program pakan kolam ini.</span>
            </div>
        @endif

        @if ($waterQualityAlert)
            <div class="flex items-start gap-2.5 p-4 bg-kritis/10 text-kritis rounded-lg text-sm">
                <x-icon name="alert" class="w-4 h-4 shrink-0 mt-0.5" />
                <span><strong>Alert:</strong> ada parameter kualitas air di luar standar mutu pada input terbaru — cek <a href="{{ route('stockings.daily-logs.index', $stocking) }}" class="underline font-medium">Pakan & Kualitas Air Harian</a> atau <a href="{{ route('stockings.water-quality-weekly.index', $stocking) }}" class="underline font-medium">Kualitas Air Mingguan</a>.</span>
            </div>
        @endif

        @if ($flushOutRecommended)
            <div class="flex items-start gap-2.5 p-4 bg-kritis/10 text-kritis rounded-lg text-sm">
                <x-icon name="alert" class="w-4 h-4 shrink-0 mt-0.5" />
                <span>
                    <strong>Rekomendasi: Pertimbangkan Flush-out.</strong>
                    DOC masih di bawah 30 dan kondisi kritis terdeteksi ({{ $srDropSharp ? 'SR turun tajam' : '' }}{{ $srDropSharp && $recentEmergency ? ' & ' : '' }}{{ $recentEmergency ? 'ada kejadian darurat dalam 7 hari terakhir' : '' }}).
                    Keputusan akhir tetap di tangan tim lapangan — catat di <a href="{{ route('stockings.emergency-logs.index', $stocking) }}" class="underline font-medium">Emergency & Kesehatan</a>.
                </span>
            </div>
        @endif

        @if (! $stocking->tgl_pakan_pertama)
            <div class="flex items-start gap-2.5 p-4 bg-perhatian/10 text-perhatian rounded-lg text-sm">
                <x-icon name="alert" class="w-4 h-4 shrink-0 mt-0.5" />
                <span>Tanggal pakan pertama belum diisi — DOC belum bisa dihitung. <a href="{{ route('stockings.edit', $stocking) }}" class="underline font-medium">Lengkapi di sini</a>.</span>
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <x-stat-tile icon="clock" label="DOC" :value="$doc ?? '—'" />
            <x-stat-tile icon="trend-up" label="MBW Terakhir" :value="$latestSampling ? number_format($latestSampling->mbw, 2).' gr' : '—'" tone="emerald" />
            <x-stat-tile icon="pond" label="SR%" :value="$survivalRate !== null ? number_format($survivalRate, 1).'%' : '—'" />
            <x-stat-tile icon="harvest" label="Biomass" :value="$biomassKg !== null ? number_format($biomassKg, 1).' kg' : '—'" tone="amber" />
            <x-stat-tile icon="feed" label="FCR" :value="$fcr !== null ? number_format($fcr, 2) : '—'" />
        </div>

        @if ($chartTumbuh)
            <div class="grid sm:grid-cols-3 gap-4">
                <x-line-chart title="MBW per Sampling" :points="$chartTumbuh['mbw']" unit="gr" :decimals="2" />
                <x-line-chart title="ADG per Sampling" :points="$chartTumbuh['adg']" unit="gr/hari" :decimals="3" />
                <x-line-chart title="SR% per Sampling" :points="$chartTumbuh['sr']" unit="%" :decimals="1" />
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card class="text-sm text-ink/70 space-y-2">
                <div class="flex justify-between"><span class="text-ink/40">Tgl Tebar</span><span class="font-mono font-medium text-ink/80">{{ $stocking->tgl_tebar->format('d M Y') }}</span></div>
                <div class="flex justify-between"><span class="text-ink/40">Tgl Pakan Pertama</span><span class="font-mono font-medium text-ink/80">{{ $stocking->tgl_pakan_pertama?->format('d M Y') ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-ink/40">Asal Benur</span><span class="font-medium text-ink/80">{{ $stocking->asal_benur ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-ink/40">Jumlah Tebar</span><span class="font-mono font-medium text-ink/80">{{ number_format($stocking->jumlah_tebar) }} ekor</span></div>
                <div class="flex justify-between"><span class="text-ink/40">Akumulasi Pakan</span><span class="font-mono font-medium text-ink/80">{{ number_format($akumulasiPakanKg, 1) }} kg</span></div>
                <a href="{{ route('stockings.edit', $stocking) }}" class="inline-flex items-center gap-1.5 pt-2 text-teal-mid hover:underline font-medium">
                    <x-icon name="pencil" class="w-3.5 h-3.5" /> Edit data siklus
                </a>
            </x-card>

            <x-card :padded="false" class="p-2">
                <div class="grid grid-cols-1 gap-1">
                    @foreach ($modules as $module)
                        <a href="{{ route($module['route'], $stocking) }}" class="flex items-center gap-3 text-sm px-3 py-2.5 rounded-lg hover:bg-sand/30 text-ink/80 transition">
                            <div class="w-8 h-8 rounded-lg bg-teal-mid/10 text-teal-mid flex items-center justify-center shrink-0">
                                <x-icon :name="$module['icon']" class="w-4 h-4" />
                            </div>
                            {{ $module['label'] }}
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 ms-auto text-ink/30" />
                        </a>
                    @endforeach
                </div>
            </x-card>
        </div>

        <x-card :padded="false">
            <div class="px-5 py-4 border-b border-lumpur/20 font-display font-semibold text-ink text-sm">
                7 Hari Terakhir — Pakan & Kualitas Air
            </div>
            @if ($recentDailyLogs->isEmpty())
                <div class="p-8 text-center text-sm text-ink/40">Belum ada input harian.</div>
            @else
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Tanggal</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Total Pakan (kg)</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Mortalitas</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">DO Pagi/Sore</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @foreach ($recentDailyLogs as $log)
                            <tr class="hover:bg-sand/30">
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ $log->tgl->format('d M Y') }}</td>
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ number_format($log->pakan_07_kg + $log->pakan_11_kg + $log->pakan_15_kg + $log->pakan_19_kg, 2) }}</td>
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ $log->mortalitas ?? '—' }}</td>
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ $log->do_pagi ?? '—' }} / {{ $log->do_sore ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-right">
                                    <a href="{{ route('stockings.daily-logs.edit', [$stocking, $log]) }}" class="text-ink/40 hover:text-teal-mid">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-card>

        <x-card :padded="false">
            <div class="px-5 py-4 border-b border-lumpur/20 font-display font-semibold text-ink text-sm">
                Riwayat Sampling
            </div>
            @if ($samplings->isEmpty())
                <div class="p-8 text-center text-sm text-ink/40">Belum ada sampling. Jadwal otomatis: DOC 30, lalu tiap 7 hari.</div>
            @else
                <table class="min-w-full divide-y divide-lumpur/10 text-sm">
                    <thead class="bg-sand/30">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Tanggal</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">DOC</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">MBW (gr)</th>
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-ink/50 uppercase tracking-wide">Populasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumpur/10">
                        @foreach ($samplings as $sampling)
                            <tr class="hover:bg-sand/30">
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ $sampling->tgl->format('d M Y') }}</td>
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ $sampling->doc }}</td>
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ number_format($sampling->mbw, 2) }}</td>
                                <td class="px-5 py-2.5 font-mono text-ink/70">{{ number_format($sampling->populasi) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-card>

    </div>
</x-app-layout>
