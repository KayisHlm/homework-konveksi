<?php

use App\Models\Customer;
use App\Models\Procurement;
use App\Models\Suppliers;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->customer = Customer::create(['name' => 'Nadia', 'email' => 'nadia@example.com', 'institute' => 'Univ', 'phone' => '1', 'address' => 'x']);
    $this->supplier = Suppliers::create(['pic_name' => 'Hendra', 'store_name' => 'CV Drill', 'product_category' => 'Kain', 'phone' => '1', 'address' => 'x']);
});

it('deletes a customer without orders', function () {
    $this->delete(route('customer.destroy', $this->customer))
        ->assertRedirect(route('customer'))
        ->assertSessionHas('success');

    expect(Customer::count())->toBe(0);
});

it('refuses to delete a customer who still has orders', function () {
    orderWithProduct($this->customer->id, ['M' => 1], 50000);

    $this->delete(route('customer.destroy', $this->customer))->assertSessionHas('error');

    expect(Customer::count())->toBe(1);
});

it('deletes a supplier without procurements', function () {
    $this->delete(route('supplier.destroy', $this->supplier))
        ->assertRedirect(route('supplier'))
        ->assertSessionHas('success');

    expect(Suppliers::count())->toBe(0);
});

it('refuses to delete a supplier that still has procurements', function () {
    $order = orderWithProduct($this->customer->id, ['M' => 1], 50000);
    Procurement::create([
        'order_id' => $order->id, 'supplier_id' => $this->supplier->id, 'material_name' => 'Drill',
        'quantity' => 1, 'unit' => 'meter', 'unit_price' => 1000, 'ordered_at' => now()->toDateString(), 'status' => Procurement::STATUS_DIPESAN,
    ]);

    $this->delete(route('supplier.destroy', $this->supplier))->assertSessionHas('error');

    expect(Suppliers::count())->toBe(1);
});

it('shows a working delete button only when deletion is allowed', function () {
    orderWithProduct($this->customer->id, ['M' => 1], 50000);
    Customer::create(['name' => 'Bebas', 'email' => 'bebas@example.com', 'institute' => 'U', 'phone' => '2', 'address' => 'y']);

    $this->get(route('customer'))
        ->assertSee('action="'.route('customer.destroy', Customer::where('email', 'bebas@example.com')->first()).'"', false)
        ->assertDontSee('action="'.route('customer.destroy', $this->customer).'"', false);
});
