<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

/**
 * Pengeluaran operasional yang tidak terhubung ke pesanan mana pun (listrik, sewa, dll.).
 * Pengeluaran per pesanan dibuat oleh OrderSeeder melalui pengadaan.
 */
class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(5, 0) as $monthsAgo) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            Expense::create([
                'category' => Expense::CATEGORY_OPERASIONAL,
                'description' => 'Listrik & air workshop '.$month->translatedFormat('F Y'),
                'amount' => 450000 + ($monthsAgo * 15000),
                'spent_at' => $month->copy()->addDays(4)->toDateString(),
            ]);
        }

        Expense::create([
            'category' => Expense::CATEGORY_TRANSPORTASI,
            'description' => 'Bensin & parkir ambil bahan ke Cigondewah',
            'amount' => 175000,
            'spent_at' => now()->subDays(9)->toDateString(),
        ]);

        Expense::create([
            'category' => Expense::CATEGORY_LAINNYA,
            'description' => 'Servis mesin obras',
            'amount' => 350000,
            'spent_at' => now()->subDays(40)->toDateString(),
        ]);
    }
}
