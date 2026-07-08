# Aquaclear — Sistem Digital Manajemen Tambak Udang (76 Kolam)

Klien: PT Aquaclear Teknologi Internasional — Tambak Malimping, Lebak, Banten.
PRD lengkap: `docs/PRD_Sistem_Tambak_Aquaclear_v1.docx` (acuan utama, baca ulang kalau ragu).
Timeline: 14 hari, solo dev. Nilai kontrak Rp 27.000.000, free maintenance 4 bulan.

> ⚠️ **STATUS SEMENTARA (2026-07-04, dikonfirmasi ulang 2026-07-05 saat deploy)**: `routes/web.php` sengaja dilonggarkan buat testing — role `operasional` ditambahkan ke akses tulis "Kualitas Air Mingguan" (asli: `lab` saja) dan "Sampling & Pertumbuhan" (asli: `analis` saja), supaya 1 akun bisa coba semua menu. Cari komentar `TEMP TESTING (2026-07-04)` di `routes/web.php` dan `tests/Feature/RoleAccessTest.php`. **Keputusan user saat deploy pertama ke Hostinger (2026-07-05): TETAP dibiarkan longgar di production**, bukan lupa — user masih mau lanjut demo/testing langsung di server sebelum client pakai serius. Kalau nanti mau diperketat: balikin ke role asli sesuai PRD Bagian 3 (`lab` saja / `analis` saja) sebelum client mulai pakai beneran.

Stack: **Laravel 12 + MySQL + Blade + TailwindCSS**. Deploy: git push ke GitHub → SSH pull di Hostinger.
Basis kode banyak reuse dari project POS SaaS sebelumnya (auth, multi-tenant hardening, role/permission, komponen UI Blade+Tailwind) — cek pola itu dulu sebelum bikin dari nol.

## Deploy Pertama ke Hostinger (2026-07-05)

Domain sementara: `midnightblue-pig-552346.hostingersite.com` (dibuat via hPanel → Tambah Website → **Custom website PHP/HTML** — BUKAN "Aplikasi Web Node.js", itu buat runtime Node bukan PHP). Struktur di server: repo di-clone ke `~/domains/<domain>/app`, lalu `public_html` di-symlink ke `app/public` (`ln -s app/public public_html`) — bukan clone langsung ke `public_html`.

Server Hostinger PHP-nya **8.3.30**, tapi laptop dev pakai **PHP 8.5.6** — ini bikin masalah nyata: `composer.lock` yang di-generate di laptop otomatis pilih versi terbaru package (termasuk beberapa `symfony/*` yang butuh PHP ≥8.4.1) karena Composer resolve berdasarkan PHP yang lagi jalan, bukan `"php": "^8.2"` yang dideclare di `composer.json`. Alhasil `composer install --no-dev` di server Hostinger gagal duluan pas awal deploy pertama.

**Fix**: tambahin `"config.platform.php"` di `composer.json` (di-set ke `8.3.0`, samain persis versi PHP Hostinger) supaya Composer SELALU resolve dependency yang kompatibel dengan 8.3, apapun versi PHP yang jalan pas `composer update` di laptop manapun. Juga ketauan `spatie/laravel-permission ^8.1` butuh PHP ^8.3 (bukan ^8.2 kayak yang dideclare project ini) — jadi `"php"` di `require` juga dinaikkan ke `^8.3` biar konsisten. Setelah fix, `composer update` regenerate lock dengan versi symfony yang di-downgrade ke seri 7.4.x (kompatibel 8.3), `spatie/laravel-permission` naik ke 8.3.0 — 82 test tetap lolos.

**Pelajaran buat sesi depan**: kalau pindah mesin dev lagi atau upgrade PHP lokal, cek `composer.json config.platform.php` masih cocok sama PHP di server production SEBELUM push — jangan asumsikan lock file dari laptop otomatis jalan di server, terutama kalau versi PHP lokal lebih baru dari server.

`public/build` (hasil `npm run build`) sengaja di-commit ke git (dihapus dari `.gitignore`) karena server Hostinger cuma perlu PHP+Composer, TIDAK perlu install Node.js — build asset dilakukan di laptop sebelum push, bukan di server.

### Bug MIME type `.js` (ditemukan & diperbaiki)

Server Apache/LiteSpeed Hostinger awalnya ngirim file `.js` dengan `Content-Type: application/x-javascript` (legacy), bukan `text/javascript` — browser modern nolak eksekusi `<script type="module">` kalau MIME type-nya bukan yang standar. Fix: tambah `AddType text/javascript .js` + `<FilesMatch>` `ForceType` di `public/.htaccess` (di luar `<IfModule mod_mime.c>` karena LiteSpeed kadang nggak ngenalin nama module Apache itu, jadi `IfModule` block-nya ke-skip diam-diam). Juga sempat kejebak cache CDN Hostinger (`hcdn`) yang nyimpen response lama di sebagian edge node — solusinya generate hash filename baru (`resources/js/app.js`/`resources/css/app.css` diubah dikit) biar dianggap file baru, bukan andalin manual purge cache.

### Bug dropdown Logout tidak bisa diklik — BELUM KETEMU AKAR MASALAHNYA, di-workaround (2026-07-05)

Setelah fix MIME type di atas, dropdown user menu (Alpine.js, `x-dropdown` component) di sidebar TETAP nggak bisa diklik — baik di production maupun **lokal** (jadi bukan soal Hostinger/CDN), di Chrome maupun browser lain, tanpa ada satupun console error. `window.Alpine` selalu `undefined` di browser user. Anehnya, test via Chrome headless (`google-chrome --headless=new --enable-logging=stderr`) di mesin yang SAMA nunjukkin Alpine.js berhasil load normal (`typeof window.Alpine === 'object'`) — kontradiksi yang nggak sempat terpecahkan (kemungkinan sesuatu spesifik di profil browser/sistem user, tapi belum ketemu penyebab pastinya karena user mau lanjut cepat).

**Keputusan**: daripada terus debug, `resources/views/layouts/navigation.blade.php` bagian user-menu diubah dari `<x-dropdown>` (butuh Alpine.js) jadi **link statis yang selalu kelihatan** (bukan disembunyikan di balik dropdown) — "Profil" pakai `<a>` biasa, "Keluar" pakai `<form method="POST">` + `<button type="submit">` TANPA `onclick`/JS sama sekali. Ini robust terhadap masalah JS apapun karena logout adalah aksi kritis yang nggak boleh bergantung ke Alpine/JS buat bisa diakses.

