<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Orders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (!Orders::query()->exists()) {
            $this->call(OrderSeeder::class);
        }

        $orders = Orders::query()->with('customer')->orderBy('id')->take(5)->get()->values();
        $statuses = ['paid', 'sent', 'unpaid', 'draft', 'cancelled'];

        foreach ($orders as $index => $order) {
            $issuedAt = $order->created_at?->copy()->addDays(3) ?? now();
            $status = $statuses[$index % count($statuses)];
            $subtotal = (float) $order->total_price;
            $discount = $index === 0 ? 50000 : 0;
            $total = max(0, $subtotal - $discount);

            $invoice = Invoice::query()->create([
                'invoice_number' => 'INV-' . $issuedAt->format('ym') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'issued_at' => $issuedAt,
                'due_date' => $issuedAt->copy()->addDays(14)->toDateString(),
                'payment_status' => $status,
                'payment_via' => $status === 'paid' ? 'Transfer BCA' : 'Transfer Bank',
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'notes' => 'Invoice sample untuk order ' . $order->order_number,
            ]);

            DB::table('invoice_items')->insert([
                'invoice_id' => $invoice->id,
                'product_name' => $order->product_name,
                'quantity' => $order->quantity,
                'unit_price' => $order->unit_price,
                'line_total' => $subtotal,
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ]);
        }
    }
}
