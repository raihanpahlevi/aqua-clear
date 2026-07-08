<?php

namespace App\Http\Controllers;

use App\Models\Stocking;
use App\Models\WaterQualityWeekly;
use App\Services\WaterQualityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Menu "Uji Lab" (fase 2, permintaan client 2026-07-08): grafik deret waktu semua
 * parameter uji air mingguan per kolam. Vibrio & ammonia juga tampil di dashboard;
 * halaman ini buat analisis lengkapnya. Read-only, semua role boleh lihat.
 */
class UjiLabController extends Controller
{
    public function index(Request $request, WaterQualityService $waterQualityService): View
    {
        $farmId = auth()->user()->farm_id;

        // Stocking yang punya data uji mingguan — buat dropdown pilihan kolam
        $stockings = Stocking::whereHas('pond.block', fn ($q) => $q->where('farm_id', $farmId))
            ->whereHas('waterQualityWeeklies')
            ->withCount('waterQualityWeeklies')
            ->with('pond', 'cycle')
            ->get()
            ->sortBy(fn (Stocking $s) => $s->pond->kode_kolam, SORT_NATURAL)
            ->values();

        $selected = null;
        if ($stockings->isNotEmpty()) {
            // Default: kolam dengan riwayat uji terbanyak, biar grafik langsung informatif
            $selected = $stockings->firstWhere('id', $request->integer('stocking'))
                ?? $stockings->sortByDesc('water_quality_weeklies_count')->first();
        }

        $charts = [];
        if ($selected) {
            $rows = $selected->waterQualityWeeklies()->orderBy('tgl')->orderBy('id')->get();

            $seri = function (callable $ambil) use ($rows): array {
                return $rows
                    ->map(fn (WaterQualityWeekly $w) => ['label' => $w->tgl->format('d/m'), 'value' => $ambil($w)])
                    ->filter(fn (array $p) => $p['value'] !== null)
                    ->values()
                    ->all();
            };

            // Ambang dari PRD 5.3 (sinkron dengan WaterQualityService)
            $charts = [
                ['title' => 'TAN', 'unit' => 'ppm', 'threshold' => 2.0, 'decimals' => 2, 'points' => $seri(fn ($w) => $w->tan !== null ? (float) $w->tan : null)],
                ['title' => 'Ammonia', 'unit' => 'ppm', 'threshold' => 0.1, 'decimals' => 3, 'points' => $seri(fn ($w) => $w->ammonia !== null ? (float) $w->ammonia : null)],
                ['title' => 'Nitrit', 'unit' => 'ppm', 'threshold' => 0.1, 'decimals' => 3, 'points' => $seri(fn ($w) => $w->nitrit !== null ? (float) $w->nitrit : null)],
                ['title' => 'Nitrat', 'unit' => 'ppm', 'threshold' => 50.0, 'decimals' => 1, 'points' => $seri(fn ($w) => $w->nitrat !== null ? (float) $w->nitrat : null)],
                ['title' => 'Rasio Vibrio/Bakteri', 'unit' => '%', 'threshold' => 10.0, 'decimals' => 1, 'points' => $seri(fn ($w) => $waterQualityService->vibrioRatioPercent($w))],
                ['title' => 'Kepadatan Vibrio Hijau', 'unit' => 'koloni', 'threshold' => null, 'decimals' => 0, 'points' => $seri(fn ($w) => $w->vibrio_hijau !== null ? (float) $w->vibrio_hijau : null)],
                ['title' => 'TOM', 'unit' => 'ppm', 'threshold' => null, 'decimals' => 1, 'points' => $seri(fn ($w) => $w->tom !== null ? (float) $w->tom : null)],
                ['title' => 'Alkalinitas', 'unit' => 'ppm', 'threshold' => null, 'decimals' => 0, 'points' => $seri(fn ($w) => $w->alkalinitas !== null ? (float) $w->alkalinitas : null)],
            ];
        }

        return view('uji-lab.index', compact('stockings', 'selected', 'charts'));
    }
}
