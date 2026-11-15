<?php

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Orders;
use App\Models\Procurement;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(fn () => $this->seed(DatabaseSeeder::class));

it('renders every page with seeded data for the owner', function () {
    $this->actingAs(User::where('role', User::ROLE_PEMILIK)->first());

    $urls = [
        route('dashboard'),
        ...array_map(fn ($p) => route('dashboard', ['periode' => $p]), ['harian', 'mingguan', 'tahunan']),
        ...array_map(fn ($p) => route('report', ['periode' => $p]), ['harian', 'mingguan', 'bulanan', 'tahunan']),
        route('order'), route('order.create'), route('procurement'), route('procurement.create'),
        route('invoice'), route('invoice.create'), route('expense'), route('expense.create'),
        route('customer'), route('supplier'), route('team'), route('team.create'),
        route('invoice.create', ['order_id' => Orders::first()->id]),
        route('expense.edit', Expense::whereNull('procurement_id')->first()),
    ];
    foreach (Orders::all() as $order) {
        $urls[] = route('order-detail', $order);
        $urls[] = route('order.edit', $order);
    }
    foreach (Invoice::all() as $invoice) {
        $urls[] = route('invoice-detail', $invoice);
        $urls[] = route('invoice.edit', $invoice);
    }
    foreach (Procurement::all() as $procurement) {
        $urls[] = route('procurement.edit', $procurement);
    }

    foreach ($urls as $url) {
        expect($this->get($url)->status())->toBe(200, $url);
    }
});

it('hides profit figures from admin on the order detail page', function () {
    $this->actingAs(User::where('role', User::ROLE_ADMIN)->first())
        ->get(route('order-detail', Orders::first()))
        ->assertOk()
        ->assertDontSee('Laba:')
        ->assertDontSee(route('expense.create', ['order_id' => Orders::first()->id]));
});
