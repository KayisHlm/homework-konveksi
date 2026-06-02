<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Orders;
use App\Models\Suppliers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        InvoiceItem::query()->truncate();
        Invoice::query()->truncate();
        Orders::query()->truncate();
        Contact::query()->truncate();
        Suppliers::query()->truncate();
        Customer::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            ContactSeeder::class,
            SupplierSeeder::class,
            OrderSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
