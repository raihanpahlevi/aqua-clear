<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $dashboardService): View
    {
        $data = $dashboardService->controlRoomData(
            auth()->user()->farm_id,
            $request->integer('kolam') ?: null,
            $request->integer('siklus') ?: null,
        );

        return view('dashboard', $data);
    }
}
