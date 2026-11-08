<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockReconciliationController;
use App\Http\Controllers\WasteReportController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// F5: Stock Reconciliation
Route::resource('stock-reconciliations', StockReconciliationController::class)
    ->only(['index', 'create', 'store', 'show']);
Route::post('stock-reconciliations/{id}/approve', [StockReconciliationController::class, 'approve'])
    ->name('stock-reconciliations.approve');
Route::post('stock-reconciliations/{id}/reject',  [StockReconciliationController::class, 'reject'])
    ->name('stock-reconciliations.reject');

// F6: Waste Reports
Route::resource('waste-reports', WasteReportController::class)
    ->only(['index', 'create', 'store', 'show']);
Route::post('waste-reports/{id}/approve', [WasteReportController::class, 'approve'])
    ->name('waste-reports.approve');
Route::post('waste-reports/{id}/reject',  [WasteReportController::class, 'reject'])
    ->name('waste-reports.reject');
