<?php

namespace Tests\Feature\Services;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\Farm;
use App\Models\Pond;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_kolam_aktif_turun_otomatis_setelah_status_jadi_panen(): void
    {
        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $pond1 = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $pond2 = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A2', 'status' => 'aktif']);

        $service = app(DashboardService::class);

        $before = $service->kolamAktif($farm->id);
        $this->assertSame(2, $before['aktif']);
        $this->assertSame(2, $before['total']);

        $pond1->update(['status' => 'panen']);

        $after = $service->kolamAktif($farm->id);
        $this->assertSame(1, $after['aktif'], 'Kolam aktif harus turun jadi 1 setelah A1 dipanen');
        $this->assertSame(2, $after['total'], 'Total kolam tetap 2, cuma yang aktif yang berkurang');
    }

    public function test_rata_rata_fcr_cuma_hitung_kolam_aktif_dan_exclude_yang_belum_ada_data(): void
    {
        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        // Kolam 1: aktif, ada data pakan + sampling -> FCR terhitung
        $pond1 = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $stocking1 = $pond1->stockings()->create([
            'cycle_id' => $cycle->id, 'tgl_tebar' => '2026-05-01', 'tgl_pakan_pertama' => '2026-05-01', 'jumlah_tebar' => 60000,
        ]);
        $stocking1->dailyLogs()->create(['tgl' => '2026-06-01', 'pakan_07_kg' => 20]);
        $stocking1->samplings()->create(['tgl' => '2026-06-01', 'doc' => 31, 'berat_sampel_total' => 1500, 'jumlah_sampel' => 100, 'mbw' => 15, 'populasi' => 55000]);

        // Kolam 2: aktif, TAPI belum ada data pakan/sampling sama sekali -> FCR null, harus di-exclude dari rata-rata
        $pond2 = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A2', 'status' => 'aktif']);
        $pond2->stockings()->create([
            'cycle_id' => $cycle->id, 'tgl_tebar' => '2026-05-01', 'tgl_pakan_pertama' => '2026-05-01', 'jumlah_tebar' => 60000,
        ]);

        // Kolam 3: SUDAH PANEN (tidak aktif) meski ada data -> harus di-exclude sepenuhnya
        $pond3 = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A3', 'status' => 'panen']);
        $stocking3 = $pond3->stockings()->create([
            'cycle_id' => $cycle->id, 'tgl_tebar' => '2026-05-01', 'tgl_pakan_pertama' => '2026-05-01', 'jumlah_tebar' => 60000,
        ]);
        $stocking3->dailyLogs()->create(['tgl' => '2026-06-01', 'pakan_07_kg' => 999]);
        $stocking3->samplings()->create(['tgl' => '2026-06-01', 'doc' => 31, 'berat_sampel_total' => 1500, 'jumlah_sampel' => 100, 'mbw' => 15, 'populasi' => 55000]);

        $service = app(DashboardService::class);
        $rataRata = $service->rataRataFcr($farm->id);

        // Biomass kolam1 = 55000*15/1000 = 825kg, pakan=20kg, FCR = 20/825 = 0.02424...
        $this->assertEqualsWithDelta(20 / 825, $rataRata, 0.0001, 'Rata-rata FCR harus cuma dari kolam1 (satu-satunya yang aktif + punya FCR valid), bukan rata-rata dengan kolam2 (null) atau kolam3 (sudah panen)');
    }
}
