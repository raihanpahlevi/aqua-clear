<?php

namespace App\Services;

use App\Models\Sampling;
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
     * Konversi kematian ekor → kg pakai MBW (gr). Aturan lama "×2 kanibalisme"
     * DIHAPUS atas instruksi Pak Jubir via client (2026-07-08) — kematian dicatat
     * apa adanya, kg = ekor × MBW sampling terakhir. Lihat CLAUDE.md.
     */
    public function mortalitasKg(int $ekor, ?float $mbwGram): ?float
    {
        if ($mbwGram === null || $mbwGram <= 0) {
            return null;
        }

        return $ekor * $mbwGram / 1000;
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

        return $this->isSharpSrDrop($duaTerakhir->get(0), $duaTerakhir->get(1), $stocking->jumlah_tebar, $ambangPoin);
    }

    /**
     * Versi murni (tanpa query) dari hasSharpSrDrop — buat pemanggil yang sudah
     * preload sampling-nya (mis. dashboard) supaya tidak query per stocking.
     */
    public function isSharpSrDrop(?Sampling $terbaru, ?Sampling $sebelumnya, int $jumlahTebar, float $ambangPoin = 10.0): bool
    {
        if (! $terbaru || ! $sebelumnya) {
            return false;
        }

        $srTerbaru = $this->survivalRate($terbaru->populasi, $jumlahTebar);
        $srSebelumnya = $this->survivalRate($sebelumnya->populasi, $jumlahTebar);

        return ($srSebelumnya - $srTerbaru) > $ambangPoin;
    }
}
