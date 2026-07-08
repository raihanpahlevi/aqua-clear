# Skenario Testing Lengkap — Isi Persis Per Modul

Dokumen ini beda dari `PANDUAN_TESTING.md` (yang isinya konsep/alur) — ini **naskah isian**: field mana, isi apa, angka berapa, biar semua trigger di sistem kebukti nyala. Semua nilai di bawah **sudah saya coba jalankan beneran** di sistem (bukan cuma teori), hasilnya saya sertakan biar kamu bisa cocokkan.

## Skenario Besar

Kita pakai **2 kolam** biar sekali jalan, ketutup semua kasus:

- **Kolam A1** = "kolam bermasalah" — siklus masih muda (DOC di bawah 30), dipakai buat mancing SEMUA alert (ancho, kualitas air harian, kualitas air mingguan, rekomendasi flush-out).
- **Kolam A2** = "kolam sehat, dipanen" — siklus lebih tua, datanya normal (buat lihat kontras "nggak ada alert"), terus dipanen 3 tahap sampai selesai (buat cek akumulasi panen + status kolam + KPI Dashboard).

**Tentang tanggal**: semua tanggal di bawah udah saya tulis sebagai **tanggal kalender asli** (bukan "H-X" lagi), dihitung dari hari ini **5 Juli 2026**. Tinggal ketik persis apa yang tertulis, JANGAN itung-itung sendiri — supaya nggak kejadian salah masuk bulan kayak sebelumnya (Tanggal Pakan Pertama kepencet jadi bulan depan, bikin DOC minus).

⚠️ **Kalau kamu baca dokumen ini BUKAN di tanggal 5 Juli 2026**, tanggal-tanggal di bawah udah nggak akurat (DOC bakal keitung aneh). Kasih tahu saya tanggal hari ini, saya bikinin ulang versi barunya dengan tanggal yang sesuai — jangan coba itung manual sendiri, gampang salah kayak yang baru kejadian.

## 0. Setup Awal (wajib pertama)

**Login sebagai `owner`.**

### Data Kolam — buat 2 kolam
Menu Data Kolam → Tambah Kolam (ulangi 2x):

| Field | Kolam 1 | Kolam 2 |
|---|---|---|
| Blok | A | A |
| Kode Kolam | `A1` | `A2` |
| Luas (m²) | 1000 | 1000 |
| Kapasitas | 5000 | 5000 |
| Status | kosong | kosong |

### Siklus — buat 1 siklus
Menu Siklus → isi nama: `Siklus 1 2026` → Tambah.

---

## 1. Persiapan Tambak & Air

**Login sebagai `owner`. Modul ini di kolam A1 doang** (buat A2 boleh dilewat, nggak wajib).

Dari Data Kolam → klik A1 → link "Persiapan Tambak & Air" → Catat Progres:

| Field | Isi |
|---|---|
| Jenis | Persiapan Tambak |
| Tanggal | **20 Juni 2026** |
| Siklus | (boleh kosong) |
| Checklist | centang "Pembersihan kolam" dan "Sterilisasi" |
| Biaya | 500000 |

**Hasil yang diharapkan**: tersimpan, tidak ada efek ke modul lain (murni pencatatan).

---

## 2. Mulai Stocking (Tebar Benur)

**Login sebagai `owner`.**

### Kolam A1 (siklus muda — buat semua alert)
Dari Data Kolam → klik A1 → "Mulai Siklus Baru":

| Field | Isi | Kenapa |
|---|---|---|
| Siklus | Siklus 1 2026 | — |
| Tanggal Tebar | **23 Juni 2026** | — |
| Tanggal Pakan Pertama | **25 Juni 2026** | DOC hari ini jadi ±10 — di bawah 30, syarat rekomendasi flush-out |
| Asal Benur | Benur Sumber A | bebas |
| Jumlah Tebar | 60000 | dipakai hitung SR% nanti |
| Harga Benur | 3000000 | TOTAL, bukan per ekor |

