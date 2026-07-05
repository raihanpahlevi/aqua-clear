<?php

namespace App\Services;

/**
 * PRD Bagian 5.4 — Panen multi-tahap (Vaname saja).
 */
class HarvestService
{
    public const TAHAP_REFERENSI = [
        'partial1' => ['label' => 'Partial 1', 'mbw' => '13–15 gr', 'size' => '65–70 ekor/kg'],
        'partial2' => ['label' => 'Partial 2', 'mbw' => '20 gr', 'size' => '50 ekor/kg'],
        'total' => ['label' => 'Total / Habis', 'mbw' => '30–40 gr', 'size' => '25–35 ekor/kg'],
    ];

    public function pendapatan(float $beratKg, float $hargaPerKg): float
    {
        return $beratKg * $hargaPerKg;
    }
}
