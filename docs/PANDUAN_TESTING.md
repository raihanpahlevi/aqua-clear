# Panduan Testing — Sistem Manajemen Tambak Aquaclear

Panduan ini buat kamu coba sistem sendiri sebelum dipakai beneran. Diurutkan sesuai alur kerja asli di lapangan, dan di TIAP modul ada bagian **"🔗 Efek Domino"** — biar kamu ngerti kenapa suatu angka di modul lain berubah setelah kamu input sesuatu di sini. Jangan kaget kalau habis input di satu tempat, angka di tempat lain ikut berubah — itu emang disengaja, bukan bug.

Kalau butuh penjelasan detail tiap field satu-satu (buat apa, arti field-nya), lihat `PANDUAN_PENGGUNAAN.md`. Dokumen ini fokus ke urutan testing + efek dominonya.

## 1. Jalankan Sistem

Kalau server belum jalan:

```
cd /Users/bebylolita/aqua-clear
brew services start postgresql@16
export PATH="/opt/homebrew/opt/postgresql@16/bin:$PATH"
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser. **Pastikan cuma 1 tab yang buka aplikasi ini**, dan selalu pakai alamat ini persis (bukan port lain) — biar nggak ada masalah sesi nyangkut ke server versi lama.

## 2. Akun Login per Role

Password sama semua: **`password`**

| Role | Email | Bisa input |
|---|---|---|
| Owner | `owner@aquaclear.test` | Data Kolam, Siklus, Stocking, Panen, Emergency |
| Operasional | `operasional@aquaclear.test` | Data Kolam, Siklus, Stocking, Pakan & Air Harian, Manajemen Dasar Tambak, Aplikasi Kimia & Biologi, Panen, Emergency |
| Analis | `analis@aquaclear.test` | Sampling, Pakan & Air Harian, Manajemen Dasar Tambak, Emergency |
| Lab | `lab@aquaclear.test` | Kualitas Air Mingguan |

## 3. Peta Efek Domino (Ringkasan Cepat)

Sebelum masuk detail per-modul, ini gambaran besar data siapa mempengaruhi apa:

```
Tanggal Pakan Pertama (di Mulai Stocking)
   → jadi ANCHOR buat DOC di semua tempat (Hub Siklus, Sampling, jadwal sampling)

Sampling (Populasi + MBW)
   → SR%, Biomass, ADG, Size — dan Biomass ini dipakai hitung FCR/FR% di Pakan Harian & Dashboard
   → "Est. Biomass Siap Panen" di Dashboard (kalau MBW sampling terakhir >= 13gr)

Pakan Harian (kg pakan 4 sesi)
   → akumulasi jadi FCR (butuh Biomass dari Sampling terbaru, kalau belum ada sampling FCR tampil "—")
   → kg-nya masuk ke KPI Dashboard "Pakan Bulan Ini" (bagian kg SAJA)
   → 2x ancho "Sisa Banyak" berturut-turut → alert merah di Hub Siklus

Aplikasi Kimia & Biologi (kategori "pakan", "probiotik", dst + Biaya)
   → masuk ke Biaya & Laporan (per kategori)
   → kategori "pakan" + biayanya → masuk ke KPI Dashboard "Pakan Bulan Ini" (bagian Rp SAJA — beda sumber dari kg di atas!)

Harga Benur (di Mulai Stocking)
   → masuk ke Total Biaya di Biaya & Laporan

Panen (semua tahap)
   → jumlah berat semua tahap → jadi pembagi di rumus HPP, dan progress bar target 1.100-1.200kg
   → jumlah pendapatan semua tahap → Total Pendapatan di Biaya & Laporan
   → tahap "Total/Habis" → status kolam otomatis jadi "panen" (di Data Kolam)

Emergency & Kesehatan
   → kejadian dalam 3 hari terakhir DAN kolam masih berstatus "aktif" → masuk KPI Dashboard "Kolam Emergency"
   → begitu kolam berstatus "panen", kejadian emergency-nya nggak lagi dihitung di KPI itu meski baru dicatat
