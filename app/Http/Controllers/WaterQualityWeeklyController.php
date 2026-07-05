<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaterQualityWeeklyRequest;
use App\Http\Requests\UpdateWaterQualityWeeklyRequest;
use App\Models\Stocking;
use App\Models\WaterQualityWeekly;
use App\Services\WaterQualityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WaterQualityWeeklyController extends Controller
{
    public function index(Stocking $stocking, WaterQualityService $waterQualityService): View
    {
        $logs = $stocking->waterQualityWeeklies()->latest('tgl')->get();
        $ratios = $logs->mapWithKeys(fn (WaterQualityWeekly $log) => [$log->id => $waterQualityService->vibrioRatioPercent($log)]);

        return view('water-quality-weekly.index', compact('stocking', 'logs', 'ratios', 'waterQualityService'));
    }

    public function create(Stocking $stocking): View
    {
        return view('water-quality-weekly.create', compact('stocking'));
    }

    public function store(StoreWaterQualityWeeklyRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->waterQualityWeeklies()->create($request->validated());

        return redirect()->route('stockings.water-quality-weekly.index', $stocking)->with('status', 'Data kualitas air berhasil disimpan.');
    }

    public function edit(Stocking $stocking, WaterQualityWeekly $waterQualityWeekly): View
    {
        return view('water-quality-weekly.edit', compact('stocking', 'waterQualityWeekly'));
    }

    public function update(UpdateWaterQualityWeeklyRequest $request, Stocking $stocking, WaterQualityWeekly $waterQualityWeekly): RedirectResponse
    {
        $waterQualityWeekly->update($request->validated());

        return redirect()->route('stockings.water-quality-weekly.index', $stocking)->with('status', 'Data kualitas air berhasil diperbarui.');
    }
}
