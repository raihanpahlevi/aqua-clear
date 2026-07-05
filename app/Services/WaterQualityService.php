<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\WaterQualityWeekly;

/**
 * PRD Bagian 5.3 — standar mutu air & alert otomatis, buat parameter HARIAN (DailyLog)
 * dan MINGGUAN (WaterQualityWeekly). "Nilai di atas jadi basis alert otomatis di sistem."
 */
class WaterQualityService
{
    // === Ambang standar PRD 5.3 ===
    private const DO_MIN = 4.0;
    private const PH_MIN = 7.5;
    private const PH_MAX = 8.5;
    private const SUHU_MIN = 28.0;
    private const SUHU_MAX = 32.0;
    private const SALINITAS_MIN = 25.0;
    private const SALINITAS_MAX = 30.0;
    private const TAN_MAX = 2.0;
    private const AMMONIA_MAX = 0.1;
    private const NITRIT_MAX = 0.1;
    private const NITRAT_MAX = 50.0;
    private const VIBRIO_RATIO_MAX = 10.0;

    // === Cek harian (DailyLog) ===

    public function isDoLow(?float $value): bool
    {
        return $value !== null && $value <= self::DO_MIN;
    }

    public function isPhOut(?float $value): bool
    {
        return $value !== null && ($value < self::PH_MIN || $value > self::PH_MAX);
    }

    public function isSuhuOut(?float $value): bool
    {
        return $value !== null && ($value < self::SUHU_MIN || $value > self::SUHU_MAX);
    }

    public function isSalinitasOut(?float $value): bool
    {
        return $value !== null && ($value < self::SALINITAS_MIN || $value > self::SALINITAS_MAX);
    }

    /**
     * Daftar pesan pelanggaran ambang harian untuk satu DailyLog. Kosong berarti semua normal.
     */
    public function dailyViolations(DailyLog $log): array
    {
        $violations = [];

        if ($this->isDoLow($log->do_pagi)) {
            $violations[] = "DO pagi {$log->do_pagi} ppm (standar >4)";
        }
        if ($this->isDoLow($log->do_sore)) {
            $violations[] = "DO sore {$log->do_sore} ppm (standar >4)";
        }
        if ($this->isPhOut($log->ph_pagi)) {
            $violations[] = "pH pagi {$log->ph_pagi} (standar 7,5–8,5)";
        }
        if ($this->isPhOut($log->ph_sore)) {
            $violations[] = "pH sore {$log->ph_sore} (standar 7,5–8,5)";
        }
        if ($this->isSuhuOut($log->suhu_pagi)) {
            $violations[] = "Suhu pagi {$log->suhu_pagi}°C (standar 28–32)";
        }
        if ($this->isSuhuOut($log->suhu_sore)) {
            $violations[] = "Suhu sore {$log->suhu_sore}°C (standar 28–32)";
        }
        if ($this->isSalinitasOut($log->salinitas)) {
            $violations[] = "Salinitas {$log->salinitas} ppt (standar 25–30)";
        }

        return $violations;
    }

    public function hasDailyViolation(DailyLog $log): bool
    {
        return ! empty($this->dailyViolations($log));
    }

    // === Cek mingguan (WaterQualityWeekly) ===

    public function isTanHigh(?float $value): bool
    {
        return $value !== null && $value >= self::TAN_MAX;
    }

    public function isAmmoniaHigh(?float $value): bool
    {
        return $value !== null && $value >= self::AMMONIA_MAX;
    }

    public function isNitritHigh(?float $value): bool
    {
        return $value !== null && $value >= self::NITRIT_MAX;
    }

    public function isNitratHigh(?float $value): bool
    {
        return $value !== null && $value >= self::NITRAT_MAX;
    }

    public function vibrioRatioPercent(WaterQualityWeekly $log): ?float
    {
        $totalBakteri = (float) ($log->total_bakteri ?? 0);

        if ($totalBakteri <= 0) {
            return null;
        }

        $vibrio = (float) ($log->vibrio_hijau ?? 0) + (float) ($log->vibrio_hitam ?? 0) + (float) ($log->vibrio_luminer ?? 0);

        return ($vibrio / $totalBakteri) * 100;
    }

    public function isVibrioHigh(WaterQualityWeekly $log): bool
    {
        $ratio = $this->vibrioRatioPercent($log);

        return $ratio !== null && $ratio > self::VIBRIO_RATIO_MAX;
    }

    /**
     * Daftar pesan pelanggaran ambang mingguan untuk satu WaterQualityWeekly. Kosong berarti semua normal.
     */
    public function weeklyViolations(WaterQualityWeekly $log): array
    {
        $violations = [];

        if ($this->isTanHigh($log->tan)) {
            $violations[] = "TAN {$log->tan} ppm (standar <2)";
        }
        if ($this->isAmmoniaHigh($log->ammonia)) {
            $violations[] = "Ammonia {$log->ammonia} ppm (standar <0,1)";
        }
        if ($this->isNitritHigh($log->nitrit)) {
            $violations[] = "Nitrit {$log->nitrit} ppm (standar <0,1)";
        }
        if ($this->isNitratHigh($log->nitrat)) {
            $violations[] = "Nitrat {$log->nitrat} ppm (standar <50)";
        }
        if ($this->isVibrioHigh($log)) {
            $ratio = number_format($this->vibrioRatioPercent($log), 1);
            $violations[] = "Rasio V/B {$ratio}% (standar <10%)";
        }

        return $violations;
    }

    public function hasWeeklyViolation(WaterQualityWeekly $log): bool
    {
        return ! empty($this->weeklyViolations($log));
    }
}
