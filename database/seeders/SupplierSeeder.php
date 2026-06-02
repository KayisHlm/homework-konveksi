<?php

namespace Database\Seeders;

use App\Models\Suppliers;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'pic_name' => 'Hendra Gunawan',
                'store_name' => 'CV Sinar Drill Nusantara',
                'product_category' => 'Kain drill, ripstop, dan kanvas',
                'phone' => '022-603-1188',
                'address' => 'Jl. ABC No. 45, Astanaanyar, Bandung',
                'created_at' => '2025-07-02 09:00:00',
            ],
            [
                'pic_name' => 'Merry Anggraini',
                'store_name' => 'PT Karya Bordir Mandiri',
                'product_category' => 'Bordir komputer, emblem, dan patch',
                'phone' => '021-7888-4503',
                'address' => 'Jl. Raya Bogor Km 28, Pasar Rebo, Jakarta Timur',
                'created_at' => '2025-07-16 10:20:00',
            ],
            [
                'pic_name' => 'Robby Setiawan',
                'store_name' => 'UD Mitra Resleting Jaya',
                'product_category' => 'Resleting, kancing, dan stopper',
                'phone' => '031-7490-2211',
                'address' => 'Jl. Kapasan No. 120, Surabaya',
                'created_at' => '2025-08-08 13:10:00',
            ],
            [
                'pic_name' => 'Dewi Lestari',
                'store_name' => 'CV Sentra Cotton Bandung',
                'product_category' => 'Cotton combed 24s, 30s, dan carded',
                'phone' => '022-732-6677',
                'address' => 'Jl. Cigondewah Kaler No. 18, Bandung',
                'created_at' => '2025-08-28 15:45:00',
            ],
            [
                'pic_name' => 'Ari Wibowo',
                'store_name' => 'PT Warna Abadi Textile',
                'product_category' => 'Fleece, baby terry, dan taslan',
                'phone' => '0274-441-900',
                'address' => 'Jl. Wates Km 9, Bantul, DI Yogyakarta',
                'created_at' => '2025-09-11 08:50:00',
            ],
            [
                'pic_name' => 'Nur Kholis',
                'store_name' => 'Toko Benang Prima',
                'product_category' => 'Benang jahit, benang obras, dan benang bordir',
                'phone' => '024-671-5538',
                'address' => 'Jl. MT Haryono No. 201, Semarang',
                'created_at' => '2025-10-03 11:25:00',
            ],
            [
                'pic_name' => 'Siska Maharani',
                'store_name' => 'CV Safetywear Persada',
                'product_category' => 'Scotlight, bahan rompi safety, dan webbing',
                'phone' => '0542-880-771',
                'address' => 'Jl. Jenderal Sudirman No. 78, Balikpapan',
                'created_at' => '2025-10-26 14:30:00',
            ],
            [
                'pic_name' => 'Bambang Hermawan',
                'store_name' => 'PT Canvas Indo Perkasa',
                'product_category' => 'Canvas, twill, dan kain outdoor',
                'phone' => '021-5577-6602',
                'address' => 'Jl. Daan Mogot Km 13, Kalideres, Jakarta Barat',
                'created_at' => '2025-11-10 09:40:00',
            ],
            [
                'pic_name' => 'Lina Oktavia',
                'store_name' => 'CV Label Tenun Nusantara',
                'product_category' => 'Label woven, hang tag, dan size tag',
                'phone' => '022-205-9901',
                'address' => 'Jl. Peta No. 166, Bojongloa Kaler, Bandung',
                'created_at' => '2025-12-04 10:15:00',
            ],
            [
                'pic_name' => 'Yoga Aditya',
                'store_name' => 'UD Kemasan Offset Mandiri',
                'product_category' => 'Plastik kemasan, paper bag, dan box lipat',
                'phone' => '031-531-2276',
                'address' => 'Jl. Kembang Jepun No. 47, Surabaya',
                'created_at' => '2026-01-14 13:00:00',
            ],
        ];

        Suppliers::query()->insert(array_map(function (array $supplier) {
            $createdAt = Carbon::parse($supplier['created_at']);

            return [
                'pic_name' => $supplier['pic_name'],
                'store_name' => $supplier['store_name'],
                'product_category' => $supplier['product_category'],
                'phone' => $supplier['phone'],
                'address' => $supplier['address'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(3),
            ];
        }, $suppliers));
    }
}
