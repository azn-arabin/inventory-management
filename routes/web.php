<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Product routes
Route::resource('products', ProductController::class);

// Sale routes
Route::resource('sales', SaleController::class);

// Payment routes (collect due payments)
Route::get('/sales/{sale}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/sales/{sale}/payments', [PaymentController::class, 'store'])->name('payments.store');

// Report routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    Route::get('/journal-entries', [ReportController::class, 'journalEntries'])->name('journal_entries');
    Route::get('/chart-of-accounts', [ReportController::class, 'chartOfAccounts'])->name('chart_of_accounts');
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
});
