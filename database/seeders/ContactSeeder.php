<?php

namespace Database\Seeders;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Rina Wulandari',
                'email' => 'rina.wulandari@smkn1bogor.sch.id',
                'phone' => '0812-1900-4502',
                'city' => 'Bogor',
                'address' => 'Jl. Heulang No. 12, Tanah Sareal, Bogor',
                'created_at' => '2025-07-06 08:40:00',
            ],
            [
                'name' => 'Arif Setiawan',
                'email' => 'arif.setiawan@ciptagarment.co.id',
                'phone' => '0813-2201-7704',
                'city' => 'Bandung',
                'address' => 'Jl. Moch. Toha No. 118, Regol, Bandung',
                'created_at' => '2025-07-22 10:15:00',
            ],
            [
                'name' => 'Maya Salsabila',
                'email' => 'maya.salsabila@himalayaevent.id',
                'phone' => '0812-3334-9910',
                'city' => 'Jakarta',
                'address' => 'Jl. Cipete Raya No. 37, Jakarta Selatan',
                'created_at' => '2025-08-05 15:30:00',
            ],
            [
                'name' => 'Farhan Akbar',
                'email' => 'farhan.akbar@bemunsoed.ac.id',
                'phone' => '0811-7788-2045',
                'city' => 'Purwokerto',
                'address' => 'Jl. Prof. Dr. HR Boenyamin No. 708, Purwokerto',
                'created_at' => '2025-08-27 13:10:00',
            ],
            [
                'name' => 'Nita Karunia',
                'email' => 'nita.karunia@sehatbersama.id',
                'phone' => '0812-5533-4012',
                'city' => 'Semarang',
                'address' => 'Jl. Gajahmada No. 95, Semarang Tengah',
                'created_at' => '2025-09-09 09:50:00',
            ],
            [
                'name' => 'Doni Saputra',
                'email' => 'doni.saputra@relawanpeduli.org',
                'phone' => '0812-6021-3360',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Kaliurang Km 5, Sleman',
                'created_at' => '2025-09-26 11:45:00',
            ],
            [
                'name' => 'Tari Maharani',
                'email' => 'tari.maharani@smamandala.sch.id',
                'phone' => '0813-1177-9208',
                'city' => 'Malang',
                'address' => 'Jl. Ijen No. 24, Klojen, Malang',
                'created_at' => '2025-10-14 14:20:00',
            ],
            [
                'name' => 'Bambang Nugroho',
                'email' => 'bambang.nugroho@pasificargo.co.id',
                'phone' => '0812-7711-4835',
                'city' => 'Surabaya',
                'address' => 'Jl. Margomulyo Indah Blok F-12, Surabaya',
                'created_at' => '2025-11-01 16:10:00',
            ],
            [
                'name' => 'Sheila Anindita',
                'email' => 'sheila.anindita@kampuskreatif.id',
                'phone' => '0812-9300-1114',
                'city' => 'Bandung',
                'address' => 'Jl. Dipatiukur No. 61, Coblong, Bandung',
                'created_at' => '2025-11-18 09:25:00',
            ],
            [
                'name' => 'Reza Maulana',
                'email' => 'reza.maulana@garudasport.id',
                'phone' => '0811-2255-7416',
                'city' => 'Solo',
                'address' => 'Jl. Slamet Riyadi No. 221, Laweyan, Solo',
                'created_at' => '2025-12-09 10:05:00',
            ],
            [
                'name' => 'Salsa Mutiara',
                'email' => 'salsa.mutiara@pkk-kotabatu.go.id',
                'phone' => '0812-8811-6217',
                'city' => 'Batu',
                'address' => 'Jl. Panglima Sudirman No. 507, Kota Batu',
                'created_at' => '2026-01-20 08:55:00',
            ],
            [
                'name' => 'Kevin Surya',
                'email' => 'kevin.surya@berkahkriya.com',
                'phone' => '0812-6044-3381',
                'city' => 'Denpasar',
                'address' => 'Jl. Teuku Umar Barat No. 99, Denpasar',
                'created_at' => '2026-02-12 13:40:00',
            ],
        ];

        Contact::query()->insert(array_map(function (array $contact) {
            $createdAt = Carbon::parse($contact['created_at']);

            return [
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'city' => $contact['city'],
                'address' => $contact['address'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDay(),
            ];
        }, $contacts));
    }
}
