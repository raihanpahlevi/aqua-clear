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

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private Stocking $stocking;

    private Farm $farm;

    private function userWithRole(string $role, Farm $farm): User
    {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);

        $user = User::factory()->create(['farm_id' => $farm->id]);
        $user->assignRole($role);

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
        $block = Block::create(['farm_id' => $farm->id, 'nama' => 'A']);
        $pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);
        $cycle = Cycle::create(['nama' => 'Siklus Test']);

        $this->stocking = $pond->stockings()->create([
            'cycle_id' => $cycle->id,
            'tgl_tebar' => '2026-05-01',
            'tgl_pakan_pertama' => '2026-05-03',
            'jumlah_tebar' => 60000,
        ]);

        $this->farm = $farm;
    }

    public function test_analis_tidak_bisa_bikin_kolam_baru(): void
    {
        $analis = $this->userWithRole('analis', $this->farm);

        $this->actingAs($analis)->get('/ponds/create')->assertForbidden();
    }

    public function test_owner_bisa_bikin_kolam_baru(): void
    {
        $owner = $this->userWithRole('owner', $this->farm);

        $this->actingAs($owner)->get('/ponds/create')->assertOk();
    }

    public function test_lab_tidak_bisa_input_sampling(): void
    {
        $lab = $this->userWithRole('lab', $this->farm);

        $this->actingAs($lab)->get("/stockings/{$this->stocking->id}/samplings/create")->assertForbidden();
    }

    public function test_analis_bisa_input_sampling(): void
    {
        $analis = $this->userWithRole('analis', $this->farm);

        $this->actingAs($analis)->get("/stockings/{$this->stocking->id}/samplings/create")->assertOk();
    }

    // TEMP TESTING (2026-07-04): route sengaja dibuka buat 'operasional' juga biar 1 akun bisa
    // testing semua menu (lihat catatan TEMP TESTING di routes/web.php). Assertion dibalik sementara.
    // Kalau routes/web.php sudah dibalikin ke 'role:lab' saja, balikin juga assertForbidden() di sini.
    public function test_operasional_sementara_bisa_input_kualitas_air_mingguan_untuk_testing(): void
    {
        $operasional = $this->userWithRole('operasional', $this->farm);

        $this->actingAs($operasional)->get("/stockings/{$this->stocking->id}/water-quality-weekly/create")->assertOk();
    }

    public function test_semua_role_bisa_lihat_laporan_biaya(): void
    {
        foreach (['owner', 'analis', 'operasional', 'lab'] as $role) {
            $user = $this->userWithRole($role, $this->farm);

            $this->actingAs($user)->get("/stockings/{$this->stocking->id}/report")->assertOk();
        }
    }
}
