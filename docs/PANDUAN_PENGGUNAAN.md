# Panduan Penggunaan — Sistem Manajemen Tambak Aquaclear

Panduan lengkap tiap menu: buat apa, siapa yang pakai, dan cara isinya. Kalau butuh urutan testing cepat, lihat `PANDUAN_TESTING.md`. Dokumen ini buat referensi detail tiap kali lupa cara pakai sebuah menu.

## Login & Role

Buka `http://127.0.0.1:8000`, masuk pakai email + password yang dikasih admin.

4 role, beda hak akses:

| Role | Bisa lihat | Bisa input/edit |
|---|---|---|
| **Owner** | Semua menu | Data Kolam, Siklus, Stocking, Panen, Emergency |
| **Operasional** | Semua menu | Data Kolam, Siklus, Stocking, Pakan & Air Harian, Manajemen Dasar Tambak, Aplikasi Kimia & Biologi, Panen, Emergency |
| **Analis** | Semua menu | Sampling & Pertumbuhan, Pakan & Air Harian, Manajemen Dasar Tambak, Emergency |
| **Lab** | Semua menu | Kualitas Air Mingguan |

Owner sengaja TIDAK bisa input data harian (cuma pantau + approval). Kalau login pakai role yang salah terus coba input, sistem akan nolak (halaman "403 Forbidden").

## Alur Besar Sistem

```
Data Kolam (master 76 kolam)
      ↓
Persiapan Tambak & Air (checklist sebelum tebar)
      ↓
Siklus (nama batch) + Mulai Stocking (tebar benur) → masuk ke HUB SIKLUS
      ↓
   ┌──────────────────────────────────────────────────────────┐
   │  Dari Hub Siklus, semua ini bisa diakses:                 │
   │  Pakan & Air Harian │ Manajemen Dasar Tambak               │
   │  Kualitas Air Mingguan │ Sampling & Pertumbuhan            │
   │  Aplikasi Kimia & Biologi │ Panen │ Emergency              │
   │  Biaya & Laporan                                          │
   └──────────────────────────────────────────────────────────┘
```

**Kunci penting**: Data Kolam dan Siklus itu menu global (ada di sidebar). Selebihnya (Pakan, Air, Sampling, dst) NEMPEL ke kombinasi kolam+siklus tertentu — makanya nggak ada di sidebar, tapi muncul di halaman "hub siklus" setelah kamu mulai stocking di kolam tertentu.

---

## 1. Data Kolam

**Buat apa**: Master data 76 kolam — kode, luas, kapasitas, status.

**Siapa yang input**: Owner, Operasional.

**Cara akses**: Sidebar → Data Kolam.

### Tambah satu kolam
Klik **"Tambah Kolam"**, isi:
- **Blok** — pilih blok (A/B/C/D/R/RW)
- **Kode Kolam** — nama unik kolam dalam blok itu, mis. "A1"
- **Luas (m²)** — opsional
- **Kapasitas** — opsional
- **Status** — pilih salah satu:
  - `kosong` — belum ada apa-apa
  - `siap_tebar` — udah disiapin, siap ditebar
  - `aktif` — lagi ada siklus berjalan
  - `panen` — abis panen total
  - `maintenance` — lagi diperbaiki/nggak dipakai

### Tambah banyak kolam sekaligus
Klik **"Banyak Sekaligus"** — cocok buat setup awal 76 kolam. Isi:
- **Blok**, **Prefix Kode** (mis. "A"), **Nomor Mulai** (mis. 1), **Jumlah Kolam** (mis. 15) → otomatis bikin A1, A2, ... A15.
- Luas/Kapasitas/Status berlaku sama buat semua kolam yang dibuat.
- Kalau kode yang mau dibuat udah ada, otomatis di-skip (nggak dobel), pesan hasil bakal bilang mana yang dibuat vs dilewati.

### Lihat & edit kolam
Di daftar kolam, klik **"Detail →"** buat lihat detail + riwayat siklus kolam itu, atau ikon pensil buat edit data kolamnya (blok/kode/luas/kapasitas/status).

---

## 2. Persiapan Tambak & Air

**Buat apa**: Checklist progres sebelum tebar — pembersihan kolam, sterilisasi, isi air, dst. Checklist ini BEBAS (nggak divalidasi ketat), karena standar mutu resminya belum ditentukan klien.

**Siapa yang input**: Owner, Operasional.

**Cara akses**: Data Kolam → klik kolam → link "Persiapan Tambak & Air" di halaman detail kolam.

