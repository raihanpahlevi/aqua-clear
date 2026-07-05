<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService): View
    {
        $farmId = auth()->user()->farm_id;

        $kolamAktif = $dashboardService->kolamAktif($farmId);
        $rataRataFcr = $dashboardService->rataRataFcr($farmId);
        $kolamEmergency = $dashboardService->kolamEmergencyCount($farmId);
        $pakanBulanIni = $dashboardService->pakanBulanIni($farmId);
        $estimasiBiomassSiapPanen = $dashboardService->estimasiBiomassSiapPanen($farmId);
        $estimasiLabaRugi = $dashboardService->estimasiLabaRugiBerjalan($farmId);
        $aktivitasTerbaru = $dashboardService->aktivitasTerbaru($farmId);

        return view('dashboard', compact(
            'kolamAktif',
            'rataRataFcr',
            'kolamEmergency',
            'pakanBulanIni',
            'estimasiBiomassSiapPanen',
            'estimasiLabaRugi',
            'aktivitasTerbaru',
        ));
    }
}
