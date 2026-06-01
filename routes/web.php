<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\CustomerController;



Route::get('/', function () {
    return view('Page.welcome');
});
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Team
Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
Route::get('/team', [TeamController::class, 'index'])->name('team');
// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
// Order
Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
Route::get('/order', [OrderController::class, 'index'])->name('order');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
// Order Detail
Route::get('/order-detail/{order}', [OrderDetailController::class, 'index'])->name('order-detail');
// Schedule
// Supplier
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::get('/supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
// Invoice
Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
// Invoice Detail
Route::get('/invoice-detail/{invoice}', [InvoiceDetailController::class, 'index'])->name('invoice-detail');
// Customer
Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