**Kalau nanti mau investigasi ulang akar masalah Alpine.js ini** (opsional, bukan blocker lagi): coba cek `chrome://policy`, extension yang terinstall, atau setting "Disable JavaScript" di DevTools Command Menu (`Cmd+Shift+P`) — itu yang belum sempat dicek user pas kejadian ini. Komponen `<x-dropdown>` (`resources/views/components/dropdown.blade.php`) masih ada filenya tapi SEKARANG SUDAH TIDAK DIPAKAI DI MANA PUN (dead code, sengaja dibiarkan file-nya kalau-kalau Alpine.js ternyata beres dan mau dipakai lagi nanti) — jangan pasang balik ke navigasi tanpa mastiin Alpine.js beneran jalan dulu.

## Migrasi PostgreSQL → MySQL (2026-07-05)

Project ini awalnya dibangun pakai PostgreSQL (lihat riwayat di bawah), tapi paket Hostinger yang sudah dibeli client (**Cloud Hosting "Startup"**) cuma nyediain database MySQL — dicek langsung lewat hPanel (menu Database → Pengelolaan cuma ada opsi "Buat Database MySQL", nggak ada PostgreSQL sama sekali) dan nggak ada akses root/sudo buat install PostgreSQL sendiri (bukan VPS). Keputusan user: migrasi ke MySQL daripada upgrade ke VPS.

Yang diubah:
- `.env` dan `phpunit.xml`: `DB_CONNECTION=mysql`, port `3306` (sebelumnya `pgsql` port `5432`).
- Migration `2026_07_02_080014_add_pakan_category_to_inventory_usage_table.php` — satu-satunya migration yang pakai raw SQL khusus Postgres (`ALTER TABLE ... DROP CONSTRAINT` buat nambah value enum `kategori`). Diganti ke sintaks MySQL (`ALTER TABLE ... MODIFY kategori ENUM(...)`). Migration lain semua pakai `$table->enum()` Laravel yang otomatis translate ke tipe native di kedua dialect, jadi aman tanpa perubahan.
- Semua `selectRaw()` di service (`CostService`, `FeedService`, `DashboardService`) sudah dicek portable (cuma `COALESCE`/`SUM`, bukan sintaks khusus Postgres) — aman tanpa perubahan.
- Setup lokal sekarang pakai **MySQL Community Server 9.7 dari installer resmi mysql.com** (`/usr/local/mysql`), BUKAN Homebrew — di Mac ini `brew install mysql` kejebak compile dari source (macOS 13 udah masuk Tier 3 Homebrew, nggak ada bottle precompiled), bisa 1-2 jam. Installer resmi kasih binary siap pakai.
- Kalau reinstall/pindah mesin lagi, cek `PATH` harus include `/usr/local/mysql/bin` (beda dari lokasi Homebrew `/opt/homebrew/opt/postgresql@16/bin` yang dipakai sebelumnya).

## Audit 2026-07-05 (Edit/Hapus/Rumus) — Temuan & Perbaikan

User minta audit fungsi edit/hapus/rumus penginputan sebelum deploy. Ditemukan 3 bug nyata, semua sudah diperbaiki + diverifikasi lewat test otomatis DAN manual curl ke app yang jalan beneran (bukan cuma test):

1. **Edit "Pakan & Kualitas Air Harian" crash (500)** — `UpdateDailyLogRequest::rules()` manggil `$this->route('daily_log')` (underscore), padahal parameter route-nya `dailyLog` (camelCase, sesuai `routes/web.php`). Salah nama → selalu `null` → fatal error tiap kali ada yang coba update data harian. Ini modul paling sering dipakai (4x/hari) dan nggak ketauan di test suite lama karena belum ada test yang cover update-nya. Fix: ganti ke `$this->route('dailyLog')`. Ditambah test regresi `tests/Feature/DailyLogUpdateTest.php`.
2. **Prep-log "Item Lainnya" hilang diam-diam pas edit** — di `prep-logs/_form.blade.php`, field custom "Item Lainnya" pas mode edit selalu tampil kosong (nggak pernah baca dari data tersimpan), padahal item custom itu ikut ke-merge ke kolom `checklist` (JSON array) saat create. Kalau user save form edit tanpa notice & ngetik ulang manual, item custom itu hilang permanen dari checklist. Fix: prefill `item_lainnya` dengan entry di `checklist` yang bukan bagian dari `PrepLogController::CHECKLIST_ITEMS` baku.
3. **Duplikat `kode_kolam` per block bikin crash 500, bukan pesan error rapi** — `StorePondRequest`/`UpdatePondRequest` nggak ada validasi unique buat `kode_kolam` per `block_id`, padahal ada unique constraint di DB. Insert/update duplikat jadi `QueryException` mentah (500) bukan pesan validasi. Fix: tambah `Rule::unique('ponds')->where('block_id', ...)` (dengan `->ignore()` di Update).

4. **Tampilan mortalitas ×2 cuma kelihatan di 1 tempat** — SUDAH DIPERBAIKI. Tabel `daily-logs/index` dan riwayat Hub Siklus (`stockings/show`) sebelumnya nampilin `mortalitas` mentah (observasi asli, belum ×2), padahal angka yang udah dikoreksi cuma kelihatan di panel info halaman edit satu row. Bukan salah hitung (SR%/Biomass tetap sengaja dari `populasi` hasil sampling manual, bukan akumulasi mortalitas harian — itu nggak berubah), tapi berisiko operasional/analis salah baca jumlah kematian asli. Sekarang kedua tabel nampilin angka terkoreksi (×2) sebagai angka utama, dengan observasi asli tetap kelihatan kecil di sebelahnya `(obs. X)` buat referensi.

**Role vs PRD Bagian 3 (poin 4 audit di bawah) — SENGAJA dibiarkan seperti sekarang** (keputusan user 2026-07-05, bukan lupa): akses role yang lebih longgar dari PRD asli, termasuk 2 baris `TEMP TESTING (2026-07-04)`, TETAP dipertahankan apa adanya sampai ada instruksi eksplisit buat diperketat lagi.

