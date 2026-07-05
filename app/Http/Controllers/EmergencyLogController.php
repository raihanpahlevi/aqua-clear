<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmergencyLogRequest;
use App\Models\Stocking;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmergencyLogController extends Controller
{
    public function index(Stocking $stocking): View
    {
        $logs = $stocking->emergencyLogs()->latest('tgl')->get();

        return view('emergency-logs.index', compact('stocking', 'logs'));
    }

    public function create(Stocking $stocking): View
    {
        return view('emergency-logs.create', compact('stocking'));
    }

    public function store(StoreEmergencyLogRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->emergencyLogs()->create($request->validated());

        return redirect()->route('stockings.emergency-logs.index', $stocking)->with('status', 'Kejadian berhasil dicatat.');
    }
}
