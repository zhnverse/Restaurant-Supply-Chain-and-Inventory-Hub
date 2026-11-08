@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="fa-solid fa-gauge me-2 text-primary"></i>Restaurant Inventory &amp; Supply Hub</h1>
        <p>Operational control center for recipes, sales deductions, expiry tracking, procurement, reconciliation, and waste.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-success">
            <i class="fa-solid fa-cash-register me-1"></i> POS Sale
        </a>
        <form method="POST" action="{{ route('purchase-orders.auto-draft') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-info text-dark fw-bold">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Reorder Check
            </button>
        </form>
        <a href="{{ route('stock-reconciliations.create') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-scale-balanced me-1"></i> Stock Count
        </a>
        <a href="{{ route('waste-reports.create') }}" class="btn btn-danger">
            <i class="fa-solid fa-trash-can me-1"></i> Report Waste
        </a>
    </div>
</div>

<!-- Primary Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-number">{{ $stats['total_ingredients'] }}</div>
            <div class="stat-label">Ingredients</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="stat-number">৳{{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">Sales Revenue</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card {{ $stats['expiring_soon_batches'] > 0 ? 'stat-red' : 'stat-teal' }}">
            <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="stat-number">{{ $stats['expiring_soon_batches'] }}</div>
            <div class="stat-label">Expiring Soon</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="stat-number">{{ $stats['draft_purchase_orders'] }}</div>
            <div class="stat-label">Draft POs</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fa-solid fa-scale-balanced"></i></div>
            <div class="stat-number">{{ $stats['pending_reconciliations'] }}</div>
            <div class="stat-label">Pending Counts</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card stat-red">
            <div class="stat-icon"><i class="fa-solid fa-trash-can"></i></div>
            <div class="stat-number">{{ $stats['pending_waste_reports'] }}</div>
            <div class="stat-label">Waste Reports</div>
        </div>
    </div>
</div>

<!-- Critical Expiry Alert Banner -->
@if($expiringSoonBatches->isNotEmpty())
    <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-danger"></i>
            <div>
                <strong>Batch Expiry Warning:</strong>
                <strong>{{ $expiringSoonBatches->count() }} ingredient batch(es)</strong> are expiring within the next 7 days!
                <div class="small text-muted">Examine batches and prioritize usage or record waste to avoid contamination.</div>
            </div>
        </div>
        <a href="{{ route('batches.index', ['status' => 'warning']) }}" class="btn btn-sm btn-danger text-nowrap">
            <i class="fa-solid fa-eye me-1"></i> Review Expiring Batches
        </a>
    </div>
@endif

<!-- Row: Recent Sales & Low Stock Alerts -->
<div class="row g-4 mb-4">
    <!-- Recent POS Sales -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-receipt me-2 text-success"></i>Recent Kitchen Sales &amp; Recipe Deductions</span>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-success">View All Sales</a>
            </div>
            <div class="card-body p-0">
                @if($recentSales->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-cash-register fa-2x opacity-25 mb-2"></i>
                        <p class="small mb-0">No sales recorded yet. Click POS Sale to record an order.</p>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Dish</th>
                                <th>Portions</th>
                                <th>Revenue</th>
                                <th>Deductions</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentSales as $sale)
                            <tr>
                                <td><span class="fw-bold text-primary">#ORD-{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                <td><div class="fw-semibold">{{ $sale->dish->name ?? 'Dish' }}</div></td>
                                <td><span class="badge bg-light text-dark">{{ $sale->quantity }}x</span></td>
                                <td><span class="fw-bold text-success">৳{{ number_format($sale->total_amount, 2) }}</span></td>
                                <td>
                                    <small class="text-muted">{{ $sale->deductions->count() }} ingredients</small>
                                </td>
                                <td><small class="text-muted">{{ $sale->created_at->diffForHumans() }}</small></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Low Usable Stock Alerts -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Low Usable Stock Alerts</span>
                <form method="POST" action="{{ route('purchase-orders.auto-draft') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-info">Auto-Draft</button>
                </form>
            </div>
            <div class="card-body p-0">
                @if($lowStockIngredients->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa-solid fa-check-circle fa-2x text-success opacity-50 mb-2"></i>
                        <p class="small mb-0">All ingredients have healthy usable inventory levels.</p>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Usable Stock</th>
                                <th>Threshold</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($lowStockIngredients->take(5) as $ing)
                            <tr>
                                <td class="fw-semibold">{{ $ing->name }}</td>
                                <td>
                                    <span class="fw-bold {{ $ing->usable_stock <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ number_format($ing->usable_stock, 1) }} {{ $ing->unit }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $ing->reorder_threshold }} {{ $ing->unit }}</small></td>
                                <td>
                                    @if($ing->usable_stock <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row: Purchase Orders, Reconciliations & Waste -->
<div class="row g-4">
    <!-- Recent POs -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-cart-shopping me-2 text-info"></i>Purchase Orders</span>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-info">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentPOs->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <p class="small mb-0">No purchase orders created yet.</p>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentPOs as $po)
                            <tr>
                                <td>
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="fw-bold text-decoration-none small">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td><small class="fw-bold">৳{{ number_format($po->total_amount, 2) }}</small></td>
                                <td><span class="badge-status badge-{{ $po->status }}">{{ ucfirst($po->status) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Reconciliations -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-scale-balanced me-2 text-primary"></i>Stock Counts</span>
                <a href="{{ route('stock-reconciliations.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentReconciliations->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <p class="small mb-0">No reconciliations recorded yet.</p>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentReconciliations as $r)
                            <tr>
                                <td><a href="{{ route('stock-reconciliations.show', $r) }}" class="fw-semibold text-decoration-none">#{{ $r->id }}</a></td>
                                <td><small>{{ $r->submitter->name ?? 'Staff' }}</small></td>
                                <td><span class="badge-status badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Waste Reports -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-trash-can me-2 text-danger"></i>Waste Reports</span>
                <a href="{{ route('waste-reports.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentWasteReports->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <p class="small mb-0">No waste reports recorded yet.</p>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Loss</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentWasteReports as $w)
                            <tr>
                                <td>
                                    <a href="{{ route('waste-reports.show', $w) }}" class="fw-semibold text-decoration-none small">
                                        {{ $w->ingredient->name ?? 'Item' }}
                                    </a>
                                </td>
                                <td><small class="fw-bold text-danger">৳{{ number_format($w->estimated_loss, 2) }}</small></td>
                                <td><span class="badge-status badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
