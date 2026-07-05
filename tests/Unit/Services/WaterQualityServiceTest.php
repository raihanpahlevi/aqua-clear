<?php

namespace Tests\Unit\Services;

use App\Models\DailyLog;
use App\Models\WaterQualityWeekly;
use App\Services\WaterQualityService;
use Tests\TestCase;

class WaterQualityServiceTest extends TestCase
{
    private WaterQualityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WaterQualityService();
    }

    public function test_rasio_vibrio_terhadap_total_bakteri(): void
    {
        $log = new WaterQualityWeekly([
            'vibrio_hijau' => 2,
            'vibrio_hitam' => 1,
            'vibrio_luminer' => 0,
            'total_bakteri' => 100,
        ]);

        $this->assertEqualsWithDelta(3.0, $this->service->vibrioRatioPercent($log), 0.001);
        $this->assertFalse($this->service->isVibrioHigh($log));
    }

    public function test_warning_kalau_rasio_lebih_dari_10_persen(): void
    {
        $log = new WaterQualityWeekly([
            'vibrio_hijau' => 8,
            'vibrio_hitam' => 5,
            'vibrio_luminer' => 0,
            'total_bakteri' => 100,
        ]);

        $this->assertTrue($this->service->isVibrioHigh($log));
    }

    public function test_null_kalau_total_bakteri_kosong(): void
    {
        $log = new WaterQualityWeekly(['vibrio_hijau' => 2, 'total_bakteri' => null]);

        $this->assertNull($this->service->vibrioRatioPercent($log));
        $this->assertFalse($this->service->isVibrioHigh($log));
    }

    // === Ambang harian — PRD 5.3 ===

    public function test_do_alert_kalau_kurang_dari_sama_dengan_4(): void
    {
        $this->assertTrue($this->service->isDoLow(4.0));
        $this->assertTrue($this->service->isDoLow(3.5));
        $this->assertFalse($this->service->isDoLow(4.1));
        $this->assertFalse($this->service->isDoLow(null));
    }

    public function test_ph_alert_kalau_di_luar_7_5_sampai_8_5(): void
    {
        $this->assertTrue($this->service->isPhOut(7.4));
        $this->assertTrue($this->service->isPhOut(8.6));
        $this->assertFalse($this->service->isPhOut(7.5));
        $this->assertFalse($this->service->isPhOut(8.5));
        $this->assertFalse($this->service->isPhOut(8.0));
    }

    public function test_suhu_alert_kalau_di_luar_28_sampai_32(): void
    {
        $this->assertTrue($this->service->isSuhuOut(27.9));
        $this->assertTrue($this->service->isSuhuOut(32.1));
        $this->assertFalse($this->service->isSuhuOut(30.0));
    }

    public function test_salinitas_alert_kalau_di_luar_25_sampai_30(): void
    {
        $this->assertTrue($this->service->isSalinitasOut(24.9));
        $this->assertTrue($this->service->isSalinitasOut(30.1));
        $this->assertFalse($this->service->isSalinitasOut(27.0));
    }

    public function test_daily_violations_kumpulin_semua_parameter_yang_melanggar(): void
    {
        $log = new DailyLog([
            'do_pagi' => 3.5, // alert
            'do_sore' => 5.0, // ok
            'ph_pagi' => 8.0, // ok
            'ph_sore' => 9.0, // alert
            'suhu_pagi' => 30, // ok
            'suhu_sore' => 35, // alert
            'salinitas' => 28, // ok
        ]);

        $violations = $this->service->dailyViolations($log);

        $this->assertCount(3, $violations);
        $this->assertTrue($this->service->hasDailyViolation($log));
    }

    public function test_daily_violations_kosong_kalau_semua_normal(): void
    {
        $log = new DailyLog([
            'do_pagi' => 5, 'do_sore' => 5,
            'ph_pagi' => 8, 'ph_sore' => 8,
            'suhu_pagi' => 30, 'suhu_sore' => 30,
            'salinitas' => 27,
        ]);

        $this->assertSame([], $this->service->dailyViolations($log));
        $this->assertFalse($this->service->hasDailyViolation($log));
    }

    // === Ambang mingguan — PRD 5.3 ===

    public function test_tan_ammonia_nitrit_nitrat_alert_sesuai_ambang(): void
    {
        $this->assertTrue($this->service->isTanHigh(2.0));
        $this->assertFalse($this->service->isTanHigh(1.9));

        $this->assertTrue($this->service->isAmmoniaHigh(0.1));
        $this->assertFalse($this->service->isAmmoniaHigh(0.09));

        $this->assertTrue($this->service->isNitritHigh(0.1));
        $this->assertFalse($this->service->isNitritHigh(0.09));

        $this->assertTrue($this->service->isNitratHigh(50));
        $this->assertFalse($this->service->isNitratHigh(49.9));
    }

    public function test_weekly_violations_kumpulin_semua_termasuk_vibrio(): void
    {
        $log = new WaterQualityWeekly([
            'tan' => 3, // alert
            'ammonia' => 0.05, // ok
            'nitrit' => 0.2, // alert
            'nitrat' => 40, // ok
            'vibrio_hijau' => 15, 'vibrio_hitam' => 0, 'vibrio_luminer' => 0, 'total_bakteri' => 100, // alert (15%)
        ]);

        $violations = $this->service->weeklyViolations($log);

        $this->assertCount(3, $violations);
        $this->assertTrue($this->service->hasWeeklyViolation($log));
    }
}
