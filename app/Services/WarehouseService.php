<?php

namespace App\Services;

use App\Models\InventoryUsage;
use App\Models\WarehouseEntry;
use App\Models\WarehouseItem;
use Illuminate\Support\Collection;

/**
 * Modul Gudang (fase 2, 2026-07-08). Saldo = total barang masuk (warehouse_entries)
 * − total pemakaian (inventory_usage yang tertaut warehouse_item_id). Pemakaian
 * TANPA tautan gudang tidak mengurangi saldo (barang lama/di luar gudang).
 */
class WarehouseService
{
    /**
     * Ringkasan semua barang satu farm — masuk/terpakai/saldo — dalam 3 query
     * (bukan per item), urut nama.
     *
     * @return Collection<int, array{item: WarehouseItem, masuk: float, terpakai: float, saldo: float}>
     */
    public function ringkasanSaldo(int $farmId): Collection
    {
        $items = WarehouseItem::where('farm_id', $farmId)->orderBy('nama')->get();
        $itemIds = $items->pluck('id');

        $masuk = WarehouseEntry::whereIn('warehouse_item_id', $itemIds)
            ->selectRaw('warehouse_item_id, COALESCE(SUM(qty),0) as total')
            ->groupBy('warehouse_item_id')
            ->pluck('total', 'warehouse_item_id');

        $terpakai = InventoryUsage::whereIn('warehouse_item_id', $itemIds)
            ->selectRaw('warehouse_item_id, COALESCE(SUM(qty),0) as total')
            ->groupBy('warehouse_item_id')
            ->pluck('total', 'warehouse_item_id');

        return $items->map(function (WarehouseItem $item) use ($masuk, $terpakai) {
            $in = (float) ($masuk[$item->id] ?? 0);
            $out = (float) ($terpakai[$item->id] ?? 0);

            return [
                'item' => $item,
                'masuk' => $in,
                'terpakai' => $out,
                'saldo' => $in - $out,
            ];
        });
    }

    /** Saldo satu barang (buat validasi/tampilan form). */
    public function saldo(WarehouseItem $item): float
    {
        return (float) $item->entries()->sum('qty') - (float) $item->usages()->sum('qty');
    }
}
