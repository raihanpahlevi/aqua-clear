<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockingRequest;
use App\Http\Requests\UpdateStockingRequest;
use App\Models\Cycle;
use App\Models\Pond;
use App\Models\Stocking;
use App\Services\DocService;
use App\Services\FeedService;
use App\Services\GrowthService;
use App\Services\WaterQualityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockingController extends Controller
{
    public function create(Pond $pond): View
    {
        $cycles = Cycle::orderBy('nama')->get();

        return view('stockings.create', compact('pond', 'cycles'));
    }

    public function store(StoreStockingRequest $request, Pond $pond): RedirectResponse
    {
        $stocking = $pond->stockings()->create($request->validated());

        $pond->update(['status' => 'aktif']);

        return redirect()->route('stockings.show', $stocking)->with('status', 'Siklus baru berhasil dimulai.');
    }

    public function show(
        Stocking $stocking,
        DocService $docService,
        GrowthService $growthService,
        FeedService $feedService,
        WaterQualityService $waterQualityService,
    ): View {
        $stocking->load('pond.block', 'cycle');

        $doc = $docService->today($stocking);
        $latestSampling = $growthService->latestSampling($stocking);
        $biomassKg = $growthService->latestBiomassKg($stocking);
        $survivalRate = $latestSampling ? $growthService->survivalRate($latestSampling->populasi, $stocking->jumlah_tebar) : null;
        $akumulasiPakanKg = $feedService->akumulasiPakanKg($stocking);
        $fcr = $feedService->fcr($akumulasiPakanKg, $biomassKg);
        $anchoAlert = $feedService->shouldAlertAncho($feedService->recentAnchoResults($stocking));

        $recentDailyLogs = $stocking->dailyLogs()->latest('tgl')->limit(7)->get();
        $samplings = $stocking->samplings()->latest('tgl')->get();

        // Titik grafik MBW/ADG/SR% per sampling (rumus tetap dari GrowthService)
        $chartTumbuh = null;
        $urut = $samplings->sortBy('tgl')->values();
        if ($urut->count() >= 2) {
            $mbwPts = [];
            $srPts = [];
            $adgPts = [];
            $prev = null;
            foreach ($urut as $s) {
                $label = $s->tgl->format('d/m');
                $mbwPts[] = ['label' => $label, 'value' => (float) $s->mbw];
                $srPts[] = ['label' => $label, 'value' => $growthService->survivalRate($s->populasi, $stocking->jumlah_tebar)];
                if ($prev) {
                    $adgPts[] = ['label' => $label, 'value' => $growthService->adg((float) $s->mbw, (float) $prev->mbw, $prev->tgl->diffInDays($s->tgl))];
                }
                $prev = $s;
            }
            $chartTumbuh = ['mbw' => $mbwPts, 'sr' => $srPts, 'adg' => $adgPts];
        }

        $latestDailyLog = $recentDailyLogs->first();
        $latestWeeklyLog = $stocking->waterQualityWeeklies()->latest('tgl')->first();
        $waterQualityAlert = ($latestDailyLog && $waterQualityService->hasDailyViolation($latestDailyLog))
            || ($latestWeeklyLog && $waterQualityService->hasWeeklyViolation($latestWeeklyLog));

        $srDropSharp = $growthService->hasSharpSrDrop($stocking);
        $recentEmergency = $stocking->emergencyLogs()->where('tgl', '>=', now()->subDays(7))->exists();
        $kondisiKritis = $srDropSharp || $recentEmergency;
        $flushOutRecommended = $docService->shouldRecommendFlushOut($stocking, $kondisiKritis, now());

        return view('stockings.show', compact(
            'stocking',
            'doc',
            'latestSampling',
            'biomassKg',
            'survivalRate',
            'akumulasiPakanKg',
            'fcr',
            'anchoAlert',
            'recentDailyLogs',
            'samplings',
            'waterQualityAlert',
            'flushOutRecommended',
            'srDropSharp',
            'recentEmergency',
            'growthService',
            'chartTumbuh',
        ));
    }

    public function edit(Stocking $stocking): View
    {
        $cycles = Cycle::orderBy('nama')->get();

        return view('stockings.edit', compact('stocking', 'cycles'));
    }

    public function update(UpdateStockingRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->update($request->validated());

        return redirect()->route('stockings.show', $stocking)->with('status', 'Data siklus berhasil diperbarui.');
    }
}
