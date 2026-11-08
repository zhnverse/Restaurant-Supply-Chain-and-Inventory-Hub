@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}" class="text-muted text-decoration-none">Purchase Orders</a></li>
    <li class="breadcrumb-item active">New Order</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-cart-plus me-2 text-primary"></i>Create Manual Purchase Order Draft</h1>
    <p>Select supplier and choose ingredients with desired order quantities.</p>
</div>

<form method="POST" action="{{ route('purchase-orders.store') }}">
    @csrf

    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-truck-field me-2"></i>Order &amp; Supplier Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" name="supplier" class="form-control" placeholder="e.g. Central Food Supplies Ltd." value="{{ old('supplier', 'Central Food Supplies Ltd.') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Order Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="e.g. Urgent restocking before weekend" value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-boxes-stacked me-2"></i>Select Ingredients &amp; Quantities</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Current Usable Stock</th>
                                <th>Threshold / Target</th>
                                <th style="width:180px;">Order Quantity</th>
                                <th style="width:160px;">Unit Cost (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($ingredients as $index => $ing)
                            @php
                                $suggested = max(0, (float)$ing->target_stock - (float)$ing->usable_stock);
                            @endphp
                            <tr>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][ingredient_id]" value="{{ $ing->id }}">
                                    <input type="hidden" name="items[{{ $index }}][current_stock]" value="{{ $ing->usable_stock }}">
                                    <input type="hidden" name="items[{{ $index }}][reorder_threshold]" value="{{ $ing->reorder_threshold }}">
                                    <input type="hidden" name="items[{{ $index }}][target_stock]" value="{{ $ing->target_stock }}">
                                    <div class="fw-semibold">{{ $ing->name }}</div>
                                    <small class="text-muted">{{ $ing->category }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $ing->isLowStock() ? 'text-danger' : 'text-dark' }}">
                                        {{ number_format($ing->usable_stock, 2) }} {{ $ing->unit }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $ing->reorder_threshold }} min / {{ $ing->target_stock }} target</small>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" class="form-control" placeholder="0" value="{{ $ing->isLowStock() ? $suggested : '' }}">
                                        <span class="input-group-text">{{ $ing->unit }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_cost]" class="form-control" value="{{ $ing->cost_per_unit > 0 ? $ing->cost_per_unit : 50.00 }}">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1"></i> Create Order Draft
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
