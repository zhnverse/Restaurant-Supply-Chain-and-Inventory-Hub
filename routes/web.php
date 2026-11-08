<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockReconciliationController;
use App\Http\Controllers\WasteReportController;
use Illuminate\Support\Facades\Route;

// ── Authentication ──
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('login/quick/{role}', [AuthController::class, 'quickLogin'])->name('login.quick');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ── Application Routes ──
Route::middleware('web')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── F1: Recipe-Based Inventory Deduction ──
    Route::resource('dishes', DishController::class)->only(['index', 'create', 'store']);
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store']);

    // ── F2: Batch Expiry Monitoring ──
    Route::resource('batches', BatchController::class)->only(['index', 'create', 'store']);

    // ── F3 & F4: Automatic Reorder Drafting & Purchase-Order Approval ──
    Route::post('purchase-orders/auto-draft', [PurchaseOrderController::class, 'autoDraft'])
        ->name('purchase-orders.auto-draft');
    Route::post('purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve'])
        ->name('purchase-orders.approve');
    Route::post('purchase-orders/{id}/reject', [PurchaseOrderController::class, 'reject'])
        ->name('purchase-orders.reject');
    Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create', 'store', 'show']);

    // ── F5: Stock Reconciliation ──
    Route::resource('stock-reconciliations', StockReconciliationController::class)
        ->only(['index', 'create', 'store', 'show']);
    Route::post('stock-reconciliations/{id}/approve', [StockReconciliationController::class, 'approve'])
        ->name('stock-reconciliations.approve');
    Route::post('stock-reconciliations/{id}/reject',  [StockReconciliationController::class, 'reject'])
        ->name('stock-reconciliations.reject');

    // ── F6: Waste Reports ──
    Route::resource('waste-reports', WasteReportController::class)
        ->only(['index', 'create', 'store', 'show']);
    Route::post('waste-reports/{id}/approve', [WasteReportController::class, 'approve'])
        ->name('waste-reports.approve');
    Route::post('waste-reports/{id}/reject',  [WasteReportController::class, 'reject'])
        ->name('waste-reports.reject');
});