### Cara input
Klik **"Catat Progres"**, isi:
- **Jenis** — pilih "Persiapan Tambak" atau "Persiapan Air" (checklist yang muncul beda tergantung pilihan ini)
  - Tambak: Pembersihan kolam, Sterilisasi, Penambalan bocor
  - Air: Isi air, Sterilisasi air, Cek awal kualitas air
- **Tanggal**
- **Siklus** — opsional, kalau mau kaitkan ke siklus tertentu
- **Checklist** — centang item yang sudah selesai
- **Item Lainnya** — kalau ada progres di luar daftar, tulis bebas di sini
- **Biaya** — biaya desinfektan/sterilisasi kalau ada

Entri yang udah dibuat bisa di-edit lagi (klik ikon pensil) buat update progres — jadi nggak perlu bikin entri baru tiap kali ada progres tambahan, tinggal centang item yang baru selesai.

---

## 3. Siklus

**Buat apa**: Penamaan batch budidaya (mis. "Siklus 1 2026"), dipakai saat mulai tebar di kolam manapun. Satu nama siklus bisa dipakai di banyak kolam sekaligus (kalau semua kolam itu mulai tebar di periode yang sama).

**Siapa yang input**: Owner, Operasional.

**Cara akses**: Sidebar → Siklus.

### Cara input
Isi **Nama Siklus** di kotak yang ada di atas tabel → klik Tambah. Cuma itu, nggak ada field lain.

---

## 4. Mulai Stocking (Tebar Benur)

**Buat apa**: Mencatat mulainya satu siklus budidaya di kolam tertentu — tanggal tebar, asal benur, jumlah, dan yang PALING PENTING: tanggal pakan pertama (anchor perhitungan DOC).

**Siapa yang input**: Owner, Operasional.

**Cara akses**: Data Kolam → klik kolam → tombol "Mulai Siklus Baru".

### Cara input
- **Siklus** — pilih dari yang sudah dibuat di menu Siklus
- **Tanggal Tebar** — kapan benur ditebar ke kolam
- **Tanggal Pakan Pertama** — kapan pemberian pakan pertama kali dimulai. **INI YANG DIPAKAI BUAT HITUNG DOC, BUKAN TANGGAL TEBAR.** Boleh dikosongkan dulu kalau belum tahu, diisi menyusul lewat halaman Edit Siklus.
- **Asal Benur** — nama supplier/asal benur
- **Jumlah Tebar** — jumlah ekor benur yang ditebar
- **Harga Benur** — **TOTAL** biaya benur buat siklus ini (bukan harga per ekor)

Setelah simpan, kolam otomatis berubah status jadi **"aktif"**, dan kamu masuk ke **halaman hub siklus** — pusat semua modul lain di bawah ini.

---

## 5. Pakan & Kualitas Air Harian

**Buat apa**: Input harian yang paling sering dipakai — pakan 4x sehari, hasil ancho, kualitas air dasar, dan mortalitas.

**Siapa yang input**: Operasional (utama), Analis (khusus buat isi mortalitas kalau perlu).

**Cara akses**: Hub siklus → "Pakan & Kualitas Air Harian".

### Cara input
Klik **"Input Hari Ini"**, isi (semua boleh sebagian, nggak wajib penuh):
- **Tanggal**
- **Pakan 07.00/11.00/15.00/19.00 (kg)** — jumlah pakan tiap sesi
- **Ancho tiap sesi** — dicek ±2 jam setelah kasih pakan, pilih: Habis / Sisa Sedikit / Sisa Banyak
  - ⚠️ Kalau 2 sesi BERTURUT-TURUT hasilnya "Sisa Banyak", sistem otomatis kasih alert di halaman hub siklus
- **Kode Pakan** — kode pakan sesuai fase DOC (mis. "#0", "3M")
- **DO / pH / Suhu Pagi & Sore** — standar: DO >4ppm, pH 7,5–8,5, Suhu 28–32°C
- **Salinitas** — standar 25–30 ppt, cuma 1x/hari (bukan pagi-sore)
- **Mortalitas** — jumlah ekor mati yang KETEMU/TERAMATI langsung. Sistem otomatis KALI 2 angka ini buat laporan (karena kanibalisme udang — bangkai suka dimakan, jadi angka asli lebih tinggi dari yang keliatan)
- **Catatan** — bebas

Satu tanggal cuma bisa 1 entri per siklus — kalau mau ubah data hari yang sama, edit entri yang sudah ada, jangan bikin baru.

---

## 6. Manajemen Dasar Tambak

**Buat apa**: Pencatatan sederhana siphon, kondisi lumpur dasar, dan kincir — selama siklus berjalan.

**Siapa yang input**: Operasional, Analis.

**Cara akses**: Hub siklus → "Manajemen Dasar Tambak".

