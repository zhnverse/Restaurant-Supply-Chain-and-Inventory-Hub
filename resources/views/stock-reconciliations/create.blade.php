@extends('layouts.app')

@section('title', 'New Physical Count')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('stock-reconciliations.index') }}" class="text-muted text-decoration-none">Stock Reconciliation</a></li>
    <li class="breadcrumb-item active">New Physical Count</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-clipboard-list me-2 text-primary"></i>New Physical Stock Count</h1>
    <p>Enter the actual quantities counted physically. The system will calculate differences from recorded stock.</p>
</div>

<form method="POST" action="{{ route('stock-reconciliations.store') }}" id="reconciliationForm">
    @csrf

    <div class="row g-4">
        <!-- Notes -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-note-sticky me-2"></i>Count Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="e.g., End-of-day count, weekly audit...">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date &amp; Time</label>
                            <input type="text" class="form-control" value="{{ now()->format('d M Y, H:i') }}" disabled>
                            <small class="text-muted">Automatically recorded</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingredients Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="fa-solid fa-box-open me-2"></i>Ingredient Physical Counts</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAll">
                            <i class="fa-solid fa-check-double me-1"></i>Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearAll">
                            <i class="fa-solid fa-xmark me-1"></i>Clear
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0" id="ingredientsTable">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                <th>Ingredient</th>
                                <th>Category</th>
                                <th>Recorded Stock</th>
                                <th>Physical Count</th>
                                <th>Variance</th>
                                <th>Reason (if difference)</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($ingredients as $ingredient)
                            <tr class="ingredient-row" data-id="{{ $ingredient->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input row-check" value="{{ $ingredient->id }}">
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $ingredient->name }}</div>
                                    <small class="text-muted">{{ $ingredient->unit }}</small>
                                </td>
                                <td class="text-muted">{{ $ingredient->category ?? '—' }}</td>
                                <td>
                                    <span class="fw-semibold recorded-val" data-value="{{ $ingredient->current_stock }}">
                                        {{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}
                                    </span>
                                </td>
                                <td style="width:160px;">
                                    <input type="number"
                                           name="items[{{ $ingredient->id }}][physical_count]"
                                           class="form-control physical-input"
                                           step="0.01" min="0"
                                           placeholder="Enter count"
                                           value="{{ old('items.'.$ingredient->id.'.physical_count') }}"
                                           disabled>
                                </td>
                                <td class="variance-cell" style="width:120px;">
                                    <span class="diff-balanced">—</span>
                                </td>
                                <td>
                                    <input type="text"
                                           name="items[{{ $ingredient->id }}][reason]"
                                           class="form-control reason-input"
                                           placeholder="Optional reason"
                                           value="{{ old('items.'.$ingredient->id.'.reason') }}"
                                           disabled>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fa-solid fa-info-circle me-1"></i>
                        Check the box to include an ingredient in this count session.
                    </small>
                    <div>
                        <span class="text-muted small me-2">Selected: <strong id="selectedCount">0</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('stock-reconciliations.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-paper-plane me-1"></i> Submit for Approval
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.row-check');
    const selectedCount = document.getElementById('selectedCount');

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        selectedCount.textContent = checked;
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            const row = this.closest('tr');
            const physInput   = row.querySelector('.physical-input');
            const reasonInput = row.querySelector('.reason-input');

            physInput.disabled   = !this.checked;
            reasonInput.disabled = !this.checked;

            if (!this.checked) {
                physInput.value   = '';
                reasonInput.value = '';
                row.querySelector('.variance-cell').innerHTML = '<span class="diff-balanced">—</span>';
            }
            updateSelectedCount();
        });
    });

    // Live variance calculation
    document.querySelectorAll('.physical-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const row      = this.closest('tr');
            const recorded = parseFloat(row.querySelector('.recorded-val').dataset.value) || 0;
            const physical = parseFloat(this.value) || 0;
            const diff     = physical - recorded;
            const cell     = row.querySelector('.variance-cell');

            if (this.value === '') {
                cell.innerHTML = '<span class="diff-balanced">—</span>';
            } else if (diff > 0) {
                cell.innerHTML = `<span class="diff-surplus"><i class="fa-solid fa-arrow-up me-1"></i>+${diff.toFixed(2)}</span>`;
            } else if (diff < 0) {
                cell.innerHTML = `<span class="diff-shortage"><i class="fa-solid fa-arrow-down me-1"></i>${diff.toFixed(2)}</span>`;
            } else {
                cell.innerHTML = '<span class="diff-balanced"><i class="fa-solid fa-equals me-1"></i>0.00</span>';
            }
        });
    });

    // Select All
    document.getElementById('selectAll').addEventListener('click', function() {
        checkboxes.forEach(cb => {
            cb.checked = true;
            cb.dispatchEvent(new Event('change'));
        });
    });

    // Clear All
    document.getElementById('clearAll').addEventListener('click', function() {
        checkboxes.forEach(cb => {
            cb.checked = false;
            cb.dispatchEvent(new Event('change'));
        });
    });

    // Form validation
    document.getElementById('reconciliationForm').addEventListener('submit', function(e) {
        const anyChecked = document.querySelectorAll('.row-check:checked').length;
        if (anyChecked === 0) {
            e.preventDefault();
            alert('Please select at least one ingredient to count.');
        }
    });
});
</script>
@endpush
