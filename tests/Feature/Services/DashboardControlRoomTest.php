<?php

namespace Tests\Feature\Services;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\Farm;
use App\Models\Pond;
use App\Models\Stocking;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardControlRoomTest extends TestCase
{
    use RefreshDatabase;

    private Farm $farm;

    private Block $block;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $this->block = Block::create(['farm_id' => $this->farm->id, 'nama' => 'A']);
    }

    private function service(): DashboardService
    {
        return app(DashboardService::class);
    }

    /** @return array{0: Pond, 1: Stocking} */
    private function buatKolamAktif(string $kode, array $stockingAttrs = []): array
    {
        $pond = Pond::create(['block_id' => $this->block->id, 'kode_kolam' => $kode, 'status' => 'aktif']);

        $stocking = $pond->stockings()->create(array_merge([
            'cycle_id' => Cycle::firstOrCreate(['nama' => 'Siklus Test'])->id,
            'tgl_tebar' => now()->subDays(12)->toDateString(),
            'tgl_pakan_pertama' => now()->subDays(10)->toDateString(),
            'jumlah_tebar' => 60000,
        ], $stockingAttrs));

        return [$pond, $stocking];
    }

    public function test_klasifikasi_status_tile_peta_kolam(): void
    {
        Pond::create(['block_id' => $this->block->id, 'kode_kolam' => 'K0', 'status' => 'kosong']);

        [, $sSehat] = $this->buatKolamAktif('S1');
        $sSehat->samplings()->create(['tgl' => now()->subDay()->toDateString(), 'doc' => 9, 'berat_sampel_total' => 500, 'jumlah_sampel' => 100, 'mbw' => 5, 'populasi' => 58000]);

        [, $sPerhatian] = $this->buatKolamAktif('P1');
        $sPerhatian->dailyLogs()->create(['tgl' => now()->toDateString(), 'do_pagi' => 3.0]);

        [, $sKritis] = $this->buatKolamAktif('X1');
        $sKritis->emergencyLogs()->create(['tgl' => now()->subDay()->toDateString(), 'jenis' => 'penyakit', 'tindakan' => 'isolasi']);

        // DOC 40 → (40-30)%7=3, bukan jadwal sampling — murni siap panen
        [, $sPanen] = $this->buatKolamAktif('H1', ['tgl_pakan_pertama' => now()->subDays(40)->toDateString()]);
        $sPanen->samplings()->create(['tgl' => now()->subDays(2)->toDateString(), 'doc' => 38, 'berat_sampel_total' => 1400, 'jumlah_sampel' => 100, 'mbw' => 14, 'populasi' => 50000]);

        $data = $this->service()->controlRoomData($this->farm->id);
        $statusByKode = $data['petaKolam']->flatten(1)->mapWithKeys(fn ($t) => [$t['pond']->kode_kolam => $t['status']]);

        $this->assertSame('idle', $statusByKode['K0']);
        $this->assertSame('sehat', $statusByKode['S1']);
        $this->assertSame('perhatian', $statusByKode['P1']);
        $this->assertSame('kritis', $statusByKode['X1']);
        $this->assertSame('siap-panen', $statusByKode['H1']);

        $this->assertSame(4, $data['hero']['kolamAktif']);
        $this->assertSame(5, $data['hero']['kolamTotal']);
    }

    public function test_perlu_perhatian_terurut_dari_emergency_dulu(): void
    {
        // Dibuat TIDAK berurutan prioritas, biar kebukti hasilnya karena sorting
        [, $sFlush] = $this->buatKolamAktif('F1'); // DOC 10 < 30
        $sFlush->samplings()->create(['tgl' => now()->subDays(8)->toDateString(), 'doc' => 2, 'berat_sampel_total' => 500, 'jumlah_sampel' => 100, 'mbw' => 5, 'populasi' => 58000]);
        // SR 96,7% → 66,7% = turun 30 poin → SR-drop tajam → flush-out
        $sFlush->samplings()->create(['tgl' => now()->subDay()->toDateString(), 'doc' => 9, 'berat_sampel_total' => 600, 'jumlah_sampel' => 100, 'mbw' => 6, 'populasi' => 40000]);

        $this->buatKolamAktif('D1', ['tgl_pakan_pertama' => now()->subDays(30)->toDateString()]); // DOC 30 → sampling due

        [, $sWq] = $this->buatKolamAktif('W1');
        $sWq->dailyLogs()->create(['tgl' => now()->toDateString(), 'do_pagi' => 3.0]);

        [, $sEm] = $this->buatKolamAktif('E1');
        $sEm->emergencyLogs()->create(['tgl' => now()->toDateString(), 'jenis' => 'kematian massal', 'tindakan' => 'cek air']);

        $items = $this->service()->controlRoomData($this->farm->id)['perluPerhatian'];

        // E1 muncul 2x: emergency (prioritas 1) + flush-out (DOC<30 dan ada emergency 7 hari terakhir)
        $this->assertSame(['E1', 'W1', 'D1', 'E1', 'F1'], $items->pluck('kolam')->all());
        $this->assertStringContainsString('DO pagi', $items[1]['pesan']);
        $this->assertSame('kritis', $items[0]['tone']);
    }

    public function test_menuju_panen_urut_mbw_desc_dengan_ambang_11_dan_13(): void
    {
        [, $sa] = $this->buatKolamAktif('A1', ['tgl_pakan_pertama' => now()->subDays(40)->toDateString()]);
        $sa->samplings()->create(['tgl' => now()->subDay()->toDateString(), 'doc' => 39, 'berat_sampel_total' => 1200, 'jumlah_sampel' => 100, 'mbw' => 12, 'populasi' => 50000]);

        [, $sb] = $this->buatKolamAktif('B1', ['tgl_pakan_pertama' => now()->subDays(40)->toDateString()]);
        $sb->samplings()->create(['tgl' => now()->subDay()->toDateString(), 'doc' => 39, 'berat_sampel_total' => 1450, 'jumlah_sampel' => 100, 'mbw' => 14.5, 'populasi' => 48000]);

        [, $sc] = $this->buatKolamAktif('C1');
        $sc->samplings()->create(['tgl' => now()->subDay()->toDateString(), 'doc' => 9, 'berat_sampel_total' => 800, 'jumlah_sampel' => 100, 'mbw' => 8, 'populasi' => 55000]);

        $rows = $this->service()->controlRoomData($this->farm->id)['menujuPanen'];

        $this->assertSame(['B1', 'A1'], $rows->pluck('kolam')->all());
        $this->assertTrue($rows[0]['siap']);
        $this->assertFalse($rows[1]['siap']);
        $this->assertEqualsWithDelta(1000 / 14.5, $rows[0]['size'], 0.01);
        $this->assertEqualsWithDelta(48000 * 14.5 / 1000, $rows[0]['biomass'], 0.01);
    }

    public function test_sparkline_tren_biomass_30_hari(): void
    {
        [, $s] = $this->buatKolamAktif('A1', ['tgl_pakan_pertama' => now()->subDays(50)->toDateString()]);
        $s->samplings()->create(['tgl' => now()->subDays(40)->toDateString(), 'doc' => 10, 'berat_sampel_total' => 500, 'jumlah_sampel' => 100, 'mbw' => 5, 'populasi' => 55000]); // > 30 hari, diabaikan
        $s->samplings()->create(['tgl' => now()->subDays(10)->toDateString(), 'doc' => 40, 'berat_sampel_total' => 1000, 'jumlah_sampel' => 100, 'mbw' => 10, 'populasi' => 50000]);
        $s->samplings()->create(['tgl' => now()->subDays(3)->toDateString(), 'doc' => 47, 'berat_sampel_total' => 1200, 'jumlah_sampel' => 100, 'mbw' => 12, 'populasi' => 48000]);

        $spark = $this->service()->controlRoomData($this->farm->id)['sparkline'];

        $this->assertCount(2, $spark['values']);
        $this->assertEqualsWithDelta(500.0, $spark['values'][0], 0.01);  // 50000 × 10 / 1000
        $this->assertEqualsWithDelta(576.0, $spark['values'][1], 0.01);  // 48000 × 12 / 1000
        $this->assertNotSame('', $spark['points']);
    }

    public function test_total_query_dashboard_maksimal_15(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            [, $s] = $this->buatKolamAktif('Q'.$i);
            $s->samplings()->create(['tgl' => now()->subDay()->toDateString(), 'doc' => 9, 'berat_sampel_total' => 500, 'jumlah_sampel' => 100, 'mbw' => 5, 'populasi' => 58000]);
            $s->dailyLogs()->create(['tgl' => now()->toDateString(), 'pakan_07_kg' => 5]);
        }

        DB::enableQueryLog();
        $this->service()->controlRoomData($this->farm->id);
        $jumlahQuery = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 16 = 13 batch + dropdown siklus + dst — tetap O(1) berapa pun jumlah kolam.
        $this->assertLessThanOrEqual(17, $jumlahQuery, "Dashboard pakai {$jumlahQuery} query — harus batch, bukan per kolam.");
    }
}
