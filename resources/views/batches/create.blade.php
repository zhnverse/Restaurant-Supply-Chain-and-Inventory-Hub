@extends('layouts.app')

@section('title', 'Register Batch')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}" class="text-muted text-decoration-none">Batches</a></li>
    <li class="breadcrumb-item active">New Batch</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-box-open me-2 text-primary"></i>Register Ingredient Batch</h1>
    <p>Record a new shipment or ingredient batch with exact expiry date and cost per unit.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('batches.store') }}">
            @csrf
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-tag me-2"></i>Batch Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Ingredient <span class="text-danger">*</span></label>
                            <select name="ingredient_id" id="ingredientSelect" class="form-select" required>
                                <option value="">Select ingredient...</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" data-unit="{{ $ing->unit }}" data-cost="{{ $ing->cost_per_unit }}">
                                        {{ $ing->name }} (Current Total: {{ $ing->current_stock }} {{ $ing->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Batch Number <span class="text-danger">*</span></label>
                            <input type="text" name="batch_number" class="form-control" placeholder="e.g. BATCH-2026-CHK05" value="{{ old('batch_number', 'BATCH-' . date('Ymd') . '-' . rand(100, 999)) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Quantity Received <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="50.00" value="{{ old('quantity') }}" required>
                                <span class="input-group-text" id="unitDisplay">unit</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', now()->addDays(14)->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Cost Per Unit (BDT) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" min="0" name="cost_per_unit" id="costInput" class="form-control" placeholder="320.00" value="{{ old('cost_per_unit') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Supplier Name</label>
                            <input type="text" name="supplier" class="form-control" placeholder="e.g. Dhaka Fresh Farms" value="{{ old('supplier') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1"></i> Register Batch &amp; Add Stock
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info text-info me-1"></i>Batch Expiry Policy</h6>
                <p class="small text-muted mb-2">When a batch expires past its scheduled expiry date:</p>
                <ul class="small text-muted ps-3 mb-3">
                    <li>It triggers an automatic critical alert.</li>
                    <li>Its remaining stock is excluded from the dish preparation checks.</li>
                    <li>Can be easily routed into Waste Reporting &amp; Loss Recording.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ingSelect = document.getElementById('ingredientSelect');
    const unitDisplay = document.getElementById('unitDisplay');
    const costInput = document.getElementById('costInput');

    ingSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            unitDisplay.textContent = opt.dataset.unit || 'unit';
            if (!costInput.value) {
                costInput.value = parseFloat(opt.dataset.cost || 0).toFixed(2);
            }
        }
    });
});
</script>
@endpush
