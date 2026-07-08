<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\Farm;
use App\Models\Pond;
use App\Models\Stocking;
use App\Models\User;
use App\Models\WarehouseItem;
use App\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    private Farm $farm;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['owner', 'analis', 'operasional', 'lab'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->farm = Farm::create(['nama' => 'Tambak Test', 'lokasi' => 'Test']);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['farm_id' => $this->farm->id]);
        $user->assignRole($role);

        return $user;
    }

    private function buatStocking(): Stocking
    {
        $block = Block::create(['farm_id' => $this->farm->id, 'nama' => 'A']);
        $pond = Pond::create(['block_id' => $block->id, 'kode_kolam' => 'A1', 'status' => 'aktif']);

        return $pond->stockings()->create([
            'cycle_id' => Cycle::create(['nama' => 'Siklus Test'])->id,
            'tgl_tebar' => '2026-06-01',
            'tgl_pakan_pertama' => '2026-06-03',
            'jumlah_tebar' => 100000,
        ]);
    }

    public function test_operasional_bisa_daftarkan_barang_dan_catat_masuk(): void
    {
        $ops = $this->userWithRole('operasional');

        $this->actingAs($ops)->post('/gudang/barang', [
            'nama' => 'Pakan Grower', 'kategori' => 'pakan', 'satuan' => 'kg',
        ])->assertRedirect('/gudang');

        $item = WarehouseItem::where('nama', 'Pakan Grower')->firstOrFail();

        $this->actingAs($ops)->post('/gudang/masuk', [
            'warehouse_item_id' => $item->id, 'tgl' => '2026-07-01', 'qty' => 500, 'harga' => 8250000,
        ])->assertRedirect('/gudang');

        $this->assertSame(500.0, app(WarehouseService::class)->saldo($item));
    }

    public function test_lab_tidak_bisa_tulis_gudang_tapi_bisa_lihat(): void
    {
        $lab = $this->userWithRole('lab');

        $this->actingAs($lab)->get('/gudang')->assertOk();
        $this->actingAs($lab)->get('/gudang/barang/create')->assertForbidden();
        $this->actingAs($lab)->post('/gudang/barang', ['nama' => 'X', 'kategori' => 'obat', 'satuan' => 'botol'])->assertForbidden();
    }

    public function test_pemakaian_tertaut_mengurangi_saldo_dan_ikut_master(): void
    {
        $ops = $this->userWithRole('operasional');
        $stocking = $this->buatStocking();

        $item = WarehouseItem::create(['farm_id' => $this->farm->id, 'nama' => 'Probiotik Rhodo', 'kategori' => 'probiotik', 'satuan' => 'liter']);
        $item->entries()->create(['tgl' => '2026-07-01', 'qty' => 100]);

        // item & kategori sengaja dikosongkan — harus otomatis ikut master gudang
        $this->actingAs($ops)->post("/stockings/{$stocking->id}/inventory-usage", [
            'tgl' => '2026-07-05', 'warehouse_item_id' => $item->id, 'kategori' => '', 'item' => '', 'qty' => 30, 'satuan' => '', 'harga' => 900000,
        ])->assertRedirect("/stockings/{$stocking->id}/inventory-usage");

        $usage = $stocking->inventoryUsages()->firstOrFail();
        $this->assertSame('Probiotik Rhodo', $usage->item);
        $this->assertSame('probiotik', $usage->kategori);
        $this->assertSame('liter', $usage->satuan);
        $this->assertSame(70.0, app(WarehouseService::class)->saldo($item));
    }

    public function test_pemakaian_tanpa_gudang_wajib_isi_item_dan_tidak_sentuh_saldo(): void
    {
        $ops = $this->userWithRole('operasional');
        $stocking = $this->buatStocking();

        $item = WarehouseItem::create(['farm_id' => $this->farm->id, 'nama' => 'Kapur', 'kategori' => 'mineral', 'satuan' => 'sak']);
        $item->entries()->create(['tgl' => '2026-07-01', 'qty' => 40]);

        // tanpa warehouse_item_id, item kosong → validasi gagal
        $this->actingAs($ops)->post("/stockings/{$stocking->id}/inventory-usage", [
            'tgl' => '2026-07-05', 'kategori' => 'mineral', 'item' => '', 'qty' => 5,
        ])->assertSessionHasErrors('item');

        // item diisi manual (bukan dari gudang) → sukses, saldo gudang utuh
        $this->actingAs($ops)->post("/stockings/{$stocking->id}/inventory-usage", [
            'tgl' => '2026-07-05', 'kategori' => 'mineral', 'item' => 'Kapur luar gudang', 'qty' => 5,
        ])->assertRedirect("/stockings/{$stocking->id}/inventory-usage");

        $this->assertSame(40.0, app(WarehouseService::class)->saldo($item));
    }

    public function test_ringkasan_saldo_menghitung_masuk_terpakai(): void
    {
        $stocking = $this->buatStocking();

        $item = WarehouseItem::create(['farm_id' => $this->farm->id, 'nama' => 'Pakan 3M', 'kategori' => 'pakan', 'satuan' => 'kg']);
        $item->entries()->create(['tgl' => '2026-07-01', 'qty' => 300]);
        $item->entries()->create(['tgl' => '2026-07-03', 'qty' => 200]);
        $stocking->inventoryUsages()->create(['tgl' => '2026-07-04', 'warehouse_item_id' => $item->id, 'kategori' => 'pakan', 'item' => 'Pakan 3M', 'qty' => 120]);

        $row = app(WarehouseService::class)->ringkasanSaldo($this->farm->id)->firstWhere('item.id', $item->id);

        $this->assertSame(500.0, $row['masuk']);
        $this->assertSame(120.0, $row['terpakai']);
        $this->assertSame(380.0, $row['saldo']);
    }
}
