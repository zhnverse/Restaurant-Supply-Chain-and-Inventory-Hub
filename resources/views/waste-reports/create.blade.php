@extends('layouts.app')

@section('title', 'Report Waste')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('waste-reports.index') }}" class="text-muted text-decoration-none">Waste Reports</a></li>
    <li class="breadcrumb-item active">New Report</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Report Waste</h1>
    <p>Report damaged, spoiled, or expired ingredients for manager review.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('waste-reports.store') }}" id="wasteForm">
            @csrf
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-file-pen me-2"></i>Waste Report Details
                </div>
                <div class="card-body">
                    <div class="row g-4">

                        <!-- Ingredient -->
                        <div class="col-md-6">
                            <label class="form-label">Ingredient <span class="text-danger">*</span></label>
                            <select name="ingredient_id" class="form-select" id="ingredientSelect" required>
                                <option value="">Select ingredient...</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}"
                                            data-unit="{{ $ingredient->unit }}"
                                            data-stock="{{ $ingredient->current_stock }}"
                                            data-cost="{{ $ingredient->cost_per_unit }}"
                                            {{ old('ingredient_id') == $ingredient->id ? 'selected' : '' }}>
                                        {{ $ingredient->name }} ({{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }} available)
                                    </option>
                                @endforeach
                            </select>
                            <div id="stockInfo" class="mt-2 small text-muted" style="display:none;">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Current stock: <strong id="currentStockDisplay">—</strong>
                            </div>
                        </div>

                        <!-- Batch -->
                        <div class="col-md-6">
                            <label class="form-label">Batch <span class="text-muted fw-normal">(optional)</span></label>
                            <select name="batch_id" class="form-select" id="batchSelect">
                                <option value="">No specific batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}"
                                            data-ingredient="{{ $batch->ingredient_id }}"
                                            class="batch-option"
                                            {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                        {{ $batch->batch_number }} — {{ $batch->ingredient->name ?? '' }}
                                        (Exp: {{ $batch->expiry_date ? $batch->expiry_date->format('d M Y') : 'No expiry' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reason -->
                        <div class="col-md-6">
                            <label class="form-label">Waste Reason <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select" required id="reasonSelect">
                                <option value="">Select reason...</option>
                                @foreach($reasons as $value => $label)
                                    <option value="{{ $value }}" {{ old('reason') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6">
                            <label class="form-label">Quantity Wasted <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="quantity" class="form-control" id="quantityInput"
                                       step="0.01" min="0.01"
                                       placeholder="0.00"
                                       value="{{ old('quantity') }}" required>
                                <span class="input-group-text" id="unitDisplay">unit</span>
                            </div>
                            <div id="quantityWarning" class="mt-1 small text-danger" style="display:none;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Exceeds current stock level.
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Describe the condition, what happened, etc.">{{ old('description') }}</textarea>
                        </div>

                        <!-- Estimated Loss -->
                        <div class="col-md-6">
                            <label class="form-label">Estimated Loss (BDT)</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" name="estimated_loss" class="form-control" id="estimatedLoss"
                                       step="0.01" min="0"
                                       placeholder="Auto-calculated"
                                       value="{{ old('estimated_loss') }}">
                            </div>
                            <small class="text-muted">
                                <i class="fa-solid fa-calculator me-1"></i>
                                Auto-calculated from cost per unit. Override if needed.
                            </small>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="p-3 rounded w-100" style="background:#fef2f2;border:1px solid #fecaca;">
                                <div class="text-muted small mb-1">Estimated Financial Loss</div>
                                <div class="fw-bold text-danger fs-5" id="lossPreview">৳0.00</div>
                                <small class="text-muted">Based on cost per unit × quantity</small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="{{ route('waste-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Waste Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-circle-info me-2 text-info"></i>How it Works</div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot dot-blue"></div>
                        <div class="fw-semibold small">1. Submit Report</div>
                        <div class="text-muted" style="font-size:0.8rem;">Staff reports the waste with reason and quantity.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot dot-gray"></div>
                        <div class="fw-semibold small">2. Manager Reviews</div>
                        <div class="text-muted" style="font-size:0.8rem;">Manager approves or rejects the waste report.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot dot-green"></div>
                        <div class="fw-semibold small">3. Stock Updated</div>
                        <div class="text-muted" style="font-size:0.8rem;">If approved, the quantity is deducted from inventory.</div>
                    </div>
                </div>

                <hr>

                <div class="small">
                    <div class="fw-semibold mb-2">Waste Reasons:</div>
                    <div class="d-flex flex-column gap-1">
                        <div><i class="fa-solid fa-hammer me-2 text-warning"></i><strong>Damaged</strong> — Physical damage during handling</div>
                        <div><i class="fa-solid fa-bug me-2 text-success"></i><strong>Spoiled</strong> — Gone bad naturally</div>
                        <div><i class="fa-solid fa-calendar-xmark me-2 text-danger"></i><strong>Expired</strong> — Past expiry date</div>
                        <div><i class="fa-solid fa-biohazard me-2 text-purple"></i><strong>Contaminated</strong> — Cross-contamination</div>
                        <div><i class="fa-solid fa-circle-question me-2 text-muted"></i><strong>Other</strong> — Other reason</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ingredientSelect = document.getElementById('ingredientSelect');
    const quantityInput    = document.getElementById('quantityInput');
    const unitDisplay      = document.getElementById('unitDisplay');
    const stockInfo        = document.getElementById('stockInfo');
    const currentStockDisplay = document.getElementById('currentStockDisplay');
    const estimatedLoss    = document.getElementById('estimatedLoss');
    const lossPreview      = document.getElementById('lossPreview');
    const quantityWarning  = document.getElementById('quantityWarning');
    const batchSelect      = document.getElementById('batchSelect');
    const batchOptions     = document.querySelectorAll('.batch-option');

    let currentStock = 0;
    let costPerUnit  = 0;

    ingredientSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            const unit  = opt.dataset.unit;
            const stock = parseFloat(opt.dataset.stock) || 0;
            const cost  = parseFloat(opt.dataset.cost) || 0;

            currentStock = stock;
            costPerUnit  = cost;

            unitDisplay.textContent = unit;
            stockInfo.style.display = 'block';
            currentStockDisplay.textContent = stock.toFixed(2) + ' ' + unit;

            // Filter batches
            batchOptions.forEach(bo => {
                bo.style.display = bo.dataset.ingredient == opt.value ? '' : 'none';
            });
            batchSelect.value = '';

            updateLoss();
        } else {
            unitDisplay.textContent = 'unit';
            stockInfo.style.display = 'none';
            currentStock = 0;
            costPerUnit  = 0;
            batchOptions.forEach(bo => bo.style.display = '');
        }
    });

    function updateLoss() {
        const qty = parseFloat(quantityInput.value) || 0;
        const loss = qty * costPerUnit;

        // Show warning if exceeds stock
        if (qty > currentStock && currentStock > 0) {
            quantityWarning.style.display = 'block';
        } else {
            quantityWarning.style.display = 'none';
        }

        // Auto-fill estimated loss if empty
        if (!estimatedLoss.value || estimatedLoss.dataset.autoFilled === 'true') {
            estimatedLoss.value = loss.toFixed(2);
            estimatedLoss.dataset.autoFilled = 'true';
        }

        lossPreview.textContent = '৳' + ((parseFloat(estimatedLoss.value) || loss)).toFixed(2);
    }

    quantityInput.addEventListener('input', updateLoss);

    estimatedLoss.addEventListener('input', function() {
        this.dataset.autoFilled = 'false';
        lossPreview.textContent = '৳' + (parseFloat(this.value) || 0).toFixed(2);
    });

    // Trigger change on page load if old value set
    if (ingredientSelect.value) {
        ingredientSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
