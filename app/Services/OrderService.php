<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Orders;
use Illuminate\Support\Facades\DB;

/**
 * Menyimpan pesanan beserta pelanggan, item, dan uang muka dalam satu transaksi.
 */
class OrderService
{
    public function save(array $data, ?Orders $order = null): Orders
    {
        return DB::transaction(function () use ($data, $order) {
            $customer = $this->upsertCustomer($data);

            $order ??= new Orders;
            $order->fill([
                'customer_id' => $customer->id,
                'design_notes' => $data['design_notes'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ])->save();

            // Item & rincian ukuran diganti utuh setiap kali pesanan disimpan (komposisi).
            $order->items()->delete();
            foreach ($data['items'] as $itemData) {
                $item = $order->items()->create($itemData);
                foreach ($itemData['sizes'] as $size => $detail) {
                    $item->sizes()->create(['size' => $size, ...$detail]);
                }
                $item->recalculate();
            }
            $order->recalculateTotal();

            if ((float) ($data['down_payment'] ?? 0) > 0) {
                $order->invoices()->create([
                    'type' => Invoice::TYPE_UANG_MUKA,
                    'amount' => $data['down_payment'],
                    'issued_at' => now()->toDateString(),
                    'status' => Invoice::STATUS_LUNAS,
                    'paid_at' => now()->toDateString(),
                    'payment_method' => $data['down_payment_method'],
                    'notes' => 'Uang muka dicatat saat pesanan dibuat.',
                ]);
            }

            $order->syncPaymentStatus();

            return $order;
        });
    }

    protected function upsertCustomer(array $data): Customer
    {
        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'institute' => $data['institute'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ];

        if (! empty($data['customer_id'])) {
            $customer = Customer::query()->findOrFail($data['customer_id']);
            $customer->update($attributes);

            return $customer;
        }

        return Customer::query()->updateOrCreate(['email' => $data['email']], $attributes);
    }
}
