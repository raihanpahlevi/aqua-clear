<?php

namespace Tests\Feature\Services;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\Farm;
use App\Models\Pond;
use App\Models\Stocking;
use App\Services\DocService;
use App\Services\GrowthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlushOutRecommendationTest extends TestCase
{
    use RefreshDatabase;

    private function makeStocking(string $tglPakanPertama): Stocking
    {
        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        return $pond->stockings()->create([
            'cycle_id' => $cycle->id,
            'tgl_tebar' => '2026-05-01',
            'tgl_pakan_pertama' => $tglPakanPertama,
            'jumlah_tebar' => 60000,
        ]);
    }

    public function test_sr_turun_tajam_terdeteksi_kalau_lebih_dari_10_poin(): void
    {
        $stocking = $this->makeStocking('2026-05-01');

        $stocking->samplings()->create(['tgl' => '2026-05-15', 'doc' => 14, 'berat_sampel_total' => 1000, 'jumlah_sampel' => 100, 'mbw' => 10, 'populasi' => 58000]); // SR 96.7%
        $stocking->samplings()->create(['tgl' => '2026-05-20', 'doc' => 19, 'berat_sampel_total' => 1100, 'jumlah_sampel' => 100, 'mbw' => 11, 'populasi' => 45000]); // SR 75% -> turun 21.7 poin

        $service = new GrowthService();

        $this->assertTrue($service->hasSharpSrDrop($stocking));
    }

    public function test_sr_turun_wajar_tidak_dianggap_tajam(): void
    {
        $stocking = $this->makeStocking('2026-05-01');

        $stocking->samplings()->create(['tgl' => '2026-05-15', 'doc' => 14, 'berat_sampel_total' => 1000, 'jumlah_sampel' => 100, 'mbw' => 10, 'populasi' => 58000]); // SR 96.7%
        $stocking->samplings()->create(['tgl' => '2026-05-20', 'doc' => 19, 'berat_sampel_total' => 1100, 'jumlah_sampel' => 100, 'mbw' => 11, 'populasi' => 57000]); // SR 95% -> turun 1.7 poin

        $service = new GrowthService();

        $this->assertFalse($service->hasSharpSrDrop($stocking));
    }

    public function test_kurang_dari_2_sampling_tidak_bisa_dianggap_turun_tajam(): void
    {
        $stocking = $this->makeStocking('2026-05-01');
        $stocking->samplings()->create(['tgl' => '2026-05-15', 'doc' => 14, 'berat_sampel_total' => 1000, 'jumlah_sampel' => 100, 'mbw' => 10, 'populasi' => 30000]);

        $service = new GrowthService();

        $this->assertFalse($service->hasSharpSrDrop($stocking));
    }

    public function test_flush_out_direkomendasikan_kalau_doc_kurang_30_dan_kondisi_kritis(): void
    {
        $stocking = $this->makeStocking('2026-05-01');
        $docService = new DocService();

        // DOC di tanggal 2026-05-15 = 14 hari (di bawah 30)
        $this->assertTrue($docService->shouldRecommendFlushOut($stocking, true, '2026-05-15'));
        $this->assertFalse($docService->shouldRecommendFlushOut($stocking, false, '2026-05-15'), 'Meski DOC<30, tanpa kondisi kritis TIDAK direkomendasikan');
    }

    public function test_flush_out_tidak_direkomendasikan_kalau_doc_sudah_lewat_30(): void
    {
        $stocking = $this->makeStocking('2026-05-01');
        $docService = new DocService();

        // DOC di tanggal 2026-06-15 = 45 hari (di atas 30)
        $this->assertFalse($docService->shouldRecommendFlushOut($stocking, true, '2026-06-15'), 'DOC udah di atas 30, meski kondisi kritis TIDAK direkomendasikan flush-out');
    }
}
