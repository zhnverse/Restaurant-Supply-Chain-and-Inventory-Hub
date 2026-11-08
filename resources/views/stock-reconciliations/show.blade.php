@extends('layouts.app')

@section('title', 'Reconciliation #' . $stockReconciliation->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('stock-reconciliations.index') }}" class="text-muted text-decoration-none">Stock Reconciliation</a></li>
    <li class="breadcrumb-item active">#{{ $stockReconciliation->id }}</li>
@endsection

@section('content')

@php
    $r = $stockReconciliation;
    $isManager = true; // In full app this would check auth()->user()->isManager()
@endphp

<!-- Header -->
<div class="page-header d-flex align-items-start justify-content-between">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h1 class="mb-0"><i class="fa-solid fa-scale-balanced me-2 text-primary"></i>Reconciliation #{{ $r->id }}</h1>
            <span class="badge-status badge-{{ $r->status }} fs-6">{{ ucfirst($r->status) }}</span>
        </div>
        <p>Submitted by <strong>{{ $r->submitter->name ?? 'Unknown' }}</strong>
           on {{ $r->created_at->format('d M Y \a\t H:i') }}</p>
    </div>
    <a href="{{ route('stock-reconciliations.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">

    <!-- Items Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-list-check me-2"></i>Stock Count Items
                <span class="badge bg-primary ms-2">{{ $r->items->count() }} ingredients</span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Ingredient</th>
                            <th>Unit</th>
                            <th>Recorded Stock</th>
                            <th>Physical Count</th>
                            <th>Variance</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($r->items as $item)
                        @php $diff = (float)$item->physical_count - (float)$item->recorded_stock; @endphp
                        <tr>
                            <td class="fw-semibold">{{ $item->ingredient->name ?? 'Unknown' }}</td>
                            <td class="text-muted">{{ $item->ingredient->unit ?? '' }}</td>
                            <td>{{ number_format($item->recorded_stock, 2) }}</td>
                            <td class="fw-semibold">{{ number_format($item->physical_count, 2) }}</td>
                            <td>
                                @if($diff > 0)
                                    <span class="diff-surplus">
                                        <i class="fa-solid fa-arrow-trend-up me-1"></i>+{{ number_format($diff, 2) }}
                                    </span>
                                @elseif($diff < 0)
                                    <span class="diff-shortage">
                                        <i class="fa-solid fa-arrow-trend-down me-1"></i>{{ number_format($diff, 2) }}
                                    </span>
                                @else
                                    <span class="diff-balanced">
                                        <i class="fa-solid fa-equals me-1"></i>0.00
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $item->adjustment_reason ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="fw-semibold text-end">Total Net Variance:</td>
                            <td colspan="2">
                                @php
                                    $totalDiff = $r->items->sum(fn($i) => (float)$i->physical_count - (float)$i->recorded_stock);
                                @endphp
                                @if($totalDiff > 0)
                                    <span class="diff-surplus fw-bold fs-6">+{{ number_format($totalDiff, 2) }}</span>
                                @elseif($totalDiff < 0)
                                    <span class="diff-shortage fw-bold fs-6">{{ number_format($totalDiff, 2) }}</span>
                                @else
                                    <span class="diff-balanced fw-bold fs-6">0.00</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">

        <!-- Status Timeline -->
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-timeline me-2"></i>Activity</div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot dot-blue"></div>
                        <div class="fw-semibold small">Submitted</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $r->submitter->name ?? '—' }}<br>
                            {{ $r->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    @if($r->status === 'approved')
                    <div class="timeline-item">
                        <div class="timeline-dot dot-green"></div>
                        <div class="fw-semibold small text-success">Approved</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $r->approver->name ?? '—' }}<br>
                            {{ $r->approved_at ? $r->approved_at->format('d M Y, H:i') : '—' }}
                        </div>
                        @if($r->manager_notes)
                        <div class="mt-1 p-2 rounded" style="background:#f0fdf4;font-size:0.8rem;color:#166534;">
                            "{{ $r->manager_notes }}"
                        </div>
                        @endif
                    </div>
                    @elseif($r->status === 'rejected')
                    <div class="timeline-item">
                        <div class="timeline-dot dot-red"></div>
                        <div class="fw-semibold small text-danger">Rejected</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $r->approver->name ?? '—' }}<br>
                            {{ $r->approved_at ? $r->approved_at->format('d M Y, H:i') : '—' }}
                        </div>
                        @if($r->manager_notes)
                        <div class="mt-1 p-2 rounded" style="background:#fef2f2;font-size:0.8rem;color:#991b1b;">
                            "{{ $r->manager_notes }}"
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="timeline-item">
                        <div class="timeline-dot dot-gray"></div>
                        <div class="text-muted small">Awaiting manager review...</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($r->notes)
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-note-sticky me-2"></i>Staff Notes</div>
            <div class="card-body">
                <p class="mb-0 text-muted" style="font-size:0.875rem;">{{ $r->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Manager Actions -->
        @if($r->isPending() && $isManager)
        <div class="card border-warning">
            <div class="card-header" style="background:#fffbeb;color:#92400e;">
                <i class="fa-solid fa-gavel me-2"></i>Manager Decision
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Approving will update stock levels for all {{ $r->items->count() }} ingredient(s) based on the physical counts.
                </p>

                <!-- Approve -->
                <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fa-solid fa-check me-1"></i> Approve & Update Stock
                </button>

                <!-- Reject -->
                <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fa-solid fa-xmark me-1"></i> Reject
                </button>
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Approve Modal -->
@if($r->isPending())
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fa-solid fa-check-circle text-success me-2"></i>Approve Reconciliation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('stock-reconciliations.approve', $r->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <strong>{{ $r->items->count() }} ingredient(s)</strong> will have their stock updated based on the physical count. This action cannot be undone.
                    </div>
                    <label class="form-label">Manager Notes <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="manager_notes" class="form-control" rows="3"
                              placeholder="Add any notes about this approval..."></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-check me-1"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fa-solid fa-xmark-circle text-danger me-2"></i>Reject Reconciliation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('stock-reconciliations.reject', $r->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        Stock levels will <strong>not</strong> be changed. The staff member may need to resubmit a new count.
                    </div>
                    <label class="form-label">Reason for Rejection</label>
                    <textarea name="manager_notes" class="form-control" rows="3"
                              placeholder="Please explain why this count is being rejected..."></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fa-solid fa-xmark me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
