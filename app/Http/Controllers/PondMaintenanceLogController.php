<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePondMaintenanceLogRequest;
use App\Models\PondMaintenanceLog;
use App\Models\Stocking;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PondMaintenanceLogController extends Controller
{
    public function index(Stocking $stocking): View
    {
        $logs = $stocking->pondMaintenanceLogs()->latest('tgl')->get();

        return view('pond-maintenance-logs.index', compact('stocking', 'logs'));
    }

    public function create(Stocking $stocking): View
    {
        return view('pond-maintenance-logs.create', compact('stocking'));
    }

    public function store(StorePondMaintenanceLogRequest $request, Stocking $stocking): RedirectResponse
    {
        $stocking->pondMaintenanceLogs()->create($request->validated());

        return redirect()->route('stockings.pond-maintenance-logs.index', $stocking)->with('status', 'Catatan manajemen dasar tambak berhasil disimpan.');
    }

    public function edit(Stocking $stocking, PondMaintenanceLog $pondMaintenanceLog): View
    {
        return view('pond-maintenance-logs.edit', compact('stocking', 'pondMaintenanceLog'));
    }

    public function update(StorePondMaintenanceLogRequest $request, Stocking $stocking, PondMaintenanceLog $pondMaintenanceLog): RedirectResponse
    {
        $pondMaintenanceLog->update($request->validated());

        return redirect()->route('stockings.pond-maintenance-logs.index', $stocking)->with('status', 'Catatan berhasil diperbarui.');
    }
}
