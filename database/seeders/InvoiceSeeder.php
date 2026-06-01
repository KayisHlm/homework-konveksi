<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Customer::query()->count() === 0) {
            $this->call(CustomerSeeder::class);
        }

        $faker = fake();
        $customers = Customer::query()->pluck('id')->all();
        $paymentVias = ['Cash', 'Transfer', 'PayPal', 'Credit Card'];
        $statuses = ['draft', 'sent', 'unpaid', 'paid', 'cancelled'];

        for ($i = 0; $i < 30; $i++) {
            $issuedAt = $faker->dateTimeBetween('-60 days', 'now');
            $dueDate = (clone $issuedAt);
            $dueDate->modify('+' . $faker->numberBetween(7, 30) . ' days');

            $invoice = Invoice::create([
                'invoice_number' => 'RB' . $faker->unique()->numberBetween(10000, 99999),
                'customer_id' => $faker->randomElement($customers),
                'issued_at' => $issuedAt,
                'due_date' => $dueDate->format('Y-m-d'),
                'payment_status' => $faker->randomElement($statuses),
                'payment_via' => $faker->randomElement($paymentVias),
                'notes' => $faker->optional(0.6)->paragraph(),
            ]);

            $itemCount = $faker->numberBetween(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $quantity = $faker->numberBetween(1, 5);
                $unitPrice = $faker->randomFloat(2, 25, 500);
                $lineTotal = round($quantity * $unitPrice, 2);
                $subtotal += $lineTotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_name' => $faker->words(3, true),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $discount = $faker->boolean(35) ? round($subtotal * 0.1, 2) : 0;
            $total = round($subtotal - $discount, 2);

            $invoice->update([
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
            ]);
        }
    }
}