### Kolam A2 (siklus tua — buat panen lengkap)
Dari Data Kolam → klik A2 → "Mulai Siklus Baru":

| Field | Isi | Kenapa |
|---|---|---|
| Siklus | Siklus 1 2026 | sama, boleh dipakai bareng A1 |
| Tanggal Tebar | **21 Mei 2026** | — |
| Tanggal Pakan Pertama | **23 Mei 2026** | DOC hari ini jadi ±43 — di atas 30, biar flush-out TIDAK relevan lagi (buat kontras) |
| Asal Benur | Benur Sumber B | bebas |
| Jumlah Tebar | 60000 | — |
| Harga Benur | 3200000 | TOTAL |

**Hasil yang diharapkan**: kedua kolam otomatis berubah status jadi **"aktif"**. Dicek: buka Data Kolam, status A1 dan A2 harus "aktif".

---

## 3. Pakan & Kualitas Air Harian

**Login sebagai `operasional`.**

### Kolam A1 — isi buat mancing SEMUA alert sekaligus
Dari Hub Siklus A1 → "Pakan & Kualitas Air Harian" → Input Hari Ini:

| Field | Isi | Kenapa |
|---|---|---|
| Tanggal | **3 Juli 2026** | — |
| Pakan 07/11/15/19 | 5 / 5 / 5 / 5 | angka kecil, biar FCR nanti keliatan hitungannya |
| Kode Pakan | 3M | bebas |
| Ancho 07 | (kosongkan) | — |
| Ancho 11 | (kosongkan) | — |
| Ancho 15 | **Sisa Banyak** | 2 sesi terakhir sama-sama "Sisa Banyak" → trigger alert ancho |
| Ancho 19 | **Sisa Banyak** | ↑ (ini + ancho 15 = 2 berturut-turut) |
| DO Pagi | **3.0** | di bawah standar (>4) → alert DO |
| DO Sore | **3.5** | di bawah standar → alert DO |
| pH Pagi | **9.0** | di luar 7,5–8,5 → alert pH |
| pH Sore | 8.0 | normal, biar keliatan kontras sama pH Pagi |
| Suhu Pagi | **35** | di luar 28–32 → alert Suhu |
| Suhu Sore | 30 | normal |
| Salinitas | **20** | di luar 25–30 → alert Salinitas |
| Mortalitas | 15 | ~~dikali 2~~ (DICABUT 2026-07-08: tampil apa adanya, kg = ekor × MBW) di background = 30, cek di halaman Edit entri ini |

**Hasil yang diharapkan**: DO, pH Pagi, Suhu Pagi, Salinitas tampil **merah** di tabel. Balik ke Hub Siklus A1 → muncul 2 banner merah: "2x berturut-turut hasil ancho sisa banyak" DAN "ada parameter kualitas air di luar standar mutu".

### Kolam A2 — isi data SEHAT (kontras, tanpa alert)
Dari Hub Siklus A2 → Input Hari Ini:

| Field | Isi |
|---|---|
| Tanggal | **4 Juli 2026** |
| Pakan 07/11/15/19 | 20 / 20 / 20 / 20 |
| Kode Pakan | PL-40 |
| Ancho 07/11/15/19 | semua **Habis** |
| DO Pagi/Sore | 5 / 5.5 |
| pH Pagi/Sore | 8 / 8 |
| Suhu Pagi/Sore | 30 / 30 |
| Salinitas | 27 |
| Mortalitas | 5 |

**Hasil yang diharapkan**: semua kolom normal (hitam, bukan merah). Hub Siklus A2 TIDAK ada banner apapun.

---

## 4. Manajemen Dasar Tambak

**Login sebagai `analis` (atau `operasional`). Cukup di A1.**

Dari Hub Siklus A1 → "Manajemen Dasar Tambak" → Catat Hari Ini:

| Field | Isi |
|---|---|
| Tanggal | **3 Juli 2026** (boleh sama kayak Pakan Harian, atau beda) |
| Siphon Dilakukan? | Ya |
| Kondisi Lumpur Dasar | buruk |
| Jumlah Kincir Nyala | 2 |
| Jam Nyala Kincir | 16 |

