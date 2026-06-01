<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        for ($i = 0; $i < 24; $i++) {
            Contact::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'city' => $faker->city(),
                'address' => $faker->streetAddress(),
            ]);
        }
    }
}

