<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Orders;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();

        if (!Customer::query()->exists()) {
            $this->call(CustomerSeeder::class);
        }

        $customerIds = Customer::query()->pluck('id')->all();

        for ($i = 0; $i < 50; $i++) {
            Orders::create([
                'customer_id' => $faker->randomElement($customerIds),
                'product_name' => $faker->words(3, true),
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'payment_type' => $faker->randomElement(['cash', 'transfer', 'instalment']),
                'payment_status' => $faker->randomElement(['pending', 'paid', 'cancelled']),
            ]);
        }
    }
}
