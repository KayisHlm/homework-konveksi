<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Orders;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        if (!Orders::query()->exists()) {
            $this->call(OrderSeeder::class);
        }

        $orders = Orders::query()->orderBy('id')->take(3)->get()->values();
        $categories = [Expense::CATEGORY_JAHIT, Expense::CATEGORY_SABLON, Expense::CATEGORY_TRANSPORTASI];

        foreach ($orders as $index => $order) {
            Expense::query()->create([
                'order_id' => $order->id,
                'category' => $categories[$index % count($categories)],
                'description' => 'Biaya tambahan produksi untuk ' . $order->product_name,
                'amount' => 150000 + ($index * 50000),
                'spent_at' => ($order->created_at?->copy()->addDays(2) ?? now())->toDateString(),
            ]);
        }

        Expense::query()->create([
            'order_id' => null,
            'category' => Expense::CATEGORY_OPERASIONAL,
            'description' => 'Biaya listrik dan operasional workshop',
            'amount' => 450000,
            'spent_at' => now()->subDays(3)->toDateString(),
        ]);
    }
}