### Cara input
Klik **"Catat Hari Ini"**, isi:
- **Tanggal**
- **Siphon Dilakukan?** — Ya/Tidak
- **Kondisi Lumpur Dasar** — bebas, kualitatif (mis. "baik", "sedang", "buruk")
- **Jumlah Kincir Nyala**
- **Jam Nyala Kincir**
- **Catatan**

Sama seperti Pakan Harian, 1 tanggal cuma 1 entri per siklus.

---

## 7. Kualitas Air Mingguan

**Buat apa**: Hasil uji lab air yang nggak dicek tiap hari — parameter kimia (mingguan), TOM/alkalinitas/Fe (10 harian), dan vibrio/bakteri (7 harian).

**Siapa yang input**: Lab.

**Cara akses**: Hub siklus → "Kualitas Air Mingguan".

### Cara input
Klik **"Input Uji Air"**, isi sesuai data yang ada (boleh sebagian — nggak semua parameter dicek bareng):
- **Tanggal Uji**
- **Mingguan**: TAN (<2ppm), Ammonia (<0,1ppm), Nitrit (<0,1ppm), Nitrat (<50ppm)
- **10 Hari Sekali**: TOM, Alkalinitas, Fe — belum ada ambang standar pasti, cuma dicatat
- **7 Hari Sekali**: Vibrio Hijau, Vibrio Hitam, Vibrio Luminer, Total Bakteri
  - Sistem otomatis hitung **rasio V/B** (vibrio dibanding total bakteri) — kalau >10%, muncul warning merah di tabel

---

## 8. Sampling & Pertumbuhan

**Buat apa**: Ukur pertumbuhan udang secara berkala — MBW, ADG, SR%, biomass. Ini yang paling sering dicek karena datanya muncul di kartu KPI hub siklus.

**Siapa yang input**: Analis.

**Cara akses**: Hub siklus → "Sampling & Pertumbuhan".

**Jadwal disarankan**: sampling pertama di DOC 30, lalu tiap 7 hari sesudahnya (sistem nggak maksa jadwal ini, cuma pengingat).

### Cara input
Klik **"Input Sampling"**, isi:
- **Tanggal Sampling** — HARUS setelah tanggal pakan pertama siklus ini (sistem nolak kalau sebelum itu, karena DOC nggak boleh negatif)
- **Berat Sampel Total (gram)** — total berat semua udang yang ditimbang
- **Jumlah Sampel (ekor)** — jumlah ekor yang ditimbang. **JANGAN hitung MBW sendiri** — sistem otomatis hitung MBW = berat total ÷ jumlah sampel
- **Estimasi Populasi Saat Ini (ekor)** — perkiraan jumlah udang hidup sekarang di kolam
- **Kondisi Organ** — opsional, catatan kondisi fisik udang
- **Catatan**

Setelah disimpan, sistem otomatis hitung dan tampilkan: DOC (snapshot saat itu), MBW, ADG (dibanding sampling sebelumnya), Size (ekor/kg), SR%, dan Biomass — semua muncul di tabel riwayat sampling dan di kartu KPI hub siklus.

---

## 9. Aplikasi Kimia & Biologi

