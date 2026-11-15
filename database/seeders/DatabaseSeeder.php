<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Jalankan dengan: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            OrderSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
