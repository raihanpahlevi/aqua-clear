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

class PrepLogAndMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    private Pond $pond;

    private Stocking $stocking;

    private Farm $farm;

    private function userWithRole(string $role): User
    {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user = User::factory()->create(['farm_id' => $this->farm->id]);
        $user->assignRole($role);

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $this->farm->id, 'nama' => 'A']);
        $this->pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'kosong']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        $this->stocking = $this->pond->stockings()->create([
            'cycle_id' => $cycle->id,
            'tgl_tebar' => '2026-05-01',
            'tgl_pakan_pertama' => '2026-05-03',
            'jumlah_tebar' => 60000,
        ]);
    }

    public function test_owner_bisa_catat_persiapan_tambak(): void
    {
        $owner = $this->userWithRole('owner');

        $response = $this->actingAs($owner)->post("/ponds/{$this->pond->id}/prep-logs", [
            'jenis' => 'tambak',
            'tgl' => '2026-04-01',
            'checklist' => ['Pembersihan kolam' => '1', 'Sterilisasi' => '1'],
            'biaya' => 500000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('prep_logs', ['pond_id' => $this->pond->id, 'jenis' => 'tambak']);

        $log = $this->pond->prepLogs()->first();
        $this->assertEqualsCanonicalizing(['Pembersihan kolam', 'Sterilisasi'], $log->checklist);
    }

    public function test_lab_tidak_bisa_catat_persiapan_tambak(): void
    {
        $lab = $this->userWithRole('lab');

        $this->actingAs($lab)->get("/ponds/{$this->pond->id}/prep-logs/create")->assertForbidden();
    }

    public function test_operasional_bisa_catat_manajemen_dasar_tambak(): void
    {
        $operasional = $this->userWithRole('operasional');

        $response = $this->actingAs($operasional)->post("/stockings/{$this->stocking->id}/pond-maintenance-logs", [
            'tgl' => '2026-06-01',
            'siphon' => '1',
            'kondisi_lumpur' => 'baik',
            'jumlah_kincir' => 2,
            'jam_nyala_kincir' => 18,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pond_maintenance_logs', [
            'stocking_id' => $this->stocking->id,
            'kondisi_lumpur' => 'baik',
            'siphon' => true,
        ]);
    }

    public function test_lab_tidak_bisa_catat_manajemen_dasar_tambak(): void
    {
        $lab = $this->userWithRole('lab');

        $this->actingAs($lab)->get("/stockings/{$this->stocking->id}/pond-maintenance-logs/create")->assertForbidden();
    }

    public function test_tanggal_maintenance_log_unik_per_stocking(): void
    {
        $operasional = $this->userWithRole('operasional');

        $this->stocking->pondMaintenanceLogs()->create(['tgl' => '2026-06-01', 'siphon' => true]);

        $response = $this->actingAs($operasional)->post("/stockings/{$this->stocking->id}/pond-maintenance-logs", [
            'tgl' => '2026-06-01',
            'siphon' => '0',
        ]);

        $response->assertSessionHasErrors('tgl');
    }
}