```

---

## 4. Alur Uji Bertahap

### A. Setup Data Kolam — login `owner`/`operasional`

1. Menu **Data Kolam** → **Tambah Banyak Sekaligus** → isi Blok A, prefix `A`, mulai 1, jumlah 10.
2. Cek 10 kolam (A1–A10) muncul, semua status **"kosong"**.
3. Klik salah satu kolam → cek ada 2 aksi: **"Detail →"** (lihat riwayat siklus) dan ikon pensil (edit data kolam).

**🔗 Efek Domino**: belum ada efek ke modul lain, murni master data.

### B. Persiapan Tambak & Air — login `owner`/`operasional`

1. Dari detail kolam → link **"Persiapan Tambak & Air"** → **Catat Progres**.
2. Isi jenis "Persiapan Tambak", centang 2 dari 3 checklist, isi biaya 500rb.
3. Cek di tabel: checklist yang muncul cuma yang dicentang, biaya tampil format Rupiah.

**🔗 Efek Domino**: TIDAK ada efek ke modul lain — checklist ini berdiri sendiri, murni pencatatan progres (biaya di sini TIDAK masuk ke Biaya & Laporan, karena itu laporan per-siklus sedangkan persiapan ini terjadi sebelum siklus resmi mulai).

### C. Buat Siklus — login `owner`/`operasional`

1. Menu **Siklus** → isi nama "Siklus 1 2026" → Tambah.

**🔗 Efek Domino**: siklus ini akan muncul sebagai pilihan dropdown pas **Mulai Stocking** di kolam manapun. Satu nama siklus bisa dipakai di banyak kolam sekaligus.

### D. Mulai Stocking (Tebar Benur) — login `owner`/`operasional`

1. Dari detail kolam → **Mulai Siklus Baru**.
2. Isi siklus, **Tanggal Tebar = 1 Mei**, **Tanggal Pakan Pertama = 3 Mei** (SENGAJA beda, buat lihat efeknya), asal benur, jumlah tebar 60.000, harga benur 3.000.000 (ini TOTAL, bukan per ekor).
3. Simpan → otomatis masuk ke **Hub Siklus**.

**🔗 Efek Domino — cek semua ini**:
- Status kolam di **Data Kolam** sekarang berubah otomatis jadi **"aktif"** (buka tab lain, cek).
- Kartu **DOC** di Hub Siklus dihitung dari 3 Mei (tanggal pakan pertama), BUKAN dari 1 Mei (tanggal tebar) — kalau hari ini misalnya 2 Juni, DOC harusnya 30, bukan 32.
- Harga benur 3.000.000 akan muncul nanti di **Biaya & Laporan** kategori "benur".
- Kalau kamu KOSONGKAN tanggal pakan pertama: kartu DOC bakal tampil "—", dan begitu nanti coba input **Sampling**, sistem NOLAK dengan pesan suruh lengkapi tanggal pakan pertama dulu.

### E. Pakan & Kualitas Air Harian — login `operasional` (atau `analis`)

1. Dari Hub Siklus → **Input Hari Ini**.
2. Isi pakan cuma di jam 15 dan 19 (boleh sebagian), pilih ancho **kedua jam itu "Sisa Banyak"**, isi mortalitas 10, isi DO/pH/suhu/salinitas.

**🔗 Efek Domino — cek semua ini**:
- Balik ke **Hub Siklus** → harusnya muncul **banner alert merah** ("2x berturut-turut sisa banyak") karena kamu isi ancho 2 sesi jadi "Sisa Banyak".
- Kartu **FCR** di Hub Siklus: kalau BELUM ada data Sampling sama sekali, FCR bakal tampil **"—"** (bukan 0.00) — ini normal, FCR baru bisa dihitung setelah ada Biomass dari Sampling.
- Mortalitas yang kamu isi (10) TIDAK langsung ngurangin kartu "SR%" atau "Biomass" — dua kartu itu cuma berubah kalau kamu input **Sampling** baru dengan angka populasi yang sudah disesuaikan manual. Mortalitas di sini cuma buat pencatatan/laporan (~~dikali 2~~ (DICABUT 2026-07-08: tampil apa adanya, kg = ekor × MBW) di belakang layar = 20).
- Kg pakan yang kamu isi di sini bakal nambah ke KPI Dashboard **"Pakan Bulan Ini"** bagian **kg** — TAPI bagian **Rp**-nya TETAP 0 sampai kamu isi pembelian pakan di modul **Aplikasi Kimia & Biologi** (dua sumber data beda, lihat langkah I).
- Coba login sebagai `lab` → buka halaman ini → harusnya diblokir (403) kalau coba akses form input.

### F. Manajemen Dasar Tambak — login `operasional`/`analis`

1. Dari Hub Siklus → **Catat Hari Ini** → isi siphon "Ya", kondisi lumpur "baik", kincir 2 buah 18 jam.
2. Coba input tanggal yang SAMA sekali lagi → harusnya ditolak (1 tanggal cuma boleh 1 entri per siklus).

**🔗 Efek Domino**: TIDAK ada — modul ini berdiri sendiri, murni pencatatan, tidak masuk kalkulasi FCR/biaya/dashboard manapun.

### G. Kualitas Air Mingguan — login `lab`

1. Dari Hub Siklus → **Input Uji Air** → isi vibrio_hijau=15, total_bakteri=100 (rasio 15%, di atas ambang 10%).

**🔗 Efek Domino**:
- Baris itu di tabel Kualitas Air Mingguan bakal tampil **rasio V/B dengan warna merah + tanda warning** karena >10%.
- TIDAK mempengaruhi kartu KPI manapun di Hub Siklus atau Dashboard — warning ini cuma tampil lokal di tabelnya sendiri.

### H. Sampling & Pertumbuhan — login `analis`

**Ini modul dengan efek domino PALING BANYAK — perhatikan baik-baik.**

1. Coba dulu input tanggal sampling **SEBELUM** 3 Mei (tanggal pakan pertama) → harus DITOLAK.
2. Input sampling valid: tanggal 2 Juni, berat sampel total 1500 gram, jumlah sampel 100 ekor, populasi 55.000.
3. Simpan, lalu tambah SATU LAGI di tanggal 9 Juni dengan berat sampel 1800 gram (MBW lebih besar).

**🔗 Efek Domino — cek semua ini**:
- **MBW** otomatis = 1500 ÷ 100 = 15 gram — kamu TIDAK isi MBW manual, itu selalu dihitung sistem.
- **ADG** di sampling kedua otomatis muncul (selisih MBW dibagi selisih hari dari sampling pertama) — sampling PERTAMA nggak punya ADG (nggak ada pembanding sebelumnya).
- Balik ke **Hub Siklus**: kartu **MBW Terakhir**, **SR%**, **Biomass**, **FCR** SEMUA ke-update pakai data sampling PALING BARU (9 Juni), bukan rata-rata dari semua sampling.
- **SR%** = populasi (55.000) ÷ jumlah tebar (60.000) — kalau di sampling berikutnya kamu isi populasi lebih kecil (mis. karena ada kematian), SR% otomatis turun. Sistem TIDAK otomatis ngurangin populasi dari mortalitas harian — kamu yang estimasi manual tiap sampling.
- Kalau MBW sampling terakhir kamu ≥13 gram: **Biomass**-nya bakal masuk ke KPI Dashboard **"Est. Biomass Siap Panen"**. Kalau di bawah 13, kolam itu nggak dihitung di KPI itu meski udah ada sampling.
- Sekarang balik ke kartu **FCR** di Hub Siklus (dari langkah E) — harusnya SEKARANG udah muncul angka (bukan "—" lagi), karena Biomass dari Sampling sudah ada.

### I. Aplikasi Kimia & Biologi — login `operasional`

1. Dari Hub Siklus → **Catat Pemakaian** → isi kategori "pakan", item "Pakan 3M", qty 100kg, biaya 2.000.000.
2. Catat lagi kategori "probiotik", biaya 500.000.

**🔗 Efek Domino — cek semua ini**:
- Buka **Biaya & Laporan** → biaya "pakan" (2jt) dan "probiotik" (500rb) harusnya muncul di breakdown biaya per kategori.
- Balik ke **Dashboard** → KPI **"Pakan Bulan Ini"** bagian **Rp** sekarang harusnya keisi 2.000.000 (asalkan tanggal pembelian di BULAN INI, kalau tanggalnya bulan lalu nggak kehitung).
- Total Biaya di Biaya & Laporan = Harga Benur (langkah D) + biaya pakan + biaya probiotik + kategori lain yang belum diisi (mineral/desinfektan/obat = 0).

### J. Panen — login `owner`/`operasional`

1. Dari Hub Siklus → **Catat Panen** → tahap **Partial 1**, berat 300kg, harga 45.000/kg.
2. Cek progress bar di halaman Panen — harusnya 300kg dari target 1.100-1.200kg.
3. Catat lagi tahap **Total/Habis**, berat 850kg, harga 42.000/kg.

**🔗 Efek Domino — cek semua ini**:
- Status kolam di **Data Kolam** otomatis berubah jadi **"panen"** (cuma trigger dari tahap "Total/Habis", tahap partial TIDAK mengubah status).
- **Biaya & Laporan**: Total Pendapatan = (300×45.000) + (850×42.000) = 13.500.000 + 35.700.000 = 49.200.000.
- **HPP/kg** = Total Biaya ÷ Total Berat Panen (300+850=1150kg) — coba hitung manual, harus cocok sama yang ditampilkan.
- **Estimasi Laba/Rugi** = Total Pendapatan − Total Biaya.
- Progress bar sekarang harusnya penuh (1150kg dari target 1100-1200kg).
- **Penting**: karena status kolam sekarang "panen" (bukan "aktif" lagi), kolam ini SEKARANG TIDAK LAGI dihitung di KPI Dashboard "Kolam Aktif", "Rata-rata FCR", "Est. Biomass Siap Panen" — semua KPI yang basisnya "kolam aktif" bakal langsung nge-drop begitu status berubah jadi panen. Ini normal, bukan bug.

### K. Emergency & Kesehatan — login `owner`/`operasional`/`analis`

**Lakukan ini SEBELUM langkah J (Panen tahap Total)** kalau mau lihat efeknya di Dashboard — karena begitu kolam "panen", kolam itu nggak lagi dihitung di KPI Kolam Emergency.

1. Dari Hub Siklus → **Catat Kejadian** → isi tanggal hari ini, jenis "SR turun", keputusan "Lanjut".

**🔗 Efek Domino**:
- Buka **Dashboard** → KPI **"Kolam Emergency"** harusnya bertambah 1 — TAPI CUMA KALAU kolam ini masih berstatus "aktif" saat itu. Kalau kamu sudah panen total duluan (langkah J), kejadian ini TIDAK dihitung meskipun baru dicatat.

### L. Biaya & Laporan — semua role (read-only)

Ini bukan tempat input — cuma tempat NGECEK hasil akhir dari langkah D, I, J. Kalau ada angka yang kelihatan aneh, balik cek 3 langkah itu, bukan cari masalah di sini.

### M. Dashboard — semua role (read-only)

Ini tempat paling gampang buat lihat apakah data kamu di semua modul udah "nyambung" satu sama lain. Kalau ada KPI yang keliatan salah, biasanya bukan Dashboard-nya yang salah, tapi ada input di modul sumbernya yang belum lengkap/keliru (lihat Peta Efek Domino di bagian 3).

---

## 5. Hal yang Perlu Diperhatikan

- **Role yang salah kena blokir (403 Forbidden)** kalau maksa buka form yang bukan haknya — disengaja, laporkan cuma kalau arahnya kebalik (harusnya boleh malah diblokir, atau sebaliknya).
- **Dua sumber data pakan yang beda tujuan**: kg pakan (Pakan Harian, buat FCR) vs Rp pakan (Aplikasi Kimia & Biologi kategori "pakan", buat biaya) — isi DUA-DUANYA kalau mau laporan lengkap, jangan kira isi salah satu udah cukup.
- **FCR/Biomass/SR% di Hub Siklus selalu dari sampling PALING BARU**, bukan rata-rata atau akumulasi dari semua sampling.
- **Status kolam "aktif" vs "panen" menentukan apakah kolam itu dihitung di KPI Dashboard** — begitu panen total, kolam itu "hilang" dari sebagian besar KPI (memang benar begitu, bukan bug).
- **KPI "Kolam Emergency"** dan **"Estimasi Biomass Siap Panen"** pakai ambang yang saya tentukan sendiri (bukan dari spek asli klien) — kalau kerasa nggak masuk akal, kasih tahu saya, gampang disesuaikan.
- **Ancho, target panen 1.100–1.200kg, checklist persiapan tambak** — masih status asumsi sementara, belum final dari klien (lihat `CLAUDE.md`).
- Data testing kamu **beneran kesimpen** di database. Reset kalau perlu mulai bersih lagi:
  ```
  php artisan migrate:fresh --seed
  ```
  Hapus SEMUA data, balik ke 4 akun awal doang.

## 6. Verifikasi Fitur Kritis (Hasil Audit 2026-07-05)

Bagian ini khusus buat ngecek ulang 5 titik yang pernah diaudit — kalau nanti ada perubahan kode di area ini, jalanin ulang langkah-langkah ini buat mastiin nggak regresi.

### 6.1 Akumulasi Panen Multi-Tahap (Partial 1 → Partial 2 → Total)

**Status**: sudah benar, tapi tetap cek manual kalau ada perubahan di `CostService`/`HarvestController`.

1. Di satu siklus, catat Panen **Partial 1** (mis. 300kg) → cek progress bar & kartu Biaya & Laporan → biomass panen harus 300kg.
2. Catat Panen **Partial 2** (mis. 400kg) → biomass panen harus jadi **700kg** (300+400), BUKAN cuma 400kg.
3. Catat Panen **Total** (mis. 450kg) → biomass panen harus jadi **1150kg** (300+400+450).
4. Pastikan tiap tahap NAMBAH, bukan GANTI angka sebelumnya.

### 6.2 Alert Kualitas Air — Semua Parameter

**Status**: diperbaiki 2026-07-05 — sebelumnya cuma vibrio yang punya alert.

1. Di **Pakan & Kualitas Air Harian**, input DO=3 (di bawah standar >4) → baris itu di tabel harus tampil **angka merah**, dan muncul ikon warning di kolom tanggal.
2. Coba juga isi pH di luar 7,5–8,5, Suhu di luar 28–32, atau Salinitas di luar 25–30 → masing-masing kolom harus merah kalau di luar ambang.
3. Balik ke **Hub Siklus** → harus muncul banner merah "ada parameter kualitas air di luar standar mutu pada input terbaru".
4. Di **Kualitas Air Mingguan**, input TAN=3 (di atas standar <2), atau ammonia/nitrit/nitrat di atas ambangnya → kolom itu harus merah juga (bukan cuma vibrio kayak sebelumnya).
5. Alert di Hub Siklus cuma ngecek data **PALING BARU** (daily log terbaru + weekly log terbaru) — kalau data lama yang bermasalah tapi udah ada data baru yang normal, alert nggak muncul lagi (ini disengaja, alert buat kondisi TERKINI bukan riwayat).

### 6.3 Rekomendasi Flush-out Otomatis

**Status**: diimplementasikan 2026-07-05 — sebelumnya sama sekali nggak ada rekomendasi otomatis, cuma dropdown manual.

**Syarat munculnya rekomendasi** (DUA-DUANYA harus kejadian): DOC < 30 **DAN** kondisi kritis (SR turun >10 poin persentase antar 2 sampling terakhir, ATAU ada Emergency Log dalam 7 hari terakhir).

1. Pastikan siklus masih DOC di bawah 30 (tanggal pakan pertama belum lama).
2. Input **Sampling** pertama dengan populasi tinggi (mis. 58.000 dari tebar 60.000 = SR 96,7%).
3. Input **Sampling** kedua dengan populasi jauh lebih rendah (mis. 40.000 = SR 66,7%, turun 30 poin — di atas ambang 10 poin).
4. Balik ke **Hub Siklus** → harus muncul banner "Rekomendasi: Pertimbangkan Flush-out" dengan alasan "SR turun tajam".
5. Coba juga trigger lewat jalur lain: kondisi SR normal, tapi catat **Emergency & Kesehatan** baru (tanggal hari ini) → banner flush-out harus tetap muncul (karena kondisi kritisnya dari kejadian darurat, bukan SR).
6. Kalau DOC sudah di atas 30 (siklus sudah lama), banner ini TIDAK BOLEH muncul meski SR turun tajam — flush-out cuma relevan buat siklus muda.
7. **Catatan**: ambang "SR turun tajam" (10 poin) itu asumsi saya sendiri, bukan angka pasti dari PRD/klien — kalau kerasa kurang/kelebihan sensitif, kasih tahu, gampang disesuaikan di `GrowthService::hasSharpSrDrop()`.

### 6.4 Akses Role (Belum Diubah — Tunggu Konfirmasi)

**Status**: dilaporkan 2026-07-05, BELUM diubah menunggu keputusan.

PRD Bagian 3 cuma sebutin role eksplisit buat 4 aktivitas: Analis→Sampling+mortalitas, Operasional→pakan/air/ancho/obat, Lab→uji air mingguan, Owner→lihat+approval. Modul lain (Data Kolam, Siklus, Persiapan Tambak, Manajemen Dasar Tambak, Panen, Emergency, Aplikasi Kimia) nggak disebut eksplisit — role yang sekarang jalan itu keputusan desain berdasarkan pola "siapa paling relevan pegang ini", didokumentasikan di `CLAUDE.md`. Ada juga 2 baris kode dengan tag **TEMP TESTING** yang emang sengaja dilonggarkan sementara atas permintaanmu sendiri, bukan penyimpangan baru.

Kalau mau diperketat sesuai PRD literal (Analis CUMA Sampling, Operasional CUMA pakan/air/kimia, dst — tanpa akses ke Manajemen Dasar Tambak/Emergency/dst), kasih tahu saya, saya sesuaikan `routes/web.php`.

### 6.5 KPI Dashboard — Kolam Aktif, Rata-rata FCR, Estimasi Laba/Rugi

**Status**: sudah benar, diverifikasi test otomatis (`DashboardServiceAuditTest`).

1. Catat 2 kolam aktif dengan data lengkap → cek KPI **"Kolam Aktif"** = 2/2 (atau sesuai total kolam).
2. Panen salah satu kolam sampai tahap **Total** → refresh Dashboard → **"Kolam Aktif"** harus otomatis turun jadi 1, dan kolam yang udah panen itu HILANG dari perhitungan **"Rata-rata FCR"** juga.
3. Kalau ada kolam aktif yang BELUM ada data Sampling sama sekali (FCR-nya "—"), kolam itu di-**exclude** dari rata-rata FCR (bukan dihitung sebagai 0) — jadi rata-rata cuma dari kolam yang beneran punya data.
4. **"Estimasi Laba/Rugi"** cek muncul di DUA tempat: Dashboard utama (angka gabungan SEMUA kolam aktif) dan halaman Biaya & Laporan tiap siklus (angka cuma buat 1 kolam itu) — kedua angka ini WAJAR beda karena beda cakupan, bukan bug kalau nggak sama.

## 7. Daftar Lengkap Trigger — Supaya Nggak Ada yang Kelewat Dites

Ini rekap SEMUA kondisi otomatis/validasi yang ada di kode, per modul, lengkap dengan **syarat pasti** buat mancingnya. Kalau testing dan mau mastiin "semua kejadian yang mungkin" udah dicoba, centang satu-satu dari daftar ini.

### 7.1 Data Kolam

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Status jadi "aktif" | Mulai Stocking baru di kolam itu | `pond.status` berubah otomatis |
| Status jadi "panen" | Catat Panen tahap **"Total/Habis"** (bukan Partial1/2) | `pond.status` berubah otomatis |
| Hapus kolam DITOLAK | Kolam punya ≥1 riwayat Stocking | Pesan error muncul di halaman Edit Kolam |
| Hapus kolam BERHASIL | Kolam belum pernah ada Stocking sama sekali | Kolam hilang dari daftar |
| Bulk-create skip duplikat | Kode kolam yang mau dibuat udah ada di blok yang sama | Kode itu di-skip, pesan hasil sebutin mana yang dibuat vs dilewati |

### 7.2 Siklus

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Hapus siklus DITOLAK | Siklus itu udah dipakai ≥1 Stocking | Pesan error muncul di halaman Siklus |

### 7.3 Mulai Stocking (Tebar Benur)

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| DOC bisa dihitung | Tanggal Pakan Pertama diisi | Kartu DOC di Hub Siklus tampil angka |
| DOC tampil "—" | Tanggal Pakan Pertama DIKOSONGKAN | Kartu DOC tampil "—", banner kuning muncul nyuruh lengkapi |
| Sampling DITOLAK total | Tanggal Pakan Pertama masih kosong, tapi coba input Sampling | Error validasi, sampling nggak tersimpan |

### 7.4 Pakan & Kualitas Air Harian

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Alert ancho muncul | 2 SESI BERTURUT-TURUT (bisa beda jam di hari sama, atau lintas hari — diurutkan dari yang paling akhir) hasilnya **"Sisa Banyak"** | Banner merah di Hub Siklus |
| Alert ancho TIDAK muncul | 2 sesi terakhir bukan dua-duanya "Sisa Banyak" (mis. selang-seling dengan "Habis"/"Sisa Sedikit") | Tidak ada banner |
| DO alert (highlight merah) | DO pagi ATAU sore **≤ 4** ppm | Sel DO merah di tabel, ikon warning di kolom tanggal |
| pH alert | pH pagi ATAU sore **< 7,5 atau > 8,5** | Sel pH merah |
| Suhu alert | Suhu pagi ATAU sore **< 28°C atau > 32°C** | Sel Suhu merah |
| Salinitas alert | Salinitas **< 25 atau > 30** ppt | Sel Salinitas merah |
| Banner "Kualitas Air" di Hub Siklus | Input HARIAN PALING BARU (bukan riwayat lama) kena salah satu alert di atas | Banner merah muncul di Hub Siklus |
| Input DITOLAK (duplikat tanggal) | Tanggal yang sama diinput 2x untuk siklus yang sama | Error validasi |
| FCR tampil "—" | Belum ada Sampling sama sekali (biomass belum ada) ATAU akumulasi pakan masih 0 | FCR "—", bukan "0.00" |
| Mortalitas ~~dikali 2~~ (DICABUT 2026-07-08: tampil apa adanya, kg = ekor × MBW) | Isi field Mortalitas berapa pun (mis. 10) | Nilai terkoreksi (20) muncul di halaman Edit entri itu, dipakai di laporan — TAPI TIDAK otomatis ngurangin Populasi/SR% |

### 7.5 Manajemen Dasar Tambak

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Input DITOLAK (duplikat tanggal) | Tanggal yang sama diinput 2x untuk siklus yang sama | Error validasi |
| Tidak ada efek ke modul lain | — | Murni pencatatan, nggak masuk kalkulasi FCR/biaya/dashboard manapun |

### 7.6 Kualitas Air Mingguan

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| TAN alert | TAN **≥ 2** ppm | Sel TAN merah |
| Ammonia alert | Ammonia **≥ 0,1** ppm | Sel Ammonia merah |
| Nitrit alert | Nitrit **≥ 0,1** ppm | Sel Nitrit merah |
| Nitrat alert | Nitrat **≥ 50** ppm | Sel Nitrat merah |
| Rasio V/B alert | (vibrio hijau+hitam+luminer) ÷ total bakteri **> 10%** | Sel rasio merah + simbol ⚠, mis. vibrio_hijau=15 & total_bakteri=100 → 15% |
| Banner "Kualitas Air" di Hub Siklus | Input MINGGUAN PALING BARU kena salah satu alert di atas | Banner merah di Hub Siklus (bisa juga muncul karena daily log, lihat 7.4) |

### 7.7 Sampling & Pertumbuhan

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Input DITOLAK | Tanggal sampling < Tanggal Pakan Pertama siklus itu | Error validasi, tidak tersimpan |
| Input DITOLAK | Tanggal Pakan Pertama siklus belum diisi sama sekali | Error validasi, disuruh lengkapi Tanggal Pakan Pertama dulu |
| MBW dihitung otomatis | Isi Berat Sampel Total + Jumlah Sampel | MBW = berat ÷ jumlah, TIDAK bisa diinput manual |
| ADG muncul | Ini sampling KEDUA dst (butuh sampling sebelumnya buat pembanding) | Kolom ADG di tabel keisi; sampling pertama ADG-nya kosong |
| Kartu Hub Siklus ke-update | Sampling baru tersimpan | MBW/SR%/Biomass/FCR di Hub Siklus pakai data sampling PALING BARU |
| Masuk KPI "Est. Biomass Siap Panen" | MBW sampling terakhir **≥ 13** gram | Biomass kolam itu ditambahkan ke KPI Dashboard |
| TIDAK masuk KPI "Est. Biomass Siap Panen" | MBW sampling terakhir **< 13** gram | Kolam itu di-skip dari KPI meski udah ada sampling |
| Rekomendasi Flush-out ikut ke-trigger | SR turun **> 10 poin persentase** dibanding sampling sebelumnya, DAN DOC siklus < 30 | Banner rekomendasi flush-out di Hub Siklus (lihat 7.11) |

### 7.8 Aplikasi Kimia & Biologi

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Masuk Biaya & Laporan | Isi kategori apapun (pakan/probiotik/mineral/desinfektan/obat) + biaya | Breakdown biaya per kategori ke-update |
| Masuk KPI Dashboard "Pakan Bulan Ini" (Rp) | Kategori = **"pakan"**, tanggal masih dalam BULAN KALENDER BERJALAN | Bagian Rp di KPI ke-update; kalau tanggalnya bulan lalu, TIDAK kehitung |

### 7.9 Panen

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Status kolam jadi "panen" | Tahap = **"Total/Habis"** (Partial1/2 TIDAK memicu ini) | `pond.status` berubah, kolam hilang dari KPI "Kolam Aktif" dst |
| Pendapatan dihitung otomatis | Isi Berat (kg) + Harga/kg | Pendapatan = berat × harga, muncul di tabel |
| Progress bar bertambah | Tiap tahap panen baru (Partial1/2/Total) | Biomass panen AKUMULASI semua tahap (lihat 6.1), bukan cuma tahap terakhir |
| HPP bisa dihitung | Sudah ada ≥1 entri Panen (biomass panen > 0) | HPP/kg tampil angka di Biaya & Laporan |
| HPP tampil "—" | BELUM ada Panen sama sekali | HPP "—" (pembagi nol) |

### 7.10 Emergency & Kesehatan

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Masuk KPI "Kolam Emergency" | Kejadian dicatat dalam **3 hari terakhir**, DAN kolamnya masih berstatus **"aktif"** | KPI Dashboard bertambah 1 |
| TIDAK masuk KPI "Kolam Emergency" | Kejadian lebih dari 3 hari lalu, ATAU kolamnya udah "panen"/status lain | KPI tidak bertambah meski kejadian tercatat |
| Ikut memicu rekomendasi Flush-out | Kejadian dicatat dalam **7 hari terakhir**, DAN DOC siklus < 30 | Banner rekomendasi flush-out (lihat 7.11) — TIDAK peduli "jenis" kejadiannya apa, keberadaan kejadian aja cukup |

### 7.11 Rekomendasi Flush-out (Gabungan Sampling + Emergency)

| Trigger | Syarat Pasti | Yang Terjadi |
|---|---|---|
| Banner rekomendasi muncul | DOC < 30 **DAN** (SR turun >10 poin dari sampling sebelumnya **ATAU** ada Emergency Log dalam 7 hari terakhir) | Banner "Rekomendasi: Pertimbangkan Flush-out" di Hub Siklus, sebutin alasan spesifiknya |
| Banner TIDAK muncul meski SR turun tajam | DOC sudah ≥ 30 | Tidak ada banner — flush-out cuma relevan buat siklus muda |
| Banner TIDAK muncul meski DOC < 30 | Nggak ada SR turun tajam DAN nggak ada emergency log 7 hari terakhir | Tidak ada banner — DOC rendah doang belum cukup |

### 7.12 Dashboard (Gabungan Semua Modul)

| KPI | Syarat Hitungan Pasti |
|---|---|
| Kolam Aktif | Hitung `pond.status = 'aktif'` dibanding total kolam — turun otomatis begitu ada yang jadi "panen" |
| Rata-rata FCR | Cuma dari kolam berstatus aktif; kolam yang FCR-nya belum bisa dihitung (belum ada sampling/pakan) di-**exclude**, bukan dianggap 0 |
| Kolam Emergency | Emergency log ≤3 hari lalu + kolam masih aktif (lihat 7.10) |
| Pakan Bulan Ini (kg) | Total kg pakan dari Pakan Harian, cuma tanggal di BULAN KALENDER INI |
| Pakan Bulan Ini (Rp) | Total biaya kategori "pakan" dari Aplikasi Kimia & Biologi, cuma tanggal BULAN INI — SUMBER BEDA dari kg di atas |
| Est. Biomass Siap Panen | Jumlah biomass kolam aktif yang MBW sampling terakhirnya ≥13 gram |
| Est. Laba/Rugi Berjalan | Jumlah (pendapatan−biaya) SEMUA kolam aktif — beda dari angka di Biaya & Laporan yang cuma 1 kolam |
| Aktivitas Terbaru | Gabungan Pakan Harian + Sampling + Panen terbaru lintas kolam, diurutkan `created_at` (bukan tanggal datanya) |

### 7.13 Role & Akses (403 Forbidden)

Coba akses tiap halaman `create`/`store`/`update` pakai role yang SALAH — semua harus mental 403:

| Modul | Role yang DIBLOKIR (contoh) |
|---|---|
| Data Kolam, Siklus, Stocking, Panen | `analis`, `lab` |
| Pakan & Kualitas Air Harian | `lab` |
| Manajemen Dasar Tambak | `owner`, `lab` |
| Kualitas Air Mingguan | `analis` (kecuali lagi TEMP TESTING operasional juga boleh, lihat 6.4) |
| Sampling & Pertumbuhan | `lab` (kecuali lagi TEMP TESTING operasional juga boleh, lihat 6.4) |
| Aplikasi Kimia & Biologi | `owner`, `analis`, `lab` |
| Emergency & Kesehatan | `lab` |

---

## 8. Kalau Nemu Masalah

Kasih tahu: halaman mana, login sebagai siapa, apa yang kamu input, dan angka/perilaku apa yang kamu HARAPKAN vs yang BENERAN kejadian. Kalau bisa sebutkan juga langkah di Peta Efek Domino yang kamu curigai — biar saya langsung tahu kalkulasi mana yang perlu dicek.
