<?php

namespace Database\Seeders;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Customer::query()->count() === 0) {
            $this->call(CustomerSeeder::class);
        }

        $customers = Customer::query()->get()->keyBy('email');
        $catalog = [
            'Work Jacket' => 185000,
            'Coach Jacket' => 165000,
            'Polo Shirt' => 95000,
            'Seragam PDH' => 210000,
            'Kaos Event' => 78000,
            'Rompi Safety' => 120000,
            'Hoodie Komunitas' => 175000,
            'Apron Bordir' => 85000,
            'Kemeja Lapangan' => 195000,
            'Topi Bordir' => 45000,
        ];

        $invoices = [
            [
                'invoice_number' => 'INV-KONV-2507-001',
                'customer_email' => 'rizky.pratama@smkn2bandung.sch.id',
                'issued_at' => '2025-07-10 09:30:00',
                'payment_status' => 'paid',
                'payment_via' => 'Transfer BCA',
                'discount_percent' => 0.05,
                'notes' => 'Pelunasan seragam PDH untuk kegiatan MPLS.',
                'items' => [
                    ['product_name' => 'Seragam PDH', 'quantity' => 28],
                    ['product_name' => 'Topi Bordir', 'quantity' => 28],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2508-002',
                'customer_email' => 'anisa.permata@polines.ac.id',
                'issued_at' => '2025-08-04 14:00:00',
                'payment_status' => 'paid',
                'payment_via' => 'Transfer Mandiri',
                'discount_percent' => 0,
                'notes' => 'Jaket panitia ospek gelombang kedua.',
                'items' => [
                    ['product_name' => 'Coach Jacket', 'quantity' => 35],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2509-003',
                'customer_email' => 'yogi@artakonstruksi.co.id',
                'issued_at' => '2025-09-12 10:20:00',
                'payment_status' => 'sent',
                'payment_via' => 'Transfer BRI',
                'discount_percent' => 0,
                'notes' => 'Termin pertama pengadaan rompi safety proyek gudang.',
                'items' => [
                    ['product_name' => 'Rompi Safety', 'quantity' => 20],
                    ['product_name' => 'Work Jacket', 'quantity' => 10],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2510-004',
                'customer_email' => 'laila@saungpadi.com',
                'issued_at' => '2025-10-06 11:45:00',
                'payment_status' => 'paid',
                'payment_via' => 'Cash',
                'discount_percent' => 0.1,
                'notes' => 'Seragam dapur dan frontliner restoran cabang baru.',
                'items' => [
                    ['product_name' => 'Apron Bordir', 'quantity' => 40],
                    ['product_name' => 'Polo Shirt', 'quantity' => 12],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2511-005',
                'customer_email' => 'bagas@karyamandiriteknik.co.id',
                'issued_at' => '2025-11-18 08:55:00',
                'payment_status' => 'unpaid',
                'payment_via' => 'Transfer BNI',
                'discount_percent' => 0,
                'notes' => 'Kemeja lapangan untuk tim maintenance plant.',
                'items' => [
                    ['product_name' => 'Kemeja Lapangan', 'quantity' => 18],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2512-006',
                'customer_email' => 'dewi.oktaviana@cahayainsan.sch.id',
                'issued_at' => '2025-12-03 13:30:00',
                'payment_status' => 'paid',
                'payment_via' => 'Transfer BCA',
                'discount_percent' => 0.05,
                'notes' => 'Seragam guru dan staf semester ganjil.',
                'items' => [
                    ['product_name' => 'Seragam PDH', 'quantity' => 24],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2601-007',
                'customer_email' => 'dimas@larisubuh.id',
                'issued_at' => '2026-01-15 09:10:00',
                'payment_status' => 'paid',
                'payment_via' => 'QRIS',
                'discount_percent' => 0,
                'notes' => 'Hoodie komunitas untuk long run awal tahun.',
                'items' => [
                    ['product_name' => 'Hoodie Komunitas', 'quantity' => 30],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2602-008',
                'customer_email' => 'mega@klinikmelati.id',
                'issued_at' => '2026-02-07 15:25:00',
                'payment_status' => 'draft',
                'payment_via' => 'Transfer Mandiri',
                'discount_percent' => 0,
                'notes' => 'Penawaran awal untuk seragam resepsionis klinik.',
                'items' => [
                    ['product_name' => 'Polo Shirt', 'quantity' => 18],
                    ['product_name' => 'Apron Bordir', 'quantity' => 18],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2602-009',
                'customer_email' => 'fajar@langitbirueo.com',
                'issued_at' => '2026-02-28 10:40:00',
                'payment_status' => 'paid',
                'payment_via' => 'Transfer BCA',
                'discount_percent' => 0.08,
                'notes' => 'Kaos event konser kampus dan crew lapangan.',
                'items' => [
                    ['product_name' => 'Kaos Event', 'quantity' => 80],
                    ['product_name' => 'Polo Shirt', 'quantity' => 10],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2603-010',
                'customer_email' => 'ahmad.fauzan@sinarjaya-logistik.co.id',
                'issued_at' => '2026-03-14 09:50:00',
                'payment_status' => 'unpaid',
                'payment_via' => 'Transfer BCA',
                'discount_percent' => 0,
                'notes' => 'Tagihan pengadaan work jacket operasional gudang.',
                'items' => [
                    ['product_name' => 'Work Jacket', 'quantity' => 25],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2604-011',
                'customer_email' => 'putri@rumahkreatif.org',
                'issued_at' => '2026-04-08 14:15:00',
                'payment_status' => 'paid',
                'payment_via' => 'Cash',
                'discount_percent' => 0.05,
                'notes' => 'Kaos relawan dan hoodie mentor workshop kreatif.',
                'items' => [
                    ['product_name' => 'Kaos Event', 'quantity' => 60],
                    ['product_name' => 'Hoodie Komunitas', 'quantity' => 12],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2604-012',
                'customer_email' => 'ilham.ramadhan@ugm.ac.id',
                'issued_at' => '2026-04-27 16:30:00',
                'payment_status' => 'sent',
                'payment_via' => 'Transfer BSI',
                'discount_percent' => 0,
                'notes' => 'Jaket himpunan untuk rapat kerja nasional.',
                'items' => [
                    ['product_name' => 'Coach Jacket', 'quantity' => 32],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2605-013',
                'customer_email' => 'siti.rahmawati@alfalahbekasi.sch.id',
                'issued_at' => '2026-05-11 08:35:00',
                'payment_status' => 'paid',
                'payment_via' => 'Transfer BNI',
                'discount_percent' => 0.05,
                'notes' => 'Seragam angkatan dan topi panitia class meeting.',
                'items' => [
                    ['product_name' => 'Seragam PDH', 'quantity' => 36],
                    ['product_name' => 'Topi Bordir', 'quantity' => 36],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2605-014',
                'customer_email' => 'nadia.putri@wirabuana.ac.id',
                'issued_at' => '2026-05-26 12:00:00',
                'payment_status' => 'cancelled',
                'payment_via' => 'Transfer Mandiri',
                'discount_percent' => 0,
                'notes' => 'Pesanan dibatalkan karena revisi desain fakultas.',
                'items' => [
                    ['product_name' => 'Polo Shirt', 'quantity' => 20],
                ],
            ],
            [
                'invoice_number' => 'INV-KONV-2606-015',
                'customer_email' => 'rizky.pratama@smkn2bandung.sch.id',
                'issued_at' => '2026-06-01 10:10:00',
                'payment_status' => 'sent',
                'payment_via' => 'Transfer BCA',
                'discount_percent' => 0,
                'notes' => 'Penagihan susulan untuk work jacket dan rompi kegiatan praktik.',
                'items' => [
                    ['product_name' => 'Work Jacket', 'quantity' => 16],
                    ['product_name' => 'Rompi Safety', 'quantity' => 16],
                ],
            ],
        ];

        foreach ($invoices as $invoice) {
            $customer = $customers->get($invoice['customer_email']);
            $issuedAt = Carbon::parse($invoice['issued_at']);
            $dueDate = $issuedAt->copy()->addDays(match ($invoice['payment_status']) {
                'draft' => 21,
                'paid' => 14,
                default => 30,
            });

            $subtotal = collect($invoice['items'])->sum(
                fn (array $item) => $item['quantity'] * $catalog[$item['product_name']]
            );
            $discount = round($subtotal * $invoice['discount_percent'], 2);
            $total = $subtotal - $discount;

            $invoiceId = DB::table('invoices')->insertGetId([
                'invoice_number' => $invoice['invoice_number'],
                'customer_id' => $customer->id,
                'issued_at' => $issuedAt,
                'due_date' => $dueDate->toDateString(),
                'payment_status' => $invoice['payment_status'],
                'payment_via' => $invoice['payment_via'],
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'notes' => $invoice['notes'],
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt->copy()->addDays($invoice['payment_status'] === 'paid' ? 7 : 2),
            ]);

            DB::table('invoice_items')->insert(array_map(function (array $item) use ($catalog, $invoiceId, $issuedAt) {
                $unitPrice = $catalog[$item['product_name']];

                return [
                    'invoice_id' => $invoiceId,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'line_total' => $item['quantity'] * $unitPrice,
                    'created_at' => $issuedAt,
                    'updated_at' => $issuedAt->copy()->addDay(),
                ];
            }, $invoice['items']));
        }
    }
}