## Audit 2026-07-05 (5 Titik PRD) — Temuan & Perbaikan

User minta audit 5 titik spesifik vs PRD sebelum ada perbaikan apa pun. Hasilnya:

1. **Panen Partial 2 (akumulasi multi-tahap)** — TIDAK ADA BUG. `CostService::totalBiomassPanenKg()` pakai `SUM(berat_kg)` murni tanpa filter tahap, otomatis akumulasi semua tahap apapun urutannya. Diverifikasi test `HarvestAccumulationTest` (300→700→1150kg berurutan).
2. **Alert Kualitas Air** — BUG NYATA, SUDAH DIPERBAIKI. Sebelumnya cuma vibrio yang punya alert; DO/pH/suhu/salinitas/TAN/ammonia/nitrit/nitrat sama sekali nggak dicek. `WaterQualityService` sekarang punya method cek semua parameter (`isDoLow`, `isPhOut`, `isSuhuOut`, `isSalinitasOut`, `isTanHigh`, `isAmmoniaHigh`, `isNitritHigh`, `isNitratHigh`) + `dailyViolations()`/`weeklyViolations()` buat agregasi. Tabel `daily-logs/index` dan `water-quality-weekly/index` sekarang highlight merah nilai di luar ambang, dan Hub Siklus (`stockings/show`) punya banner alert agregat kalau data PALING BARU (bukan riwayat) ada yang melanggar.
3. **Rekomendasi Flush-out** — BUG NYATA, SUDAH DIPERBAIKI. `DocService::isEligibleForFlushOut()` (cuma cek DOC<30) sebelumnya dead code, nggak pernah dipanggil. Ditambah `GrowthService::hasSharpSrDrop()` (SR turun >10 poin persentase antar 2 sampling terakhir — **ambang ini asumsi saya, PRD nggak kasih angka pasti**) dan `DocService::shouldRecommendFlushOut()` yang gabungin DOC<30 DAN (SR turun tajam ATAU ada emergency_log dalam 7 hari terakhir). Muncul sebagai banner rekomendasi di Hub Siklus — keputusan akhir tetap manual lewat Emergency & Kesehatan, sistem cuma menyarankan.
4. **Akses Role vs PRD Bagian 3** — DILAPORKAN, BELUM DIUBAH (nunggu keputusan user). Lihat tabel lengkap di `docs/PANDUAN_TESTING.md` bagian 6.4. Sebagian besar adalah keputusan desain yang sudah didokumentasikan sebelumnya (bukan penyimpangan baru); 2 baris `TEMP TESTING (2026-07-04)` di `routes/web.php` sengaja longgar sementara.
5. **KPI Dashboard (Kolam Aktif, Rata-rata FCR, Estimasi Laba/Rugi)** — TIDAK ADA BUG. Diverifikasi test `DashboardServiceAuditTest`: Kolam Aktif otomatis turun begitu status jadi "panen"; Rata-rata FCR cuma dari kolam aktif dan exclude yang FCR-nya null (bukan dianggap 0); Estimasi Laba/Rugi muncul di Dashboard (agregat semua kolam aktif) DAN Biaya & Laporan (per-siklus) — dua-duanya benar, beda cakupan.

## Known Limitation — Isolasi Data Antar-Farm (Diputuskan, JANGAN Diubah Diam-diam)

Ditemukan saat security review (2026-07-04): controller (`Stocking`, `DailyLog`, `WaterQualityWeekly`, `Sampling`, `Harvest`, `InventoryUsage`, `EmergencyLog`, `Report`, `Cycle`) akses data lewat route-model-binding TANPA cek `farm_id` — cuma `PondController::index()` yang filter per farm. Artinya kalau ada 2 farm dalam 1 database, user farm A bisa akses data farm B dengan nebak ID di URL.

**Keputusan user (2026-07-04): TIDAK ditambal.** Model bisnisnya jual-putus per klien — tiap tambak yang beli dapat instance/database sendiri-sendiri, bukan 1 sistem dipakai bareng banyak tambak. Jadi gap ini nggak pernah jadi risiko nyata dalam skenario penjualan mereka.

**Kalau suatu saat rencana berubah** (mis. mau dijadikan produk SaaS multi-tenant beneran, 1 database dipakai banyak klien) — gap ini WAJIB ditambal dulu sebelum onboarding klien ke-2 manapun. Caranya: tambahkan scope check `farm_id` di tiap controller yang disebut di atas (pola: `abort_unless($model->pond->block->farm_id === auth()->user()->farm_id, 403)` atau lewat form request `authorize()`).

## Menjalankan Lokal

```
export PATH="/usr/local/mysql/bin:$PATH"   # kalau mysql/mysqld belum kebaca (installer resmi mysql.com, BUKAN Homebrew — lihat "Migrasi PostgreSQL → MySQL" di atas)
php artisan migrate:fresh --seed    # reset + seed farm/roles/users
npm run build                       # atau `npm run dev` untuk watch mode
php artisan serve                   # http://127.0.0.1:8000
```

MySQL server dijalankan lewat System Settings → MySQL (panel dari installer resmi) → Start MySQL Server, bukan `brew services`.

Login dev (password sama semua: `password`): `owner@aquaclear.test`, `analis@aquaclear.test`, `operasional@aquaclear.test`, `lab@aquaclear.test`.

Test suite (MySQL, bukan sqlite — biar sama dialect dengan production di Hostinger):
```
mysql -u root -p -e "CREATE DATABASE aquaclear_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"   # sekali saja
php artisan test
```

**Belum di-deploy ke Hostinger** — project baru diuji lokal per keputusan user (2026-07-02). GitHub repo user sudah ada tapi belum di-push. `.env` sengaja di-gitignore; `.env` production harus dibuat manual langsung di server (APP_ENV=production, APP_DEBUG=false, kredensial DB Hostinger, dst.), bukan lewat git.

## Aturan Non-Negosiasi (jangan diam-diam diubah)

