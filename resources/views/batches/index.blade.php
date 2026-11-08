@extends('layouts.app')

@section('title', 'Batch Expiry Monitoring')

@section('breadcrumb')
    <li class="breadcrumb-item active">Batch Expiry Monitoring</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Batch Expiry Monitoring</h1>
        <p>Monitor ingredient batch shelf life with configurable warning windows. Expired batches are automatically excluded from usable inventory.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('batches.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Register New Batch
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-red">
            <div class="stat-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
            <div class="stat-number">{{ $expiredCount }}</div>
            <div class="stat-label">Expired Batches (Zero Usable)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-number">{{ $criticalCount }}</div>
            <div class="stat-label">Critical (Expires ≤ 3 Days)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-teal">
            <div class="stat-icon"><i class="fa-solid fa-bell"></i></div>
            <div class="stat-number">{{ $warningCount }}</div>
            <div class="stat-label">Warning (Within {{ $window }} Days)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="stat-number">{{ number_format($totalUsableStock, 1) }}</div>
            <div class="stat-label">Total Usable Stock Units</div>
        </div>
    </div>
</div>

<!-- Usable Stock vs Expired Banner -->
@if($totalExpiredQuantity > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-shield-halved fa-2x text-warning"></i>
            <div>
                <strong>Safety Rule Enforced:</strong>
                <strong>{{ number_format($totalExpiredQuantity, 2) }} units</strong> of expired stock are quarantined and excluded from usable inventory to protect food safety.
            </div>
        </div>
        <a href="{{ route('waste-reports.create') }}" class="btn btn-sm btn-danger text-nowrap">
            <i class="fa-solid fa-trash-can me-1"></i> Report as Waste
        </a>
    </div>
@endif

<!-- Configurable Warning Window & Filter Tabs -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div class="filter-tabs mb-0">
        <a href="{{ route('batches.index') }}" class="filter-tab {{ !$status ? 'active' : '' }}">
            <i class="fa-solid fa-list me-1"></i> All Batches
        </a>
        <a href="{{ route('batches.index', ['status' => 'expired', 'window' => $window]) }}" class="filter-tab {{ $status === 'expired' ? 'active' : '' }}">
            <i class="fa-solid fa-ban me-1 text-danger"></i> Expired ({{ $expiredCount }})
        </a>
        <a href="{{ route('batches.index', ['status' => 'critical', 'window' => $window]) }}" class="filter-tab {{ $status === 'critical' ? 'active' : '' }}">
            <i class="fa-solid fa-fire me-1 text-danger"></i> Critical ≤ 3 Days ({{ $criticalCount }})
        </a>
        <a href="{{ route('batches.index', ['status' => 'warning', 'window' => $window]) }}" class="filter-tab {{ $status === 'warning' ? 'active' : '' }}">
            <i class="fa-solid fa-clock me-1 text-warning"></i> Warning ≤ {{ $window }} Days ({{ $warningCount }})
        </a>
        <a href="{{ route('batches.index', ['status' => 'fresh', 'window' => $window]) }}" class="filter-tab {{ $status === 'fresh' ? 'active' : '' }}">
            <i class="fa-solid fa-check me-1 text-success"></i> Fresh
        </a>
    </div>

    <!-- Window selector -->
    <form method="GET" action="{{ route('batches.index') }}" class="d-flex align-items-center gap-2">
        @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
        <label class="small text-muted text-nowrap mb-0"><i class="fa-solid fa-sliders me-1"></i>Warning Window:</label>
        <select name="window" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="3" {{ $window == 3 ? 'selected' : '' }}>3 Days</option>
            <option value="7" {{ $window == 7 ? 'selected' : '' }}>7 Days (Default)</option>
            <option value="14" {{ $window == 14 ? 'selected' : '' }}>14 Days</option>
            <option value="30" {{ $window == 30 ? 'selected' : '' }}>30 Days</option>
        </select>
    </form>
</div>

<!-- Batches Table -->
<div class="card">
    <div class="card-body p-0">
        @if($batches->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-boxes-packing fa-3x opacity-25 mb-3"></i>
                <h5>No batches found matching this filter</h5>
                <p class="small mb-3">All ingredients are fresh or no batches have been registered yet.</p>
                <a href="{{ route('batches.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Register Batch
                </a>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Batch #</th>
                        <th>Ingredient</th>
                        <th>Remaining Stock</th>
                        <th>Expiry Date</th>
                        <th>Shelf Life Status</th>
                        <th>Cost / Unit</th>
                        <th>Supplier</th>
                        <th>Usable In Recipes?</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($batches as $batch)
                    @php
                        $days = $batch->days_remaining;
                        $isExp = $batch->isExpired();
                    @endphp
                    <tr class="{{ $isExp ? 'table-danger-subtle' : '' }}">
                        <td><span class="fw-bold text-dark">{{ $batch->batch_number }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $batch->ingredient->name ?? 'Ingredient' }}</div>
                            <small class="text-muted">{{ $batch->ingredient->category ?? 'Category' }}</small>
                        </td>
                        <td>
                            <span class="fw-bold {{ $batch->remaining_quantity <= 0 ? 'text-muted' : ($isExp ? 'text-danger' : 'text-primary') }}">
                                {{ number_format($batch->remaining_quantity, 2) }} {{ $batch->ingredient->unit ?? '' }}
                            </span>
                            <div class="text-muted" style="font-size:0.75rem;">Initial: {{ $batch->quantity }} {{ $batch->ingredient->unit ?? '' }}</div>
                        </td>
                        <td>
                            @if($batch->expiry_date)
                                <div>{{ $batch->expiry_date->format('d M Y') }}</div>
                                <small class="text-muted">{{ $batch->expiry_date->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">— No Expiry —</span>
                            @endif
                        </td>
                        <td>
                            @if($isExp)
                                <span class="badge bg-danger text-white"><i class="fa-solid fa-ban me-1"></i>EXPIRED ({{ abs($days) }}d ago)</span>
                            @elseif($days !== null && $days <= 3)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fa-solid fa-fire me-1"></i>CRITICAL ({{ $days }}d left)</span>
                            @elseif($days !== null && $days <= $window)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-clock me-1"></i>WARNING ({{ $days }}d left)</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check me-1"></i>FRESH ({{ $days ?? '∞' }}d)</span>
                            @endif
                        </td>
                        <td>৳{{ number_format($batch->cost_per_unit, 2) }}</td>
                        <td><small class="text-muted">{{ $batch->supplier ?: '—' }}</small></td>
                        <td>
                            @if($isExp)
                                <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>EXCLUDED (Zero)</span>
                            @else
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>YES</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($batches->hasPages())
                <div class="p-3 border-top">
                    {{ $batches->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
