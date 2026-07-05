<?php

namespace App\Services;

use App\Models\Stocking;

/**
 * Rumus pertumbuhan & populasi — PRD Bagian 5.2.
 */
class GrowthService
{
    public function mbw(float $beratSampelTotalGram, int $jumlahSampelEkor): float
    {
        return $jumlahSampelEkor > 0 ? $beratSampelTotalGram / $jumlahSampelEkor : 0.0;
    }

    public function adg(float $mbwSekarang, float $mbwLalu, int $selisihHari): float
    {
        return $selisihHari > 0 ? ($mbwSekarang - $mbwLalu) / $selisihHari : 0.0;
    }

    public function size(float $mbwGram): float
    {
        return $mbwGram > 0 ? 1000 / $mbwGram : 0.0;
    }

    public function survivalRate(int $populasiSaatIni, int $jumlahTebar): float
    {
        return $jumlahTebar > 0 ? ($populasiSaatIni / $jumlahTebar) * 100 : 0.0;
    }

    public function biomassKg(int $populasi, float $mbwGram): float
    {
        return ($populasi * $mbwGram) / 1000;
    }

    /**
     * Mortalitas harian dikali 2 — kanibalisme udang, lihat CLAUDE.md.
     */
    public function correctedMortality(int $mortalitasObservasi): int
    {
        return $mortalitasObservasi * 2;
    }

    public function latestSampling(Stocking $stocking)
    {
        return $stocking->samplings()->latest('tgl')->first();
    }

    public function latestBiomassKg(Stocking $stocking): ?float
    {
        $latest = $this->latestSampling($stocking);

        if (! $latest) {
            return null;
        }

        return $this->biomassKg($latest->populasi, (float) $latest->mbw);
    }

    /**
     * "SR turun tajam" — salah satu syarat rekomendasi flush-out di PRD 5.6, ambang persisnya
     * tidak didefinisikan PRD. Asumsi tambahan saya: turun >10 poin persentase antar 2 sampling
     * terakhir dianggap "tajam" — lihat CLAUDE.md.
     */
    public function hasSharpSrDrop(Stocking $stocking, float $ambangPoin = 10.0): bool
    {
        $duaTerakhir = $stocking->samplings()->latest('tgl')->limit(2)->get();

        if ($duaTerakhir->count() < 2) {
            return false;
        }

        [$terbaru, $sebelumnya] = $duaTerakhir;

        $srTerbaru = $this->survivalRate($terbaru->populasi, $stocking->jumlah_tebar);
        $srSebelumnya = $this->survivalRate($sebelumnya->populasi, $stocking->jumlah_tebar);

        return ($srSebelumnya - $srTerbaru) > $ambangPoin;
    }
}
