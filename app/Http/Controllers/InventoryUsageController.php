<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryUsageRequest;
use App\Http\Requests\UpdateInventoryUsageRequest;
use App\Models\InventoryUsage;
use App\Models\Stocking;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryUsageController extends Controller
{
    public function index(Stocking $stocking): View
    {
        $usages = $stocking->inventoryUsages()->latest('tgl')->get();

        return view('inventory-usage.index', compact('stocking', 'usages'));
    }

    public function create(Stocking $stocking): View
    {
        return view('inventory-usage.create', compact('stocking'));
    }

    public function store(StoreInventoryUsageRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->inventoryUsages()->create($request->validated());

        return redirect()->route('stockings.inventory-usage.index', $stocking)->with('status', 'Pemakaian berhasil dicatat.');
    }

    public function edit(Stocking $stocking, InventoryUsage $inventoryUsage): View
    {
        return view('inventory-usage.edit', compact('stocking', 'inventoryUsage'));
    }

    public function update(UpdateInventoryUsageRequest $request, Stocking $stocking, InventoryUsage $inventoryUsage): RedirectResponse
    {
        $inventoryUsage->update($request->validated());

        return redirect()->route('stockings.inventory-usage.index', $stocking)->with('status', 'Pemakaian berhasil diperbarui.');
    }

    public function destroy(Stocking $stocking, InventoryUsage $inventoryUsage): RedirectResponse
    {
        $inventoryUsage->delete();

        return redirect()->route('stockings.inventory-usage.index', $stocking)->with('status', 'Pemakaian berhasil dihapus.');
    }
}
