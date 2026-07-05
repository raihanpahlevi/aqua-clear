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

class HarvestAccumulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_akumulasi_3_tahap_partial1_partial2_total(): void
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

        $stocking->harvests()->create(['tahap' => 'partial1', 'tgl' => '2026-06-01', 'berat_kg' => 300, 'harga_per_kg' => 45000, 'pendapatan' => 300*45000]);
        $service = new CostService();
        $progres1 = $service->progresTargetPanen($stocking);
        $this->assertEqualsWithDelta(300, $progres1['biomass_kg'], 0.01, 'Setelah partial1 doang harusnya 300kg');

        $stocking->harvests()->create(['tahap' => 'partial2', 'tgl' => '2026-06-15', 'berat_kg' => 400, 'harga_per_kg' => 43000, 'pendapatan' => 400*43000]);
        $progres2 = $service->progresTargetPanen($stocking);
        $this->assertEqualsWithDelta(700, $progres2['biomass_kg'], 0.01, 'Setelah partial1+partial2 harusnya 700kg (300+400)');

        $stocking->harvests()->create(['tahap' => 'total', 'tgl' => '2026-06-30', 'berat_kg' => 450, 'harga_per_kg' => 42000, 'pendapatan' => 450*42000]);
        $progres3 = $service->progresTargetPanen($stocking);
        $this->assertEqualsWithDelta(1150, $progres3['biomass_kg'], 0.01, 'Setelah semua 3 tahap harusnya 1150kg (300+400+450)');

        $this->assertEqualsWithDelta(1150, $service->totalBiomassPanenKg($stocking), 0.01);
        $this->assertEqualsWithDelta((300*45000)+(400*43000)+(450*42000), $service->totalPendapatanPanen($stocking), 0.01);
    }
}