**Hasil yang diharapkan**: tersimpan, TIDAK ada efek ke modul lain (murni pencatatan). Coba input tanggal yang SAMA sekali lagi → harus ditolak (1 tanggal = 1 entri per siklus).

---

## 5. Kualitas Air Mingguan

**Login sebagai `lab`. Cukup di A1** (biar semua alert mingguan ke-trigger sekaligus).

Dari Hub Siklus A1 → "Kualitas Air Mingguan" → Input Uji Air:

| Field | Isi | Kenapa |
|---|---|---|
| Tanggal | **2 Juli 2026** | — |
| TAN | **3** | di atas standar (<2) → alert |
| Ammonia | **0.2** | di atas standar (<0,1) → alert |
| Nitrit | **0.15** | di atas standar (<0,1) → alert |
| Nitrat | **60** | di atas standar (<50) → alert |
| TOM | 5 | tanpa ambang pasti, cuma dicatat |
| Alkalinitas | 100 | tanpa ambang pasti |
| Fe | 0.5 | tanpa ambang pasti |
| Vibrio Hijau | **15** | rasio V/B jadi (15+5+0)/100=20% → di atas 10% → alert |
| Vibrio Hitam | 5 | ↑ |
| Vibrio Luminer | 0 | ↑ |
| Total Bakteri | 100 | ↑ |

**Hasil yang diharapkan**: TAN, Ammonia, Nitrit, Nitrat, dan Rasio V/B semua tampil **merah** di tabel. Banner "kualitas air di luar standar" di Hub Siklus A1 tetap muncul (gabungan dari harian + mingguan).

---

## 6. Sampling & Pertumbuhan

**Login sebagai `analis`.**

### Kolam A1 — 2 sampling, buat mancing "SR turun tajam"

**Sampling pertama:**

| Field | Isi |
|---|---|
| Tanggal | **26 Juni 2026** |
| Berat Sampel Total | 1000 gram |
| Jumlah Sampel | 100 ekor |
| Populasi | 58000 |

→ MBW otomatis = 10 gram. SR% = 58000/60000 = **96,7%**.

**Sampling kedua (SR anjlok):**

| Field | Isi |
|---|---|
| Tanggal | **4 Juli 2026** |
| Berat Sampel Total | 1300 gram |
| Jumlah Sampel | 100 ekor |
| Populasi | **40000** |

→ MBW otomatis = **13 gram** (pas di ambang 13, trigger KPI "Est. Biomass Siap Panen"). SR% = 40000/60000 = **66,7%** — turun **30 poin** dari sampling sebelumnya (96,7→66,7), jauh di atas ambang 10 poin → **kondisi kritis "SR turun tajam" aktif**.

**Hasil yang diharapkan di Hub Siklus A1**: DOC (10) < 30 DAN kondisi kritis aktif → muncul banner **"Rekomendasi: Pertimbangkan Flush-out"**. Kartu KPI: MBW Terakhir 13.00gr, SR% 66.7%, Biomass 520.0kg (=40000×13/1000), FCR 0.04 (=20kg pakan ÷ 520kg biomass).

**Coba juga validasi ini**: input tanggal sampling SEBELUM 25 Juni 2026 (tanggal pakan pertama A1) → harus DITOLAK.

### Kolam A2 — 1 sampling normal

| Field | Isi |
|---|---|
| Tanggal | **22 Juni 2026** (DOC pas 30, sesuai jadwal PRD) |
| Berat Sampel Total | 1500 gram |
| Jumlah Sampel | 100 ekor |
| Populasi | 55000 |

→ MBW = 15 gram, SR% = 91,7%.

---

## 7. Aplikasi Kimia & Biologi

**Login sebagai `operasional`.**

### Kolam A1 — 2 entri
Dari Hub Siklus A1 → "Aplikasi Kimia & Biologi" → Catat Pemakaian (ulangi 2x):

