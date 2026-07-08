<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Cycle;
use App\Models\DailyLog;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\InventoryUsage;
use App\Models\Pond;
use App\Models\Sampling;
use App\Models\Stocking;
use App\Models\WaterQualityWeekly;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Dataset demo skala penuh (76 kolam) supaya dashboard "ruang kontrol" bisa
 * dievaluasi realistis. Jalankan manual (TIDAK dipanggil DatabaseSeeder):
 *
 *   php artisan db:seed --class=DemoFarmSeeder
 *
 * Deterministik: pakai LCG internal ber-seed konstan (bukan Faker, karena Faker
 * ada di require-dev dan tidak terpasang di production --no-dev). Angka acak
 * sama di tiap run; tanggal relatif ke hari ini supaya DOC/alert selalu hidup.
 *
 * Aman diulang: stocking buatan seeder ditandai asal_benur "Benur Demo" dan
 * dihapus dulu (cascade ke semua log) sebelum digenerate ulang. Kolam yang
 * sudah punya stocking NON-demo (input manual) tidak disentuh sama sekali.
 */
class DemoFarmSeeder extends Seeder
{
    private const MARKER_BENUR = 'Benur Demo';

    private int $lcg = 987654321;

    /** @var array<string, int> */
    private const BLOCKS = ['A' => 13, 'B' => 13, 'C' => 13, 'D' => 13, 'E' => 13, 'F' => 11];

    /** Kolam non-aktif (16) — sisanya (60) berstatus aktif. */
    private const NON_ACTIVE = [
        'A13' => 'panen', 'B12' => 'panen', 'B13' => 'panen', 'C12' => 'panen',
        'C13' => 'maintenance', 'D11' => 'maintenance',
        'D12' => 'siap_tebar', 'D13' => 'siap_tebar', 'E10' => 'siap_tebar', 'F8' => 'siap_tebar',
        'E11' => 'kosong', 'E12' => 'kosong', 'E13' => 'kosong', 'F9' => 'kosong', 'F10' => 'kosong', 'F11' => 'kosong',
    ];

    /** Kolam yang log HARI INI-nya melanggar ambang mutu air → tile "perhatian". */
    private const VIOLATION_PONDS = ['A4' => 'do', 'B7' => 'ph', 'C2' => 'suhu', 'E5' => 'salinitas'];

    /** Kolam dengan emergency ≤3 hari terakhir → tile "kritis". */
    private const EMERGENCY_PONDS = ['A7', 'C9'];

    /** Kolam yang sengaja jatuh tempo sampling hari ini (DOC dipaksa 44 & 58). */
    private const SAMPLING_DUE_PONDS = ['B5' => 44, 'D3' => 58];

    public function run(): void
    {
        // Basis (farm/roles/users/blok A-D+R+RW) — DatabaseSeeder idempotent.
        if (! Farm::where('nama', 'Tambak Malimping')->exists()) {
            $this->call(DatabaseSeeder::class);
        }

        $farm = Farm::where('nama', 'Tambak Malimping')->firstOrFail();
        $cycle = Cycle::firstOrCreate(['nama' => 'Siklus 1 2026']);
        $today = Carbon::today();

        DB::transaction(function () use ($farm, $cycle, $today) {
            // Bersihkan hasil run sebelumnya (hanya data bertanda demo; cascade ke log anak).
            Stocking::where('asal_benur', self::MARKER_BENUR)->delete();

            $activeIndex = 0;
            $ringkas = ['aktif' => 0, 'skip' => 0, 'sampling' => 0, 'dailyLog' => 0];

            foreach (self::BLOCKS as $namaBlok => $jumlahKolam) {
                $block = Block::firstOrCreate(['farm_id' => $farm->id, 'nama' => $namaBlok]);

                for ($n = 1; $n <= $jumlahKolam; $n++) {
                    $kode = $namaBlok.$n;
                    $status = self::NON_ACTIVE[$kode] ?? 'aktif';

                    $pond = Pond::firstOrCreate(
                        ['block_id' => $block->id, 'kode_kolam' => $kode],
                        ['luas' => 900 + $this->rand(0, 6) * 50, 'kapasitas' => 150000, 'status' => $status]
                    );

                    // Jangan sentuh kolam yang punya data manual (non-demo).
                    if ($pond->stockings()->where('asal_benur', '!=', self::MARKER_BENUR)->exists()) {
                        $ringkas['skip']++;

                        // Konsumsi jatah acak kolam aktif supaya determinisme kolam lain terjaga.
                        if ($status === 'aktif') {
                            $activeIndex++;
                        }

                        continue;
                    }

                    $pond->update(['status' => $status]);

                    if ($status === 'aktif') {
                        $this->seedActivePond($pond, $cycle, $today, $activeIndex, $ringkas);
                        $activeIndex++;
                    } elseif ($status === 'panen') {
                        $this->seedHarvestedPond($pond, $cycle, $today);
                    }
                }
            }

            $this->command?->info(sprintf(
                'DemoFarmSeeder: %d kolam, %d stocking demo, %d sampling, %d daily log.',
                Pond::whereHas('block', fn ($q) => $q->where('farm_id', $farm->id))->count(),
                Stocking::where('asal_benur', self::MARKER_BENUR)->count(),
                Sampling::whereIn('stocking_id', Stocking::where('asal_benur', self::MARKER_BENUR)->pluck('id'))->count(),
                DailyLog::whereIn('stocking_id', Stocking::where('asal_benur', self::MARKER_BENUR)->pluck('id'))->count(),
            ));
        });
    }

