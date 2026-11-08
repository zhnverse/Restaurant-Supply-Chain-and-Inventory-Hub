@extends('layouts.app')

@section('title', 'Stock Reconciliations')

@section('breadcrumb')
    <li class="breadcrumb-item active">Stock Reconciliation</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-scale-balanced me-2 text-primary"></i>Physical Stock Reconciliation</h1>
        <p>Submit and review physical stock counts against recorded inventory levels.</p>
    </div>
    <a href="{{ route('stock-reconciliations.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> New Physical Count
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#3b82f6;">{{ $stats['total'] }}</div>
            <div class="text-muted small mt-1">Total</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#f59e0b;">{{ $stats['pending'] }}</div>
            <div class="text-muted small mt-1">Pending Review</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#10b981;">{{ $stats['approved'] }}</div>
            <div class="text-muted small mt-1">Approved</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#ef4444;">{{ $stats['rejected'] }}</div>
            <div class="text-muted small mt-1">Rejected</div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="{{ route('stock-reconciliations.index') }}"
       class="filter-tab {{ !$currentStatus ? 'active' : '' }}">
        <i class="fa-solid fa-list me-1"></i> All
    </a>
    <a href="{{ route('stock-reconciliations.index', ['status' => 'pending']) }}"
       class="filter-tab {{ $currentStatus === 'pending' ? 'active' : '' }}">
        <i class="fa-solid fa-clock me-1"></i> Pending
    </a>
    <a href="{{ route('stock-reconciliations.index', ['status' => 'approved']) }}"
       class="filter-tab {{ $currentStatus === 'approved' ? 'active' : '' }}">
        <i class="fa-solid fa-check me-1"></i> Approved
    </a>
    <a href="{{ route('stock-reconciliations.index', ['status' => 'rejected']) }}"
       class="filter-tab {{ $currentStatus === 'rejected' ? 'active' : '' }}">
        <i class="fa-solid fa-xmark me-1"></i> Rejected
    </a>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        @if($reconciliations->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-inbox fa-3x mb-3 opacity-25"></i>
                <p class="mb-1">No reconciliations found.</p>
                <a href="{{ route('stock-reconciliations.create') }}" class="btn btn-primary mt-2">
                    <i class="fa-solid fa-plus me-1"></i> Submit First Count
                </a>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Submitted By</th>
                        <th>Items</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Reviewed By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($reconciliations as $r)
                    <tr>
                        <td><span class="fw-semibold text-primary">#{{ $r->id }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.7rem;font-weight:700;">
                                    {{ strtoupper(substr($r->submitter->name ?? 'U', 0, 1)) }}
                                </div>
                                <span>{{ $r->submitter->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark">{{ $r->items->count() }} ingredients</span></td>
                        <td class="text-muted" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $r->notes ?: '—' }}
                        </td>
                        <td><span class="badge-status badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        <td class="text-muted small">{{ $r->approver->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $r->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <a href="{{ route('stock-reconciliations.show', $r) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($reconciliations->hasPages())
                <div class="p-3 border-top">
                    {{ $reconciliations->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
