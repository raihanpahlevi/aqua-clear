@php
    $tileClasses = [
        'sehat' => 'bg-sehat/10 border-sehat/30 hover:border-sehat',
        'perhatian' => 'bg-perhatian/15 border-perhatian/50 hover:border-perhatian',
        'kritis' => 'bg-kritis/10 border-kritis hover:bg-kritis/20',
        'siap-panen' => 'bg-sehat/10 border-sehat hover:bg-sehat/20',
        'idle' => 'bg-sand/30 border-lumpur/20 hover:border-lumpur/40',
    ];
    $legend = [
        ['warna' => 'bg-sehat/40 border border-sehat/60', 'label' => 'Sehat'],
        ['warna' => 'bg-perhatian/40 border border-perhatian/60', 'label' => 'Perhatian'],
        ['warna' => 'bg-kritis/40 border border-kritis', 'label' => 'Kritis'],
        ['warna' => 'bg-sehat/20 border border-sehat', 'label' => 'Siap panen'],
        ['warna' => 'bg-sand border border-lumpur/40', 'label' => 'Non-aktif'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Dashboard" subtitle="Ruang kontrol operasional Tambak Malimping." />
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- === HERO === --}}
        <x-card>
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex-1 flex flex-wrap items-end gap-x-6 gap-y-3 min-w-0">
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-ink/50 uppercase tracking-wider">Total Biomass Aktif</div>
                        <div class="font-mono font-semibold text-4xl sm:text-5xl text-ink mt-1 whitespace-nowrap">
                            {{ number_format($hero['totalBiomass'], 1, ',', '.') }} <span class="text-xl sm:text-2xl text-ink/40">kg</span>
                        </div>
                    </div>
                    @if ($sparkline['points'] !== '')
                        <div class="mb-1">
                            <svg viewBox="0 0 100 30" class="w-36 sm:w-44 h-12 text-teal-mid" preserveAspectRatio="none" fill="none" aria-hidden="true">
                                <polyline points="{{ $sparkline['points'] }}" stroke="currentColor" stroke-width="2"
                                          vector-effect="non-scaling-stroke" stroke-linejoin="round" stroke-linecap="round" />
                            </svg>
                            <div class="text-[10px] font-mono text-ink/40 text-right">tren 30 hari</div>
                        </div>
                    @endif
                </div>
                <div class="flex flex-wrap gap-x-8 gap-y-3 md:border-l md:border-lumpur/20 md:pl-8">
                    <div>
                        <div class="text-xs font-semibold text-ink/50 uppercase tracking-wider">SR% Rata-rata</div>
                        <div class="font-mono font-semibold text-2xl text-ink mt-1">{{ $hero['avgSr'] !== null ? number_format($hero['avgSr'], 1, ',', '.').'%' : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-ink/50 uppercase tracking-wider">DOC Rata-rata</div>
                        <div class="font-mono font-semibold text-2xl text-ink mt-1">{{ $hero['avgDoc'] ?? '—' }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-lumpur/15 text-xs text-ink/50">
                Siklus aktif: <span class="font-mono font-semibold text-ink/70">{{ $hero['kolamAktif'] }}/{{ $hero['kolamTotal'] }}</span> kolam
                · DOC rata-rata <span class="font-mono font-semibold text-ink/70">{{ $hero['avgDoc'] ?? '—' }}</span>
                · estimasi bersifat proyeksi, bukan angka pasti.
            </div>
        </x-card>

        {{-- === PETA KOLAM === --}}
        <x-card :padded="false">
            <div class="px-5 py-4 border-b border-lumpur/20 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                <span class="font-display font-semibold text-ink text-sm">Peta Kolam</span>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-ink/60">
                    @foreach ($legend as $l)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded {{ $l['warna'] }}"></span>{{ $l['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="p-5 space-y-5">
                @forelse ($petaKolam as $blok => $tiles)
                    <div>
                        <div class="font-mono text-[11px] font-semibold text-ink/40 uppercase tracking-widest mb-2">Blok {{ $blok }}</div>
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(68px,1fr))] gap-1.5">
                            @foreach ($tiles as $tile)
                                <a href="{{ route('ponds.show', $tile['pond']) }}"
                                   title="Kolam {{ $tile['pond']->kode_kolam }}{{ $tile['doc'] !== null ? ' · DOC '.$tile['doc'] : '' }}{{ ! empty($tile['violations']) ? ' · '.implode(', ', $tile['violations']) : '' }}"
                                   class="border rounded-lg px-2 py-1.5 flex flex-col gap-0.5 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-mid {{ $tileClasses[$tile['status']] }}">
                                    <span class="flex items-center justify-between gap-1">
                                        <span class="font-mono text-xs font-semibold truncate {{ $tile['status'] === 'idle' ? 'text-ink/50' : 'text-ink' }}">{{ $tile['pond']->kode_kolam }}</span>
                                        @if ($tile['status'] === 'kritis')
                                            <x-icon name="alert" class="w-3 h-3 text-kritis shrink-0" />
                                        @elseif ($tile['siapPanen'])
                                            <x-icon name="harvest" class="w-3 h-3 text-sehat shrink-0" />
                                        @endif
                                    </span>
                                    <span class="font-mono text-[10px] truncate {{ $tile['status'] === 'idle' ? 'text-ink/30' : 'text-ink/50' }}">
                                        {{ $tile['doc'] !== null ? 'DOC '.$tile['doc'] : str_replace('_', ' ', $tile['pond']->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-sm text-ink/40">Belum ada kolam terdaftar.</div>
                @endforelse
            </div>
        </x-card>

        {{-- === METRIK SEKUNDER === --}}
        <x-card :padded="false" class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-lumpur/15">
            <div class="px-5 py-3.5">
                <div class="text-[11px] font-semibold text-ink/50 uppercase tracking-wider">Rata-rata FCR</div>
                <div class="font-mono font-semibold text-lg text-ink mt-0.5">{{ $metrik['fcr'] !== null ? number_format($metrik['fcr'], 2, ',', '.') : '—' }}</div>
            </div>
            <div class="px-5 py-3.5">
                <div class="text-[11px] font-semibold text-ink/50 uppercase tracking-wider">Pakan Bulan Ini</div>
                <div class="font-mono font-semibold text-lg text-ink mt-0.5">
                    {{ number_format($metrik['pakanBulanIni']['kg'], 1, ',', '.') }} kg
                    <span class="text-xs text-ink/40 font-normal">· Rp {{ number_format($metrik['pakanBulanIni']['rp'], 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="px-5 py-3.5">
                <div class="text-[11px] font-semibold text-ink/50 uppercase tracking-wider">Est. Laba/Rugi Berjalan</div>
                <div class="font-mono font-semibold text-lg mt-0.5 {{ $metrik['labaRugi'] >= 0 ? 'text-sehat' : 'text-kritis' }}">
                    Rp {{ number_format($metrik['labaRugi'], 0, ',', '.') }}
                </div>
            </div>
        </x-card>

        {{-- === PERLU PERHATIAN + MENUJU PANEN === --}}
        <div class="grid lg:grid-cols-2 gap-4 items-start">
            <x-card :padded="false">
                <div class="px-5 py-3.5 border-b border-lumpur/20 font-display font-semibold text-ink text-sm">Perlu Perhatian Hari Ini</div>
                @if ($perluPerhatian->isEmpty())
                    <div class="px-5 py-8 text-sm text-ink/60 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sehat inline-block"></span>
                        Semua kolam dalam kondisi normal.
                    </div>
                @else
                    <ul class="divide-y divide-lumpur/10">
                        @foreach ($perluPerhatian as $item)
                            <li>
                                <a href="{{ $item['url'] }}"
                                   class="flex items-start gap-3 px-4 py-2.5 border-l-2 hover:bg-sand/30 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-mid {{ $item['tone'] === 'kritis' ? 'border-l-kritis' : 'border-l-perhatian' }}">
                                    <span class="font-mono text-xs font-semibold text-ink w-10 shrink-0 pt-0.5">{{ $item['kolam'] }}</span>
                                    <span class="text-sm text-ink/80">{{ $item['pesan'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <x-card :padded="false">
                <div class="px-5 py-3.5 border-b border-lumpur/20 font-display font-semibold text-ink text-sm">Menuju Panen</div>
                @if ($menujuPanen->isEmpty())
                    <div class="px-5 py-8 text-sm text-ink/40">Belum ada kolam dengan MBW ≥ 11 gr.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-lumpur/20">
                                    <th class="px-4 py-2 text-left text-[11px] font-semibold text-ink/50 uppercase tracking-wider">Kolam</th>
                                    <th class="px-4 py-2 text-left text-[11px] font-semibold text-ink/50 uppercase tracking-wider">DOC</th>
                                    <th class="px-4 py-2 text-left text-[11px] font-semibold text-ink/50 uppercase tracking-wider">MBW (gr)</th>
                                    <th class="px-4 py-2 text-left text-[11px] font-semibold text-ink/50 uppercase tracking-wider">Size</th>
                                    <th class="px-4 py-2 text-right text-[11px] font-semibold text-ink/50 uppercase tracking-wider">Est. Biomass</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-lumpur/10">
                                @foreach ($menujuPanen as $row)
                                    <tr class="{{ $row['siap'] ? 'bg-sehat/10' : '' }}">
                                        <td class="px-4 py-2 font-mono text-xs font-semibold text-ink">
                                            <a href="{{ $row['url'] }}" class="hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-mid rounded">{{ $row['kolam'] }}</a>
                                            @if ($row['siap'])
                                                <span class="text-[10px] text-sehat font-semibold ml-1">siap panen</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 font-mono text-xs text-ink/70">{{ $row['doc'] ?? '—' }}</td>
                                        <td class="px-4 py-2 font-mono text-xs text-ink/70">{{ number_format($row['mbw'], 2, ',', '.') }}</td>
                                        <td class="px-4 py-2 font-mono text-xs text-ink/70">{{ number_format($row['size'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 font-mono text-xs text-ink/70 text-right">{{ number_format($row['biomass'], 1, ',', '.') }} kg</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- === AKTIVITAS TERBARU (diturunkan derajatnya) === --}}
        <x-card :padded="false">
            <div class="px-5 py-3 border-b border-lumpur/20 font-display font-semibold text-ink/70 text-xs uppercase tracking-wider">Aktivitas Terbaru</div>
            @if ($aktivitasTerbaru->isEmpty())
                <div class="p-6 text-center text-xs text-ink/40">Belum ada aktivitas.</div>
            @else
                <ul class="divide-y divide-lumpur/10">
                    @foreach ($aktivitasTerbaru as $item)
                        <li class="flex items-center gap-3 px-5 py-2">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-medium text-ink/80">{{ $item['tipe'] }} — Kolam {{ $item['kolam'] }}</span>
                                <span class="text-[11px] text-ink/40 font-mono ml-2">{{ $item['tgl']->format('d M Y') }}</span>
                            </div>
                            <div class="text-[11px] text-ink/40 shrink-0 font-mono">{{ $item['waktu']->diffForHumans() }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

    </div>
</x-app-layout>
