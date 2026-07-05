<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrepLogRequest;
use App\Models\Pond;
use App\Models\PrepLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrepLogController extends Controller
{
    /**
     * Checklist bebas per jenis — asumsi sementara, lihat CLAUDE.md Bagian 8.
     */
    public const CHECKLIST_ITEMS = [
        'tambak' => ['Pembersihan kolam', 'Sterilisasi', 'Penambalan bocor (jika ada)'],
        'air' => ['Isi air', 'Sterilisasi air', 'Cek awal kualitas air'],
    ];

    public function index(Pond $pond): View
    {
        $prepLogs = $pond->prepLogs()->with('cycle')->latest('tgl')->get();

        return view('prep-logs.index', compact('pond', 'prepLogs'));
    }

    public function create(Pond $pond): View
    {
        $cycles = \App\Models\Cycle::orderBy('nama')->get();

        return view('prep-logs.create', compact('pond', 'cycles'));
    }

    public function store(StorePrepLogRequest $request, Pond $pond): RedirectResponse
    {
        $validated = $request->validated();

        $checklist = array_keys(array_filter($validated['checklist'] ?? []));

        if (! empty($validated['item_lainnya'])) {
            $checklist[] = $validated['item_lainnya'];
        }

        $pond->prepLogs()->create([
            'cycle_id' => $validated['cycle_id'] ?? null,
            'jenis' => $validated['jenis'],
            'tgl' => $validated['tgl'],
            'checklist' => $checklist,
            'biaya' => $validated['biaya'] ?? null,
        ]);

        return redirect()->route('ponds.prep-logs.index', $pond)->with('status', 'Progres persiapan berhasil dicatat.');
    }

    public function edit(Pond $pond, PrepLog $prepLog): View
    {
        $cycles = \App\Models\Cycle::orderBy('nama')->get();

        return view('prep-logs.edit', compact('pond', 'prepLog', 'cycles'));
    }

    public function update(StorePrepLogRequest $request, Pond $pond, PrepLog $prepLog): RedirectResponse
    {
        $validated = $request->validated();

        $checklist = array_keys(array_filter($validated['checklist'] ?? []));

        if (! empty($validated['item_lainnya'])) {
            $checklist[] = $validated['item_lainnya'];
        }

        $prepLog->update([
            'cycle_id' => $validated['cycle_id'] ?? null,
            'jenis' => $validated['jenis'],
            'tgl' => $validated['tgl'],
            'checklist' => $checklist,
            'biaya' => $validated['biaya'] ?? null,
        ]);

        return redirect()->route('ponds.prep-logs.index', $pond)->with('status', 'Progres persiapan berhasil diperbarui.');
    }
}