| Field | Entri 1 | Entri 2 |
|---|---|---|
| Tanggal | **30 Juni 2026** | **1 Juli 2026** |
| Kategori | pakan | probiotik |
| Nama Item | Pakan 3M | Probiotik X |
| Qty | 50 | 5 |
| Satuan | kg | liter |
| Biaya Total | 1000000 | 300000 |

### Kolam A2 — 1 entri
Dari Hub Siklus A2 → Catat Pemakaian:

| Field | Isi |
|---|---|
| Tanggal | **25 Juni 2026** |
| Kategori | pakan |
| Nama Item | Pakan PL-40 |
| Qty | 200 |
| Satuan | kg |
| Biaya Total | 4000000 |

**Hasil yang diharapkan**: masuk ke breakdown biaya per kategori di Biaya & Laporan masing-masing kolam. Kategori "pakan" juga nambah ke KPI Dashboard "Pakan Bulan Ini" (asal tanggalnya masih bulan kalender berjalan).

---

## 8. Panen

**Login sebagai `owner`. Cuma di Kolam A2** (biar A1 tetap aktif buat demo alert).

Dari Hub Siklus A2 → "Panen" → Catat Panen (ulangi 3x, BERURUTAN):

| Field | Tahap 1 | Tahap 2 | Tahap 3 |
|---|---|---|---|
| Tahap | **Partial 1** | **Partial 2** | **Total/Habis** |
| Tanggal | **2 Juli 2026** | **4 Juli 2026** | **5 Juli 2026 (hari ini)** |
| Berat (kg) | 300 | 400 | 450 |
| Size (ekor/kg) | 68 | 50 | 30 |
| Harga/kg | 45000 | 43000 | 42000 |

**Cek progress bar SETIAP SELESAI 1 tahap** (jangan tunggu semua tahap selesai baru dicek):
- Setelah Tahap 1: biomass panen = **300kg**
- Setelah Tahap 2: biomass panen = **700kg** (300+400, BUKAN cuma 400)
- Setelah Tahap 3: biomass panen = **1150kg** (300+400+450) — masuk target 1.100–1.200kg!

**Hasil yang diharapkan setelah Tahap 3 (Total) disimpan**: status Kolam A2 di Data Kolam otomatis berubah jadi **"panen"**.

---

## 9. Emergency & Kesehatan

**Login sebagai `analis` (atau `owner`/`operasional`). Cuma di Kolam A1.**

Dari Hub Siklus A1 → "Emergency & Kesehatan" → Catat Kejadian:

| Field | Isi |
|---|---|
| Tanggal | **5 Juli 2026 (hari ini)** |
| Jenis Kejadian | SR turun drastis, dicurigai penyakit |
| Tindakan Penanganan | Cek ulang kualitas air, isolasi kolam |
| Keputusan | Lanjut |

**Hasil yang diharapkan**: kejadian ini (dalam 3 hari terakhir + kolam A1 masih aktif) bikin KPI Dashboard "Kolam Emergency" jadi **1**. Kejadian ini (dalam 7 hari terakhir) juga ikut jadi salah satu penyebab valid buat rekomendasi flush-out di poin 6 tadi (meski SR-drop aja udah cukup memicu).

---

## 10. Biaya & Laporan (cuma cek, nggak ada input)

**Semua role bisa buka, coba login siapa aja.**

### Kolam A2 (yang udah dipanen) — buka Hub Siklus A2 → "Biaya & Laporan"

Angka yang HARUS muncul persis:

| Item | Nilai |
|---|---|
| Biaya benur | Rp 3.200.000 |
| Biaya pakan | Rp 4.000.000 |
| Biaya probiotik/mineral/desinfektan/obat | Rp 0 (belum diisi) |
| **Total Biaya** | **Rp 7.200.000** |
| **Total Pendapatan Panen** | **Rp 49.600.000** (=300×45rb + 400×43rb + 450×42rb) |
| **HPP/kg** | **Rp 6.261** (=7.200.000 ÷ 1150kg) |
| **Estimasi Laba/Rugi** | **Rp 42.400.000** (hijau, karena untung) |

