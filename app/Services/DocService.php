<?php

namespace App\Services;

use App\Models\Stocking;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * DOC (Day of Culture) dihitung dari tgl_pakan_pertama, BUKAN tgl_tebar.
 * Koreksi eksplisit dari Fardan — lihat CLAUDE.md.
 */
class DocService
{
    public function forDate(Stocking $stocking, string|CarbonInterface $date): ?int
    {
        if (! $stocking->tgl_pakan_pertama) {
            return null;
        }

        $date = $date instanceof CarbonInterface ? $date : Carbon::parse($date);

        return $stocking->tgl_pakan_pertama->startOfDay()->diffInDays($date->copy()->startOfDay(), false);
    }

    public function today(Stocking $stocking): ?int
    {
        return $this->forDate($stocking, now());
    }

    /**
     * Jadwal sampling otomatis: pertama di DOC 30, lalu tiap 7 hari.
     */
    public function isSamplingDue(Stocking $stocking, string|CarbonInterface $date): bool
    {
        $doc = $this->forDate($stocking, $date);

        if ($doc === null || $doc < 30) {
            return false;
        }

        return ($doc - 30) % 7 === 0;
    }

    /**
     * Syarat pertama flush-out: DOC < 30. Syarat kedua (kondisi kritis) dicek terpisah
     * oleh pemanggil lewat shouldRecommendFlushOut().
     */
    public function isEligibleForFlushOut(Stocking $stocking, string|CarbonInterface $date): bool
    {
        $doc = $this->forDate($stocking, $date);

        return $doc !== null && $doc < 30;
    }

    /**
     * Rekomendasi flush-out PRD 5.6: DOC < 30 DAN kondisi kritis (SR turun tajam / serangan
     * penyakit). $kondisiKritis dihitung pemanggil — lihat GrowthService::hasSharpSrDrop()
     * dan/atau keberadaan emergency_log terbaru.
     */
    public function shouldRecommendFlushOut(Stocking $stocking, bool $kondisiKritis, string|CarbonInterface $date): bool
    {
        return $this->isEligibleForFlushOut($stocking, $date) && $kondisiKritis;
    }
}
