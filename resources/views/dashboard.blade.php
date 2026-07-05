<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Dashboard" subtitle="Ringkasan operasional {{ $kolamAktif['total'] }} kolam Tambak Malimping." />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <div class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-6 gap-4">
            <x-stat-tile icon="pond" label="Kolam Aktif" :value="$kolamAktif['aktif'].' / '.$kolamAktif['total']" />
            <x-stat-tile icon="trend-up" label="Rata-rata FCR" :value="$rataRataFcr !== null ? number_format($rataRataFcr, 2) : '—'" />
            <x-stat-tile
                icon="alert"
                label="Kolam Emergency"
                :value="$kolamEmergency"
                :tone="$kolamEmergency > 0 ? 'rose' : 'slate'"
            />
            <x-stat-tile
                icon="feed"
                label="Pakan Bulan Ini"
                :value="number_format($pakanBulanIni['kg'], 1).' kg'"
                :sublabel="'Rp '.number_format($pakanBulanIni['rp'], 0, ',', '.')"
                tone="amber"
            />
            <x-stat-tile icon="harvest" label="Est. Biomass Siap Panen" :value="number_format($estimasiBiomassSiapPanen, 1).' kg'" tone="emerald" />
            <x-stat-tile
                icon="report"
                label="Est. Laba/Rugi Berjalan"
                value="Rp {{ number_format($estimasiLabaRugi, 0, ',', '.') }}"
                :tone="$estimasiLabaRugi >= 0 ? 'emerald' : 'rose'"
            />
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500 -mt-2">Estimasi biomass & laba/rugi bersifat proyeksi, bukan angka pasti.</p>

        <x-card :padded="false">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 font-semibold text-slate-700 dark:text-slate-200 text-sm">
                Aktivitas Terbaru
            </div>
            @if ($aktivitasTerbaru->isEmpty())
                <div class="p-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada aktivitas.</div>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($aktivitasTerbaru as $item)
                        @php
                            $tipeIcon = match ($item['tipe']) {
                                'Pakan & Kualitas Air' => 'feed',
                                'Sampling' => 'sampling',
                                'Panen' => 'harvest',
                                default => 'dashboard',
                            };
                        @endphp
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                                <x-icon :name="$tipeIcon" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $item['tipe'] }} — Kolam {{ $item['kolam'] }}</div>
                                <div class="text-xs text-slate-400">{{ $item['tgl']->format('d M Y') }}</div>
                            </div>
                            <div class="text-xs text-slate-400 shrink-0">{{ $item['waktu']->diffForHumans() }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

    </div>
</x-app-layout>
