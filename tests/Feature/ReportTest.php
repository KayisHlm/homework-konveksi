<?php

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-11-15 10:00'));
    $this->actingAs(User::factory()->pemilik()->create());

    $customer = Customer::create(['name' => 'Nadia', 'email' => 'nadia@example.com', 'institute' => 'Univ', 'phone' => '1', 'address' => 'x']);
    $this->order = orderWithProduct($customer->id, ['M' => 10], 100000);

    $pay = fn (string $date, int $amount, string $status = Invoice::STATUS_LUNAS) => Invoice::create([
        'order_id' => $this->order->id, 'type' => Invoice::TYPE_TERMIN, 'amount' => $amount, 'issued_at' => $date,
        'status' => $status, 'paid_at' => $date, 'payment_method' => Invoice::METHOD_TRANSFER,
    ]);
    $pay('2026-09-20', 100000);
    $pay('2026-10-05', 200000);
    $pay('2026-11-10', 300000);
    $pay('2026-11-11', 50000, Invoice::STATUS_BELUM_DIBAYAR);

    Expense::create(['category' => Expense::CATEGORY_OPERASIONAL, 'description' => 'Listrik', 'amount' => 40000, 'spent_at' => '2026-10-03']);
    Expense::create(['category' => Expense::CATEGORY_JAHIT, 'description' => 'Jahit', 'amount' => 70000, 'spent_at' => '2026-11-12', 'order_id' => $this->order->id]);
});

it('is only available to the owner', function () {
    $this->actingAs(User::factory()->create())->get(route('report'))->assertForbidden();
    $this->actingAs(User::factory()->pemilik()->create())->get(route('report'))->assertOk();
});

it('builds a monthly recap for the chosen range with one row per month', function () {
    $response = $this->get(route('report', ['periode' => 'bulanan', 'dari' => '2026-10-01', 'sampai' => '2026-11-01']))->assertOk();

    $recap = $response->viewData('recap');
    expect($recap['rows'])->toHaveCount(2)
        ->and($recap['rows'][0]['income'])->toBe(200000.0)
        ->and($recap['rows'][0]['expense'])->toBe(40000.0)
        ->and($recap['rows'][1]['income'])->toBe(300000.0)
        ->and($recap['rows'][1]['expense'])->toBe(70000.0)
        ->and($recap['totals']['income'])->toBe(500000.0)
        ->and($recap['totals']['expense'])->toBe(110000.0)
        ->and($recap['totals']['profit'])->toBe(390000.0)
        ->and($recap['payments'])->toHaveCount(2)
        ->and($recap['expensesByCategory']->all())->toBe(['jahit' => 70000.0, 'operasional' => 40000.0]);
});

it('snaps a weekly range to whole Monday-to-Sunday weeks', function () {
    // 12 Nov 2026 is a Thursday: the week runs from Mon 9 Nov to Sun 15 Nov.
    $recap = $this->get(route('report', ['periode' => 'mingguan', 'dari' => '2026-11-12', 'sampai' => '2026-11-12']))->viewData('recap');

    expect($recap['from']->toDateString())->toBe('2026-11-09')
        ->and($recap['to']->toDateString())->toBe('2026-11-15')
        ->and($recap['rows'])->toHaveCount(1)
        ->and($recap['rows'][0]['label'])->toBe('Mg 46 (9–15 Nov 2026)')
        ->and($recap['totals']['income'])->toBe(300000.0);
});

it('builds a daily recap for a single day', function () {
    $recap = $this->get(route('report', ['periode' => 'harian', 'dari' => '2026-11-10', 'sampai' => '2026-11-10']))->viewData('recap');

    expect($recap['rows'])->toHaveCount(1)->and($recap['totals']['income'])->toBe(300000.0)->and($recap['totals']['expense'])->toBe(0.0);
});

it('builds a yearly recap', function () {
    $recap = $this->get(route('report', ['periode' => 'tahunan', 'dari' => '2025-01-01', 'sampai' => '2026-01-01']))->viewData('recap');

    expect($recap['rows'])->toHaveCount(2)
        ->and($recap['rows'][0]['label'])->toBe('2025')
        ->and($recap['rows'][1]['income'])->toBe(600000.0);
});

it('swaps a reversed range instead of failing', function () {
    $recap = $this->get(route('report', ['periode' => 'bulanan', 'dari' => '2026-11-01', 'sampai' => '2026-09-01']))->viewData('recap');

    expect($recap['from']->toDateString())->toBe('2026-09-01')->and($recap['rows'])->toHaveCount(3);
});

it('rejects a daily range longer than 93 days', function () {
    $this->get(route('report', ['periode' => 'harian', 'dari' => '2026-01-01', 'sampai' => '2026-11-01']))
        ->assertRedirect(route('report'))
        ->assertSessionHasErrors('sampai');
});

it('defaults to the last six months and renders the printable sheet', function () {
    $response = $this->get(route('report'))->assertOk();

    expect($response->viewData('recap')['rows'])->toHaveCount(6);
    $response->assertSee('Rekap Keuangan Bulanan')->assertSee('Jun 2026 – Nov 2026')->assertSee('window.print()', false);
});

it('lists orders created in the range', function () {
    $recap = $this->get(route('report', ['periode' => 'bulanan', 'dari' => '2026-11-01', 'sampai' => '2026-11-01']))->viewData('recap');

    expect($recap['orders']->pluck('id')->all())->toBe([$this->order->id])
        ->and($recap['totals']['orders'])->toBe(1);
});

it('uses a sensible default range for every recap type', function (string $period, int $rows) {
    $this->get(route('report', ['periode' => $period]))->assertOk()->assertViewHas('recap', fn ($recap) => count($recap['rows']) === $rows);
})->with([['harian', 7], ['mingguan', 4], ['bulanan', 6], ['tahunan', 2]]);
