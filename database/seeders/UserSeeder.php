<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Andi Prasetyo',
                'email' => 'owner@homeworkkonveksi',
                'role' => 'Owner',
                'whatsapp' => '6281210011001',
                'password' => 'password123',
            ],
            [
                'name' => 'Nabila Salsabila',
                'email' => 'nabila.salsabila@homework-konveksi.test',
                'role' => 'Sales Lead',
                'whatsapp' => '6281210011002',
                'password' => 'password123',
            ],
            [
                'name' => 'Rian Kurnia',
                'email' => 'rian.kurnia@homework-konveksi.test',
                'role' => 'Production Coordinator',
                'whatsapp' => '6281210011003',
                'password' => 'password123',
            ],
            [
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.anggraini@homework-konveksi.test',
                'role' => 'Pattern Designer',
                'whatsapp' => '6281210011004',
                'password' => 'password123',
            ],
            [
                'name' => 'Fahmi Ramadhan',
                'email' => 'fahmi.ramadhan@homework-konveksi.test',
                'role' => 'QC Supervisor',
                'whatsapp' => '6281210011005',
                'password' => 'password123',
            ],
            [
                'name' => 'Sinta Maharani',
                'email' => 'sinta.maharani@homework-konveksi.test',
                'role' => 'Finance Admin',
                'whatsapp' => '6281210011006',
                'password' => 'password123',
            ],
            [
                'name' => 'Yoga Saputra',
                'email' => 'yoga.saputra@homework-konveksi.test',
                'role' => 'Customer Support',
                'whatsapp' => '6281210011007',
                'password' => 'password123',
            ],
            [
                'name' => 'Luthfi Hakim',
                'email' => 'luthfi.hakim@homework-konveksi.test',
                'role' => 'Warehouse Staff',
                'whatsapp' => '6281210011008',
                'password' => 'password123',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
