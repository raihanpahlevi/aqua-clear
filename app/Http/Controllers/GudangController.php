<?php

namespace App\Http\Controllers;

use App\Models\WarehouseEntry;
use App\Models\WarehouseItem;
use App\Services\WarehouseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Modul Gudang (fase 2, 2026-07-08): master barang + barang masuk + saldo.
 * Barang keluar otomatis dari pemakaian di Aplikasi Kimia & Biologi yang
 * tertaut ke barang gudang — tidak ada input keluar terpisah (anti dobel input).
 * Role tulis: operasional (konsisten PRD "obat-obatan"); semua role bisa lihat.
 */
class GudangController extends Controller
{
    public function index(WarehouseService $warehouseService): View
    {
        $rows = $warehouseService->ringkasanSaldo(auth()->user()->farm_id);

        return view('gudang.index', compact('rows'));
    }

    public function createItem(): View
    {
        return view('gudang.item-create');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $farmId = auth()->user()->farm_id;

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150', Rule::unique('warehouse_items')->where('farm_id', $farmId)],
            'kategori' => ['required', 'in:pakan,probiotik,mineral,desinfektan,obat'],
            'satuan' => ['required', 'string', 'max:30'],
        ]);

        WarehouseItem::create([...$validated, 'farm_id' => $farmId]);

        return redirect()->route('gudang.index')->with('status', 'Barang gudang berhasil didaftarkan.');
    }

    public function editItem(WarehouseItem $item): View
    {
        return view('gudang.item-edit', compact('item'));
    }

    public function updateItem(Request $request, WarehouseItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150', Rule::unique('warehouse_items')->where('farm_id', $item->farm_id)->ignore($item->id)],
            'kategori' => ['required', 'in:pakan,probiotik,mineral,desinfektan,obat'],
            'satuan' => ['required', 'string', 'max:30'],
        ]);

        $item->update($validated);

        return redirect()->route('gudang.index')->with('status', 'Barang gudang berhasil diperbarui.');
    }

    public function createEntry(): View
    {
        $items = WarehouseItem::where('farm_id', auth()->user()->farm_id)->orderBy('nama')->get();

        return view('gudang.entry-create', compact('items'));
    }

    public function storeEntry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_item_id' => [
                'required',
                Rule::exists('warehouse_items', 'id')->where('farm_id', auth()->user()->farm_id),
            ],
            'tgl' => ['required', 'date'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'harga' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        WarehouseEntry::create($validated);

        return redirect()->route('gudang.index')->with('status', 'Barang masuk berhasil dicatat.');
    }
}
