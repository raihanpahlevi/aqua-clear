<?php

namespace Tests\Unit\Services;

use App\Services\GrowthService;
use Tests\TestCase;

class GrowthServiceTest extends TestCase
{
    private GrowthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GrowthService();
    }

    public function test_mbw_is_berat_sampel_dibagi_jumlah_sampel(): void
    {
        $this->assertEqualsWithDelta(15.0, $this->service->mbw(1500, 100), 0.001);
    }

    public function test_mbw_zero_jumlah_sampel_returns_zero(): void
    {
        $this->assertSame(0.0, $this->service->mbw(1500, 0));
    }

    public function test_adg_is_selisih_mbw_dibagi_selisih_hari(): void
    {
        $this->assertEqualsWithDelta(0.5, $this->service->adg(15, 8, 14), 0.001);
    }

    public function test_size_is_1000_dibagi_mbw(): void
    {
        $this->assertEqualsWithDelta(66.67, $this->service->size(15), 0.01);
    }

    public function test_survival_rate_percent(): void
    {
        $this->assertEqualsWithDelta(91.67, $this->service->survivalRate(55000, 60000), 0.01);
    }

    public function test_biomass_kg(): void
    {
        $this->assertEqualsWithDelta(825.0, $this->service->biomassKg(55000, 15), 0.001);
    }

    public function test_mortalitas_kg_dari_ekor_kali_mbw(): void
    {
        // Aturan ×2 dihapus per instruksi Jubir via client (2026-07-08) — konversi kg murni ekor × MBW.
        $this->assertSame(1.5, $this->service->mortalitasKg(100, 15.0));
        $this->assertNull($this->service->mortalitasKg(100, null));
    }
}