### Kolam A1 (belum panen) — buka Hub Siklus A1 → "Biaya & Laporan"

| Item | Nilai |
|---|---|
| Biaya benur | Rp 3.000.000 |
| Biaya pakan | Rp 1.000.000 |
| Biaya probiotik | Rp 300.000 |
| **Total Biaya** | **Rp 4.300.000** |
| **HPP/kg** | **"—"** (belum ada panen sama sekali di A1) |

---

## 11. Dashboard (cuma cek, nggak ada input)

**Login sebagai `owner`, buka menu Dashboard.**

Setelah semua langkah di atas selesai, KPI yang harus muncul:

| KPI | Nilai yang Diharapkan | Kenapa |
|---|---|---|
| **Kolam Aktif** | 1 / 2 | A1 masih aktif, A2 udah "panen" jadi nggak dihitung |
| **Rata-rata FCR** | ≈0.04 | cuma dari A1 (satu-satunya kolam aktif yang punya data FCR) |
| **Kolam Emergency** | 1 | dari Emergency Log A1 di poin 9 |
| **Pakan Bulan Ini (kg)** | tergantung tanggal² di atas jatuh di bulan kalender berjalan atau nggak — kalau 3 Juli 2026 (tanggal Pakan Harian A1) masih bulan ini, harus ke-hitung 20kg (5+5+5+5) dari A1 doang (A2 udah nggak aktif) |
| **Pakan Bulan Ini (Rp)** | sama kayak di atas, dari kategori "pakan" di Aplikasi Kimia A1 (30 Juni 2026) kalau masih bulan ini |
| **Est. Biomass Siap Panen** | 520 kg | dari A1, MBW sampling terakhir (13gr) udah ≥13 |
| **Est. Laba/Rugi Berjalan** | negatif sekitar -Rp4.300.000 | cuma dari A1 (kolam aktif), karena A1 belum ada pendapatan panen sama sekali tapi udah ada biaya |

**Catatan tanggal**: karena "Pakan Bulan Ini" cuma ngitung bulan kalender BERJALAN, kalau kamu jalanin scenario ini pas awal bulan (tanggal 1-9), sebagian tanggal di atas bisa "nyebrang" ke bulan sebelumnya dan nggak kehitung. Kalau itu terjadi, itu BUKAN bug — cek dulu tanggal aslinya masuk bulan mana sebelum lapor aneh.

---

## Ringkasan Semua Trigger yang Sudah Kena Sentuh Skenario Ini

Kalau ikutin semua langkah di atas persis, kamu udah otomatis nyoba SEMUA ini:

- ✅ Status kolam: kosong → aktif → panen
- ✅ Alert ancho (2 sesi berturut-turut sisa banyak)
- ✅ Alert kualitas air harian: DO, pH, Suhu, Salinitas (4 dari 4)
- ✅ Alert kualitas air mingguan: TAN, Ammonia, Nitrit, Nitrat, Rasio V/B (5 dari 5)
- ✅ Validasi sampling: tanggal sebelum pakan pertama ditolak
- ✅ MBW/ADG/SR%/Biomass/FCR terhitung otomatis
- ✅ Rekomendasi flush-out (DOC<30 + SR turun tajam)
- ✅ KPI "Kolam Emergency" dari Emergency Log
- ✅ Akumulasi panen 3 tahap (Partial1→Partial2→Total)
- ✅ HPP, Laba/Rugi, breakdown biaya per kategori
- ✅ Semua 6 KPI Dashboard + kontras kolam aktif vs panen
- ✅ Validasi tanggal unik (Pakan Harian, Manajemen Dasar Tambak)
- ✅ Role: owner/operasional/analis/lab semua kepake sesuai bagiannya

Kalau semua ini kejadian PERSIS kayak yang saya tulis, sistemnya beneran jalan bener. Kalau ada satu aja yang beda, itu baru layak dilaporkan sebagai bug.
