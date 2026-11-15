# Rombak Database & Domain Model — Homework Konveksi

Tanggal: 2026-11-15 · Konteks: revisi skripsi, tahap Preliminary Design (4.2.B *Memperbarui Domain Model*, Milestone 2).
Acuan: `Skripsi_main_mstn1-rombak_kode.pdf` (BAB IV, SRS-HK-001 s.d. 017, Gambar 4.1).

## 1. Tujuan

Menyelaraskan basis data dan kode dengan konsep relasi baru dari penulis:

| # | Relasi usulan penulis | Status setelah analisis |
|---|---|---|
| 1 | Pemasok – Pengadaan | Dipertahankan (1 : 0..*) |
| 2 | Pengadaan – Pesanan | Dipertahankan (0..* : 1) |
| 3 | Pengeluaran – Pesanan | Dipertahankan (0..* : 0..1) |
| 4 | Invoice – Pesanan | Dipertahankan, **diubah menjadi 1 : 0..* (opsi B)** |
| 5 | Item – Pesanan | **Baru**, komposisi (1 : 1..*) |
| 6 | Pelanggan – Pesanan | Dipertahankan (1 : 0..*) |
| 7 | Pengeluaran – Pengadaan | Dipertahankan (0..1 : 1) |

Relasi yang **dihapus** dari Gambar 4.1:

- **Invoice – Pelanggan**: pelanggan sudah bisa diketahui lewat `invoice → pesanan → pelanggan`. Relasi langsung adalah redundansi transitif yang memungkinkan data tidak konsisten (invoice untuk pelanggan A, padahal pesanannya milik pelanggan B).
- **Invoice ◆ Item Invoice**: item tidak lagi diketik ulang di invoice, tetapi diambil dari Item Pesanan (sesuai SRS-HK-013, "item yang terisi dari data pesanan").
- **Pesanan ◆ Produksi**: status produksi cukup menjadi atribut Pesanan karena tidak punya identitas atau siklus hidup sendiri.

## 2. Analisis logika

### 2.1 Pesanan sebagai pusat (TT-01)
Semua dokumen (item, pengadaan, pengeluaran, invoice) menggantung ke `orders.id`, dan `order_number` (ORD-YYMM-0001) adalah identitas yang dibaca manusia.

### 2.2 Item Pesanan (SRS-HK-006)
Sebelumnya pesanan hanya punya satu `product_name`, satu `material`, dan `size_details` berupa teks bebas, sehingga "jumlah per ukuran" tidak bisa dihitung. Sekarang satu baris item mewakili satu kombinasi **produk + bahan + warna + ukuran**, lengkap dengan `quantity`, `unit_price`, dan `subtotal`. Dengan cara ini, ukuran yang harganya beda (misalnya XXL) bisa dicatat.
`orders.total_price` adalah **atribut turunan** (Σ subtotal item − diskon), dihitung ulang setiap kali item disimpan.

### 2.3 Invoice dan pembayaran (opsi B: 1 pesanan → banyak invoice)
- Invoice adalah **tagihan bertahap** atas satu pesanan. Jenisnya: `uang_muka`, `termin`, atau `pelunasan`, dengan `amount` (nominal yang ditagih).
- Status invoice: `belum_dibayar`, `lunas`, atau `dibatalkan`. Kalau lunas, `paid_at` dan `payment_method` (`tunai`/`transfer`) diisi.
- **Sumber kebenaran pembayaran adalah invoice.** Pada Pesanan:
  - `amount_paid` = Σ amount invoice berstatus lunas (turunan)
  - `remaining_balance` = total_price − amount_paid (turunan)
  - `payment_status` = `belum_bayar` / `sebagian` / `lunas`. Nilai ini **disimpan sebagai cache turunan**, disinkronkan setiap kali invoice disimpan atau dihapus, supaya bisa difilter dan diurutkan di daftar pesanan (SRS-HK-009, SRS-HK-015).
- Uang muka saat pencatatan pesanan (SRS-HK-006): jika diisi > 0, sistem otomatis membuat invoice `uang_muka` berstatus `lunas`. Kolom `orders.down_payment` dan `payment_type` dihapus karena keduanya kini diwakili invoice.
- Aturan: total nominal invoice yang tidak dibatalkan ≤ total pesanan, sehingga tagihan tidak bisa melebihi nilai pesanan.
- Diskon dipindah dari invoice ke **pesanan**, karena diskon adalah kesepakatan harga pesanan, bukan milik satu tagihan.

