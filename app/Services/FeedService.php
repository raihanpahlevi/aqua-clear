<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Stocking;

/**
 * Rumus & aturan pakan — PRD Bagian 5.1.
 * Aturan ancho (jeda, penyesuaian %, porsi %, alert) masih "asumsi sementara" — lihat CLAUDE.md Bagian 8, jangan diubah diam-diam.
 */
class FeedService
{
    private const ANCHO_PORTION_PERCENT = 0.02;
    private const ANCHO_ADJUSTMENT_PERCENT = 0.10;

    public function totalPakanHarianKg(DailyLog $log): float
    {
        return (float) $log->pakan_07_kg
            + (float) $log->pakan_11_kg
            + (float) $log->pakan_15_kg
            + (float) $log->pakan_19_kg;
    }

    public function akumulasiPakanKg(Stocking $stocking): float
    {
        $sums = $stocking->dailyLogs()
            ->selectRaw('COALESCE(SUM(pakan_07_kg),0) + COALESCE(SUM(pakan_11_kg),0) + COALESCE(SUM(pakan_15_kg),0) + COALESCE(SUM(pakan_19_kg),0) as total')
            ->value('total');

        return (float) $sums;
    }

    public function fr(float $pakanHarianKg, ?float $biomassKg): ?float
    {
        if (! $biomassKg || $biomassKg <= 0) {
            return null;
        }

        return ($pakanHarianKg / $biomassKg) * 100;
    }

    public function fcr(float $akumulasiPakanKg, ?float $biomassKg): ?float
    {
        if (! $biomassKg || $biomassKg <= 0 || $akumulasiPakanKg <= 0) {
            return null;
        }

        return $akumulasiPakanKg / $biomassKg;
    }

    public function anchoPortionKg(float $totalPakanHarianKg): float
    {
        return $totalPakanHarianKg * self::ANCHO_PORTION_PERCENT;
    }

    public function anchoAdjustmentPercent(string $hasilAncho): float
    {
        return match ($hasilAncho) {
            'habis' => self::ANCHO_ADJUSTMENT_PERCENT * 100,
            'sisa_banyak' => -self::ANCHO_ADJUSTMENT_PERCENT * 100,
            default => 0.0,
        };
    }

    /**
     * Alert dipicu setelah 2x berturut-turut (antar sesi pakan) hasilnya "sisa banyak".
     * $hasilBerurutan diurutkan dari yang terbaru dulu (index 0 = paling akhir).
     */
    public function shouldAlertAncho(array $hasilBerurutan): bool
    {
        return count($hasilBerurutan) >= 2
            && $hasilBerurutan[0] === 'sisa_banyak'
            && $hasilBerurutan[1] === 'sisa_banyak';
    }

    /**
     * Ambil hasil ancho 2 sesi terakhir (lintas hari bila perlu) untuk cek alert,
     * urut dari sesi paling akhir ke paling awal.
     */
    public function recentAnchoResults(Stocking $stocking, int $limit = 2): array
    {
        $logs = $stocking->dailyLogs()->latest('tgl')->limit(5)->get();

        $results = [];
        foreach ($logs as $log) {
            foreach (['ancho_19', 'ancho_15', 'ancho_11', 'ancho_07'] as $field) {
                if ($log->{$field} !== null) {
                    $results[] = $log->{$field};
                }
                if (count($results) >= $limit) {
                    return $results;
                }
            }
        }

        return $results;
    }
}
