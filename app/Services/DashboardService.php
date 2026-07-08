<?php

namespace App\Services;

use App\Models\Cycle;
use App\Models\DailyLog;
use App\Models\EmergencyLog;
use App\Models\Harvest;
use App\Models\InventoryUsage;
use App\Models\Pond;
use App\Models\Sampling;
use App\Models\Stocking;
use App\Models\WaterQualityWeekly;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 6 kartu KPI dashboard — PRD Bagian 7 — plus data "ruang kontrol" (controlRoomData).
 */
class DashboardService
{
    /** Ambang MBW panen partial 1 (PRD 5.4) — sama dengan estimasiBiomassSiapPanen. */
    private const MBW_SIAP_PANEN = 13.0;

    /** Ambang MBW masuk daftar "Menuju Panen" di dashboard. */
    private const MBW_MENUJU_PANEN = 11.0;

    public function __construct(
        private GrowthService $growthService,
        private FeedService $feedService,
        private CostService $costService,
        private DocService $docService,
        private WaterQualityService $waterQualityService,
    ) {
    }

    public function kolamAktif(int $farmId): array
    {
        return [
            'aktif' => Pond::whereHas('block', fn ($q) => $q->where('farm_id', $farmId))->where('status', 'aktif')->count(),
            'total' => Pond::whereHas('block', fn ($q) => $q->where('farm_id', $farmId))->count(),
        ];
    }

    private function activeStockings(int $farmId): Collection
    {
        return Stocking::whereHas('pond', fn ($q) => $q->where('status', 'aktif')
            ->whereHas('block', fn ($q2) => $q2->where('farm_id', $farmId)))
            ->with('pond')
            ->get();
    }

    public function rataRataFcr(int $farmId): ?float
    {
        $stockings = $this->activeStockings($farmId);

        $fcrs = $stockings
            ->map(function (Stocking $stocking) {
                $biomass = $this->growthService->latestBiomassKg($stocking);
                $akumulasiPakan = $this->feedService->akumulasiPakanKg($stocking);

                return $this->feedService->fcr($akumulasiPakan, $biomass);
            })
            ->filter(fn ($fcr) => $fcr !== null);

        return $fcrs->isEmpty() ? null : $fcrs->avg();
    }

    /**
     * Kolam Emergency = kolam dengan emergency_log dalam 3 hari terakhir untuk stocking aktifnya.
     * PRD tidak definisikan ambang ini secara eksplisit — asumsi tambahan saya, lihat CLAUDE.md.
     */
    public function kolamEmergencyCount(int $farmId): int
    {
        return $this->activeStockings($farmId)
            ->filter(fn (Stocking $stocking) => $stocking->emergencyLogs()->where('tgl', '>=', now()->subDays(3))->exists())
            ->count();
    }

