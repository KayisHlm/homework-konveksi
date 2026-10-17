<?php

namespace Database\Seeders;

use App\Models\Orders;
use App\Models\Procurement;
use App\Models\Suppliers;
use Illuminate\Database\Seeder;

class ProcurementSeeder extends Seeder
{
    public function run(): void
    {
        if (!Orders::query()->exists()) {
            $this->call(OrderSeeder::class);
        }

        if (!Suppliers::query()->exists()) {
            $this->call(SupplierSeeder::class);
        }

        $orders = Orders::query()->orderBy('id')->take(4)->get()->values();
        $suppliers = Suppliers::query()->orderBy('id')->take(4)->get()->values();

        foreach ($orders as $index => $order) {
            $orderedAt = $order->created_at?->copy()->addDays(1) ?? now();
            $expectedAt = $orderedAt->copy()->addDays(4);
            $receivedAt = $index % 2 === 0 ? $expectedAt->copy()->addDay() : null;
            $status = $receivedAt ? Procurement::STATUS_DITERIMA : Procurement::STATUS_DIPESAN;

            Procurement::query()->create([
                'order_id' => $order->id,
                'supplier_id' => $suppliers[$index % $suppliers->count()]->id,
                'material_name' => $order->material,
                'quantity' => $order->quantity,
                'unit' => 'pcs',
                'unit_price' => max(10000, (float) $order->unit_price * 0.35),
                'total_cost' => 0,
                'ordered_at' => $orderedAt->toDateString(),
                'expected_at' => $expectedAt->toDateString(),
                'received_at' => $receivedAt?->toDateString(),
                'status' => $status,
                'notes' => 'Pengadaan otomatis dari seeder.',
            ]);
        }
    }
}
