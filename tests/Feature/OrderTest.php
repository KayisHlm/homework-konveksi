<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\OrderItemSize;
use App\Models\Orders;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function orderPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Rizky Pratama',
        'email' => 'rizky@example.com',
        'institute' => 'SMKN 2 Bandung',
        'phone' => '0812',
        'address' => 'Bandung',
        'due_date' => now()->addDays(14)->toDateString(),
        'design_notes' => 'Logo sablon dada kiri',
        'discount_amount' => 0,
        'down_payment' => 0,
        'items' => [kaosItem()],
    ], $overrides);
}

/** Kaos hitam: M 10 x 80.000 + XXL 2 x (80.000 + 10.000) = 980.000. Ukuran qty 0 diabaikan. */
function kaosItem(array $overrides = []): array
{
    return array_merge([
        'product_name' => 'Kaos', 'material' => 'Cotton 24s', 'color' => 'Hitam', 'unit_price' => 80000,
        'sizes' => [
            'M' => ['quantity' => 10, 'extra_price' => 0],
            'L' => ['quantity' => 0, 'extra_price' => 0],
            'XXL' => ['quantity' => 2, 'extra_price' => 10000],
        ],
    ], $overrides);
}

function topiItem(): array
{
    return ['product_name' => 'Topi', 'material' => 'Drill', 'color' => 'Hitam', 'unit_price' => 35000, 'sizes' => ['All Size' => ['quantity' => 20, 'extra_price' => 0]]];
}

it('stores an order with items, computed total and an order number', function () {
    $this->post(route('order.store'), orderPayload())->assertRedirect();

    $order = Orders::with('items.sizes')->sole();
    $item = $order->items->sole();
    expect($order->order_number)->toBe('ORD-'.now()->format('ym').'-0001')
        ->and($item->sizes->pluck('quantity', 'size')->all())->toBe(['M' => 10, 'XXL' => 2])
        ->and($item->quantity)->toBe(12)
        ->and((float) $item->subtotal)->toBe(980000.0)
        ->and((float) $order->total_price)->toBe(980000.0)
        ->and($order->production_status)->toBe(Orders::PRODUCTION_STATUS_PESANAN_DITERIMA)
        ->and($order->payment_status)->toBe(Orders::PAYMENT_STATUS_BELUM_BAYAR)
        ->and(Customer::count())->toBe(1);
});

it('stores several products in one order', function () {
    $this->post(route('order.store'), orderPayload(['items' => [kaosItem(), topiItem()]]))->assertRedirect();

    $order = Orders::with('items')->sole();
    expect($order->items)->toHaveCount(2)
        ->and($order->totalQuantity())->toBe(32)
        ->and((float) $order->total_price)->toBe(1680000.0)
        ->and($order->productSummary())->toBe('Kaos, Topi');
});

it('requires every product to have at least one size with a quantity', function () {
    $this->post(route('order.store'), orderPayload(['items' => [
        kaosItem(['sizes' => ['M' => ['quantity' => 0, 'extra_price' => 0]]]),
    ]]))->assertSessionHasErrors('items.0.sizes');

    expect(Orders::count())->toBe(0);
});

it('rejects sizes outside the standard list', function () {
    $this->post(route('order.store'), orderPayload(['items' => [
        kaosItem(['sizes' => ['Jumbo' => ['quantity' => 3, 'extra_price' => 0]]]),
    ]]))->assertSessionHasErrors('items.0.sizes');
});

it('applies the order discount to the total', function () {
    $this->post(route('order.store'), orderPayload(['discount_amount' => 80000]));

    expect((float) Orders::sole()->total_price)->toBe(900000.0);
});

it('records the down payment as a paid uang_muka invoice', function () {
    $this->post(route('order.store'), orderPayload(['down_payment' => 400000, 'down_payment_method' => 'transfer']));

    $order = Orders::sole();
    $invoice = $order->invoices()->sole();
    expect($invoice->type)->toBe(Invoice::TYPE_UANG_MUKA)
        ->and($invoice->status)->toBe(Invoice::STATUS_LUNAS)
        ->and((float) $invoice->amount)->toBe(400000.0)
        ->and($order->fresh()->payment_status)->toBe(Orders::PAYMENT_STATUS_SEBAGIAN)
        ->and($order->remainingBalance())->toBe(580000.0);
});

it('rejects a down payment larger than the order total', function () {
    $this->post(route('order.store'), orderPayload(['down_payment' => 5000000, 'down_payment_method' => 'tunai']))
        ->assertSessionHasErrors('down_payment');

    expect(Orders::count())->toBe(0);
});