    // ─────────────────────────────────────────────────────────────────────
    private function seedActivePond(Pond $pond, Cycle $cycle, Carbon $today, int $i, array &$ringkas): void
    {
        $kode = $pond->kode_kolam;

        // DOC tersebar 5–95; dua kolam dipaksa jatuh tempo sampling hari ini.
        $doc = self::SAMPLING_DUE_PONDS[$kode] ?? (5 + intdiv(90 * $i, 59));

        $jumlahTebar = $this->rand(100, 150) * 1000;
        $tglPakan = $today->copy()->subDays($doc);

        $stocking = Stocking::create([
            'pond_id' => $pond->id,
            'cycle_id' => $cycle->id,
            'tgl_tebar' => $tglPakan->copy()->subDays(2)->toDateString(),
            'tgl_pakan_pertama' => $tglPakan->toDateString(),
            'asal_benur' => self::MARKER_BENUR,
            'jumlah_tebar' => $jumlahTebar,
            'harga_benur' => $jumlahTebar * 45, // TOTAL (bukan per ekor), ±Rp45/benur
        ]);

        // Kurva tumbuh per kolam. 8 kolam aktif terakhir (DOC 84–95) dipatok MBW ≥ 13
        // ("siap panen"); sisanya di-cap < 13 supaya jumlah siap-panen terkendali (8).
        $siapPanen = $i >= 52;
        if ($siapPanen) {
            $mbwTarget = 13.2 + ($i - 52) * 0.55;           // 13,2 – 17,05 gr
            $adg = $mbwTarget / max(1, $doc - 8);
            $cap = 18.0;
        } else {
            $adg = 0.15 + $this->rand(0, 80) / 1000;         // ADG 0,15–0,23 g/hari
            $cap = 11.0 + $this->rand(0, 14) / 10;           // mentok 11,0–12,4 gr
        }
        $sr100 = 75 + $this->rand(0, 15);                    // SR proyeksi 75–90% di DOC 100

        // ── Sampling: DOC 30 lalu tiap 7 hari (kolam sampling-due berhenti seminggu sebelum) ──
        $samplings = [];
        $maxDocSampling = array_key_exists($kode, self::SAMPLING_DUE_PONDS) ? $doc - 7 : $doc;
        for ($d = 30; $d <= $maxDocSampling; $d += 7) {
            $mbw = round($this->mbwAt($d, $adg, $cap) + $this->rand(-15, 15) / 100, 2);
            $mbw = max(0.5, $mbw);
            $tgl = $tglPakan->copy()->addDays($d);
            $samplings[] = [
                'stocking_id' => $stocking->id,
                'tgl' => $tgl->toDateString(),
                'doc' => $d,
                'berat_sampel_total' => round($mbw * 100, 2),
                'jumlah_sampel' => 100,
                'mbw' => $mbw,
                'populasi' => $this->populasiAt($d, $jumlahTebar, $sr100),
                'kondisi_organ' => null,
                'catatan' => null,
                'created_at' => $tgl->copy()->setTime(10, 0),
                'updated_at' => $tgl->copy()->setTime(10, 0),
            ];
        }
        if ($samplings !== []) {
            Sampling::insert($samplings);
            $ringkas['sampling'] += count($samplings);
        }

        // ── Daily log 30 hari terakhir (sejak pakan pertama bila DOC < 30) ──
        $logs = [];
        $mulai = max(0, $doc - 29);
        for ($d = $mulai; $d <= $doc; $d++) {
            $tgl = $tglPakan->copy()->addDays($d);
            $biomass = $this->mbwAt($d, $adg, $cap) * $this->populasiAt($d, $jumlahTebar, $sr100) / 1000;
            $totalPakan = $d < 25
                ? round(($jumlahTebar / 100000) * (3 + $d * 0.5), 1)   // blind feeding awal siklus
                : round(max(3, $biomass * 0.04), 1);                   // ±FR 4% dari biomass
            $porsi = round($totalPakan / 4, 1);

            $log = [
                'stocking_id' => $stocking->id,
                'tgl' => $tgl->toDateString(),
                'pakan_07_kg' => $porsi, 'pakan_11_kg' => $porsi, 'pakan_15_kg' => $porsi, 'pakan_19_kg' => $porsi,
                'kode_pakan' => $biomass < 200 ? '2M' : ($biomass < 900 ? '3M' : 'PL-40'),
                'ancho_07' => $this->rand(0, 9) < 7 ? 'habis' : 'sisa_sedikit',
                'ancho_11' => 'habis',
                'ancho_15' => $this->rand(0, 9) < 8 ? 'habis' : 'sisa_sedikit',
                'ancho_19' => 'habis',
                'do_pagi' => 4.6 + $this->rand(0, 16) / 10,
                'do_sore' => 5.0 + $this->rand(0, 15) / 10,
                'ph_pagi' => 7.7 + $this->rand(0, 6) / 10,
                'ph_sore' => 7.7 + $this->rand(0, 6) / 10,
                'suhu_pagi' => 28.6 + $this->rand(0, 26) / 10,
                'suhu_sore' => 28.8 + $this->rand(0, 26) / 10,
                'salinitas' => 26 + $this->rand(0, 30) / 10,
                'mortalitas' => $this->rand(3, 45),
                'catatan' => null,
                // Log hari ini pakai jam subuh supaya created_at tidak jatuh di masa depan.
                'created_at' => $tgl->copy()->setTime($d === $doc ? 6 : 16, $d === $doc ? 45 : 30),
                'updated_at' => $tgl->copy()->setTime($d === $doc ? 6 : 16, $d === $doc ? 45 : 30),
            ];

            // Log TERBARU (hari ini) kolam terpilih dibikin melanggar ambang PRD 5.3.
            if ($d === $doc && isset(self::VIOLATION_PONDS[$kode])) {
                match (self::VIOLATION_PONDS[$kode]) {
                    'do' => [$log['do_pagi'] = 3.2, $log['do_sore'] = 3.8],
                    'ph' => $log['ph_sore'] = 8.9,
                    'suhu' => $log['suhu_pagi'] = 33.5,
                    default => $log['salinitas'] = 22.0,
                };
            }

            $logs[] = $log;
        }
        DailyLog::insert($logs);
        $ringkas['dailyLog'] += count($logs);
        $ringkas['aktif']++;

        // ── Uji air mingguan (lab) tiap 7 hari sejak DOC 7 → grafik ammonia/vibrio & menu Uji Lab ──
        $weeklies = [];
        for ($d = 7; $d <= $doc; $d += 7) {
            $tgl = $tglPakan->copy()->addDays($d);
            $totalBakteri = 80 + $this->rand(0, 60);
            $weeklies[] = [
                'stocking_id' => $stocking->id,
                'tgl' => $tgl->toDateString(),
                'tan' => round(0.4 + $this->rand(0, 110) / 100, 2),          // aman < ambang 2
                'ammonia' => round((2 + $this->rand(0, 6)) / 100, 3),          // aman < ambang 0,1
                'nitrit' => round((2 + $this->rand(0, 6)) / 100, 3),
                'nitrat' => round(10 + $this->rand(0, 28) + $d / 8, 1),
                'tom' => round(3 + $this->rand(0, 50) / 10, 1),
                'alkalinitas' => 95 + $this->rand(0, 45),
                'fe' => round($this->rand(1, 8) / 10, 2),
                'vibrio_hijau' => $this->rand(1, 7),
                'vibrio_hitam' => $this->rand(0, 3),
                'vibrio_luminer' => 0,
                'total_bakteri' => $totalBakteri,
                'created_at' => $tgl->copy()->setTime(11, 0),
                'updated_at' => $tgl->copy()->setTime(11, 0),
            ];
        }
        if ($weeklies !== []) {
            WaterQualityWeekly::insert($weeklies);
        }

        // ── Emergency ≤3 hari terakhir buat 2 kolam → tile kritis ──
        if (in_array($kode, self::EMERGENCY_PONDS, true)) {
            $tglEm = $today->copy()->subDays($kode === self::EMERGENCY_PONDS[0] ? 1 : 2);
            $stocking->emergencyLogs()->create([
                'tgl' => $tglEm->toDateString(),
                'jenis' => $kode === self::EMERGENCY_PONDS[0]
                    ? 'Udang naik ke permukaan pagi hari, dicurigai DO drop'
                    : 'Kematian mendadak di anco, dicurigai white feces',
                'tindakan' => 'Kincir ditambah, siphon dasar, cek ulang kualitas air',
                'keputusan' => 'lanjut',
            ]);
        }

        // ── Pembelian pakan bulan berjalan → KPI "Pakan Bulan Ini (Rp)" ──
        $tglBeli = $today->copy()->startOfMonth()->addDays($this->rand(0, max(0, $today->day - 1)));
        $qty = $this->rand(500, 1500); // kg
        InventoryUsage::create([
            'stocking_id' => $stocking->id,
            'tgl' => $tglBeli->toDateString(),
            'kategori' => 'pakan',
            'item' => 'Pakan Grower '.($this->rand(0, 1) ? 'SGH' : 'Irawan 683'),
            'qty' => $qty,
            'satuan' => 'kg',
            'harga' => $qty * 16500,
        ]);
        if ($this->rand(0, 9) < 3) {
            InventoryUsage::create([
                'stocking_id' => $stocking->id,
                'tgl' => $tglBeli->toDateString(),
                'kategori' => 'probiotik',
                'item' => 'Probiotik Rhodo',
                'qty' => $this->rand(5, 20),
                'satuan' => 'liter',
                'harga' => $this->rand(300, 900) * 1000,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    private function seedHarvestedPond(Pond $pond, Cycle $cycle, Carbon $today): void
    {
        $selesai = 100 + $this->rand(5, 25);                 // panen total 105–125 hari lalu dimulai
        $tglPakan = $today->copy()->subDays($selesai + $this->rand(3, 20));
        $jumlahTebar = $this->rand(100, 150) * 1000;

        $stocking = Stocking::create([
            'pond_id' => $pond->id,
            'cycle_id' => $cycle->id,
            'tgl_tebar' => $tglPakan->copy()->subDays(2)->toDateString(),
            'tgl_pakan_pertama' => $tglPakan->toDateString(),
            'asal_benur' => self::MARKER_BENUR,
            'jumlah_tebar' => $jumlahTebar,
            'harga_benur' => $jumlahTebar * 45,
        ]);

        $tahapan = [
            ['tahap' => 'partial1', 'doc' => 75, 'berat' => $this->rand(300, 400), 'size' => 68, 'harga' => 45000],
            ['tahap' => 'partial2', 'doc' => 90, 'berat' => $this->rand(380, 480), 'size' => 50, 'harga' => 43000],
            ['tahap' => 'total', 'doc' => $selesai, 'berat' => $this->rand(400, 550), 'size' => 32, 'harga' => 42000],
        ];

        $rows = [];
        foreach ($tahapan as $t) {
            $tgl = $tglPakan->copy()->addDays($t['doc']);
            $rows[] = [
                'stocking_id' => $stocking->id,
                'tahap' => $t['tahap'],
                'tgl' => $tgl->toDateString(),
                'berat_kg' => $t['berat'],
                'size' => $t['size'],
                'harga_per_kg' => $t['harga'],
                'pendapatan' => $t['berat'] * $t['harga'], // snapshot, konsisten HarvestService
                'catatan' => null,
                // created_at ikut tanggal panen supaya Aktivitas Terbaru tidak didominasi waktu seeding
                'created_at' => $tgl->copy()->setTime(14, 0),
                'updated_at' => $tgl->copy()->setTime(14, 0),
            ];
        }
        Harvest::insert($rows);
    }

    // ─────────────────────────────────────────────────────────────────────
    /** Kurva MBW vaname sederhana: mulai efektif DOC 8, naik linear per ADG, mentok di cap. */
    private function mbwAt(int $doc, float $adg, float $cap): float
    {
        return max(0.3, min($cap, ($doc - 8) * $adg));
    }

    /** Populasi menurun linear menuju SR {$sr100}% di DOC 100. */
    private function populasiAt(int $doc, int $jumlahTebar, int $sr100): int
    {
        return (int) round($jumlahTebar * (1 - (1 - $sr100 / 100) * min(100, $doc) / 100));
    }

    /** LCG deterministik — pengganti Faker (tidak tersedia di install --no-dev). */
    private function rand(int $min, int $max): int
    {
        $this->lcg = ($this->lcg * 1103515245 + 12345) & 0x7FFFFFFF;

        return $min + ($this->lcg % ($max - $min + 1));
    }
}
