<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkStorePondRequest;
use App\Http\Requests\StorePondRequest;
use App\Http\Requests\UpdatePondRequest;
use App\Models\Block;
use App\Models\Pond;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PondController extends Controller
{
    public function index(): View
    {
        $ponds = Pond::with('block')
            ->whereHas('block', fn ($q) => $q->where('farm_id', auth()->user()->farm_id))
            ->orderBy('block_id')
            ->orderBy('kode_kolam')
            ->get()
            ->groupBy(fn (Pond $pond) => $pond->block->nama);

        return view('ponds.index', compact('ponds'));
    }

    public function create(): View
    {
        $blocks = Block::where('farm_id', auth()->user()->farm_id)->orderBy('nama')->get();

        return view('ponds.create', compact('blocks'));
    }

    public function store(StorePondRequest $request): RedirectResponse
    {
        Pond::create($request->validated());

        return redirect()->route('ponds.index')->with('status', 'Kolam berhasil ditambahkan.');
    }

    public function bulkCreate(): View
    {
        $blocks = Block::where('farm_id', auth()->user()->farm_id)->orderBy('nama')->get();

        return view('ponds.bulk-create', compact('blocks'));
    }

    public function bulkStore(BulkStorePondRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $dibuat = [];
        $dilewati = [];

        for ($i = 0; $i < $validated['jumlah']; $i++) {
            $kodeKolam = $validated['prefix'].($validated['nomor_mulai'] + $i);

            $sudahAda = Pond::where('block_id', $validated['block_id'])->where('kode_kolam', $kodeKolam)->exists();

            if ($sudahAda) {
                $dilewati[] = $kodeKolam;

                continue;
            }

            Pond::create([
                'block_id' => $validated['block_id'],
                'kode_kolam' => $kodeKolam,
                'luas' => $validated['luas'] ?? null,
                'kapasitas' => $validated['kapasitas'] ?? null,
                'status' => $validated['status'],
            ]);

            $dibuat[] = $kodeKolam;
        }

        $pesan = count($dibuat).' kolam berhasil dibuat ('.implode(', ', $dibuat).').';

        if (! empty($dilewati)) {
            $pesan .= ' Dilewati karena kode sudah ada: '.implode(', ', $dilewati).'.';
        }

        return redirect()->route('ponds.index')->with('status', $pesan);
    }

    public function show(Pond $pond): View
    {
        $pond->load('block.farm');
        $stockings = $pond->stockings()->with('cycle')->latest('tgl_tebar')->get();

        return view('ponds.show', compact('pond', 'stockings'));
    }

    public function edit(Pond $pond): View
    {
        $blocks = Block::where('farm_id', auth()->user()->farm_id)->orderBy('nama')->get();

        return view('ponds.edit', compact('pond', 'blocks'));
    }

    public function update(UpdatePondRequest $request, Pond $pond): RedirectResponse
    {
        $pond->update($request->validated());

        return redirect()->route('ponds.index')->with('status', 'Kolam berhasil diperbarui.');
    }

    public function destroy(Pond $pond): RedirectResponse
    {
        if ($pond->stockings()->exists()) {
            return back()->with('error', 'Kolam tidak bisa dihapus karena masih punya riwayat siklus/stocking.');
        }

        $pond->delete();

        return redirect()->route('ponds.index')->with('status', 'Kolam berhasil dihapus.');
    }
}
