<?php

namespace Database\Seeders;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Rizky Pratama',
                'email' => 'rizky.pratama@smkn2bandung.sch.id',
                'institute' => 'SMK Negeri 2 Bandung',
                'phone' => '0812-2011-4501',
                'address' => 'Jl. Ciliwung No. 4, Bandung Wetan, Kota Bandung',
                'created_at' => '2025-07-04 09:15:00',
            ],
            [
                'name' => 'Nadia Putri',
                'email' => 'nadia.putri@wirabuana.ac.id',
                'institute' => 'Universitas Wira Buana',
                'phone' => '0813-1122-8405',
                'address' => 'Jl. Veteran Selatan No. 88, Makassar',
                'created_at' => '2025-07-11 10:30:00',
            ],
            [
                'name' => 'Ahmad Fauzan',
                'email' => 'ahmad.fauzan@sinarjaya-logistik.co.id',
                'institute' => 'PT Sinar Jaya Logistik',
                'phone' => '0812-8877-1902',
                'address' => 'Jl. Raya Bekasi Km 22, Cakung, Jakarta Timur',
                'created_at' => '2025-08-03 14:20:00',
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti.rahmawati@alfalahbekasi.sch.id',
                'institute' => 'SMA Islam Al Falah Bekasi',
                'phone' => '0811-9988-2371',
                'address' => 'Jl. KH. Noer Ali No. 15, Bekasi Selatan',
                'created_at' => '2025-08-19 08:45:00',
            ],
            [
                'name' => 'Dimas Saputra',
                'email' => 'dimas@larisubuh.id',
                'institute' => 'Komunitas Lari Subuh Jakarta',
                'phone' => '0813-7700-4512',
                'address' => 'Jl. Tebet Timur Dalam VIII No. 12, Jakarta Selatan',
                'created_at' => '2025-09-02 11:10:00',
            ],
            [
                'name' => 'Laila Nuraini',
                'email' => 'laila@saungpadi.com',
                'institute' => 'Rumah Makan Saung Padi',
                'phone' => '0812-3344-1176',
                'address' => 'Jl. Dr. Setiabudi No. 201, Bandung',
                'created_at' => '2025-09-17 13:05:00',
            ],
            [
                'name' => 'Bagas Maulana',
                'email' => 'bagas@karyamandiriteknik.co.id',
                'institute' => 'CV Karya Mandiri Teknik',
                'phone' => '0812-7300-2289',
                'address' => 'Jl. Magelang Km 7, Sleman, DI Yogyakarta',
                'created_at' => '2025-10-06 09:40:00',
            ],
            [
                'name' => 'Anisa Permata',
                'email' => 'anisa.permata@polines.ac.id',
                'institute' => 'BEM Politeknik Negeri Semarang',
                'phone' => '0812-7654-9921',
                'address' => 'Jl. Prof. H. Soedarto, Tembalang, Semarang',
                'created_at' => '2025-10-24 15:25:00',
            ],
            [
                'name' => 'Yogi Kurniawan',
                'email' => 'yogi@artakonstruksi.co.id',
                'institute' => 'PT Arta Konstruksi Persada',
                'phone' => '0811-2233-8814',
                'address' => 'Jl. Soekarno Hatta No. 55, Balikpapan',
                'created_at' => '2025-11-08 10:55:00',
            ],
            [
                'name' => 'Dewi Oktaviana',
                'email' => 'dewi.oktaviana@cahayainsan.sch.id',
                'institute' => 'SDIT Cahaya Insan',
                'phone' => '0812-7766-5508',
                'address' => 'Jl. Merpati Raya No. 10, Depok',
                'created_at' => '2025-11-21 08:20:00',
            ],
            [
                'name' => 'Fajar Hidayat',
                'email' => 'fajar@langitbirueo.com',
                'institute' => 'Langit Biru Event Organizer',
                'phone' => '0812-1100-3387',
                'address' => 'Jl. Ahmad Yani No. 78, Surabaya',
                'created_at' => '2025-12-03 16:00:00',
            ],
            [
                'name' => 'Mega Puspitasari',
                'email' => 'mega@klinikmelati.id',
                'institute' => 'Klinik Pratama Melati',
                'phone' => '0813-9981-2044',
                'address' => 'Jl. Adi Sucipto No. 17, Solo',
                'created_at' => '2026-01-09 09:05:00',
            ],
            [
                'name' => 'Ilham Ramadhan',
                'email' => 'ilham.ramadhan@ugm.ac.id',
                'institute' => 'Himpunan Mahasiswa Teknik Mesin UGM',
                'phone' => '0812-8899-7432',
                'address' => 'Jl. Grafika No. 2, Sleman, DI Yogyakarta',
                'created_at' => '2026-02-14 11:35:00',
            ],
            [
                'name' => 'Putri Maharani',
                'email' => 'putri@rumahkreatif.org',
                'institute' => 'Yayasan Rumah Kreatif',
                'phone' => '0812-6601-5529',
                'address' => 'Jl. Pattimura No. 6, Denpasar',
                'created_at' => '2026-03-07 14:50:00',
            ],
        ];

        Customer::query()->insert(array_map(function (array $customer) {
            $createdAt = Carbon::parse($customer['created_at']);

            return [
                'name' => $customer['name'],
                'email' => $customer['email'],
                'institute' => $customer['institute'],
                'phone' => $customer['phone'],
                'address' => $customer['address'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(2),
            ];
        }, $customers));
    }
}