it('requires at least one item', function () {
    $this->post(route('order.store'), orderPayload(['items' => []]))
        ->assertSessionHasErrors('items');
});

it('replaces items on update and recalculates the total', function () {
    $this->post(route('order.store'), orderPayload());
    $order = Orders::sole();

    $this->put(route('order.update', $order), orderPayload([
        'items' => [['product_name' => 'Jaket', 'material' => 'Taslan', 'color' => 'Navy', 'unit_price' => 200000, 'sizes' => ['L' => ['quantity' => 5, 'extra_price' => 0]]]],
    ]))->assertRedirect();

    expect($order->fresh()->items)->toHaveCount(1)
        ->and($order->fresh()->items->sole()->product_name)->toBe('Jaket')
        ->and(OrderItemSize::count())->toBe(1)
        ->and((float) $order->fresh()->total_price)->toBe(1000000.0);
});

it('rejects an update that makes the total lower than the invoiced amount', function () {
    $this->post(route('order.store'), orderPayload(['down_payment' => 900000, 'down_payment_method' => 'tunai']));
    $order = Orders::sole();

    $this->put(route('order.update', $order), orderPayload([
        'items' => [['product_name' => 'Kaos', 'material' => 'Cotton', 'color' => null, 'unit_price' => 100000, 'sizes' => ['M' => ['quantity' => 1, 'extra_price' => 0]]]],
    ]))->assertSessionHasErrors('items');

    expect((float) $order->fresh()->total_price)->toBe(980000.0);
});

it('advances production status one step at a time only', function () {
    $this->post(route('order.store'), orderPayload());
    $order = Orders::sole();

    $this->patch(route('order.status', $order), ['production_status' => Orders::PRODUCTION_STATUS_SELESAI])
        ->assertSessionHasErrors('production_status');

    $this->patch(route('order.status', $order), ['production_status' => Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN])
        ->assertSessionHasNoErrors();

    expect($order->fresh()->production_status)->toBe(Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN);
});

it('refuses to delete an order that already has invoices', function () {
    $this->post(route('order.store'), orderPayload(['down_payment' => 100000, 'down_payment_method' => 'tunai']));
    $order = Orders::sole();

    $this->delete(route('order.destroy', $order))->assertSessionHas('error');

    expect(Orders::count())->toBe(1);
});

it('deletes an order without invoices together with its items', function () {
    $this->post(route('order.store'), orderPayload());

    $this->delete(route('order.destroy', Orders::sole()))->assertRedirect(route('order'));

    expect(Orders::count())->toBe(0)->and(OrderItem::count())->toBe(0)->and(OrderItemSize::count())->toBe(0);
});

it('prefills the edit form with each product size breakdown', function () {
    $this->post(route('order.store'), orderPayload(['items' => [kaosItem(), topiItem()]]));

    $this->get(route('order.edit', Orders::sole()))
        ->assertOk()
        ->assertSee('name="items[0][sizes][XXL][quantity]" class="form-control form-control-sm text-center size-qty" value="2"', false)
        ->assertSee('name="items[0][sizes][XXL][extra_price]" class="form-control form-control-sm text-center size-extra" value="10000"', false)
        ->assertSee('name="items[1][sizes][All Size][quantity]" class="form-control form-control-sm text-center size-qty" value="20"', false);
});

it('keeps every entered product on the form after a validation error', function () {
    $this->from(route('order.create'))
        ->post(route('order.store'), orderPayload(['items' => [kaosItem(), topiItem(), kaosItem(['product_name' => ''])]]))
        ->assertRedirect(route('order.create'))
        ->assertSessionHasErrors('items.2.product_name');

    $this->get(route('order.create'))
        ->assertSee('name="items[2][product_name]"', false)
        ->assertSee('value="Topi"', false);
});

it('rejects fractional or negative size quantities instead of silently dropping them', function (mixed $quantity) {
    $this->post(route('order.store'), orderPayload(['items' => [
        kaosItem(['sizes' => ['M' => ['quantity' => $quantity, 'extra_price' => 0]]]),
    ]]))->assertSessionHasErrors('items.0.sizes.M.quantity');
})->with(['2.5', '-3', 'abc']);

it('rejects amounts too large for the money columns', function () {
    $this->post(route('order.store'), orderPayload(['items' => [kaosItem(['unit_price' => 8000000000])]]))
        ->assertSessionHasErrors('items.0.unit_price');
});

it('lets money inputs accept any whole rupiah amount in the browser', function () {
    $html = $this->get(route('order.create'))->getContent();

    expect($html)->not->toContain('step="500"');
});
