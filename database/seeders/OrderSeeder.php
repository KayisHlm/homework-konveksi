<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Orders;
use App\Models\Procurement;
use App\Models\Suppliers;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Data contoh satu siklus pesanan utuh (pesanan → item → pengadaan → pengeluaran → invoice).
 * Setiap skenario mewakili kondisi berbeda agar seluruh status dapat didemokan dan diuji.
 */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->seedScenario($scenario);
        }
    }

    protected function seedScenario(array $s): void
    {
        $createdAt = CarbonImmutable::now()->subDays($s['days_ago'])->setTime(10, 0);

        $order = new Orders([
            'customer_id' => Customer::query()->where('email', $s['customer'])->value('id'),
            'design_notes' => $s['design'],
            'due_date' => $createdAt->addDays($s['lead_days'])->toDateString(),
            'discount_amount' => $s['discount'] ?? 0,
            'notes' => $s['notes'] ?? null,
        ]);
        $order->created_at = $createdAt;
        $order->updated_at = $createdAt;
        $order->production_status = $s['production_status'];
        $order->save();

        // Format item: [produk, bahan, warna, harga dasar, [ukuran => qty], [ukuran => tambahan harga]]
        foreach ($s['items'] as [$product, $material, $color, $price, $sizes, $extras]) {
            $item = $order->items()->create([
                'product_name' => $product,
                'material' => $material,
                'color' => $color,
                'unit_price' => $price,
            ]);
            foreach ($sizes as $size => $quantity) {
                $item->sizes()->create(['size' => $size, 'quantity' => $quantity, 'extra_price' => $extras[$size] ?? 0]);
            }
            $item->recalculate();
        }
        $order->recalculateTotal();

        foreach ($s['procurements'] ?? [] as [$supplier, $material, $quantity, $unit, $price, $orderedDay, $status, $receivedDay]) {
            Procurement::create([
                'order_id' => $order->id,
                'supplier_id' => Suppliers::query()->where('store_name', $supplier)->value('id'),
                'material_name' => $material,
                'quantity' => $quantity,
                'unit' => $unit,
                'unit_price' => $price,
                'ordered_at' => $createdAt->addDays($orderedDay)->toDateString(),
                'expected_at' => $createdAt->addDays($orderedDay + 4)->toDateString(),
                'received_at' => $receivedDay !== null ? $createdAt->addDays($receivedDay)->toDateString() : null,
                'status' => $status,
            ]);
        }

        foreach ($s['expenses'] ?? [] as [$category, $description, $amount, $day]) {
            Expense::create([
                'order_id' => $order->id,
                'category' => $category,
                'description' => $description,
                'amount' => $amount,
                'spent_at' => $createdAt->addDays($day)->toDateString(),
            ]);
        }

        // Nominal invoice berupa persentase dari total; 'sisa' = sisa yang belum ditagih.
        foreach ($s['invoices'] ?? [] as [$type, $share, $issuedDay, $status, $method]) {
            $amount = $share === 'sisa' ? $order->billableBalance() : round((float) $order->total_price * $share, -3);
            $issuedAt = $createdAt->addDays($issuedDay);

            Invoice::create([
                'order_id' => $order->id,
                'type' => $type,
                'amount' => $amount,
                'issued_at' => $issuedAt->toDateString(),
                'due_date' => $issuedAt->addDays(7)->toDateString(),
                'status' => $status,
                'paid_at' => $status === Invoice::STATUS_LUNAS ? $issuedAt->toDateString() : null,
                'payment_method' => $method,
            ]);
        }
    }

    protected function scenarios(): array
    {
        $lunas = Invoice::STATUS_LUNAS;
        $belum = Invoice::STATUS_BELUM_DIBAYAR;
        $batal = Invoice::STATUS_DIBATALKAN;
        $dp = Invoice::TYPE_UANG_MUKA;
        $termin = Invoice::TYPE_TERMIN;
        $pelunasan = Invoice::TYPE_PELUNASAN;
        $diterima = Procurement::STATUS_DITERIMA;
        $dipesan = Procurement::STATUS_DIPESAN;
        $dibatalkan = Procurement::STATUS_DIBATALKAN;

        return [
            // 1. Siklus lengkap: selesai & lunas (DP + pelunasan).
            [
                'customer' => 'rizky.pratama@smkn2bandung.sch.id', 'days_ago' => 160, 'lead_days' => 21,
                'production_status' => Orders::PRODUCTION_STATUS_SELESAI,
                'design' => 'Bordir logo sekolah dada kiri, nama siswa dada kanan.',
                'items' => [
                    ['Seragam PDH', 'Drill', 'Navy', 210000, ['S' => 10, 'M' => 20, 'L' => 15, 'XXL' => 3], ['XXL' => 15000]],
                ],
                'procurements' => [
                    ['CV Sinar Drill Nusantara', 'Kain drill navy', 85, 'meter', 42000, 1, $diterima, 4],
                    ['PT Karya Bordir Mandiri', 'Bordir logo sekolah', 48, 'pcs', 12000, 6, $diterima, 9],
                ],
                'expenses' => [[Expense::CATEGORY_JAHIT, 'Ongkos jahit 48 pcs PDH', 1440000, 15]],
                'invoices' => [[$dp, 0.4, 0, $lunas, Invoice::METHOD_TRANSFER], [$pelunasan, 'sisa', 20, $lunas, Invoice::METHOD_TRANSFER]],
            ],
            // 2. Selesai, pelunasan belum dibayar (piutang jatuh tempo).
            [
                'customer' => 'nadia.putri@wirabuana.ac.id', 'days_ago' => 120, 'lead_days' => 14,
                'production_status' => Orders::PRODUCTION_STATUS_SELESAI,
                'design' => 'Sablon plastisol 2 warna depan-belakang, tema dies natalis.',
                'items' => [
                    ['Kaos Event', 'Cotton Combed 24s', 'Hitam', 78000, ['S' => 20, 'M' => 30, 'L' => 20, 'XL' => 10], []],
                ],
                'procurements' => [['CV Sentra Cotton Bandung', 'Cotton combed 24s hitam', 32, 'kg', 135000, 1, $diterima, 3]],
                'expenses' => [[Expense::CATEGORY_SABLON, 'Sablon plastisol 80 pcs', 1200000, 6]],
                'invoices' => [[$dp, 0.5, 0, $lunas, Invoice::METHOD_TUNAI], [$pelunasan, 'sisa', 14, $belum, null]],
            ],
            // 3. Pembayaran bertahap (DP + termin) dengan diskon, tahap quality control.
            [
                'customer' => 'ahmad.fauzan@sinarjaya-logistik.co.id', 'days_ago' => 75, 'lead_days' => 30, 'discount' => 250000,
                'production_status' => Orders::PRODUCTION_STATUS_QUALITY_CONTROL,
                'design' => 'Scotlight 2 garis melingkar, logo perusahaan punggung.',
                'items' => [
                    ['Work Jacket', 'Taslan', 'Abu-abu', 185000, ['M' => 10, 'L' => 10, 'XL' => 5], ['XL' => 10000]],
                ],
                'procurements' => [
                    ['PT Warna Abadi Textile', 'Kain taslan abu', 50, 'meter', 38000, 1, $diterima, 5],
                    ['CV Safetywear Persada', 'Scotlight 5 cm', 60, 'meter', 9500, 2, $diterima, 5],
                    ['UD Mitra Resleting Jaya', 'Resleting YKK 60 cm', 25, 'pcs', 8000, 2, $diterima, 4],
                ],
                'invoices' => [[$dp, 0.3, 0, $lunas, Invoice::METHOD_TRANSFER], [$termin, 0.4, 25, $lunas, Invoice::METHOD_TRANSFER]],
            ],
            // 4. Dua produk dalam satu pesanan, sedang diproduksi.
            [
                'customer' => 'siti.rahmawati@alfalahbekasi.sch.id', 'days_ago' => 40, 'lead_days' => 21,
                'production_status' => Orders::PRODUCTION_STATUS_PROSES_PRODUKSI,
                'design' => 'Bordir logo OSIS; topi bordir depan.',
                'items' => [
                    ['Polo Shirt', 'Lacoste CVC', 'Putih', 95000, ['S' => 5, 'M' => 8, 'L' => 7], []],
                    ['Topi', 'Drill', 'Hitam', 35000, ['All Size' => 20], []],
                ],
                'procurements' => [
                    ['CV Sentra Cotton Bandung', 'Lacoste CVC putih', 9, 'kg', 145000, 1, $diterima, 4],
                    ['CV Sinar Drill Nusantara', 'Kain drill hitam', 6, 'meter', 42000, 1, $diterima, 3],
                ],
                'expenses' => [[Expense::CATEGORY_BORDIR, 'Bordir logo OSIS 40 titik', 600000, 8]],
                'invoices' => [[$dp, 0.5, 0, $lunas, Invoice::METHOD_TUNAI]],
            ],
            // 5. Menunggu bahan; satu pengadaan masih dipesan dan terlambat datang.
            [
                'customer' => 'dimas@larisubuh.id', 'days_ago' => 12, 'lead_days' => 18,
                'production_status' => Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN,
                'design' => 'Full print sublim, file desain dari pelanggan via WhatsApp.',
                'items' => [
                    ['Jersey Lari', 'Dri-fit', 'Biru', 85000, ['S' => 10, 'M' => 15, 'L' => 10], []],
                    ['Celana Lari', 'Dri-fit', 'Hitam', 60000, ['S' => 10, 'M' => 15, 'L' => 10], []],
                ],
                'procurements' => [
                    ['PT Warna Abadi Textile', 'Dri-fit biru', 40, 'meter', 32000, 1, $dipesan, null],
                    ['PT Canvas Indo Perkasa', 'Kain dri-fit (batal, stok kosong)', 40, 'meter', 30000, 0, $dibatalkan, null],
                ],
                'invoices' => [[$dp, 0.3, 0, $belum, null]],
            ],
            // 6. Baru diterima, DP tunai.
            [
                'customer' => 'laila@saungpadi.com', 'days_ago' => 3, 'lead_days' => 14,
                'production_status' => Orders::PRODUCTION_STATUS_PESANAN_DITERIMA,
                'design' => 'Sablon logo rumah makan di dada.',
                'items' => [['Apron', 'Kanvas', 'Coklat', 65000, ['All Size' => 12], []]],
                'invoices' => [[$dp, 0.5, 0, $lunas, Invoice::METHOD_TUNAI]],
            ],
            // 7. Invoice sempat dibatalkan lalu diterbitkan ulang.
            [
                'customer' => 'anisa.permata@polines.ac.id', 'days_ago' => 22, 'lead_days' => 25,
                'production_status' => Orders::PRODUCTION_STATUS_PROSES_PRODUKSI,
                'design' => 'Bordir nama himpunan di punggung, logo kampus di lengan.',
                'items' => [
                    ['Jaket Himpunan', 'Fleece', 'Hitam', 150000, ['M' => 20, 'L' => 15, 'XXL' => 3], ['XXL' => 10000]],
                ],
                'procurements' => [['PT Warna Abadi Textile', 'Fleece hitam', 60, 'meter', 45000, 1, $diterima, 5]],
                'invoices' => [[$dp, 0.5, 0, $batal, null], [$dp, 0.4, 1, $lunas, Invoice::METHOD_TRANSFER]],
            ],
            // 8. Belum ada pembayaran sama sekali.
            [
                'customer' => 'mega@klinikmelati.id', 'days_ago' => 1, 'lead_days' => 21,
                'production_status' => Orders::PRODUCTION_STATUS_PESANAN_DITERIMA,
                'design' => 'Bordir nama klinik di dada kiri.',
                'items' => [
                    ['Baju Scrub', 'Toyobo', 'Hijau Tosca', 120000, ['S' => 4, 'M' => 6], []],
                    ['Celana Scrub', 'Toyobo', 'Hijau Tosca', 90000, ['S' => 4, 'M' => 6], []],
                ],
                'notes' => 'Menunggu konfirmasi warna dari pelanggan.',
            ],
        ];
    }
}