### 2.4 Pengadaan → Pengeluaran (TT-03, TT-04, TT-06)
- Setiap pengadaan menghasilkan **paling banyak satu** pengeluaran kategori `bahan_baku`. Kolom `expenses.procurement_id` dibuat unik.
- Redundansi terkendali: pengeluaran dari pengadaan menyimpan `order_id` dan `procurement_id` sekaligus. Aturannya, `order_id` **selalu disalin** dari pengadaan, dan pengeluaran ini hanya bisa diubah lewat modul pengadaan.
- **Perbaikan bug:** pengadaan yang dibatalkan sekarang menghapus pengeluarannya (bahan tidak jadi dibeli).
- Mencatat pengadaan mengubah status pesanan menjadi *Menunggu Bahan* **hanya jika** status masih *Pesanan Diterima*. Status yang sudah lebih maju tidak dimundurkan.

### 2.5 Status produksi berurutan (SRS-HK-008)
Status hanya bisa maju satu langkah: Pesanan Diterima → Menunggu Bahan → Proses Produksi → Quality Control → Selesai. Field status dihapus dari form tambah dan ubah pesanan. Perubahan status dilakukan lewat tombol "Lanjutkan ke …" di detail pesanan.

### 2.6 Hak akses (Tabel 4.4, SRS-HK-003/012/016/017)
`users.role` ∈ {`pemilik`, `admin`}.

| Fitur | Pemilik | Admin |
|---|---|---|
| Dashboard & rekap keuangan (003) | ✔ | ✘ |
| Pelanggan, Pemasok, Pesanan, Pengadaan, Invoice | ✔ | ✔ |
| Pengeluaran (012) | ✔ | ✘ |
| Tim & akun (016) | ✔ | ✘ |
| Ringkasan pendapatan/biaya/laba per pesanan (017) | ✔ | ✘ (bagian ini disembunyikan) |

Admin diarahkan ke daftar pesanan setelah login. Pemilik tidak bisa menghapus akunnya sendiri.

### 2.7 Aturan hapus (integritas data)
- Pelanggan → Pesanan: `restrict`. Pelanggan yang punya pesanan tidak bisa dihapus.
- Pemasok → Pengadaan: `restrict`.
- Pesanan → Item, Pengadaan, Pengeluaran: `cascade`.
- Pesanan → Invoice: `restrict`. Pesanan yang sudah punya invoice tidak bisa dihapus, karena invoice adalah dokumen keuangan.
- Pengadaan → Pengeluaran: `cascade`.

## 3. Skema akhir (MySQL)

```
customers      (id, name, email UQ, institute, phone, address, timestamps)
suppliers      (id, pic_name, store_name, product_category, phone, address, timestamps)
orders         (id, order_number UQ, customer_id FK, design_notes, due_date,
                discount_amount, total_price, production_status, payment_status,
                notes, timestamps)
order_items    (id, order_id FK, product_name, material, color, size,
                quantity, unit_price, subtotal, timestamps)
procurements   (id, order_id FK, supplier_id FK, material_name, quantity, unit,
                unit_price, total_cost, ordered_at, expected_at, received_at,
                status, notes, timestamps)
expenses       (id, order_id FK?, procurement_id FK? UQ, category, description,
                amount, spent_at, timestamps)
invoices       (id, invoice_number UQ, order_id FK, type, amount, issued_at,
                due_date, status, paid_at, payment_method, notes, timestamps)
users          (+ role pemilik|admin, whatsapp)
```
Tabel `invoice_items` dan `contacts` dihapus.

## 4. Domain model terbaru (untuk Gambar 4.x revisi)

```
Pelanggan 1 ──── 0..* Pesanan
Pesanan   1 ◆─── 1..* ItemPesanan          (komposisi)
Pesanan   1 ──── 0..* Pengadaan
Pemasok   1 ──── 0..* Pengadaan
Pengadaan 1 ──── 0..1 Pengeluaran
Pesanan 0..1 ─── 0..* Pengeluaran          (pengeluaran operasional boleh tanpa pesanan)
Pesanan   1 ──── 0..* Invoice              (uang muka / termin / pelunasan)
```

## 5. Pendekatan migrasi

| Pendekatan | Kelebihan | Kekurangan |
|---|---|---|
| **A. Migration bersih (dipilih)** | Satu migration per tabel, mudah dijelaskan di bab Implementasi, tanpa jejak create-lalu-drop `contacts` | Butuh `migrate:fresh` (data lokal di-backup dulu ke JSON) |
| B. Migration ALTER bertahap | Data lama tetap ada | Riwayat migration berbelit, konversi `size_details` teks → item tidak bisa akurat |

