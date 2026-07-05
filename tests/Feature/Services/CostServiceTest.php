<?php

namespace Tests\Feature\Services;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\Farm;
use App\Models\Pond;
use App\Models\Stocking;
use App\Services\CostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_biaya_hpp_dan_laba_rugi_sesuai_hitungan_manual(): void
    {
        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        /** @var Stocking $stocking */
        $stocking = $pond->stockings()->create([
            'cycle_id' => $cycle->id,
            'tgl_tebar' => '2026-05-01',
            'tgl_pakan_pertama' => '2026-05-03',
            'jumlah_tebar' => 60000,
            'harga_benur' => 3_000_000, // total, bukan per ekor — lihat CLAUDE.md
        ]);

        $stocking->inventoryUsages()->create(['tgl' => '2026-06-01', 'kategori' => 'pakan', 'item' => 'Pakan 3M', 'qty' => 100, 'harga' => 2_000_000]);
        $stocking->inventoryUsages()->create(['tgl' => '2026-06-02', 'kategori' => 'probiotik', 'item' => 'Probiotik X', 'qty' => 10, 'harga' => 500_000]);

        $stocking->harvests()->create(['tahap' => 'partial1', 'tgl' => '2026-07-02', 'berat_kg' => 300, 'harga_per_kg' => 45000, 'pendapatan' => 300 * 45000]);
        $stocking->harvests()->create(['tahap' => 'total', 'tgl' => '2026-07-10', 'berat_kg' => 850, 'harga_per_kg' => 42000, 'pendapatan' => 850 * 42000]);

        $service = new CostService();

        $this->assertEqualsWithDelta(5_500_000, $service->totalBiaya($stocking), 0.01);
        $this->assertEqualsWithDelta(1150, $service->totalBiomassPanenKg($stocking), 0.01);
        $this->assertEqualsWithDelta(49_200_000, $service->totalPendapatanPanen($stocking), 0.01);
        $this->assertEqualsWithDelta(4782.6, $service->hppPerKg($stocking), 0.1);
        $this->assertEqualsWithDelta(43_700_000, $service->labaRugi($stocking), 0.01);
    }

    public function test_hpp_null_kalau_belum_ada_panen(): void
    {
        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        $stocking = $pond->stockings()->create([
            'cycle_id' => $cycle->id,
            'tgl_tebar' => '2026-05-01',
            'jumlah_tebar' => 60000,
        ]);

        $this->assertNull((new CostService())->hppPerKg($stocking));
    }
}
