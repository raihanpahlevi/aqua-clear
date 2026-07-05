<?php

namespace App\Http\Controllers;

use App\Models\Stocking;
use App\Services\CostService;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Ringkasan Biaya & Laporan semua siklus lintas kolam, plus link ke detail per-siklus (show()).
     */
    public function index(CostService $costService): View
    {
        $farmId = auth()->user()->farm_id;

        $stockings = Stocking::whereHas('pond.block', fn ($q) => $q->where('farm_id', $farmId))
            ->with('pond', 'cycle')
            ->latest('tgl_tebar')
            ->get();

        $rows = $stockings->map(fn (Stocking $stocking) => [
            'stocking' => $stocking,
            'totalBiaya' => $costService->totalBiaya($stocking),
            'totalPendapatan' => $costService->totalPendapatanPanen($stocking),
            'hppPerKg' => $costService->hppPerKg($stocking),
            'labaRugi' => $costService->labaRugi($stocking),
        ]);

        $ringkasan = [
            'totalBiaya' => $rows->sum('totalBiaya'),
            'totalPendapatan' => $rows->sum('totalPendapatan'),
            'totalLabaRugi' => $rows->sum('labaRugi'),
        ];

        return view('reports.index', compact('rows', 'ringkasan'));
    }

    public function show(Stocking $stocking, CostService $costService): View
    {
        $biayaPerKategori = $costService->biayaPerKategori($stocking);
        $totalBiaya = $costService->totalBiaya($stocking);
        $persenBiayaPakan = $costService->persenBiayaPakan($stocking);
        $totalBiomassPanen = $costService->totalBiomassPanenKg($stocking);
        $totalPendapatan = $costService->totalPendapatanPanen($stocking);
        $hppPerKg = $costService->hppPerKg($stocking);
        $labaRugi = $costService->labaRugi($stocking);
        $progresPanen = $costService->progresTargetPanen($stocking);

        return view('reports.show', compact(
            'stocking',
            'biayaPerKategori',
            'totalBiaya',
            'persenBiayaPakan',
            'totalBiomassPanen',
            'totalPendapatan',
            'hppPerKg',
            'labaRugi',
            'progresPanen',
        ));
    }
}