1. **DOC dihitung dari `tgl_pakan_pertama`, BUKAN dari `tgl_tebar`.** Ini koreksi eksplisit dari Fardan (reviewer 13 Proses Budidaya). Semua field/query yang berbasis DOC (fase pakan, jadwal sampling DOC 30 + tiap 7 hari, ambang flush-out DOC < 30) WAJIB pakai anchor ini.
2. **~~Mortalitas harian dikali 2~~ — DICABUT RESMI 2026-07-08** atas instruksi Pak Jubir yang disampaikan client (fase 2). Aturan baru: kematian diinput & ditampilkan **apa adanya (ekor)**, konversi ke kg = **ekor × MBW sampling terakhir** (`GrowthService::mortalitasKg`, keputusan user: pakai MBW terkini yang simpel, bukan MBW per tanggal kematian). `correctedMortality()` sudah dihapus — JANGAN dipasang balik tanpa instruksi eksplisit baru.
3. **Semua rumus bisnis dihitung di service class backend, TIDAK BOLEH di Blade/view.** Satu sumber kebenaran untuk FCR, SR, biomass, MBW, ADG, size, HPP, dsb. View hanya menampilkan hasil dari service.
4. **Panen: Vaname saja**, 3 tahap (partial 1, partial 2, total). Windu sudah tidak dibudidayakan — jangan tambahkan opsi jenis udang lain tanpa diminta.
5. Item yang masih **"asumsi sementara"** (lihat daftar di bawah) harus diikuti dulu sebagai default, TAPI ditandai di kode (komentar singkat merujuk ke bagian ini) dan diberi cara mudah untuk diubah kalau nanti dikonfirmasi berbeda oleh Fardan/Jubir. Jangan pernah mengganti angka/aturan asumsi ini secara diam-diam berdasarkan "tebakan lebih masuk akal".
6. Kalau nemu kebutuhan/ambiguitas yang TIDAK dijelaskan PRD — tanya user dulu, jangan menebak sendiri.

## Data Model (12 tabel — lihat PRD Bagian 4)

Hierarki: `farms → blocks → ponds`. `cycles` × `ponds` disilangkan lewat **`stockings`** (pond_id + cycle_id) — ini entitas pusat karena satu kolam dipakai berkali-kali lintas siklus, jadi semua data operasional harus menempel ke `stocking_id`, bukan langsung ke `pond_id`.

- `farms` — id, nama, lokasi
- `blocks` — id, farm_id, nama (A, B, C, D, R, RW)
- `ponds` — id, block_id, kode_kolam, luas, kapasitas, status
- `cycles` — id, nama
- `stockings` — id, pond_id, cycle_id, tgl_tebar, **tgl_pakan_pertama (anchor DOC)**, asal_benur, jumlah_tebar, harga_benur
- `daily_logs` — id, stocking_id, tgl, pakan_kg (4x/hari), kode_pakan, hasil_ancho, do/ph/suhu/salinitas (pagi-sore)
- `water_quality_weekly` — id, stocking_id, tgl, TAN, ammonia, nitrit, nitrat, vibrio, total_bakteri
- `samplings` — id, stocking_id, tgl, doc, mbw, populasi, kondisi_organ
- `harvests` — id, stocking_id, tahap (partial1/partial2/total), tgl, berat_kg, size, harga_per_kg
- `inventory_usage` — id, stocking_id, tgl, kategori, item, qty, harga
- `prep_logs` — id, pond_id, cycle_id, jenis (tambak/air), tgl, checklist, biaya
- `emergency_logs` — id, stocking_id, tgl, jenis, tindakan, keputusan

## Status Kolam (`ponds.status`)

Tidak dirinci di PRD — diputuskan bersama user (2026-07-02), bukan "asumsi sementara" PRD, jadi ini final kecuali user ubah eksplisit. Enum: `kosong`, `siap_tebar`, `aktif`, `panen`, `maintenance`.

## Roles

Owner/Manajer (lihat dashboard/laporan/approval, tidak input harian), Analis (sampling, mortalitas), Petugas Lapangan/Operasional (pakan harian, kualitas air harian, ancho, obat), Lab (hasil uji air mingguan).

## Auth, Multi-Tenant & Role (dibangun Hari 1–2)

Direplikasi dari pola `pos-saas` (`/Users/bebylolita/pos-saas`), tapi **disederhanakan** — sudah dikonfirmasi user (2026-07-02) karena kontrak ini untuk SATU klien/farm, bukan produk SaaS multi-klien:

- **Auth**: Laravel Breeze (stack Blade, dengan dark mode). TIDAK ada sistem invite-link/self-register publik — user baru dibuat langsung oleh admin/seeder, bukan lewat form pendaftaran.
- **Role**: `spatie/laravel-permission`. 4 role: `owner`, `analis`, `operasional`, `lab` (seed di `DatabaseSeeder`). Middleware `role:owner,analis` (alias `RoleMiddleware` di `bootstrap/app.php`) — OR logic antar role, abort 403 kalau tidak match. TIDAK ada role `superadmin` (beda dari pos-saas) karena tidak ada kebutuhan switch-antar-tenant.
- **Multi-tenant**: tenant = `Farm` (analog `Shop` di pos-saas). `users.farm_id` → FK ke `farms.id`. Tabel `blocks` juga punya `farm_id` langsung. Tabel di bawahnya (ponds, cycles, stockings, daily_logs, dst.) TIDAK punya `farm_id` langsung — mengikuti hierarki `farms → blocks → ponds → stockings → (semua log)`. Karena kontrak ini single-farm, scoping ketat belum krusial, tapi kalau nanti ada farm ke-2: filter data di level `blocks` (`Block::where('farm_id', auth()->user()->farm_id)`), sisanya otomatis ikut lewat relasi `belongsTo`/`hasMany` — TIDAK perlu global scope Eloquent (pola pos-saas juga scoping manual di controller, bukan global scope).
- Seeder awal: 1 farm ("Tambak Malimping"), 4 user (satu per role), email `{role}@aquaclear.test`, password `password` — untuk development saja, ganti sebelum go-live.
- Database lokal: MySQL 9.7 via installer resmi mysql.com (`/usr/local/mysql`, BUKAN Homebrew — lihat "Migrasi PostgreSQL → MySQL"), db name `aquaclear`, user `root` dengan password (di-set manual saat install, tersimpan di `.env` lokal). Kalau mysql/artisan tidak ketemu binary, tambahkan `/usr/local/mysql/bin` ke PATH.

## Rumus Bisnis Inti (Bagian 5 — semua di service class)

