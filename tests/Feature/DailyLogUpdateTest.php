<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\Farm;
use App\Models\Pond;
use App\Models\Stocking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DailyLogUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_operasional_bisa_update_daily_log_tanpa_ubah_tanggal(): void
    {
        Role::firstOrCreate(['name' => 'operasional', 'guard_name' => 'web']);

        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        $stocking = $pond->stockings()->create([
            'cycle_id' => $cycle->id,
            'tgl_tebar' => '2026-05-01',
            'tgl_pakan_pertama' => '2026-05-03',
            'jumlah_tebar' => 60000,
        ]);

        $dailyLog = $stocking->dailyLogs()->create([
            'tgl' => '2026-05-10',
            'pakan_07_kg' => 5,
            'mortalitas' => 2,
        ]);

        $operasional = User::factory()->create(['farm_id' => $farm->id]);
        $operasional->assignRole('operasional');

        $response = $this->actingAs($operasional)->put(
            "/stockings/{$stocking->id}/daily-logs/{$dailyLog->id}",
            [
                'tgl' => '2026-05-10',
                'pakan_07_kg' => 6,
                'mortalitas' => 3,
            ]
        );

        $response->assertRedirect("/stockings/{$stocking->id}/daily-logs");
        $response->assertSessionHas('status');
        $this->assertEquals(6, $dailyLog->fresh()->pakan_07_kg);
        $this->assertEquals(3, $dailyLog->fresh()->mortalitas);
    }
}
