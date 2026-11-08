@extends('layouts.app')

@section('title', 'Sales & Inventory Deductions')

@section('breadcrumb')
    <li class="breadcrumb-item active">Sales Log</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-receipt me-2 text-primary"></i>Sales &amp; Recipe Deductions</h1>
        <p>Complete historical log of dish sales with automated recipe ingredient deductions.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dishes.index') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-bowl-food me-1"></i> Dishes &amp; Recipes
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-success">
            <i class="fa-solid fa-cash-register me-1"></i> Record New Sale
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card p-3">
            <div class="text-muted small">Total Orders</div>
            <div class="fs-4 fw-bold text-dark">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card p-3">
            <div class="text-muted small">Total Sales Revenue</div>
            <div class="fs-4 fw-bold text-success">৳{{ number_format($totalRevenue, 2) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($sales->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-receipt fa-3x opacity-25 mb-3"></i>
                <h5>No Sales Recorded Yet</h5>
                <p class="small mb-3">Record your first sale to observe automatic recipe-based inventory deductions.</p>
                <a href="{{ route('sales.create') }}" class="btn btn-success">
                    <i class="fa-solid fa-cash-register me-1"></i> Record Sale
                </a>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Dish Sold</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Ingredients Automatically Deducted</th>
                        <th>Recorded By</th>
                        <th>Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td><span class="fw-bold text-primary">#ORD-{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $sale->dish->name ?? 'Dish' }}</div>
                            <small class="text-muted">৳{{ number_format($sale->unit_price, 2) }} each</small>
                        </td>
                        <td><span class="badge bg-light text-dark fs-6">{{ $sale->quantity }}x</span></td>
                        <td><span class="fw-bold text-success">৳{{ number_format($sale->total_amount, 2) }}</span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($sale->deductions as $deduction)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">
                                        -{{ $deduction->quantity_deducted }} {{ $deduction->ingredient->unit ?? '' }} {{ $deduction->ingredient->name ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td><small class="text-muted">{{ $sale->recorder->name ?? 'Staff' }}</small></td>
                        <td><small class="text-muted">{{ $sale->created_at->format('d M Y, H:i') }}</small></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($sales->hasPages())
                <div class="p-3 border-top">
                    {{ $sales->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
