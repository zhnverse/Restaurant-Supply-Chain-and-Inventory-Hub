@extends('layouts.app')

@section('title', 'Waste Report #' . $wasteReport->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('waste-reports.index') }}" class="text-muted text-decoration-none">Waste Reports</a></li>
    <li class="breadcrumb-item active">#{{ $wasteReport->id }}</li>
@endsection

@section('content')

@php
    $w = $wasteReport;
    $isManager = true;
    $reasonIcons = [
        'damaged'      => ['icon' => 'fa-hammer',        'color' => 'text-warning'],
        'spoiled'      => ['icon' => 'fa-bug',           'color' => 'text-success'],
        'expired'      => ['icon' => 'fa-calendar-xmark','color' => 'text-danger'],
        'contaminated' => ['icon' => 'fa-biohazard',     'color' => 'text-purple'],
        'other'        => ['icon' => 'fa-circle-question','color' => 'text-muted'],
    ];
    $ri = $reasonIcons[$w->reason] ?? ['icon' => 'fa-circle', 'color' => 'text-muted'];
@endphp

<!-- Header -->
<div class="page-header d-flex align-items-start justify-content-between">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h1 class="mb-0"><i class="fa-solid fa-trash-can me-2 text-danger"></i>Waste Report #{{ $w->id }}</h1>
            <span class="badge-status badge-{{ $w->status }} fs-6">{{ ucfirst($w->status) }}</span>
        </div>
        <p>Reported by <strong>{{ $w->reporter->name ?? 'Unknown' }}</strong>
           on {{ $w->created_at->format('d M Y \a\t H:i') }}</p>
    </div>
    <a href="{{ route('waste-reports.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-4">

    <!-- Main Details -->
    <div class="col-lg-8">

        <!-- Waste Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-solid fa-circle-info me-2"></i>Waste Details
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:0.05em;">Ingredient</div>
                        <div class="fs-5 fw-bold">{{ $w->ingredient->name ?? 'Unknown' }}</div>
                        <div class="text-muted small">{{ $w->ingredient->category ?? '' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:0.05em;">Quantity Wasted</div>
                        <div class="fs-5 fw-bold text-danger">
                            {{ number_format($w->quantity, 2) }} {{ $w->ingredient->unit ?? '' }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:0.05em;">Reason</div>
                        <div class="d-flex align-items-center gap-2 fs-6 fw-semibold">
                            <i class="fa-solid {{ $ri['icon'] }} {{ $ri['color'] }}"></i>
                            {{ ucfirst($w->reason) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:0.05em;">Estimated Financial Loss</div>
                        <div class="fs-5 fw-bold" style="color:#dc2626;">৳{{ number_format($w->estimated_loss, 2) }} BDT</div>
                    </div>

                    @if($w->batch)
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:0.05em;">Batch</div>
                        <div class="fw-semibold">{{ $w->batch->batch_number }}</div>
                        <div class="text-muted small">
                            Exp: {{ $w->batch->expiry_date ? $w->batch->expiry_date->format('d M Y') : 'No expiry' }}
                        </div>
                    </div>
                    @endif

                    @if($w->description)
                    <div class="col-12">
                        <div class="text-muted small mb-1 fw-semibold text-uppercase" style="letter-spacing:0.05em;">Description</div>
                        <div class="p-3 rounded" style="background:#f8fafc;border-left:3px solid #e2e8f0;">
                            {{ $w->description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Impact Summary -->
        @if($w->isApproved())
        <div class="card" style="border-left:4px solid #10b981;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:12px;background:#d1fae5;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#059669;">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success">Waste Approved & Processed</div>
                        <div class="text-muted small">
                            <strong>{{ number_format($w->quantity, 2) }} {{ $w->ingredient->unit ?? '' }}</strong>
                            of {{ $w->ingredient->name ?? '' }} was deducted from stock.
                            Loss recorded: <strong>৳{{ number_format($w->estimated_loss, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($w->isRejected())
        <div class="card" style="border-left:4px solid #ef4444;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:12px;background:#fee2e2;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#dc2626;">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-danger">Report Rejected</div>
                        <div class="text-muted small">Stock levels were not changed.</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">

        <!-- Activity Timeline -->
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-timeline me-2"></i>Activity</div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot dot-blue"></div>
                        <div class="fw-semibold small">Report Submitted</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $w->reporter->name ?? '—' }}<br>
                            {{ $w->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    @if($w->status === 'approved')
                    <div class="timeline-item">
                        <div class="timeline-dot dot-green"></div>
                        <div class="fw-semibold small text-success">Approved</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $w->approver->name ?? '—' }}<br>
                            {{ $w->approved_at ? $w->approved_at->format('d M Y, H:i') : '—' }}
                        </div>
                        @if($w->manager_notes)
                        <div class="mt-1 p-2 rounded" style="background:#f0fdf4;font-size:0.8rem;color:#166534;">
                            "{{ $w->manager_notes }}"
                        </div>
                        @endif
                    </div>
                    @elseif($w->status === 'rejected')
                    <div class="timeline-item">
                        <div class="timeline-dot dot-red"></div>
                        <div class="fw-semibold small text-danger">Rejected</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            {{ $w->approver->name ?? '—' }}<br>
                            {{ $w->approved_at ? $w->approved_at->format('d M Y, H:i') : '—' }}
                        </div>
                        @if($w->manager_notes)
                        <div class="mt-1 p-2 rounded" style="background:#fef2f2;font-size:0.8rem;color:#991b1b;">
                            "{{ $w->manager_notes }}"
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

        <!-- Manager Actions -->
        @if($w->isPending() && $isManager)
        <div class="card border-warning">
            <div class="card-header" style="background:#fffbeb;color:#92400e;">
                <i class="fa-solid fa-gavel me-2"></i>Manager Decision
            </div>
            <div class="card-body">
                <div class="p-3 mb-3 rounded" style="background:#fef9ec;border:1px solid #fde68a;">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Ingredient:</span>
                        <strong>{{ $w->ingredient->name ?? '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Will deduct:</span>
                        <strong class="text-danger">{{ $w->quantity }} {{ $w->ingredient->unit ?? '' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Financial loss:</span>
                        <strong class="text-danger">৳{{ number_format($w->estimated_loss, 2) }}</strong>
                    </div>
                </div>

                <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fa-solid fa-check me-1"></i> Approve & Deduct Stock
                </button>
                <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fa-solid fa-xmark me-1"></i> Reject Report
                </button>
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Approve Modal -->
@if($w->isPending())
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fa-solid fa-check-circle text-success me-2"></i>Approve Waste Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('waste-reports.approve', $w->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <strong>{{ number_format($w->quantity, 2) }} {{ $w->ingredient->unit ?? '' }}</strong>
                        of <strong>{{ $w->ingredient->name ?? '' }}</strong> will be permanently deducted from stock.
                        Estimated loss: <strong>৳{{ number_format($w->estimated_loss, 2) }}</strong>
                    </div>
                    <label class="form-label">Manager Notes <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="manager_notes" class="form-control" rows="3"
                              placeholder="Any notes about this waste approval..."></textarea>
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
                <h5 class="modal-title"><i class="fa-solid fa-xmark-circle text-danger me-2"></i>Reject Waste Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('waste-reports.reject', $w->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        Stock levels will <strong>not</strong> be changed. The report will be marked as rejected.
                    </div>
                    <label class="form-label">Reason for Rejection</label>
                    <textarea name="manager_notes" class="form-control" rows="3"
                              placeholder="Please explain why this report is being rejected..."></textarea>
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
