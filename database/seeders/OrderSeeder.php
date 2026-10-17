<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Orders;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (!Customer::query()->exists()) {
            $this->call(CustomerSeeder::class);
        }

        $customers = Customer::query()->orderBy('id')->get()->values();
        $templates = [
            ['product_name' => 'Seragam PDH', 'material' => 'Drill', 'quantity' => 30, 'unit_price' => 210000, 'production_status' => Orders::PRODUCTION_STATUS_SELESAI, 'payment_type' => 'transfer', 'payment_status' => 'paid'],
            ['product_name' => 'Kaos Event', 'material' => 'Cotton Combed 24s', 'quantity' => 80, 'unit_price' => 78000, 'production_status' => Orders::PRODUCTION_STATUS_PROSES_PRODUKSI, 'payment_type' => 'cash', 'payment_status' => 'pending'],
            ['product_name' => 'Work Jacket', 'material' => 'Taslan', 'quantity' => 25, 'unit_price' => 185000, 'production_status' => Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN, 'payment_type' => 'transfer', 'payment_status' => 'pending'],
            ['product_name' => 'Polo Shirt', 'material' => 'Lacoste CVC', 'quantity' => 20, 'unit_price' => 95000, 'production_status' => Orders::PRODUCTION_STATUS_QUALITY_CONTROL, 'payment_type' => 'instalment', 'payment_status' => 'paid'],
            ['product_name' => 'Rompi Safety', 'material' => 'Safety mesh', 'quantity' => 16, 'unit_price' => 120000, 'production_status' => Orders::PRODUCTION_STATUS_PESANAN_DITERIMA, 'payment_type' => 'transfer', 'payment_status' => 'pending'],
            ['product_name' => 'Coach Jacket', 'material' => 'Parasut', 'quantity' => 32, 'unit_price' => 165000, 'production_status' => Orders::PRODUCTION_STATUS_PROSES_PRODUKSI, 'payment_type' => 'transfer', 'payment_status' => 'cancelled'],
        ];

        foreach ($templates as $index => $template) {
            $customer = $customers[$index % $customers->count()];
            $createdAt = now()->subDays(40 - ($index * 5));

            Orders::query()->create([
                'customer_id' => $customer->id,
                'product_name' => $template['product_name'],
                'material' => $template['material'],
                'quantity' => $template['quantity'],
                'size_details' => 'S: 5, M: 10, L: 10, XL: 5',
                'unit_price' => $template['unit_price'],
                'total_price' => $template['quantity'] * $template['unit_price'],
                'down_payment' => ($template['quantity'] * $template['unit_price']) * 0.4,
                'due_date' => $createdAt->copy()->addDays(14)->toDateString(),
                'production_status' => $template['production_status'],
                'notes' => 'Pesanan sample untuk dashboard dan pengujian modul skripsi.',
                'payment_type' => $template['payment_type'],
                'payment_status' => $template['payment_status'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(1),
            ]);
        }
    }
}