Data lokal hanyalah data uji. Data dibackup dulu, lalu diisi ulang lewat seeder yang realistis.

## 6. Non-goals
- File skripsi tidak diubah (revisi naskah dilakukan di Milestone 2 oleh penulis).
- Tidak ada modul stok/inventori, unggah file desain, atau multi-cabang.
- Desain UI tidak diubah. Hanya form dan tabel yang menyesuaikan dengan skema baru.

## 7. Keputusan yang diambil oleh pelaksana (bisa dikoreksi)
1. Item = kombinasi produk+bahan+warna+ukuran.
2. Uang muka di form pesanan → invoice `uang_muka` lunas otomatis.
3. Diskon di level pesanan.
4. `payment_status` pesanan disimpan sebagai cache turunan.
5. Status produksi hanya maju satu langkah.
6. Peran `pemilik`/`admin` saja. Jabatan bebas dari seeder lama dihapus.

---

## Addendum A — Item per produk dengan rincian ukuran (2026-11-15)

**Permintaan:** satu pesanan memuat lebih dari satu item, dan satu item = satu produk dengan rincian jumlah per ukuran (cara pelanggan konveksi biasa memesan: "Kaos hitam S 10, M 20, XXL 2").

**Pendekatan yang dipilih penulis:** Item per produk + rincian ukuran (bukan satu baris per ukuran).

**Perubahan skema**
```
order_items      (id, order_id FK, product_name, material, color, unit_price,
                  quantity*, subtotal*, timestamps)            * turunan dari rincian ukuran
order_item_sizes (id, order_item_id FK cascade, size, quantity, extra_price default 0,
                  timestamps, UNIQUE(order_item_id, size))
```
- `subtotal ukuran = quantity × (unit_price + extra_price)`. `extra_price` adalah tambahan harga untuk ukuran besar (mis. XXL +Rp5.000).
- `order_items.quantity` = Σ qty ukuran dan `order_items.subtotal` = Σ subtotal ukuran. Keduanya disimpan sebagai nilai turunan supaya dashboard (produk terlaris) dan daftar pesanan tidak perlu join.
- Aturan: setiap item wajib punya minimal satu ukuran dengan qty > 0, dan ukuran harus termasuk daftar baku (XS…XXXL, All Size).

**Domain model:** `Pesanan 1 ◆── 1..* Item Pesanan 1 ◆── 1..* Rincian Ukuran` (komposisi bertingkat).

**UI:** form pesanan berupa kartu per produk (nama, bahan, warna, harga dasar) dengan grid ukuran (qty dan tambahan harga) serta tombol "+ Tambah Produk". Detail pesanan dan invoice menampilkan satu baris per produk beserta rincian ukurannya.

---

## Addendum B — Cetak rekap keuangan per periode (2026-11-15)

**Permintaan:** pemilik dapat mencetak rekap harian, mingguan, bulanan, dan tahunan, serta memilih periodenya sendiri.

**Keputusan:**
- Halaman `/report` (khusus pemilik) dengan rentang **Dari–Sampai**. Dari = Sampai berarti satu periode. Harian memakai input tanggal. Mingguan (Senin–Minggu, nomor minggu ISO), bulanan, dan tahunan memakai dropdown periode, karena `input type=week/month` tidak didukung Safari dan Firefox.
- Isi rekap: ringkasan (pesanan baru, pemasukan, pengeluaran, laba), tabel per periode, pengeluaran per kategori, rincian pemasukan, rincian pengeluaran, dan daftar pesanan baru. Cetak memakai `window.print()` dengan CSS cetak A4.
- Basis perhitungan: pemasukan = invoice lunas menurut `paid_at` (basis kas), pengeluaran menurut `spent_at`, pesanan menurut `created_at`.
- Batas baris per rekap: 93 hari, 104 minggu, 60 bulan, 20 tahun.
- `RecapService` dipakai bersama oleh dashboard dan halaman rekap.
- Zona waktu aplikasi diubah ke `Asia/Jakarta` agar transaksi dini hari tidak tercatat di tanggal sebelumnya.

**Domain model:** tidak berubah (tetap versi 2). Rekap adalah hasil turunan dari Invoice, Pengeluaran, dan Pesanan, bukan objek atau tabel baru. Di ICONIX, fitur ini muncul sebagai use case "Mencetak Rekap Keuangan" serta boundary dan controller pada robustness diagram.

**Saran revisi SRS-HK-003:** tambahkan "...serta memilih rentang periode rekap dan mencetaknya."
