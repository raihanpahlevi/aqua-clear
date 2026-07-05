<?php

namespace App\Services;

use App\Models\Stocking;

/**
 * Biaya & Laporan — PRD Bagian 5.5.
 * Biaya pakan dihitung dari pembelian (inventory_usage kategori 'pakan'), BUKAN dari kg pakan
 * di daily_logs — daily_logs cuma catat kg diberikan untuk FCR/biomass, bukan harga.
 * harga_benur pada stocking diasumsikan TOTAL biaya benur siklus itu (bukan harga per ekor) — lihat CLAUDE.md.
 */
class CostService
{
    public function biayaPerKategori(Stocking $stocking): array
    {
        $dariInventory = $stocking->inventoryUsages()
            ->selectRaw('kategori, COALESCE(SUM(harga), 0) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        return [
            'benur' => (float) ($stocking->harga_benur ?? 0),
            'pakan' => $dariInventory['pakan'] ?? 0.0,
            'probiotik' => $dariInventory['probiotik'] ?? 0.0,
            'mineral' => $dariInventory['mineral'] ?? 0.0,
            'desinfektan' => $dariInventory['desinfektan'] ?? 0.0,
            'obat' => $dariInventory['obat'] ?? 0.0,
        ];
    }

    public function totalBiaya(Stocking $stocking): float
    {
        return array_sum($this->biayaPerKategori($stocking));
    }

    public function persenBiayaPakan(Stocking $stocking): ?float
    {
        $total = $this->totalBiaya($stocking);

        if ($total <= 0) {
            return null;
        }

        $biaya = $this->biayaPerKategori($stocking);

        return ($biaya['pakan'] / $total) * 100;
    }

    public function totalBiomassPanenKg(Stocking $stocking): float
    {
        return (float) $stocking->harvests()->sum('berat_kg');
    }

    public function totalPendapatanPanen(Stocking $stocking): float
    {
        return (float) $stocking->harvests()->sum('pendapatan');
    }

    public function hppPerKg(Stocking $stocking): ?float
    {
        $biomass = $this->totalBiomassPanenKg($stocking);

        if ($biomass <= 0) {
            return null;
        }

        return $this->totalBiaya($stocking) / $biomass;
    }

    /**
     * Estimasi, bukan angka pasti — dipengaruhi harga jual aktual. Lihat CLAUDE.md.
     */
    public function labaRugi(Stocking $stocking): float
    {
        return $this->totalPendapatanPanen($stocking) - $this->totalBiaya($stocking);
    }

    /**
     * Target 1.100–1.200 kg/kolam diasumsikan akumulasi SEMUA tahap panen — lihat CLAUDE.md Bagian 8.
     */
    public function progresTargetPanen(Stocking $stocking): array
    {
        $biomass = $this->totalBiomassPanenKg($stocking);

        return [
            'biomass_kg' => $biomass,
            'target_min' => 1100,
            'target_max' => 1200,
            'persen_dari_target_min' => $biomass > 0 ? min(100, ($biomass / 1100) * 100) : 0.0,
        ];
    }
}
