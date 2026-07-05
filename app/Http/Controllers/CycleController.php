<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CycleController extends Controller
{
    public function index(): View
    {
        $cycles = Cycle::withCount('stockings')->latest()->get();

        return view('cycles.index', compact('cycles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
        ]);

        Cycle::create($validated);

        return redirect()->route('cycles.index')->with('status', 'Siklus berhasil ditambahkan.');
    }

    public function destroy(Cycle $cycle): RedirectResponse
    {
        if ($cycle->stockings()->exists()) {
            return back()->with('error', 'Siklus tidak bisa dihapus karena masih dipakai stocking.');
        }

        $cycle->delete();

        return redirect()->route('cycles.index')->with('status', 'Siklus berhasil dihapus.');
    }
}
