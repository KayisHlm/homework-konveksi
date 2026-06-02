<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Customer::query()->exists()) {
            $this->call(CustomerSeeder::class);
        }

        $customers = Customer::query()->get()->keyBy('email');

        $orders = [
            ['customer_email' => 'rizky.pratama@smkn2bandung.sch.id', 'product_name' => 'Seragam PDH', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2025-07-08 09:30:00'],
            ['customer_email' => 'nadia.putri@wirabuana.ac.id', 'product_name' => 'Kaos Event', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2025-07-19 11:15:00'],
            ['customer_email' => 'ahmad.fauzan@sinarjaya-logistik.co.id', 'product_name' => 'Work Jacket', 'payment_type' => 'transfer', 'payment_status' => 'pending', 'created_at' => '2025-08-06 13:05:00'],
            ['customer_email' => 'siti.rahmawati@alfalahbekasi.sch.id', 'product_name' => 'Kaos Event', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2025-08-22 10:45:00'],
            ['customer_email' => 'dimas@larisubuh.id', 'product_name' => 'Polo Shirt', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2025-09-04 08:50:00'],
            ['customer_email' => 'laila@saungpadi.com', 'product_name' => 'Apron Bordir', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2025-09-21 14:10:00'],
            ['customer_email' => 'bagas@karyamandiriteknik.co.id', 'product_name' => 'Kemeja Lapangan', 'payment_type' => 'instalment', 'payment_status' => 'pending', 'created_at' => '2025-10-09 15:30:00'],
            ['customer_email' => 'anisa.permata@polines.ac.id', 'product_name' => 'Coach Jacket', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2025-10-27 09:20:00'],
            ['customer_email' => 'yogi@artakonstruksi.co.id', 'product_name' => 'Rompi Safety', 'payment_type' => 'transfer', 'payment_status' => 'pending', 'created_at' => '2025-11-11 10:00:00'],
            ['customer_email' => 'dewi.oktaviana@cahayainsan.sch.id', 'product_name' => 'Seragam PDH', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2025-11-24 08:35:00'],
            ['customer_email' => 'fajar@langitbirueo.com', 'product_name' => 'Kaos Event', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2025-12-06 16:10:00'],
            ['customer_email' => 'mega@klinikmelati.id', 'product_name' => 'Polo Shirt', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2026-01-12 09:40:00'],
            ['customer_email' => 'ilham.ramadhan@ugm.ac.id', 'product_name' => 'Hoodie Komunitas', 'payment_type' => 'transfer', 'payment_status' => 'pending', 'created_at' => '2026-01-28 11:30:00'],
            ['customer_email' => 'putri@rumahkreatif.org', 'product_name' => 'Kaos Event', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2026-02-05 13:15:00'],
            ['customer_email' => 'rizky.pratama@smkn2bandung.sch.id', 'product_name' => 'Work Jacket', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-02-19 10:25:00'],
            ['customer_email' => 'nadia.putri@wirabuana.ac.id', 'product_name' => 'Polo Shirt', 'payment_type' => 'cash', 'payment_status' => 'cancelled', 'created_at' => '2026-02-27 15:40:00'],
            ['customer_email' => 'ahmad.fauzan@sinarjaya-logistik.co.id', 'product_name' => 'Work Jacket', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-03-10 09:10:00'],
            ['customer_email' => 'siti.rahmawati@alfalahbekasi.sch.id', 'product_name' => 'Seragam PDH', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-03-24 14:20:00'],
            ['customer_email' => 'dimas@larisubuh.id', 'product_name' => 'Hoodie Komunitas', 'payment_type' => 'instalment', 'payment_status' => 'pending', 'created_at' => '2026-04-03 12:00:00'],
            ['customer_email' => 'laila@saungpadi.com', 'product_name' => 'Apron Bordir', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2026-04-09 08:45:00'],
            ['customer_email' => 'bagas@karyamandiriteknik.co.id', 'product_name' => 'Kemeja Lapangan', 'payment_type' => 'transfer', 'payment_status' => 'pending', 'created_at' => '2026-04-18 16:25:00'],
            ['customer_email' => 'anisa.permata@polines.ac.id', 'product_name' => 'Coach Jacket', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-04-26 10:50:00'],
            ['customer_email' => 'yogi@artakonstruksi.co.id', 'product_name' => 'Rompi Safety', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-05-07 09:00:00'],
            ['customer_email' => 'dewi.oktaviana@cahayainsan.sch.id', 'product_name' => 'Seragam PDH', 'payment_type' => 'cash', 'payment_status' => 'paid', 'created_at' => '2026-05-12 11:05:00'],
            ['customer_email' => 'fajar@langitbirueo.com', 'product_name' => 'Kaos Event', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-05-21 14:45:00'],
            ['customer_email' => 'mega@klinikmelati.id', 'product_name' => 'Polo Shirt', 'payment_type' => 'transfer', 'payment_status' => 'paid', 'created_at' => '2026-05-28 10:30:00'],
            ['customer_email' => 'ilham.ramadhan@ugm.ac.id', 'product_name' => 'Coach Jacket', 'payment_type' => 'transfer', 'payment_status' => 'pending', 'created_at' => '2026-06-01 13:20:00'],
            ['customer_email' => 'putri@rumahkreatif.org', 'product_name' => 'Kaos Event', 'payment_type' => 'cash', 'payment_status' => 'pending', 'created_at' => '2026-06-02 09:55:00'],
        ];

        Orders::query()->insert(array_map(function (array $order) use ($customers) {
            $customer = $customers->get($order['customer_email']);
            $createdAt = Carbon::parse($order['created_at']);

            return [
                'customer_id' => $customer->id,
                'product_name' => $order['product_name'],
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'payment_type' => $order['payment_type'],
                'payment_status' => $order['payment_status'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(match ($order['payment_status']) {
                    'paid' => 3,
                    'cancelled' => 1,
                    default => 5,
                }),
            ];
        }, $orders));
    }
}
