<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Harvest;
use App\Models\Pond;
use App\Models\Sampling;
use App\Models\Stocking;
use Illuminate\Support\Collection;

/**
 * 6 kartu KPI dashboard — PRD Bagian 7.
 */
class DashboardService
{
    public function __construct(
        private GrowthService $growthService,
        private FeedService $feedService,
        private CostService $costService,
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
}