- **DOC** = hari sejak `tgl_pakan_pertama`
- **FR%** = (pakan harian ÷ biomass) × 100
- **FCR** = akumulasi pakan (kg) ÷ biomass (kg)
- **MBW** = berat total sampel ÷ jumlah ekor sampel
- **ADG** = (MBW sekarang − MBW lalu) ÷ selisih hari
- **Size** = 1000 ÷ MBW (ekor/kg)
- **SR%** = (populasi saat ini ÷ jumlah tebar) × 100
- **Biomass** = (populasi × MBW) ÷ 1000
- **Kematian harian** = ekor apa adanya; kg = ekor × MBW terakhir (aturan ×2 dicabut 2026-07-08, lihat Aturan Non-Negosiasi #2)
- **HPP/kg** = total biaya operasional kolam ÷ total biomass panen (akumulasi semua tahap panen)
- **% biaya pakan** = (biaya pakan ÷ total biaya) × 100 (acuan historis 60–68%)
- **Laba/Rugi per siklus** = total pendapatan panen (semua tahap) − total biaya operasional

### Target Panen — MBW/Size per tahap (Bagian 5.4)

| Tahap | MBW | Size |
|---|---|---|
| Partial 1 | 13–15 gr | 65–70 ekor/kg |
| Partial 2 | 20 gr | 50 ekor/kg |
| Total/Habis | 30–40 gr | 25–35 ekor/kg |

Target hasil: 1.100–1.200 kg per kolam per siklus (asumsi: akumulasi SEMUA tahap panen, bukan hanya tahap total — lihat daftar asumsi di bawah).

### Standar Mutu Air (Bagian 5.3)

| Parameter | Standar | Frekuensi |
|---|---|---|
| DO (pagi/sore) | > 4 ppm | Harian |
| pH (pagi/sore) | 7,5–8,5 | Harian |
| Suhu (pagi/sore) | 28–32 °C | Harian |
| Salinitas | 25–30 ppt | Harian |
| TAN | < 2 ppm | Mingguan |
| Ammonia | < 0,1 ppm | Mingguan |
| Nitrit | < 0,1 ppm | Mingguan |
| Nitrat | < 50 ppm | Mingguan |
| TOM, alkalinitas, Fe | Uji lab, tanpa ambang pasti | 10 hari sekali |
| Vibrio & bakteri | Rasio V/B, warning jika > 10% (khusus vibrio hijau/hitam/luminer) | 7 hari sekali |

Sampling & pertumbuhan: jadwal otomatis mulai DOC 30, lalu tiap 7 hari.
Flush-out: dipicu bila DOC < 30 DAN kondisi kritis (SR turun tajam / serangan penyakit) → lalu restocking.

## Asumsi Sementara — WAJIB diikuti dulu, JANGAN diam-diam diubah (Bagian 8)

Kalau salah satu ini ternyata meleset dari kondisi lapangan real, tandai/flag ke user — jangan otomatis "perbaiki" sendiri:

1. **Ancho**: jeda cek ±2 jam (default tengah dari pilihan); aturan penyesuaian pakan: Habis → naik 10%, Sisa banyak → turun 10%, Sisa sedikit → tetap; porsi pakan di ancho = 2% dari total pakan harian; alert otomatis setelah 2x berturut-turut "sisa banyak". Ancho dinilai visual saja (Habis/Sisa sedikit/Sisa banyak) — timbang persen TIDAK dipakai.
2. **Interpretasi target 1.100–1.200 kg/kolam** = akumulasi total semua tahap panen (partial1+partial2+total) per kolam per siklus, bukan hanya tahap panen total.
3. **Kriteria "sampling khusus"** di luar jadwal rutin: dipicu manual oleh analis/petugas, belum ada aturan otomatis.
4. **Checklist standar mutu Persiapan Tambak**: dibangun sebagai checklist bebas (tanpa validasi ketat), boleh diperketat di Fase 2.
5. Daftar & harga acuan bahan probiotik/mineral/desinfektan/obat masih benar-benar terbuka — field harga dikosongkan dulu, diisi manual saat data operasional tersedia.

## Dashboard — 6 kartu KPI (Bagian 7)

1. Kolam Aktif (dari 76 kapasitas)
2. Rata-rata FCR seluruh kolam aktif
3. Kolam Emergency (status darurat)
4. Pakan Bulan Ini (kg & Rp)
5. Estimasi Biomass Siap Panen
6. Estimasi Laba/Rugi Siklus Berjalan

Ditambah panel "Aktivitas Terbaru" di bawah kartu (log input pakan/sampling/panen terbaru) — bukan kartu KPI tersendiri.
Estimasi panen & laba/rugi harus ditampilkan sebagai **"estimasi"**, bukan "prediksi akurat" — dipengaruhi harga jual aktual yang fluktuatif.

## Di Luar Scope (Bagian 10)

- Migrasi data historis dari Excel (60+ sheet) — dikerjakan terpisah, bukan bagian 14 hari inti.
- Prediksi panen berbasis AI/ML — hanya estimasi rumus deterministik.
- Integrasi pihak ketiga (payment gateway, notifikasi WhatsApp otomatis, dsb).
- Modul diagnosis penyakit terpisah dengan database penyakit — sudah digabung ringkas ke Emergency.
- Export laporan kompleks di luar laporan laba/rugi sederhana.

## Aturan Kerja dengan User

- File lengkap siap-replace, bukan potongan/diff — kecuali user eksplisit minta "cuma ubah bagian X".
- Kalau nemu sesuatu di luar yang dijelaskan PRD: tanya dulu, jangan menebak.
- Solo dev, deadline 14 hari — prioritas fitur yang jalan & rapi, bukan arsitektur paling elegan. Hindari over-engineering/abstraksi prematur.
- Kalau user bilang "lanjut" tanpa detail tambahan: lanjut ke tahap berikutnya di rencana build yang sudah disusun (lihat bawah) — jangan tanya ulang dari awal.

## Modul Input Inti (dibangun Hari 3–7)

Data Kolam → Siklus (Cycle) → Stocking (Penebaran Benur) → Pakan & Kualitas Air Harian → Kualitas Air Mingguan → Sampling & Pertumbuhan. Semua rumus dipanggil dari `app/Services/` (`DocService`, `GrowthService`, `FeedService`, `WaterQualityService`) — controller dan Blade view TIDAK BOLEH menghitung ulang rumus sendiri.

**Struktur navigasi**: `Pond` (Data Kolam) → `show` menampilkan riwayat `Stocking` per kolam + tombol "Mulai Siklus Baru". `Stocking::show` adalah **hub utama** satu siklus — nampilin DOC/MBW/SR%/Biomass/FCR terkini + link ke 3 modul harian/mingguan/sampling. Mulai stocking baru otomatis set `pond.status = 'aktif'`.

**Pakan + Kualitas Air Harian digabung jadi SATU form/tabel** (`daily_logs`, satu row per stocking+tanggal) — bukan 2 form terpisah, karena keduanya memang satu baris data di skema (field pakan 4x + ancho 4x + DO/pH/suhu pagi-sore + salinitas + mortalitas ada di tabel yang sama). Menu "Pakan & Kualitas Air Harian" merujuk ke resource yang sama.

**Role write-access per modul** (view/index selalu boleh semua role yang login; hanya create/edit yang dibatasi):
- Data Kolam, Siklus, Stocking (setup/mulai siklus): `owner`, `operasional` — dikonfirmasi user (2026-07-02), PRD tidak sebutkan eksplisit siapa yang pegang bagian ini.
- Pakan & Kualitas Air Harian: `operasional`, **dan** `analis` — PRD Bagian 3 menaruh "pencatatan mortalitas" di tangan Analis, padahal field `mortalitas` ada di baris yang sama dengan data harian Operasional (pakan/ancho/air). Supaya Analis bisa isi mortalitas tanpa form terpisah, kedua role diberi akses tulis ke resource yang sama. Ini interpretasi saya menjembatani konflik PRD, bukan instruksi eksplisit — tandai kalau ternyata harus dipisah.
- Kualitas Air Mingguan: `lab` saja (hasil uji lab mingguan, sesuai PRD Bagian 3).
- Sampling & Pertumbuhan: `analis` saja (sesuai PRD Bagian 3).
- Owner **tidak** diberi akses tulis ke input harian/mingguan/sampling — sesuai PRD ("Owner ... tidak input harian").

**Snapshot vs live-computed**: `samplings.doc` dan `samplings.mbw`, serta `harvests.pendapatan`, disimpan sebagai snapshot yang dihitung SEKALI oleh service saat record dibuat/diupdate (bukan dihitung ulang tiap kali ditampilkan) — supaya riwayat historis tidak berubah kalau formula/anchor berubah di kemudian hari. Biomass, FCR, FR%, SR% saat ini SELALU dihitung live dari data terbaru (tidak disimpan), karena nilainya memang harus reflect kondisi terkini.

**Perlindungan hapus data**: `Pond::destroy` dan `Cycle::destroy` menolak hapus kalau masih ada `Stocking` yang menempel (guard di controller, bukan cuma FK cascade) — supaya kesalahan klik tidak menghapus seluruh riwayat siklus/kolam secara diam-diam.

**Route ordering**: rute statis (`ponds/create`) HARUS didaftarkan sebelum rute dinamis (`ponds/{pond}`) di `routes/web.php` — kalau kebalik, Laravel mencocokkan `{pond}` duluan dan `create` dianggap sebagai ID kolam (pernah kejadian, sudah diperbaiki).

Sudah di-smoke-test end-to-end lewat curl (login tiap role → CRUD kolam/siklus/stocking/pakan/air/sampling → verifikasi DOC/MBW/SR%/biomass/FCR/ancho-alert tampil benar → verifikasi role lain diblokir 403).

## Panen, Biaya & Laporan, Dashboard (dibangun Hari 8–11)

**Keputusan finansial dikonfirmasi user (2026-07-02)** — jangan diubah tanpa konfirmasi ulang:
- `stockings.harga_benur` = **TOTAL** biaya benur untuk siklus itu (bukan harga per ekor). Dipakai apa adanya di `CostService`, tidak dikali `jumlah_tebar`.
- **Biaya pakan** dihitung dari **pembelian** (`inventory_usage` kategori `pakan`, field `harga` = total biaya baris itu), BUKAN dari kg pakan di `daily_logs` (yang cuma untuk FCR/biomass, tidak ada info harga). Kategori `pakan` ditambahkan ke kolom enum `inventory_usage.kategori` lewat migration `2026_07_02_080014` — sejak migrasi ke MySQL (lihat "Migrasi PostgreSQL → MySQL"), migration ini pakai `ALTER TABLE ... MODIFY ... ENUM(...)` (native MySQL enum, bukan check constraint lagi) — kalau perlu tambah kategori lagi nanti, ikuti pola migration ini.

**Modul baru**: Aplikasi Kimia & Biologi (`InventoryUsage`, role tulis: `operasional` — sesuai PRD Bagian 3 "obat-obatan"), Panen multi-tahap (`Harvest`, role tulis: `owner,operasional` — mengikuti pola setup/milestone yang sama dengan Pond/Cycle/Stocking, PRD tidak sebutkan eksplisit), Emergency & Kesehatan minimal (`EmergencyLog`, create-only/append-only log, tanpa edit/hapus — cocok untuk catatan insiden; role tulis: `owner,operasional,analis`), Biaya & Laporan (read-only report per stocking).

**Panen → status kolam otomatis**: mencatat harvest tahap `total` langsung set `pond.status = 'panen'`. Tahap `partial1`/`partial2` tidak mengubah status (kolam masih `aktif`).

**`CostService`** — semua rumus biaya/laba-rugi/HPP terpusat di sini: biaya per kategori (benur, pakan, probiotik, mineral, desinfektan, obat), total biaya, % biaya pakan, HPP/kg (total biaya ÷ total biomass panen akumulasi semua tahap), laba/rugi (estimasi), progres panen vs target 1.100–1.200 kg/kolam.

**Asumsi tambahan saya untuk 2 KPI dashboard** (PRD Bagian 7 tidak definisikan ambang persisnya — bukan bagian dari daftar asumsi resmi PRD Bagian 8, jadi flag ini kalau ternyata meleset dari ekspektasi klien):
- **Kolam Emergency** = jumlah kolam aktif yang punya `emergency_log` dalam 3 hari terakhir (`DashboardService::kolamEmergencyCount`).
- **Estimasi Biomass Siap Panen** = total biomass dari kolam aktif yang sampling terakhirnya MBW ≥ 13 gr (ambang Partial 1) — lihat `DashboardService::estimasiBiomassSiapPanen`.

**FCR menampilkan "—" (bukan "0.00")** kalau belum ada data pakan sama sekali di `daily_logs` — `FeedService::fcr()` return `null` bila `akumulasiPakanKg <= 0`, supaya "belum ada data" tidak disalahartikan sebagai "efisiensi pakan sempurna".

**Dashboard** (`DashboardController` + `DashboardService`) — 6 kartu KPI + panel Aktivitas Terbaru (gabungan `daily_logs`/`samplings`/`harvests` terbaru lintas kolam, diurutkan `created_at`), semua scoped ke `farm_id` user yang login.

Sudah di-smoke-test end-to-end: pembelian pakan+probiotik → sampling capai ambang partial1 → panen partial1 → panen total (verifikasi status kolam berubah ke "panen") → cek laporan Biaya (semua angka HPP/laba-rugi/biaya-per-kategori diverifikasi cocok hitungan manual) → cek dashboard (KPI kolam-aktif, emergency, estimasi-biomass semua benar) → cek role lain diblokir 403 dengan benar.

## Persiapan Tambak & Air + Manajemen Dasar Tambak (dibangun 2026-07-04, susulan)

Dua modul "Pencatatan Dasar" (PRD Bagian 6) sempat kelewat pas rencana 14 hari awal — ditambahkan belakangan karena WAJIB ada buat testing dengan data analyst klien.

- **Persiapan Tambak & Air** (`PrepLogController`, tabel `prep_logs` yang sudah ada dari Hari 1–2) — di-attach ke `Pond` (+ opsional `Cycle`), BUKAN ke `Stocking`, karena terjadi SEBELUM tebar. Checklist per `jenis` (`tambak`: Pembersihan kolam/Sterilisasi/Penambalan bocor; `air`: Isi air/Sterilisasi air/Cek awal kualitas air) + field "Item Lainnya" bebas — checklist bebas tanpa validasi ketat sesuai PRD Bagian 6.1. Bisa dibuat lalu di-edit lagi (progress tracker, bukan cuma log sekali catat). Role tulis: `owner,operasional` (pola sama dengan Pond/Cycle/Stocking — PRD tidak sebutkan eksplisit).
- **Manajemen Dasar Tambak** (`PondMaintenanceLogController`, tabel baru `pond_maintenance_logs`) — di-attach ke `Stocking` (BUKAN ke Pond langsung), karena terjadi SELAMA siklus aktif berjalan, mirip `daily_logs`. Field: `siphon` (boolean), `kondisi_lumpur` (kualitatif bebas), `jumlah_kincir`, `jam_nyala_kincir`. Satu row per stocking+tanggal (unique constraint, sama pola dengan `daily_logs`). Role tulis: `operasional,analis` (pola sama dengan Pakan Harian).
- Catatan skema: PRD Bagian 4 tidak punya tabel khusus buat "Manajemen Dasar Tambak" dalam daftar 12 tabel intinya — saya tambah 1 tabel baru (`pond_maintenance_logs`) karena field ini (siphon/lumpur/kincir) nggak cocok ditumpuk ke `daily_logs` (beda concern) maupun `prep_logs` (beda siklus hidup: prep = sebelum tebar, maintenance = selama aktif). Ini keputusan desain saya sendiri, bukan dari PRD eksplisit.
- Link modul: Persiapan Tambak & Air dari halaman `Pond::show`, Manajemen Dasar Tambak dari hub `Stocking::show` (daftar modul).
- Sudah ditest: role owner/operasional bisa catat prep-logs, role lab diblokir 403; role operasional bisa catat maintenance-logs, role lab diblokir 403; validasi tanggal unik per stocking di maintenance-logs.

## Desain Visual — Revisi (2026-07-04)

Desain awal (gradient teal/cyan + logo custom + shadow glow) dianggap kelihatan "cyberpunk"/terlalu genit dan "AI banget" oleh user. **Direvisi jadi lebih plain**:
- **Logo dihapus** dari semua tempat (sidebar, topbar mobile, halaman login) — cuma teks "Aquaclear" polos. Component `x-application-logo` masih ada filenya (tidak dipakai di UI manapun sekarang) — jangan pasang lagi tanpa diminta.
- **Halaman login** disederhanakan total: dari split-panel gradient jadi kartu putih polos di tengah, tanpa gradient/radial-glow/wave-svg.
- **Shadow "glow" berwarna dihapus** dari semua tombol primer (`shadow-teal-600/20` dkk) — tombol solid flat, tanpa efek neon.

### AKAR MASALAH SEBENARNYA — ditemukan setelah revisi di atas masih dikira "cyberpunk"

User screenshot browser asli mereka menunjukkan seluruh app masih gelap-navy meski sudah direvisi. **Penyebabnya bukan desainnya, tapi konfigurasi dark mode**: Tailwind default (`darkMode: 'media'`) otomatis ikut `prefers-color-scheme` OS/browser user — karena OS/browser user di-set dark mode, semua class `dark:bg-slate-800/900/950` dkk otomatis aktif tanpa toggle apapun di app ini.

**Fix**: `tailwind.config.js` diset `darkMode: 'selector'` — varian `dark:` cuma aktif kalau ada class literal `.dark` di parent element, dan app ini SENGAJA TIDAK PERNAH menaruh class `.dark` di manapun (tidak ada toggle dark mode). Efeknya: app SELALU render terang, apapun setting OS/browser user. **Kalau nanti mau nambah toggle dark mode beneran, di situ baru perlu logic buat add/remove class `.dark` di `<html>` atau `<body>` — jangan hapus baris `darkMode: 'selector'` ini tanpa itu, karena begitu dihapus perilaku lama (ikut OS) akan balik lagi.**

Setelah fix ini, sedikit sentuhan warna ditambahkan biar nggak plain-plain amat (sesuai permintaan "cerah dan menarik, palette bagus"): background halaman login jadi `bg-teal-50/60` (tint lembut, bukan abu-abu polos), wordmark "Aquaclear" di sidebar/topbar/login pakai warna `text-teal-700` (bukan abu-abu gelap). ~~Palet inti tetap teal (brand/primary) + slate (netral) + amber/emerald/rose/sky~~ — **sudah digantikan total oleh design system "Air Payau" di bawah (2026-07-08)**.

**Pelajaran buat sesi depan**: kalau user komplain soal tampilan lagi, verifikasi dulu apakah itu genuinely soal desain, atau soal dark-mode/rendering environment — screenshot dari browser asli user (bukan cuma Chrome headless yang bisa salah nebak color-scheme) adalah cara paling reliable buat mastiin.

## Design System "Air Payau" (redesign 2026-07-08, branch redesign-ui)

Redesign UI atas permintaan eksplisit user (prompt "Rombak Desain"). Semua token di `tailwind.config.js`:

- **Warna**: `ink` #17211D (teks), `teal-deep` #143C36 (sidebar/brand), `teal-mid` #2B6357 (aksi/link), `paper` #FAF7F0 (background halaman & input), `sand` #E4D9BE (tint kartu, `bg-sand/40`), `lumpur` #7C6B4F (border, `border-lumpur/20`), `sehat` #3F8A5E, `perhatian` #C98A2E, `kritis` #B23B3B (status semantik).
- **Font** (Bunny Fonts): `font-display` = Space Grotesk (judul/heading), `font-sans` = Public Sans (body), `font-mono` = JetBrains Mono (SEMUA angka metrik/tanggal/kode — identitas visual "instrumen lapangan").
- **Aturan**: JANGAN pakai `slate-*`/`teal-600`/`emerald-*`/`rose-*`/`amber-*`/`sky-*` generik lagi, dan JANGAN tulis `dark:*` (dead code — darkMode 'selector' tanpa toggle; seluruh 636 kemunculan lama sudah dihapus 2026-07-08). Kedalaman pakai border 1px + shift background, bukan shadow. `x-badge` menerima tone lama (emerald/amber/rose/sky/slate) sebagai alias ke tone semantik.
- **Dashboard** = "ruang kontrol": hero biomass + sparkline SVG murni (TANPA JS/Alpine — lihat bug Alpine di atas), Peta Kolam (grid tile per blok, status kritis>perhatian>siap-panen>sehat>idle), Perlu Perhatian, Menuju Panen. Data dari `DashboardService::controlRoomData()` yang WAJIB tetap batch (~13 query, ada test guard `<=15` di `DashboardControlRoomTest`) — jangan tambah query per-kolam.

## Fase 2 — Permintaan Client via Pak Jubir (2026-07-08, branch redesign-ui)

Semua item di bawah adalah TAMBAHAN DI LUAR PRD (fase 2 / change request), diminta client setelah lihat sistem jalan, dikonfirmasi user sebelum dikerjakan:

1. **Aturan mortalitas ×2 DICABUT** (lihat Aturan Non-Negosiasi #2) — `GrowthService::mortalitasKg(ekor, mbw)` gantinya.
2. **KPI akumulasi siklus** di dashboard: akumulasi pakan (kg), kematian (ekor), kematian (kg = ekor × MBW sampling terakhir, opsi simpel pilihan user).
3. **Filter dashboard per kolam & per "batch"** — keputusan client: batch = entity `Cycle` yang sudah ada. Saat difilter: volume (biomass/pakan/kematian) dijumlah, rasio (FCR/SR%/DOC) dirata-rata. Param GET `?kolam=&siklus=`.
4. **Grafik** — komponen `x-line-chart` (SVG murni server-side, TANPA JS/Alpine, garis ambang putus-putus merah): ammonia + rasio vibrio di dashboard (rata-rata mingguan lintas kolam terfilter); MBW/ADG/SR per sampling di Hub Siklus DAN di dashboard saat difilter 1 kolam.
5. **Menu "Uji Lab"** (`/uji-lab`, read-only semua role): 8 grafik parameter air mingguan per kolam (TAN, ammonia, nitrit, nitrat, rasio V/B, kepadatan vibrio hijau, TOM, alkalinitas), default kolam dengan data terbanyak.
6. **Modul Gudang bersaldo** (`/gudang`, tulis: `operasional`): tabel `warehouse_items` (master barang, kategori sama dgn inventory_usage) + `warehouse_entries` (barang MASUK). Barang KELUAR otomatis dari `inventory_usage.warehouse_item_id` (kolom baru, nullable) — keputusan user: "nyambung", bukan dobel input. Kalau pemakaian ditautkan ke gudang: nama item/kategori/satuan dipaksa ikut master (`InventoryUsageController::withWarehouseDefaults`), field item jadi `required_without:warehouse_item_id`. Saldo bisa MINUS (pemakaian > stok tercatat) — sengaja ditampilkan merah, bukan diblokir, karena stok awal mungkin belum diinput. Rumus saldo di `WarehouseService`.
7. **Warna primer digeser ke BIRU LAUT** (permintaan client "biru", user pilih biru laut bukan biru langit): `teal-deep` → #12303F, `teal-mid` → #2D6480, `ink` → #161D23. NAMA token `teal-*` sengaja TIDAK di-rename (60+ view memakainya) — anggap "teal" = slot warna primer.

DemoFarmSeeder ikut diperluas: data uji air mingguan tiap 7 hari per kolam aktif (nilai aman di bawah ambang biar cerita alert tetap dari 4 kolam pelanggaran harian yang disengaja).

Guard query dashboard di `DashboardControlRoomTest` jadi `<=17` (tambah query dropdown siklus & deret mingguan — tetap O(1) berapa pun jumlah kolam, JANGAN tambah query per-kolam).

## Rencana Build 14 Hari

- **Hari 1–2**: skema migration + model + relasi (12 tabel) + auth/multi-tenant/role (reuse pola POS SaaS: Owner, Analis, Operasional, Lab).
- **Hari 3–7**: modul input inti — Data Kolam → Siklus/Stocking → Pakan → Kualitas Air → Sampling & Pertumbuhan.
- **Hari 8–11**: Panen multi-tahap, Biaya & Laporan (HPP, laba/rugi), Dashboard & 6 KPI + Aktivitas Terbaru.
- **Hari 12–14**: testing, polish, deploy (git push → SSH pull Hostinger).
