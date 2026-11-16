<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect(auth()->user()->homeRoute())
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login/form', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Khusus pemilik: dashboard & rekap (SRS-HK-003), pengeluaran (SRS-HK-012), tim (SRS-HK-016).
    Route::middleware('role:pemilik')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/report', [ReportController::class, 'index'])->name('report');

        Route::get('/team', [TeamController::class, 'index'])->name('team');
        Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
        Route::post('/team', [TeamController::class, 'store'])->name('team.store');
        Route::get('/team/{user}/edit', [TeamController::class, 'edit'])->name('team.edit');
        Route::put('/team/{user}', [TeamController::class, 'update'])->name('team.update');
        Route::delete('/team/{user}', [TeamController::class, 'destroy'])->name('team.destroy');

        Route::get('/expense', [ExpenseController::class, 'index'])->name('expense');
        Route::get('/expense/create', [ExpenseController::class, 'create'])->name('expense.create');
        Route::post('/expense', [ExpenseController::class, 'store'])->name('expense.store');
        Route::get('/expense/{expense}/edit', [ExpenseController::class, 'edit'])->name('expense.edit');
        Route::put('/expense/{expense}', [ExpenseController::class, 'update'])->name('expense.update');
        Route::delete('/expense/{expense}', [ExpenseController::class, 'destroy'])->name('expense.destroy');
    });

    // Pemilik & admin: data operasional.
    Route::get('/order', [OrderController::class, 'index'])->name('order');
    Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{order}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/order/{order}', [OrderController::class, 'update'])->name('order.update');
    Route::delete('/order/{order}', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::patch('/order/{order}/status', [OrderController::class, 'updateStatus'])->name('order.status');
    Route::get('/order-detail/{order}', [OrderDetailController::class, 'index'])->name('order-detail');

    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement');
    Route::get('/procurement/create', [ProcurementController::class, 'create'])->name('procurement.create');
    Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
    Route::get('/procurement/{procurement}/edit', [ProcurementController::class, 'edit'])->name('procurement.edit');
    Route::put('/procurement/{procurement}', [ProcurementController::class, 'update'])->name('procurement.update');
    Route::delete('/procurement/{procurement}', [ProcurementController::class, 'destroy'])->name('procurement.destroy');
    Route::patch('/procurement/{procurement}/receive', [ProcurementController::class, 'receive'])->name('procurement.receive');
    Route::patch('/procurement/{procurement}/cancel', [ProcurementController::class, 'cancel'])->name('procurement.cancel');

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoice', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('/invoice/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::get('/invoice-detail/{invoice}', [InvoiceDetailController::class, 'index'])->name('invoice-detail');

    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
});
