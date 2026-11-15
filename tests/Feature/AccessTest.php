<?php

use App\Models\User;

it('blocks admin from owner-only pages', function (string $route) {
    $this->actingAs(User::factory()->create())
        ->get(route($route))
        ->assertForbidden();
})->with(['dashboard', 'report', 'expense', 'team']);

it('lets admin manage operational data', function (string $route) {
    $this->actingAs(User::factory()->create())
        ->get(route($route))
        ->assertOk();
})->with(['order', 'order.create', 'procurement', 'invoice', 'customer', 'supplier']);

it('lets the owner open every page', function (string $route) {
    $this->actingAs(User::factory()->pemilik()->create())
        ->get(route($route))
        ->assertOk();
})->with(['dashboard', 'report', 'expense', 'team', 'order', 'procurement', 'invoice', 'customer', 'supplier']);

it('sends admin to orders and owner to dashboard after login', function () {
    User::factory()->create(['email' => 'admin@test.id', 'password' => 'secret123']);
    User::factory()->pemilik()->create(['email' => 'owner@test.id', 'password' => 'secret123']);

    $this->post(route('login.attempt'), ['email' => 'admin@test.id', 'password' => 'secret123'])
        ->assertRedirect(route('order'));
    $this->post(route('logout'));
    $this->post(route('login.attempt'), ['email' => 'owner@test.id', 'password' => 'secret123'])
        ->assertRedirect(route('dashboard'));
});

it('prevents the owner from deleting their own account', function () {
    $owner = User::factory()->pemilik()->create();

    $this->actingAs($owner)->delete(route('team.destroy', $owner))->assertSessionHas('error');

    expect(User::find($owner->id))->not->toBeNull();
});

it('hides owner-only menu entries from admin', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('order'))
        ->assertDontSee(route('report'))
        ->assertDontSee(route('expense'));
});
