<?php

namespace Tests\Unit\Services;

use App\Services\FeedService;
use Tests\TestCase;

class FeedServiceTest extends TestCase
{
    private FeedService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FeedService();
    }

    public function test_fr_percent(): void
    {
        $this->assertEqualsWithDelta(2.42, $this->service->fr(20, 825), 0.01);
    }

    public function test_fr_null_when_biomass_zero(): void
    {
        $this->assertNull($this->service->fr(20, 0));
        $this->assertNull($this->service->fr(20, null));
    }

    public function test_fcr_ratio(): void
    {
        $this->assertEqualsWithDelta(0.0242, $this->service->fcr(20, 825), 0.001);
    }

    public function test_fcr_null_when_belum_ada_pakan(): void
    {
        $this->assertNull($this->service->fcr(0, 825));
    }

    public function test_fcr_null_when_biomass_zero(): void
    {
        $this->assertNull($this->service->fcr(20, 0));
    }

    public function test_ancho_portion_2_persen(): void
    {
        $this->assertEqualsWithDelta(0.4, $this->service->anchoPortionKg(20), 0.001);
    }

    public function test_ancho_adjustment_habis_naik_10_persen(): void
    {
        $this->assertSame(10.0, $this->service->anchoAdjustmentPercent('habis'));
    }

    public function test_ancho_adjustment_sisa_banyak_turun_10_persen(): void
    {
        $this->assertSame(-10.0, $this->service->anchoAdjustmentPercent('sisa_banyak'));
    }

    public function test_ancho_adjustment_sisa_sedikit_tetap(): void
    {
        $this->assertSame(0.0, $this->service->anchoAdjustmentPercent('sisa_sedikit'));
    }

    public function test_alert_setelah_2x_berturut_sisa_banyak(): void
    {
        $this->assertTrue($this->service->shouldAlertAncho(['sisa_banyak', 'sisa_banyak']));
    }

    public function test_tidak_alert_kalau_tidak_2x_berturut(): void
    {
        $this->assertFalse($this->service->shouldAlertAncho(['sisa_banyak', 'habis']));
        $this->assertFalse($this->service->shouldAlertAncho(['sisa_banyak']));
    }
}
