@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-gauge me-2 text-primary"></i>Dashboard</h1>
        <p>Overview of your restaurant inventory and pending approvals.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock-reconciliations.create') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-scale-balanced me-1"></i> New Stock Count
        </a>
        <a href="{{ route('waste-reports.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-trash-can me-1"></i> Report Waste
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-number">{{ $stats['total_ingredients'] }}</div>
            <div class="stat-label">Total Ingredients</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-number">{{ $stats['low_stock'] }}</div>
            <div class="stat-label">Low Stock Alerts</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fa-solid fa-scale-balanced"></i></div>
            <div class="stat-number">{{ $stats['pending_reconciliations'] }}</div>
            <div class="stat-label">Pending Reconciliations</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-red">
            <div class="stat-icon"><i class="fa-solid fa-trash-can"></i></div>
            <div class="stat-number">{{ $stats['pending_waste_reports'] }}</div>
            <div class="stat-label">Pending Waste Reports</div>
        </div>
    </div>
</div>

<!-- Total Waste Loss Banner -->
@if($stats['total_waste_loss'] > 0)
<div class="alert alert-danger d-flex align-items-center gap-3 mb-4">
    <i class="fa-solid fa-bangladeshi-taka-sign fa-lg"></i>
    <div>
        <strong>Total Approved Waste Loss:</strong>
        ৳{{ number_format($stats['total_waste_loss'], 2) }} BDT
    </div>
</div>
@endif

<div class="row g-4">

    <!-- Recent Stock Reconciliations -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-scale-balanced me-2 text-primary"></i>Recent Stock Reconciliations</span>
                <a href="{{ route('stock-reconciliations.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentReconciliations->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0 small">No reconciliations yet.</p>
                        <a href="{{ route('stock-reconciliations.create') }}" class="btn btn-sm btn-primary mt-3">Create First Count</a>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead><tr>
                            <th>#</th><th>Submitted By</th><th>Date</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        @foreach($recentReconciliations as $r)
                            <tr>
                                <td><a href="{{ route('stock-reconciliations.show', $r) }}" class="fw-semibold text-decoration-none">#{{ $r->id }}</a></td>
                                <td>{{ $r->submitter->name ?? 'N/A' }}</td>
                                <td><span class="text-muted small">{{ $r->created_at->diffForHumans() }}</span></td>
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
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-trash-can me-2 text-danger"></i>Recent Waste Reports</span>
                <a href="{{ route('waste-reports.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentWasteReports->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-inbox fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0 small">No waste reports yet.</p>
                        <a href="{{ route('waste-reports.create') }}" class="btn btn-sm btn-primary mt-3">Report Waste</a>
                    </div>
                @else
                    <table class="table mb-0">
                        <thead><tr>
                            <th>Ingredient</th><th>Qty</th><th>Reason</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        @foreach($recentWasteReports as $w)
                            <tr>
                                <td><a href="{{ route('waste-reports.show', $w) }}" class="fw-semibold text-decoration-none">{{ $w->ingredient->name ?? 'N/A' }}</a></td>
                                <td class="text-muted small">{{ $w->quantity }} {{ $w->ingredient->unit ?? '' }}</td>
                                <td><span class="text-capitalize">{{ $w->reason }}</span></td>
                                <td><span class="badge-status badge-{{ $w->status }}">{{ ucfirst($w->status) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    @if($lowStockIngredients->isNotEmpty())
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>
                Low Stock Alerts — Ingredients Below Reorder Threshold
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr>
                        <th>Ingredient</th><th>Category</th><th>Current Stock</th><th>Threshold</th><th>Status</th>
                    </tr></thead>
                    <tbody>
                    @foreach($lowStockIngredients as $ing)
                        <tr>
                            <td class="fw-semibold">{{ $ing->name }}</td>
                            <td class="text-muted">{{ $ing->category ?? '—' }}</td>
                            <td>
                                <span class="{{ $ing->current_stock <= 0 ? 'text-danger fw-bold' : 'text-warning fw-semibold' }}">
                                    {{ $ing->current_stock }} {{ $ing->unit }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $ing->reorder_threshold }} {{ $ing->unit }}</td>
                            <td>
                                @if($ing->current_stock <= 0)
                                    <span class="badge-status badge-rejected">Out of Stock</span>
                                @else
                                    <span class="badge-status badge-pending">Low Stock</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