**Buat apa**: Catat pemakaian & biaya probiotik, mineral, desinfektan, obat — **plus pembelian pakan** (buat keperluan hitung biaya, beda dari kg pakan yang diberi harian di menu #5).

**Siapa yang input**: Operasional.

**Cara akses**: Hub siklus → "Aplikasi Kimia & Biologi".

### Cara input
Klik **"Catat Pemakaian"**, isi:
- **Tanggal**
- **Kategori** — pilih: pakan / probiotik / mineral / desinfektan / obat
- **Nama Item** — mis. "Pakan 3M", "Probiotik X"
- **Qty** dan **Satuan** — mis. 100 kg, 10 liter
- **Biaya Total (Rp)** — boleh dikosongkan dulu kalau harga acuan belum ada dari bagian operasional tambak

**Penting**: kategori "pakan" di sini beda tujuan dengan input kg pakan di menu Pakan Harian — yang di sini buat hitung BIAYA pembelian, yang di Pakan Harian buat hitung FCR/pertumbuhan. Dua-duanya perlu diisi kalau mau laporan biaya & FCR akurat.

---

## 10. Panen

**Buat apa**: Catat hasil panen per tahap — partial 1, partial 2, total.

**Siapa yang input**: Owner, Operasional.

**Cara akses**: Hub siklus → "Panen".

### Referensi ukuran per tahap (udang Vaname)
| Tahap | MBW | Size |
|---|---|---|
| Partial 1 | 13–15 gr | 65–70 ekor/kg |
| Partial 2 | 20 gr | 50 ekor/kg |
| Total/Habis | 30–40 gr | 25–35 ekor/kg |

### Cara input
Klik **"Catat Panen"**, isi:
- **Tahap** — Partial 1 / Partial 2 / Total-Habis
- **Tanggal Panen**
- **Berat (kg)**
- **Size (ekor/kg)** — hasil ukur langsung saat panen
- **Harga/kg (Rp)**
- **Catatan**

Sistem otomatis hitung **Pendapatan = Berat × Harga/kg**. Kalau pilih tahap **"Total/Habis"**, status kolam otomatis berubah jadi **"panen"**.

Progress bar di halaman ini nunjukin total berat semua tahap panen dibanding target 1.100–1.200 kg/kolam (akumulasi semua tahap, bukan cuma tahap total).

---

## 11. Emergency & Kesehatan

**Buat apa**: Catat kejadian darurat — udang sakit, air jelek, SR turun drastis — dan keputusan penanganannya.

**Siapa yang input**: Owner, Operasional, Analis.

**Cara akses**: Hub siklus → "Emergency & Kesehatan".

### Cara input
Klik **"Catat Kejadian"**, isi:
- **Tanggal**
- **Jenis Kejadian** — bebas, tulis apa yang terjadi (mis. "udang sakit", "air jelek", "SR turun")
- **Tindakan Penanganan** — apa yang sudah/akan dilakukan
- **Keputusan** — Lanjut / Flush-out / Panen Parsial
  - Flush-out biasanya diambil kalau DOC masih di bawah 30 dan kondisi kritis

Catatan ini bersifat log kejadian (nggak bisa dihapus/diedit setelah disimpan) — kalau salah input, catat entri baru buat koreksi.

Kejadian yang tercatat dalam 3 hari terakhir bakal muncul di KPI "Kolam Emergency" di Dashboard.

---

## 12. Biaya & Laporan

**Buat apa**: Ringkasan biaya, HPP, dan estimasi laba/rugi per siklus. Read-only — semua role bisa lihat, nggak ada input di sini.

**Cara akses**: Hub siklus → "Biaya & Laporan".

### Yang ditampilkan
- **Total Biaya** — gabungan benur + pakan (dari pembelian) + probiotik + mineral + desinfektan + obat
- **Total Pendapatan Panen** — gabungan semua tahap panen
- **HPP/kg** — total biaya ÷ total berat panen
- **Estimasi Laba/Rugi** — pendapatan − biaya
- **% Biaya Pakan** — acuan historis normal 60–68% dari total biaya
- **Progress panen** vs target 1.100–1.200 kg/kolam

Semua angka di sini estimasi/proyeksi — bisa berubah tergantung harga jual aktual saat panen.

---

## 13. Dashboard

**Buat apa**: Ringkasan cepat kondisi semua kolam buat Owner/Manajer. Read-only.

**Cara akses**: Sidebar → Dashboard (halaman pertama setelah login).

### 6 Kartu KPI
1. **Kolam Aktif** — jumlah kolam berstatus aktif dari total kolam
2. **Rata-rata FCR** — efisiensi pakan seluruh kolam aktif (kosong kalau belum ada data pakan)
3. **Kolam Emergency** — jumlah kolam dengan kejadian darurat dalam 3 hari terakhir
4. **Pakan Bulan Ini** — total kg & Rp pakan (dari pembelian) BULAN BERJALAN saja
5. **Est. Biomass Siap Panen** — proyeksi dari kolam yang MBW-nya udah capai ambang panen (≥13gr)
6. **Est. Laba/Rugi Berjalan** — total proyeksi laba/rugi semua kolam aktif

### Aktivitas Terbaru
Panel di bawah kartu KPI, nampilin input pakan/sampling/panen paling baru lintas semua kolam, diurutkan dari yang paling baru.

---

## Istilah yang Sering Dipakai

| Istilah | Arti |
|---|---|
| **DOC** | Day of Culture — umur budidaya dalam hari, dihitung dari tanggal pakan pertama |
| **MBW** | Mean Body Weight — rata-rata berat per ekor (gram) |
| **ADG** | Average Daily Gain — pertambahan berat rata-rata per hari |
| **SR%** | Survival Rate — persentase udang yang masih hidup |
| **FCR** | Feed Conversion Ratio — rasio pakan terpakai dibanding biomass, makin kecil makin efisien |
| **FR%** | Feeding Rate — persentase pakan harian dibanding biomass |
| **HPP** | Harga Pokok Produksi — biaya produksi per kg hasil panen |
| **Biomass** | Total bobot udang hidup di kolam (kg) — dari populasi × MBW |
| **Ancho** | Anco/serokan buat cek sisa pakan — indikator nafsu makan udang |
