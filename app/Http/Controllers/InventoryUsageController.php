<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryUsageRequest;
use App\Http\Requests\UpdateInventoryUsageRequest;
use App\Models\InventoryUsage;
use App\Models\Stocking;
use App\Models\WarehouseItem;
use App\Services\WarehouseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryUsageController extends Controller
{
    public function index(Stocking $stocking): View
    {
        $usages = $stocking->inventoryUsages()->latest('tgl')->get();

        return view('inventory-usage.index', compact('stocking', 'usages'));
    }

    public function create(Stocking $stocking, WarehouseService $warehouseService): View
    {
        $warehouseItems = $warehouseService->ringkasanSaldo(auth()->user()->farm_id);

        return view('inventory-usage.create', compact('stocking', 'warehouseItems'));
    }

    public function store(StoreInventoryUsageRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->inventoryUsages()->create($this->withWarehouseDefaults($request->validated()));

        return redirect()->route('stockings.inventory-usage.index', $stocking)->with('status', 'Pemakaian berhasil dicatat.');
    }

    public function edit(Stocking $stocking, InventoryUsage $inventoryUsage, WarehouseService $warehouseService): View
    {
        $warehouseItems = $warehouseService->ringkasanSaldo(auth()->user()->farm_id);

        return view('inventory-usage.edit', compact('stocking', 'inventoryUsage', 'warehouseItems'));
    }

    public function update(UpdateInventoryUsageRequest $request, Stocking $stocking, InventoryUsage $inventoryUsage): RedirectResponse
    {
        $inventoryUsage->update($this->withWarehouseDefaults($request->validated()));

        return redirect()->route('stockings.inventory-usage.index', $stocking)->with('status', 'Pemakaian berhasil diperbarui.');
    }

    public function destroy(Stocking $stocking, InventoryUsage $inventoryUsage): RedirectResponse
    {
        $inventoryUsage->delete();

        return redirect()->route('stockings.inventory-usage.index', $stocking)->with('status', 'Pemakaian berhasil dihapus.');
    }

    /**
     * Kalau pemakaian ditautkan ke barang gudang, nama item + kategori + satuan
     * dipaksa ikut master gudang supaya saldo & breakdown biaya konsisten.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function withWarehouseDefaults(array $validated): array
    {
        if (! empty($validated['warehouse_item_id'])) {
            $item = WarehouseItem::findOrFail($validated['warehouse_item_id']);
            $validated['item'] = $item->nama;
            $validated['kategori'] = $item->kategori;
            $validated['satuan'] = $item->satuan;
        }

        return $validated;
    }
}
