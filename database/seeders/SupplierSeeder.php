<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Suppliers;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();
        for ($i = 0; $i < 20; $i++) {
            Suppliers::create([
                'pic_name' => $faker->name(),
                'store_name' => $faker->name(),
                'product_category' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
            ]);
        }
    }
}
