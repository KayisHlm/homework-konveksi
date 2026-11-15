<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Orders;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $customer = Customer::create(['name' => 'Nadia', 'email' => 'nadia@example.com', 'institute' => 'Univ', 'phone' => '1', 'address' => 'x']);
    $this->order = orderWithProduct($customer->id, ['M' => 10], 100000);
});

function invoicePayload(Orders $order, array $overrides = []): array
{
    return array_merge([
        'order_id' => $order->id,
        'type' => Invoice::TYPE_PELUNASAN,
        'amount' => 1000000,
        'issued_at' => now()->toDateString(),
        'due_date' => now()->addDays(7)->toDateString(),
        'status' => Invoice::STATUS_BELUM_DIBAYAR,
    ], $overrides);
}

it('creates an invoice with a generated number', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order))->assertRedirect();

    expect(Invoice::sole()->invoice_number)->toBe('INV-'.now()->format('ym').'-0001');
});

it('rejects invoices that would bill more than the order total', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order, ['type' => Invoice::TYPE_UANG_MUKA, 'amount' => 600000]));

    $this->post(route('invoice.store'), invoicePayload($this->order, ['amount' => 500000]))
        ->assertSessionHasErrors('amount');

    expect(Invoice::count())->toBe(1);
});

it('marks the order lunas once paid invoices cover the total', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order, [
        'status' => Invoice::STATUS_LUNAS, 'paid_at' => now()->toDateString(), 'payment_method' => Invoice::METHOD_TRANSFER,
    ]));

    expect($this->order->fresh()->payment_status)->toBe(Orders::PAYMENT_STATUS_LUNAS);
});

it('requires payment date and method for a paid invoice', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order, ['status' => Invoice::STATUS_LUNAS]))
        ->assertSessionHasErrors(['paid_at', 'payment_method']);
});

it('downgrades the order payment status when a paid invoice is cancelled', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order, [
        'status' => Invoice::STATUS_LUNAS, 'paid_at' => now()->toDateString(), 'payment_method' => Invoice::METHOD_TUNAI,
    ]));
    $invoice = Invoice::sole();

    $this->put(route('invoice.update', $invoice), invoicePayload($this->order, ['status' => Invoice::STATUS_DIBATALKAN]))
        ->assertSessionHasNoErrors();

    expect($this->order->fresh()->payment_status)->toBe(Orders::PAYMENT_STATUS_BELUM_BAYAR);
});

it('lets an invoice be edited without counting its own amount twice', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order));

    $this->put(route('invoice.update', Invoice::sole()), invoicePayload($this->order, ['amount' => 1000000, 'notes' => 'revisi']))
        ->assertSessionHasNoErrors();
});

it('resyncs both orders when an invoice is moved to another order', function () {
    $other = orderWithProduct($this->order->customer_id, ['All Size' => 10], 50000, 'Topi');

    $this->post(route('invoice.store'), invoicePayload($this->order, [
        'amount' => 500000, 'status' => Invoice::STATUS_LUNAS, 'paid_at' => now()->toDateString(), 'payment_method' => Invoice::METHOD_TUNAI,
    ]));

    $this->put(route('invoice.update', Invoice::sole()), invoicePayload($other, [
        'amount' => 500000, 'status' => Invoice::STATUS_LUNAS, 'paid_at' => now()->toDateString(), 'payment_method' => Invoice::METHOD_TUNAI,
    ]))->assertSessionHasNoErrors();

    expect($this->order->fresh()->payment_status)->toBe(Orders::PAYMENT_STATUS_BELUM_BAYAR)
        ->and($other->fresh()->payment_status)->toBe(Orders::PAYMENT_STATUS_LUNAS);
});

it('lets the amount input accept round amounts in the browser', function () {
    $html = $this->get(route('invoice.create', ['order_id' => $this->order->id]))->getContent();

    expect($html)->not->toContain('min="1" step="500"')->not->toContain('step="500"');
});

it('ignores a malformed order_id on the create page', function () {
    $this->get(route('invoice.create').'?order_id[]=1')->assertOk();
});

it('shows only earlier payments as previous payments on the invoice sheet', function () {
    $paid = ['status' => Invoice::STATUS_LUNAS, 'payment_method' => Invoice::METHOD_TUNAI];
    $this->post(route('invoice.store'), invoicePayload($this->order, [...$paid, 'type' => Invoice::TYPE_UANG_MUKA, 'amount' => 400000, 'issued_at' => now()->subDays(10)->toDateString(), 'paid_at' => now()->subDays(10)->toDateString(), 'due_date' => null]));
    $this->post(route('invoice.store'), invoicePayload($this->order, [...$paid, 'amount' => 600000, 'paid_at' => now()->toDateString()]));
    [$dp, $final] = Invoice::orderBy('id')->get();

    $this->get(route('invoice-detail', $dp))->assertViewHas('paidBefore', 0.0)->assertViewHas('remainingAfter', 600000.0);
    $this->get(route('invoice-detail', $final))->assertViewHas('paidBefore', 400000.0)->assertViewHas('remainingAfter', 0.0);
});

it('does not subtract a cancelled invoice from the remaining balance on its sheet', function () {
    $this->post(route('invoice.store'), invoicePayload($this->order, ['status' => Invoice::STATUS_DIBATALKAN, 'amount' => 300000]));

    $this->get(route('invoice-detail', Invoice::sole()))->assertViewHas('remainingAfter', 1000000.0);
});
