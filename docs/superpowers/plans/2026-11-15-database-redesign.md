# Rombak Database Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implementasikan skema & domain model baru (Item Pesanan, invoice bertahap, hak akses pemilik/admin) di aplikasi Laravel.

**Architecture:** Migration bersih per tabel; logika domain di model Eloquent (`Orders`, `Invoice`, `Procurement`) + satu service `OrderService` untuk operasi multi-tabel (simpan pesanan + item + invoice DP dalam transaksi). Hak akses lewat middleware `role`.

**Tech Stack:** Laravel 13, PHP 8.4, MySQL (dev) / SQLite memory (test), Pest 4, Blade + Bootstrap.

**Spec:** `docs/superpowers/specs/2026-11-15-database-redesign-design.md`

## Global Constraints
- Nama tabel/kelas tetap bahasa Inggris (`orders`, `Orders`, `Suppliers`) agar tidak memecah route/view; istilah Indonesia dipakai di label UI & dokumen.
- Status produksi: `pesanan_diterima, menunggu_bahan, proses_produksi, quality_control, selesai`.
- Invoice type: `uang_muka, termin, pelunasan`; status: `belum_dibayar, lunas, dibatalkan`; method: `tunai, transfer`.
- Order payment_status: `belum_bayar, sebagian, lunas`.
- Role: `pemilik, admin`.
- Tidak mengubah desain visual; hanya field/tabel yang mengikuti skema.

## Review Focus
1. Invoice baru yang membuat total tagihan > total pesanan → ditolak dengan pesan validasi.
2. Mengubah item pesanan sehingga total < jumlah yang sudah dibayar → ditolak.
3. Admin membuka `/dashboard`, `/expense`, `/team` → 403/redirect, tidak bocor.
4. Pengadaan dibatalkan → pengeluaran bahan baku ikut hilang.
5. Hapus pesanan yang sudah punya invoice → ditolak dengan pesan, bukan error 500.

---

### Task 1: Migration bersih + model
**Files:** hapus 12 migration domain lama; buat `2026_11_15_0000{01..07}_create_{customers,suppliers,orders,order_items,procurements,expenses,invoices}_table.php`, `..._add_role_and_whatsapp_to_users_table.php`; ubah `app/Models/*`, hapus `InvoiceItem.php`, buat `OrderItem.php`.
- [ ] Backup data MySQL ke JSON, tulis migration sesuai skema spec §3 dengan aturan hapus §2.7.
- [ ] `Orders`: relasi `items()`, `invoices()`, `procurements()`, `expenses()`; `recalculateTotal(): void`, `syncPaymentStatus(): void`, accessor `amount_paid`, `remaining_balance`, `billable_balance`, `nextProductionStatus(): ?string`.
- [ ] `Invoice`: konstanta tipe/status/metode, `booted` saved/deleted → `$order->syncPaymentStatus()`.
- [ ] `Procurement`: sinkron expense; batal → hapus expense.
- [ ] Verifikasi: `php artisan migrate:fresh` sukses.

### Task 2: OrderService + controller/request pesanan
**Files:** `app/Services/OrderService.php`, `OrderRequest`, `OrderController`, `OrderDetailController`.
- [ ] Test (`tests/Feature/OrderTest.php`): simpan pesanan dengan 2 item → total = Σ, nomor ORD-YYMM-0001, DP 100000 → 1 invoice uang_muka lunas, payment_status `sebagian`; update item total < terbayar → error `items`; status maju 1 langkah, lompat ditolak; hapus pesanan ber-invoice → ditolak.
- [ ] Implementasi `OrderService::save(array $data, ?Orders $order = null): Orders`.

### Task 3: Invoice
**Files:** `InvoiceController`, `InvoiceRequest`, `InvoiceDetailController`.
- [ ] Test (`tests/Feature/InvoiceTest.php`): amount > billable → error `amount`; tandai lunas → order `lunas` bila tertutup penuh; batalkan → payment_status turun.

### Task 4: Pengadaan & Pengeluaran
- [ ] Test (`tests/Feature/ProcurementTest.php`): simpan → expense bahan_baku dengan order_id sama, status pesanan → menunggu_bahan hanya dari pesanan_diterima; batal → expense terhapus.

### Task 5: Hak akses
**Files:** `app/Http/Middleware/EnsureRole.php`, `bootstrap/app.php`, `routes/web.php`, `AuthController`, `TeamController`, sidebar.
- [ ] Test (`tests/Feature/AccessTest.php`): admin → 403 di dashboard/expense/team; pemilik → 200.

### Task 6: View & seeder & dashboard
- [ ] Form pesanan dengan repeater item; daftar/detail pesanan; form/daftar/detail invoice; dashboard (Σ invoice lunas, piutang, produk terlaris dari order_items, rekap periode); team role select; seeder realistis.
- [ ] Verifikasi: `php artisan migrate:fresh --seed`, `php artisan test`, render tiap halaman sebagai pemilik & admin tanpa error.
