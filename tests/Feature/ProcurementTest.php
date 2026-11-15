<?php

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Orders;
use App\Models\Procurement;
use App\Models\Suppliers;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->order = Orders::create(['customer_id' => Customer::create([
        'name' => 'Nadia', 'email' => 'nadia@example.com', 'institute' => 'Univ', 'phone' => '1', 'address' => 'x',
    ])->id]);
    $this->supplier = Suppliers::create([
        'pic_name' => 'Hendra', 'store_name' => 'CV Drill', 'product_category' => 'Kain', 'phone' => '1', 'address' => 'x',
    ]);
});

function procurementPayload(Orders $order, Suppliers $supplier, array $overrides = []): array
{
    return array_merge([
        'order_id' => $order->id,
        'supplier_id' => $supplier->id,
        'material_name' => 'Drill',
        'quantity' => 20,
        'unit' => 'meter',
        'unit_price' => 35000,
        'ordered_at' => now()->toDateString(),
        'expected_at' => now()->addDays(3)->toDateString(),
    ], $overrides);
}

it('records a raw-material expense linked to the same order', function () {
    $this->post(route('procurement.store'), procurementPayload($this->order, $this->supplier))->assertRedirect();

    $expense = Expense::sole();
    expect($expense->category)->toBe(Expense::CATEGORY_BAHAN_BAKU)
        ->and($expense->order_id)->toBe($this->order->id)
        ->and($expense->procurement_id)->toBe(Procurement::sole()->id)
        ->and((float) $expense->amount)->toBe(700000.0)
        ->and(Procurement::sole()->status)->toBe(Procurement::STATUS_DIPESAN)
        ->and($this->order->fresh()->production_status)->toBe(Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN);
});

it('does not move an order backwards when procuring more material', function () {
    $this->order->forceFill(['production_status' => Orders::PRODUCTION_STATUS_PROSES_PRODUKSI])->save();

    $this->post(route('procurement.store'), procurementPayload($this->order, $this->supplier));

    expect($this->order->fresh()->production_status)->toBe(Orders::PRODUCTION_STATUS_PROSES_PRODUKSI);
});

it('confirms receipt with a received date', function () {
    $this->post(route('procurement.store'), procurementPayload($this->order, $this->supplier));

    $this->patch(route('procurement.receive', Procurement::sole()));

    expect(Procurement::sole()->status)->toBe(Procurement::STATUS_DITERIMA)
        ->and(Procurement::sole()->received_at)->not->toBeNull();
});

it('removes the expense when a procurement is cancelled', function () {
    $this->post(route('procurement.store'), procurementPayload($this->order, $this->supplier));

    $this->patch(route('procurement.cancel', Procurement::sole()));

    expect(Procurement::sole()->status)->toBe(Procurement::STATUS_DIBATALKAN)
        ->and(Expense::count())->toBe(0);
});

it('moves the new order to menunggu_bahan when a procurement is reassigned', function () {
    $this->post(route('procurement.store'), procurementPayload($this->order, $this->supplier));
    $other = Orders::create(['customer_id' => $this->order->customer_id]);

    $this->put(route('procurement.update', Procurement::sole()), procurementPayload($other, $this->supplier))->assertSessionHasNoErrors();

    expect($other->fresh()->production_status)->toBe(Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN)
        ->and(Expense::sole()->order_id)->toBe($other->id);
});

it('keeps the received date when editing a received procurement without one', function () {
    $this->post(route('procurement.store'), procurementPayload($this->order, $this->supplier));
    $this->patch(route('procurement.receive', Procurement::sole()));

    $this->put(route('procurement.update', Procurement::sole()), procurementPayload($this->order, $this->supplier, ['received_at' => '']));

    expect(Procurement::sole()->received_at)->not->toBeNull();
});
