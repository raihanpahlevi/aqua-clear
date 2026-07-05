<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHarvestRequest;
use App\Http\Requests\UpdateHarvestRequest;
use App\Models\Harvest;
use App\Models\Stocking;
use App\Services\CostService;
use App\Services\HarvestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HarvestController extends Controller
{
    public function index(Stocking $stocking, CostService $costService): View
    {
        $harvests = $stocking->harvests()->oldest('tgl')->get();
        $progres = $costService->progresTargetPanen($stocking);
        $referensiTahap = HarvestService::TAHAP_REFERENSI;

        return view('harvests.index', compact('stocking', 'harvests', 'progres', 'referensiTahap'));
    }

    public function create(Stocking $stocking): View
    {
        $referensiTahap = HarvestService::TAHAP_REFERENSI;

        return view('harvests.create', compact('stocking', 'referensiTahap'));
    }

    public function store(StoreHarvestRequest $request, Stocking $stocking, HarvestService $harvestService): RedirectResponse
    {
        $validated = $request->validated();

        $stocking->harvests()->create([
            ...$validated,
            'pendapatan' => $harvestService->pendapatan((float) $validated['berat_kg'], (float) $validated['harga_per_kg']),
        ]);

        if ($validated['tahap'] === 'total') {
            $stocking->pond->update(['status' => 'panen']);
        }

        return redirect()->route('stockings.harvests.index', $stocking)->with('status', 'Data panen berhasil disimpan.');
    }

    public function edit(Stocking $stocking, Harvest $harvest): View
    {
        $referensiTahap = HarvestService::TAHAP_REFERENSI;

        return view('harvests.edit', compact('stocking', 'harvest', 'referensiTahap'));
    }

    public function update(UpdateHarvestRequest $request, Stocking $stocking, Harvest $harvest, HarvestService $harvestService): RedirectResponse
    {
        $validated = $request->validated();

        $harvest->update([
            ...$validated,
            'pendapatan' => $harvestService->pendapatan((float) $validated['berat_kg'], (float) $validated['harga_per_kg']),
        ]);

        if ($validated['tahap'] === 'total') {
            $stocking->pond->update(['status' => 'panen']);
        }

        return redirect()->route('stockings.harvests.index', $stocking)->with('status', 'Data panen berhasil diperbarui.');
    }
}
