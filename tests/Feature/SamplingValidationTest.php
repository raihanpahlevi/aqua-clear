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

class SamplingValidationTest extends TestCase
{
    use RefreshDatabase;

    private function makeStocking(?string $tglPakanPertama): Stocking
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

    private function analis(Stocking $stocking): User
    {
        Role::firstOrCreate(['name' => 'analis', 'guard_name' => 'web']);
        $user = User::factory()->create(['farm_id' => $stocking->pond->block->farm_id]);
        $user->assignRole('analis');

        return $user;
    }

    public function test_tanggal_sampling_sebelum_pakan_pertama_ditolak(): void
    {
        $stocking = $this->makeStocking('2026-05-03');
        $user = $this->analis($stocking);

        $response = $this->actingAs($user)->post("/stockings/{$stocking->id}/samplings", [
            'tgl' => '2026-05-01', // sebelum tgl_pakan_pertama (2026-05-03)
            'berat_sampel_total' => 1000,
            'jumlah_sampel' => 100,
            'populasi' => 55000,
        ]);

        $response->assertSessionHasErrors('tgl');
        $this->assertDatabaseCount('samplings', 0);
    }

    public function test_sampling_ditolak_kalau_tgl_pakan_pertama_belum_diisi(): void
    {
        $stocking = $this->makeStocking(null);
        $user = $this->analis($stocking);

        $response = $this->actingAs($user)->post("/stockings/{$stocking->id}/samplings", [
            'tgl' => '2026-06-01',
            'berat_sampel_total' => 1000,
            'jumlah_sampel' => 100,
            'populasi' => 55000,
        ]);

        $response->assertSessionHasErrors('tgl');
        $this->assertDatabaseCount('samplings', 0);
    }

    public function test_sampling_valid_setelah_pakan_pertama_diterima(): void
    {
        $stocking = $this->makeStocking('2026-05-03');
        $user = $this->analis($stocking);

        $response = $this->actingAs($user)->post("/stockings/{$stocking->id}/samplings", [
            'tgl' => '2026-06-02',
            'berat_sampel_total' => 1500,
            'jumlah_sampel' => 100,
            'populasi' => 55000,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('samplings', 1);
        $this->assertDatabaseHas('samplings', ['doc' => 30]);
    }
}
