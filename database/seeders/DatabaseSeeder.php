<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Orders;
use App\Models\Procurement;
use App\Models\Suppliers;
use App\Models\User;
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
        User::query()->truncate();
        InvoiceItem::query()->truncate();
        Invoice::query()->truncate();
        Expense::query()->truncate();
        Procurement::query()->truncate();
        Orders::query()->truncate();
        Suppliers::query()->truncate();
        Customer::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            OrderSeeder::class,
            ProcurementSeeder::class,
            ExpenseSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
