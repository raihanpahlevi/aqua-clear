<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSamplingRequest;
use App\Http\Requests\UpdateSamplingRequest;
use App\Models\Sampling;
use App\Models\Stocking;
use App\Services\DocService;
use App\Services\GrowthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SamplingController extends Controller
{
    public function index(Stocking $stocking, GrowthService $growthService): View
    {
        $samplings = $stocking->samplings()->oldest('tgl')->get();

        $rows = [];
        $previous = null;
        foreach ($samplings as $sampling) {
            $adg = $previous
                ? $growthService->adg((float) $sampling->mbw, (float) $previous->mbw, $previous->tgl->diffInDays($sampling->tgl))
                : null;
            $sr = $growthService->survivalRate($sampling->populasi, $stocking->jumlah_tebar);
            $biomass = $growthService->biomassKg($sampling->populasi, (float) $sampling->mbw);
            $size = $growthService->size((float) $sampling->mbw);

            $rows[] = compact('sampling', 'adg', 'sr', 'biomass', 'size');
            $previous = $sampling;
        }

        $rows = array_reverse($rows);

        return view('samplings.index', compact('stocking', 'rows'));
    }

    public function create(Stocking $stocking): View
    {
        return view('samplings.create', compact('stocking'));
    }

    public function store(StoreSamplingRequest $request, Stocking $stocking, DocService $docService, GrowthService $growthService): RedirectResponse
    {
        $validated = $request->validated();

        $stocking->samplings()->create([
            ...$validated,
            'doc' => $docService->forDate($stocking, $validated['tgl']) ?? 0,
            'mbw' => $growthService->mbw((float) $validated['berat_sampel_total'], (int) $validated['jumlah_sampel']),
        ]);

        return redirect()->route('stockings.samplings.index', $stocking)->with('status', 'Sampling berhasil disimpan.');
    }

    public function edit(Stocking $stocking, Sampling $sampling): View
    {
        return view('samplings.edit', compact('stocking', 'sampling'));
    }

    public function update(UpdateSamplingRequest $request, Stocking $stocking, Sampling $sampling, DocService $docService, GrowthService $growthService): RedirectResponse
    {
        $validated = $request->validated();

        $sampling->update([
            ...$validated,
            'doc' => $docService->forDate($stocking, $validated['tgl']) ?? 0,
            'mbw' => $growthService->mbw((float) $validated['berat_sampel_total'], (int) $validated['jumlah_sampel']),
        ]);

        return redirect()->route('stockings.samplings.index', $stocking)->with('status', 'Sampling berhasil diperbarui.');
    }
}
