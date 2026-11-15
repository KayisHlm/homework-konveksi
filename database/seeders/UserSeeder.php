<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Andi Prasetyo', 'email' => 'owner@homeworkkonveksi', 'role' => User::ROLE_PEMILIK, 'whatsapp' => '6281210011001'],
            ['name' => 'Nabila Salsabila', 'email' => 'nabila.salsabila@homework-konveksi.test', 'role' => User::ROLE_ADMIN, 'whatsapp' => '6281210011002'],
            ['name' => 'Sinta Maharani', 'email' => 'sinta.maharani@homework-konveksi.test', 'role' => User::ROLE_ADMIN, 'whatsapp' => '6281210011006'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(['email' => $user['email']], [...$user, 'password' => 'password123']);
        }
    }
}
