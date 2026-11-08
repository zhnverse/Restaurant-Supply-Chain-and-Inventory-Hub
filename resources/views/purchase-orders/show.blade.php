@extends('layouts.app')

@section('title', 'Purchase Order #' . $purchaseOrder->po_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}" class="text-muted text-decoration-none">Purchase Orders</a></li>
    <li class="breadcrumb-item active">{{ $purchaseOrder->po_number }}</li>
@endsection

@section('content')

@php
    $po = $purchaseOrder;
    $isManager = (auth()->user()->role ?? 'manager') === 'manager' || (auth()->user()->role ?? 'manager') === 'admin';
@endphp

<div class="page-header d-flex align-items-start justify-content-between">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h1 class="mb-0"><i class="fa-solid fa-cart-shopping me-2 text-info"></i>{{ $po->po_number }}</h1>
            <span class="badge-status badge-{{ $po->status }} fs-6">{{ ucfirst($po->status) }}</span>
            @if($po->is_auto_draft)
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle small">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Auto-Generated Draft (F3)
                </span>
            @endif
        </div>
        <p>Supplier: <strong>{{ $po->supplier }}</strong> &bull; Created on {{ $po->created_at->format('d M Y, H:i') }}</p>
    </div>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to PO List
    </a>
</div>

<div class="row g-4">
    <!-- PO Line Items Table -->
    <div class="col-lg-8">
        <form method="POST" action="{{ route('purchase-orders.approve', $po->id) }}" id="poApprovalForm">
            @csrf

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="fa-solid fa-boxes-stacked me-2"></i>Ordered Line Items</span>
                    <span class="badge bg-light text-dark">{{ $po->items->count() }} item(s)</span>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Usable Stock</th>
                                <th>Threshold / Target</th>
                                <th style="width:160px;">Order Quantity</th>
                                <th>Unit Cost</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($po->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->ingredient->name ?? 'Ingredient' }}</div>
                                    <small class="text-muted">{{ $item->ingredient->category ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $item->current_stock <= $item->reorder_threshold ? 'text-danger' : 'text-dark' }}">
                                        {{ number_format($item->current_stock, 2) }} {{ $item->ingredient->unit ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $item->reorder_threshold }} min &bull; {{ $item->target_stock }} target</small>
                                </td>
                                <td>
                                    @if($po->isDraft() && $isManager)
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0.01"
                                                   name="items[{{ $item->id }}][quantity]"
                                                   class="form-control item-qty-input"
                                                   data-cost="{{ $item->unit_cost }}"
                                                   value="{{ $item->quantity }}">
                                            <span class="input-group-text">{{ $item->ingredient->unit ?? '' }}</span>
                                        </div>
                                    @else
                                        <span class="fw-bold">{{ number_format($item->quantity, 2) }} {{ $item->ingredient->unit ?? '' }}</span>
                                    @endif
                                </td>
                                <td>৳{{ number_format($item->unit_cost, 2) }}</td>
                                <td><span class="fw-bold text-success line-total">৳{{ number_format($item->total_cost, 2) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="5" class="fw-bold text-end">Total Order Cost:</td>
                                <td><span class="fs-5 fw-bold text-success" id="grandTotalDisplay">৳{{ number_format($po->total_amount, 2) }}</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($po->isDraft() && $isManager)
                    <div class="card-footer bg-light p-3">
                        <label class="form-label small fw-semibold text-muted">Manager Approval Notes (Optional)</label>
                        <textarea name="manager_notes" class="form-control mb-3" rows="2" placeholder="e.g. Adjusted tomato quantity based on expected weekend festival rush."></textarea>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fa-solid fa-xmark me-1"></i> Reject Draft
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa-solid fa-check me-1"></i> Approve Purchase Order (F4)
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Sidebar Activity & Information -->
    <div class="col-lg-4">
        <!-- Status & Activity Card -->
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-timeline me-2"></i>Order Timeline</div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot dot-blue"></div>
                        <div class="fw-semibold small">Draft Created</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $po->is_auto_draft ? 'System Auto-Reorder Process' : ($po->generator->name ?? 'Staff') }}<br>
                            {{ $po->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    @if($po->isApproved())
                        <div class="timeline-item">
                            <div class="timeline-dot dot-green"></div>
                            <div class="fw-semibold small text-success">Approved &amp; Authorized</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                {{ $po->approver->name ?? 'Manager' }}<br>
                                {{ $po->approved_at ? $po->approved_at->format('d M Y, H:i') : '' }}
                            </div>
                            @if($po->manager_notes)
                                <div class="mt-1 p-2 rounded small" style="background:#f0fdf4; color:#166534;">
                                    "{{ $po->manager_notes }}"
                                </div>
                            @endif
                        </div>
                    @elseif($po->isRejected())
                        <div class="timeline-item">
                            <div class="timeline-dot dot-red"></div>
                            <div class="fw-semibold small text-danger">Draft Rejected</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                {{ $po->approver->name ?? 'Manager' }}<br>
                                {{ $po->approved_at ? $po->approved_at->format('d M Y, H:i') : '' }}
                            </div>
                            @if($po->manager_notes)
                                <div class="mt-1 p-2 rounded small" style="background:#fef2f2; color:#991b1b;">
                                    "{{ $po->manager_notes }}"
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="timeline-item">
                            <div class="timeline-dot dot-gray"></div>
                            <div class="text-muted small">Awaiting Manager Review</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($po->notes)
            <div class="card mb-4">
                <div class="card-header"><i class="fa-solid fa-note-sticky me-2"></i>Draft Origin Notes</div>
                <div class="card-body">
                    <p class="small text-muted mb-0">{{ $po->notes }}</p>
                </div>
            </div>
        @endif

        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-check text-success me-1"></i>SRS Feature Rules</h6>
                <p class="small text-muted mb-1"><strong>F3 (Auto Reorder):</strong> Low-stock items automatically calculate replenishment up to target stock level.</p>
                <p class="small text-muted mb-0"><strong>F4 (Approval):</strong> Only approved orders are authorized for supplier dispatch. Decision and reasons are permanently logged.</p>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($po->isDraft())
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fa-solid fa-xmark-circle text-danger me-2"></i>Reject Purchase Order Draft</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('purchase-orders.reject', $po->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        This draft purchase order will be marked as <strong>Rejected</strong>. No order will be dispatched to the supplier.
                    </div>
                    <label class="form-label small fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="manager_notes" class="form-control" rows="3" placeholder="Please explain why this order is rejected..." required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInputs = document.querySelectorAll('.item-qty-input');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');

    function updateGrandTotal() {
        let grand = 0;
        qtyInputs.forEach(input => {
            const row = input.closest('tr');
            const qty = parseFloat(input.value) || 0;
            const cost = parseFloat(input.dataset.cost) || 0;
            const total = qty * cost;
            const lineDisplay = row.querySelector('.line-total');
            if (lineDisplay) {
                lineDisplay.textContent = '৳' + total.toFixed(2);
            }
            grand += total;
        });
        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = '৳' + grand.toFixed(2);
        }
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', updateGrandTotal);
    });
});
</script>
@endpush
