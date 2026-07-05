<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyLogRequest;
use App\Http\Requests\UpdateDailyLogRequest;
use App\Models\DailyLog;
use App\Models\Stocking;
use App\Services\FeedService;
use App\Services\GrowthService;
use App\Services\WaterQualityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DailyLogController extends Controller
{
    public function index(Stocking $stocking, WaterQualityService $waterQualityService, GrowthService $growthService): View
    {
        $dailyLogs = $stocking->dailyLogs()->latest('tgl')->paginate(31);

        return view('daily-logs.index', compact('stocking', 'dailyLogs', 'waterQualityService', 'growthService'));
    }

    public function create(Stocking $stocking): View
    {
        return view('daily-logs.create', compact('stocking'));
    }

    public function store(StoreDailyLogRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->dailyLogs()->create($request->validated());

        return redirect()->route('stockings.daily-logs.index', $stocking)->with('status', 'Input harian berhasil disimpan.');
    }

    public function edit(
        Stocking $stocking,
        DailyLog $dailyLog,
        FeedService $feedService,
        GrowthService $growthService,
    ): View {
        $totalPakanHarian = $feedService->totalPakanHarianKg($dailyLog);
        $biomassKg = $growthService->latestBiomassKg($stocking);
        $fr = $feedService->fr($totalPakanHarian, $biomassKg);
        $correctedMortality = $dailyLog->mortalitas !== null ? $growthService->correctedMortality($dailyLog->mortalitas) : null;
        $anchoPortionKg = $feedService->anchoPortionKg($totalPakanHarian);

        return view('daily-logs.edit', compact(
            'stocking',
            'dailyLog',
            'totalPakanHarian',
            'fr',
            'correctedMortality',
            'anchoPortionKg',
        ));
    }

    public function update(UpdateDailyLogRequest $request, Stocking $stocking, DailyLog $dailyLog): RedirectResponse
    {
        $dailyLog->update($request->validated());

        return redirect()->route('stockings.daily-logs.index', $stocking)->with('status', 'Input harian berhasil diperbarui.');
    }
}