    public function pakanBulanIni(int $farmId): array
    {
        $stockingIds = $this->activeStockings($farmId)->pluck('id');

        $kg = DailyLog::whereIn('stocking_id', $stockingIds)
            ->whereBetween('tgl', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('COALESCE(SUM(pakan_07_kg),0) + COALESCE(SUM(pakan_11_kg),0) + COALESCE(SUM(pakan_15_kg),0) + COALESCE(SUM(pakan_19_kg),0) as total')
            ->value('total');

        $rp = \App\Models\InventoryUsage::whereIn('stocking_id', $stockingIds)
            ->where('kategori', 'pakan')
            ->whereBetween('tgl', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('harga');

        return ['kg' => (float) $kg, 'rp' => (float) $rp];
    }

    /**
     * Proyeksi biomass dari kolam yang MBW-nya sudah capai ambang panen partial 1 (>=13 gr).
     */
    public function estimasiBiomassSiapPanen(int $farmId): float
    {
        return $this->activeStockings($farmId)
            ->map(function (Stocking $stocking) {
                $latest = $this->growthService->latestSampling($stocking);

                if (! $latest || $latest->mbw < 13) {
                    return 0.0;
                }

                return $this->growthService->biomassKg($latest->populasi, (float) $latest->mbw);
            })
            ->sum();
    }

    public function estimasiLabaRugiBerjalan(int $farmId): float
    {
        return $this->activeStockings($farmId)
            ->map(fn (Stocking $stocking) => $this->costService->labaRugi($stocking))
            ->sum();
    }

    public function aktivitasTerbaru(int $farmId, int $limit = 10): Collection
    {
        $stockingIds = Stocking::whereHas('pond.block', fn ($q) => $q->where('farm_id', $farmId))->pluck('id');

        $dailyLogs = DailyLog::whereIn('stocking_id', $stockingIds)->with('stocking.pond')->latest('created_at')->limit($limit)
            ->get()->map(fn (DailyLog $log) => [
                'tipe' => 'Pakan & Kualitas Air',
                'kolam' => $log->stocking->pond->kode_kolam,
                'tgl' => $log->tgl,
                'waktu' => $log->created_at,
            ]);

        $samplings = Sampling::whereIn('stocking_id', $stockingIds)->with('stocking.pond')->latest('created_at')->limit($limit)
            ->get()->map(fn (Sampling $s) => [
                'tipe' => 'Sampling',
                'kolam' => $s->stocking->pond->kode_kolam,
                'tgl' => $s->tgl,
                'waktu' => $s->created_at,
            ]);

        $harvests = Harvest::whereIn('stocking_id', $stockingIds)->with('stocking.pond')->latest('created_at')->limit($limit)
            ->get()->map(fn (Harvest $h) => [
                'tipe' => 'Panen',
                'kolam' => $h->stocking->pond->kode_kolam,
                'tgl' => $h->tgl,
                'waktu' => $h->created_at,
            ]);

        return $dailyLogs->concat($samplings)->concat($harvests)
            ->sortByDesc('waktu')
            ->take($limit)
            ->values();
    }

    /**
     * Seluruh data dashboard "ruang kontrol" dalam belasan query TOTAL, berapa pun
     * jumlah kolam — JANGAN tambah query per-kolam/per-stocking di dalam loop sini.
     * Semua rumus tetap dari service masing-masing (Growth/Feed/Doc/WaterQuality);
     * method ini cuma mem-batch pengambilan datanya.
     */
    public function controlRoomData(int $farmId, ?int $pondId = null, ?int $cycleId = null): array
    {
        $today = now();

        // 1 query — semua kolam + blok (selalu semua, buat dropdown filter)
        $ponds = Pond::with('block')
            ->whereHas('block', fn ($q) => $q->where('farm_id', $farmId))
            ->orderBy('kode_kolam')
            ->get();

        // Filter tampilan: per kolam dan/atau per batch (= Siklus, keputusan client 2026-07-08)
        $tilePonds = $pondId ? $ponds->where('id', $pondId) : $ponds;

        $activePondIds = $tilePonds->where('status', 'aktif')->pluck('id');

        // 1 query — stocking terbaru per kolam aktif (dibatasi siklus bila difilter)
        $stockings = Stocking::whereIn('pond_id', $activePondIds)
            ->when($cycleId, fn ($q) => $q->where('cycle_id', $cycleId))
            ->orderByDesc('tgl_tebar')
            ->orderByDesc('id')
            ->get()
            ->unique('pond_id')
            ->values();

        // Saat filter siklus aktif, kolam tanpa stocking di siklus itu disembunyikan dari peta
        if ($cycleId) {
            $pondIdsInCycle = $stockings->pluck('pond_id')->all();
            $tilePonds = $tilePonds->filter(fn (Pond $p) => in_array($p->id, $pondIdsInCycle, true));
        }

        $stockingIds = $stockings->pluck('id');
        $stockingByPond = $stockings->keyBy('pond_id');

        // 1 query — SEMUA sampling stocking aktif (metrik terkini, SR-drop, tren 30 hari)
        $samplingsByStocking = Sampling::whereIn('stocking_id', $stockingIds)
            ->orderByDesc('tgl')
            ->orderByDesc('id')
            ->get()
            ->groupBy('stocking_id');

        // 1 query — log harian TERBARU per stocking
        $latestDailyLogs = $this->latestPerStocking(DailyLog::query(), 'daily_logs', $stockingIds);

        // 1 query — SEMUA uji mingguan stocking terpilih (terbaru per stocking + deret grafik air)
        $weekliesByStocking = WaterQualityWeekly::whereIn('stocking_id', $stockingIds)
            ->orderByDesc('tgl')
            ->orderByDesc('id')
            ->get()
            ->groupBy('stocking_id');

        // 1 query — emergency 7 hari terakhir (<=3 hari → status kritis, <=7 hari → flush-out)
        $emergenciesByStocking = EmergencyLog::whereIn('stocking_id', $stockingIds)
            ->where('tgl', '>=', $today->copy()->subDays(7)->startOfDay())
            ->get()
            ->groupBy('stocking_id');

        // 1 query — akumulasi pakan + kematian (ekor) per stocking, satu agregat
        $akumulasiPerStocking = DailyLog::whereIn('stocking_id', $stockingIds)
            ->selectRaw('stocking_id, COALESCE(SUM(pakan_07_kg),0) + COALESCE(SUM(pakan_11_kg),0) + COALESCE(SUM(pakan_15_kg),0) + COALESCE(SUM(pakan_19_kg),0) as total_pakan, COALESCE(SUM(mortalitas),0) as total_mati')
            ->groupBy('stocking_id')
            ->get()
            ->keyBy('stocking_id');

        // === Status per kolam (murni PHP, tanpa query tambahan) ===
        $tiles = $tilePonds->values()->map(function (Pond $pond) use ($stockingByPond, $samplingsByStocking, $latestDailyLogs, $weekliesByStocking, $emergenciesByStocking, $today) {
            $stocking = $pond->status === 'aktif' ? $stockingByPond->get($pond->id) : null;

            if (! $stocking) {
                return [
                    'pond' => $pond,
                    'stocking' => null,
                    'doc' => null,
                    'status' => 'idle',
                    'siapPanen' => false,
                    'latestSampling' => null,
                    'violations' => [],
                    'samplingDue' => false,
                    'emergency3d' => false,
                    'emergencies' => collect(),
                    'flushOut' => false,
                ];
            }

            $samplings = $samplingsByStocking->get($stocking->id, collect());
            $latestSampling = $samplings->first();
            $prevSampling = $samplings->skip(1)->first();

            $latestDaily = $latestDailyLogs->get($stocking->id);
            $latestWeekly = $weekliesByStocking->get($stocking->id)?->first();
            $violations = array_merge(
                $latestDaily ? $this->waterQualityService->dailyViolations($latestDaily) : [],
                $latestWeekly ? $this->waterQualityService->weeklyViolations($latestWeekly) : [],
            );

            $emergencies = $emergenciesByStocking->get($stocking->id, collect());
            $emergency3d = $emergencies->contains(fn (EmergencyLog $log) => $log->tgl->gte($today->copy()->subDays(3)));

            // Jadwal sampling hari ini — tidak ditagih lagi kalau hari ini sudah ada sampling masuk
            $samplingDue = $this->docService->isSamplingDue($stocking, $today)
                && (! $latestSampling || ! $latestSampling->tgl->isSameDay($today));

            $siapPanen = $latestSampling && (float) $latestSampling->mbw >= self::MBW_SIAP_PANEN;

            $srDrop = $this->growthService->isSharpSrDrop($latestSampling, $prevSampling, $stocking->jumlah_tebar);
            $flushOut = $this->docService->shouldRecommendFlushOut($stocking, $srDrop || $emergencies->isNotEmpty(), $today);

            $status = match (true) {
                $emergency3d => 'kritis',
                ! empty($violations) || $samplingDue => 'perhatian',
                $siapPanen => 'siap-panen',
                default => 'sehat',
            };

            return [
                'pond' => $pond,
                'stocking' => $stocking,
                'doc' => $this->docService->today($stocking),
                'status' => $status,
                'siapPanen' => $siapPanen,
                'latestSampling' => $latestSampling,
                'violations' => $violations,
                'samplingDue' => $samplingDue,
                'emergency3d' => $emergency3d,
                'emergencies' => $emergencies,
                'flushOut' => $flushOut,
            ];
        });

        $activeTiles = $tiles->filter(fn (array $t) => $t['stocking'] !== null);
        $withSampling = $activeTiles->filter(fn (array $t) => $t['latestSampling'] !== null);

        // === Hero ===
        $totalBiomass = $withSampling->sum(fn (array $t) => $this->growthService->biomassKg($t['latestSampling']->populasi, (float) $t['latestSampling']->mbw));
        $avgSr = $withSampling->isEmpty()
            ? null
            : $withSampling->avg(fn (array $t) => $this->growthService->survivalRate($t['latestSampling']->populasi, $t['stocking']->jumlah_tebar));
        $docs = $activeTiles->pluck('doc')->filter(fn ($d) => $d !== null);
        $avgDoc = $docs->isEmpty() ? null : (int) round($docs->avg());

        // === Tren biomass 30 hari (reuse sampling yang sudah dimuat — tanpa query baru) ===
        $trend = $samplingsByStocking->flatten(1)
            ->filter(fn (Sampling $s) => $s->tgl->gte($today->copy()->subDays(30)->startOfDay()))
            ->groupBy(fn (Sampling $s) => $s->tgl->format('Y-m-d'))
            ->map(fn (Collection $group) => $group->sum(fn (Sampling $s) => $this->growthService->biomassKg($s->populasi, (float) $s->mbw)))
            ->sortKeys();

        // === Metrik sekunder ===
        $fcrs = $activeTiles->map(function (array $t) use ($akumulasiPerStocking) {
            $biomass = $t['latestSampling']
                ? $this->growthService->biomassKg($t['latestSampling']->populasi, (float) $t['latestSampling']->mbw)
                : null;

            return $this->feedService->fcr((float) ($akumulasiPerStocking[$t['stocking']->id]->total_pakan ?? 0), $biomass);
        })->filter(fn ($f) => $f !== null);

        // === KPI akumulasi siklus berjalan (permintaan client 2026-07-08) ===
        // Kematian kg = ekor × MBW sampling TERAKHIR (opsi simpel, keputusan user).
        $akumulasi = ['pakan' => 0.0, 'matiEkor' => 0, 'matiKg' => 0.0];
        foreach ($activeTiles as $t) {
            $row = $akumulasiPerStocking->get($t['stocking']->id);
            if (! $row) {
                continue;
            }
            $akumulasi['pakan'] += (float) $row->total_pakan;
            $akumulasi['matiEkor'] += (int) $row->total_mati;
            $mbw = $t['latestSampling'] ? (float) $t['latestSampling']->mbw : null;
            $akumulasi['matiKg'] += $this->growthService->mortalitasKg((int) $row->total_mati, $mbw) ?? 0.0;
        }

        // 2 query — pakan bulan berjalan (kg dari daily_logs, Rp dari pembelian inventory)
        $pakanKg = (float) DailyLog::whereIn('stocking_id', $stockingIds)
            ->whereBetween('tgl', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->selectRaw('COALESCE(SUM(pakan_07_kg),0) + COALESCE(SUM(pakan_11_kg),0) + COALESCE(SUM(pakan_15_kg),0) + COALESCE(SUM(pakan_19_kg),0) as total')
            ->value('total');
        $pakanRp = (float) InventoryUsage::whereIn('stocking_id', $stockingIds)
            ->where('kategori', 'pakan')
            ->whereBetween('tgl', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->sum('harga');

        // 2 query — laba/rugi berjalan; rumus identik CostService::labaRugi
        // (pendapatan panen − (harga_benur + semua biaya inventory)), cuma di-batch.
        $biayaInventory = InventoryUsage::whereIn('stocking_id', $stockingIds)
            ->selectRaw('stocking_id, COALESCE(SUM(harga),0) as total')
            ->groupBy('stocking_id')
            ->pluck('total', 'stocking_id');
        $pendapatanPanen = Harvest::whereIn('stocking_id', $stockingIds)
            ->selectRaw('stocking_id, COALESCE(SUM(pendapatan),0) as total')
            ->groupBy('stocking_id')
            ->pluck('total', 'stocking_id');
        $labaRugi = $stockings->sum(fn (Stocking $s) => (float) ($pendapatanPanen[$s->id] ?? 0)
            - ((float) ($s->harga_benur ?? 0) + (float) ($biayaInventory[$s->id] ?? 0)));

        // === Perlu Perhatian Hari Ini (prioritas: emergency → mutu air → sampling due → flush-out) ===
        $perluPerhatian = collect();
        foreach ($activeTiles as $t) {
            $url = route('stockings.show', $t['stocking']);
            $kolam = $t['pond']->kode_kolam;

            if ($t['emergency3d']) {
                $terakhir = $t['emergencies']->sortByDesc('tgl')->first();
                $perluPerhatian->push(['prioritas' => 1, 'tone' => 'kritis', 'kolam' => $kolam, 'pesan' => 'Emergency: '.$terakhir->jenis, 'url' => $url]);
            }
            if (! empty($t['violations'])) {
                $lain = count($t['violations']) - 1;
                $pesan = $t['violations'][0].($lain > 0 ? " (+{$lain} pelanggaran lain)" : '');
                $perluPerhatian->push(['prioritas' => 2, 'tone' => 'perhatian', 'kolam' => $kolam, 'pesan' => $pesan, 'url' => $url]);
            }
            if ($t['samplingDue']) {
                $perluPerhatian->push(['prioritas' => 3, 'tone' => 'perhatian', 'kolam' => $kolam, 'pesan' => 'Jadwal sampling hari ini (DOC '.$t['doc'].')', 'url' => $url]);
            }
            if ($t['flushOut']) {
                $perluPerhatian->push(['prioritas' => 4, 'tone' => 'perhatian', 'kolam' => $kolam, 'pesan' => 'Rekomendasi flush-out — DOC < 30 dengan kondisi kritis', 'url' => $url]);
            }
        }

        // === Menuju Panen ===
        $menujuPanen = $activeTiles
            ->filter(fn (array $t) => $t['latestSampling'] && (float) $t['latestSampling']->mbw >= self::MBW_MENUJU_PANEN)
            ->map(fn (array $t) => [
                'kolam' => $t['pond']->kode_kolam,
                'doc' => $t['doc'],
                'mbw' => (float) $t['latestSampling']->mbw,
                'size' => $this->growthService->size((float) $t['latestSampling']->mbw),
                'biomass' => $this->growthService->biomassKg($t['latestSampling']->populasi, (float) $t['latestSampling']->mbw),
                'siap' => (float) $t['latestSampling']->mbw >= self::MBW_SIAP_PANEN,
                'url' => route('stockings.show', $t['stocking']),
            ])
            ->sortByDesc('mbw')
            ->values();

        // === Grafik air mingguan agregat: ammonia & rasio vibrio (rata-rata kolam terfilter) ===
        $weeklyRows = $weekliesByStocking->flatten(1);
        $chartAir = [
            'ammonia' => $this->weeklySeries($weeklyRows, fn (WaterQualityWeekly $w) => $w->ammonia !== null ? (float) $w->ammonia : null),
            'vibrio' => $this->weeklySeries($weeklyRows, fn (WaterQualityWeekly $w) => $this->waterQualityService->vibrioRatioPercent($w)),
        ];

        // === Grafik pertumbuhan (MBW/ADG/SR) — hanya saat filter satu kolam aktif ===
        $chartTumbuh = null;
        if ($pondId && $activeTiles->count() === 1) {
            $t = $activeTiles->first();
            $urut = $samplingsByStocking->get($t['stocking']->id, collect())->sortBy('tgl')->values();
            if ($urut->count() >= 2) {
                $mbwPts = [];
                $srPts = [];
                $adgPts = [];
                $prev = null;
                foreach ($urut as $s) {
                    $label = $s->tgl->format('d/m');
                    $mbwPts[] = ['label' => $label, 'value' => (float) $s->mbw];
                    $srPts[] = ['label' => $label, 'value' => $this->growthService->survivalRate($s->populasi, $t['stocking']->jumlah_tebar)];
                    if ($prev) {
                        $hari = $prev->tgl->diffInDays($s->tgl);
                        $adgPts[] = ['label' => $label, 'value' => $this->growthService->adg((float) $s->mbw, (float) $prev->mbw, $hari)];
                    }
                    $prev = $s;
                }
                $chartTumbuh = ['mbw' => $mbwPts, 'sr' => $srPts, 'adg' => $adgPts, 'kolam' => $t['pond']->kode_kolam];
            }
        }

        // 1 query — daftar siklus buat dropdown filter
        $cycles = Cycle::orderBy('nama')->get(['id', 'nama']);

        return [
            'hero' => [
                'totalBiomass' => $totalBiomass,
                'avgSr' => $avgSr,
                'avgDoc' => $avgDoc,
                'kolamAktif' => $tilePonds->where('status', 'aktif')->count(),
                'kolamTotal' => $tilePonds->count(),
            ],
            'sparkline' => [
                'values' => $trend->values()->all(),
                'points' => $this->sparklinePoints($trend->values()),
            ],
            'petaKolam' => $tiles->groupBy(fn (array $t) => $t['pond']->block->nama)->sortKeys(),
            'metrik' => [
                'fcr' => $fcrs->isEmpty() ? null : $fcrs->avg(),
                'pakanBulanIni' => ['kg' => $pakanKg, 'rp' => $pakanRp],
                'labaRugi' => $labaRugi,
            ],
            'akumulasi' => $akumulasi,
            'chartAir' => $chartAir,
            'chartTumbuh' => $chartTumbuh,
            'filter' => [
                'pondId' => $pondId,
                'cycleId' => $cycleId,
                'ponds' => $ponds->map(fn (Pond $p) => ['id' => $p->id, 'kode' => $p->kode_kolam])->values(),
                'cycles' => $cycles,
            ],
            'perluPerhatian' => $perluPerhatian->sortBy('prioritas')->values(),
            'menujuPanen' => $menujuPanen,
            'aktivitasTerbaru' => $this->aktivitasRingkas($farmId, 6),
        ];
    }

    /**
     * Deret mingguan rata-rata lintas kolam untuk satu parameter air —
     * dikelompokkan per tanggal uji, maksimal 12 titik terakhir.
     *
     * @param  callable(WaterQualityWeekly): ?float  $ambilNilai
     * @return list<array{label: string, value: float}>
     */
    private function weeklySeries(Collection $weeklyRows, callable $ambilNilai): array
    {
        return $weeklyRows
            ->map(fn (WaterQualityWeekly $w) => ['tgl' => $w->tgl, 'nilai' => $ambilNilai($w)])
            ->filter(fn (array $r) => $r['nilai'] !== null)
            ->groupBy(fn (array $r) => $r['tgl']->format('Y-m-d'))
            ->sortKeys()
            ->map(fn (Collection $group, string $tgl) => [
                'label' => Carbon::parse($tgl)->format('d/m'),
                'value' => round($group->avg('nilai'), 3),
            ])
            ->values()
            ->slice(-12)
            ->values()
            ->all();
    }

    /**
     * Versi hemat-query dari aktivitasTerbaru (3 query flat pakai join, tanpa
     * eager-load per model) — khusus dashboard control room. Bentuk item sama.
     */
    private function aktivitasRingkas(int $farmId, int $limit): Collection
    {
        $ambil = function (string $table, string $tipe) use ($farmId, $limit) {
            return DB::table($table)
                ->join('stockings', 'stockings.id', '=', "{$table}.stocking_id")
                ->join('ponds', 'ponds.id', '=', 'stockings.pond_id')
                ->join('blocks', 'blocks.id', '=', 'ponds.block_id')
                ->where('blocks.farm_id', $farmId)
                ->orderByDesc("{$table}.created_at")
                ->limit($limit)
                ->get(["{$table}.tgl", "{$table}.created_at", 'ponds.kode_kolam'])
                ->map(fn ($row) => [
                    'tipe' => $tipe,
                    'kolam' => $row->kode_kolam,
                    'tgl' => \Illuminate\Support\Carbon::parse($row->tgl),
                    'waktu' => \Illuminate\Support\Carbon::parse($row->created_at),
                ]);
        };

        return $ambil('daily_logs', 'Pakan & Kualitas Air')
            ->concat($ambil('samplings', 'Sampling'))
            ->concat($ambil('harvests', 'Panen'))
            ->sortByDesc('waktu')
            ->take($limit)
            ->values();
    }

    /**
     * Baris TERBARU (tgl paling akhir) per stocking dalam SATU query lewat
     * tuple-IN — hindari query per stocking.
     */
    private function latestPerStocking(Builder $query, string $table, Collection $stockingIds): Collection
    {
        if ($stockingIds->isEmpty()) {
            return collect();
        }

        return $query
            ->whereIn('stocking_id', $stockingIds)
            ->whereIn(DB::raw('(stocking_id, tgl)'), function ($sub) use ($table, $stockingIds) {
                $sub->selectRaw('stocking_id, MAX(tgl)')
                    ->from($table)
                    ->whereIn('stocking_id', $stockingIds)
                    ->groupBy('stocking_id');
            })
            ->get()
            ->keyBy('stocking_id');
    }

    /**
     * String "x,y x,y …" untuk <polyline> sparkline (viewBox 100×30, y terbalik).
     * Murni transformasi koordinat buat tampilan — bukan rumus bisnis.
     */
    private function sparklinePoints(Collection $values): string
    {
        if ($values->count() < 2) {
            return '';
        }

        $min = $values->min();
        $max = $values->max();
        $range = $max - $min;
        $stepX = 100 / ($values->count() - 1);

        return $values->values()->map(function ($v, $i) use ($min, $range, $stepX) {
            $y = $range > 0 ? 27 - (($v - $min) / $range) * 24 : 15.0;

            return round($i * $stepX, 1).','.round($y, 1);
        })->implode(' ');
    }
}
