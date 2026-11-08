@extends('layouts.app')

@section('title', 'Waste Reports')

@section('breadcrumb')
    <li class="breadcrumb-item active">Waste Reports</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-trash-can me-2 text-danger"></i>Waste Reporting</h1>
        <p>Track damaged, spoiled, or expired ingredients. Approved reports deduct from stock.</p>
    </div>
    <a href="{{ route('waste-reports.create') }}" class="btn btn-danger">
        <i class="fa-solid fa-plus me-1"></i> Report Waste
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#3b82f6;">{{ $stats['total'] }}</div>
            <div class="text-muted small mt-1">Total Reports</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div style="font-size:1.6rem;font-weight:800;color:#f59e0b;">{{ $stats['pending'] }}</div>
            <div class="text-muted small mt-1">Pending</div>
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
            <div style="font-size:1.6rem;font-weight:800;color:#ef4444;">৳{{ number_format($stats['total_loss'], 2) }}</div>
            <div class="text-muted small mt-1">Total Loss (BDT)</div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="{{ route('waste-reports.index') }}"
       class="filter-tab {{ !$currentStatus ? 'active' : '' }}">
        <i class="fa-solid fa-list me-1"></i> All
    </a>
    <a href="{{ route('waste-reports.index', ['status' => 'pending']) }}"
       class="filter-tab {{ $currentStatus === 'pending' ? 'active' : '' }}">
        <i class="fa-solid fa-clock me-1"></i> Pending
    </a>
    <a href="{{ route('waste-reports.index', ['status' => 'approved']) }}"
       class="filter-tab {{ $currentStatus === 'approved' ? 'active' : '' }}">
        <i class="fa-solid fa-check me-1"></i> Approved
    </a>
    <a href="{{ route('waste-reports.index', ['status' => 'rejected']) }}"
       class="filter-tab {{ $currentStatus === 'rejected' ? 'active' : '' }}">
        <i class="fa-solid fa-xmark me-1"></i> Rejected
    </a>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        @if($reports->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-inbox fa-3x mb-3 opacity-25"></i>
                <p class="mb-1">No waste reports found.</p>
                <a href="{{ route('waste-reports.create') }}" class="btn btn-danger mt-2">
                    <i class="fa-solid fa-plus me-1"></i> Report First Waste
                </a>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ingredient</th>
                        <th>Quantity</th>
                        <th>Reason</th>
                        <th>Estimated Loss</th>
                        <th>Reported By</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td><span class="fw-semibold text-danger">#{{ $report->id }}</span></td>
                        <td class="fw-semibold">{{ $report->ingredient->name ?? 'Unknown' }}</td>
                        <td>{{ $report->quantity }} <span class="text-muted small">{{ $report->ingredient->unit ?? '' }}</span></td>
                        <td>
                            @php
                                $reasonColors = [
                                    'damaged'      => 'bg-orange-100 text-orange-800',
                                    'spoiled'      => 'bg-yellow-100 text-yellow-800',
                                    'expired'      => 'bg-red-100 text-red-800',
                                    'contaminated' => 'bg-purple-100 text-purple-800',
                                    'other'        => 'bg-gray-100 text-gray-800',
                                ];
                                $reasonIcons = [
                                    'damaged'      => 'fa-hammer',
                                    'spoiled'      => 'fa-bug',
                                    'expired'      => 'fa-calendar-xmark',
                                    'contaminated' => 'fa-biohazard',
                                    'other'        => 'fa-circle-question',
                                ];
                            @endphp
                            <span class="badge bg-light text-dark">
                                <i class="fa-solid {{ $reasonIcons[$report->reason] ?? 'fa-circle' }} me-1"></i>
                                {{ ucfirst($report->reason) }}
                            </span>
                        </td>
                        <td class="fw-semibold text-danger">৳{{ number_format($report->estimated_loss, 2) }}</td>
                        <td class="text-muted small">{{ $report->reporter->name ?? '—' }}</td>
                        <td><span class="badge-status badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span></td>
                        <td class="text-muted small">{{ $report->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('waste-reports.show', $report) }}"
                               class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($reports->hasPages())
                <div class="p-3 border-top">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
