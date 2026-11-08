@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('breadcrumb')
    <li class="breadcrumb-item active">Purchase Orders</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-cart-shopping me-2 text-info"></i>Purchase Orders (F3 &amp; F4)</h1>
        <p>Automated reorder drafting based on inventory thresholds and managerial approval workflow.</p>
    </div>
    <div class="d-flex gap-2">
        <!-- Feature 3: Auto-Draft Trigger Button -->
        <form method="POST" action="{{ route('purchase-orders.auto-draft') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-info text-dark fw-bold">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Generate Reorder Draft (F3)
            </button>
        </form>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-plus me-1"></i> Manual Order
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#3b82f6;">{{ $stats['total'] }}</div>
            <div class="text-muted small mt-1">Total POs</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#f59e0b;">{{ $stats['draft'] }}</div>
            <div class="text-muted small mt-1">Pending Drafts (F3)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#10b981;">{{ $stats['approved'] }}</div>
            <div class="text-muted small mt-1">Approved &amp; Sent (F4)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#065f46;">৳{{ number_format($stats['total_approved_amount'], 2) }}</div>
            <div class="text-muted small mt-1">Approved Total (BDT)</div>
        </div>
    </div>
</div>

<!-- Low Stock Notice Banner -->
@if($lowStockIngredients->isNotEmpty())
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-warning"></i>
            <div>
                <strong>Reorder Threshold Alert (F3):</strong>
                <strong>{{ $lowStockIngredients->count() }} ingredient(s)</strong> are currently at or below minimum threshold:
                <span class="text-muted small">
                    ({{ $lowStockIngredients->pluck('name')->implode(', ') }})
                </span>
            </div>
        </div>
        <form method="POST" action="{{ route('purchase-orders.auto-draft') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-dark text-nowrap">
                <i class="fa-solid fa-plus me-1"></i> Draft Reorder Now
            </button>
        </form>
    </div>
@endif

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="{{ route('purchase-orders.index') }}" class="filter-tab {{ !$status ? 'active' : '' }}">
        <i class="fa-solid fa-list me-1"></i> All Orders
    </a>
    <a href="{{ route('purchase-orders.index', ['status' => 'draft']) }}" class="filter-tab {{ $status === 'draft' ? 'active' : '' }}">
        <i class="fa-solid fa-clock me-1 text-warning"></i> Drafts ({{ $stats['draft'] }})
    </a>
    <a href="{{ route('purchase-orders.index', ['status' => 'approved']) }}" class="filter-tab {{ $status === 'approved' ? 'active' : '' }}">
        <i class="fa-solid fa-check me-1 text-success"></i> Approved ({{ $stats['approved'] }})
    </a>
    <a href="{{ route('purchase-orders.index', ['status' => 'rejected']) }}" class="filter-tab {{ $status === 'rejected' ? 'active' : '' }}">
        <i class="fa-solid fa-xmark me-1 text-danger"></i> Rejected ({{ $stats['rejected'] }})
    </a>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-cart-shopping fa-3x opacity-25 mb-3"></i>
                <h5>No Purchase Orders Found</h5>
                <p class="small mb-3">Run the automated reorder check or create a manual order draft.</p>
                <form method="POST" action="{{ route('purchase-orders.auto-draft') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info text-dark fw-bold">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Run Auto-Draft Check
                    </button>
                </form>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>PO #</th>
                        <th>Supplier</th>
                        <th>Items Count</th>
                        <th>Generation Method</th>
                        <th>Estimated Total</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $po)
                    <tr>
                        <td>
                            <a href="{{ route('purchase-orders.show', $po) }}" class="fw-bold text-decoration-none">
                                {{ $po->po_number }}
                            </a>
                        </td>
                        <td>{{ $po->supplier }}</td>
                        <td><span class="badge bg-light text-dark">{{ $po->items->count() }} item(s)</span></td>
                        <td>
                            @if($po->is_auto_draft)
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                    <i class="fa-solid fa-robot me-1"></i>Auto Threshold Draft (F3)
                                </span>
                            @else
                                <span class="badge bg-light text-muted">Manual Draft</span>
                            @endif
                        </td>
                        <td><span class="fw-bold text-success">৳{{ number_format($po->total_amount, 2) }}</span></td>
                        <td><span class="badge-status badge-{{ $po->status }}">{{ ucfirst($po->status) }}</span></td>
                        <td><small class="text-muted">{{ $po->created_at->format('d M Y, H:i') }}</small></td>
                        <td>
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> Review &amp; Manage
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($orders->hasPages())
                <div class="p-3 border-top">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
