@extends('layouts.app')

@section('title', 'Record Sale (POS)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-muted text-decoration-none">Sales</a></li>
    <li class="breadcrumb-item active">Record Sale</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-cash-register me-2 text-success"></i>Kitchen Sale (POS) &amp; Recipe Deduction</h1>
        <p>Select a dish and portion quantity. The system automatically verifies usable ingredient stock and deducts recipe quantities.</p>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-list me-1"></i> Sales Log
    </a>
</div>

@if($errors->has('stock_error'))
    <div class="alert alert-danger d-flex align-items-center gap-3 mb-4 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation fa-2x text-danger"></i>
        <div>
            <h6 class="fw-bold mb-1">Transaction Rejected: Insufficient Usable Stock</h6>
            <div class="small">{{ $errors->first('stock_error') }}</div>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Sale Form -->
    <div class="col-lg-7">
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
            @csrf

            <div class="card">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <span><i class="fa-solid fa-bowl-food me-2"></i>Order Details</span>
                    <span class="badge bg-success">POS Terminal</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Select Dish <span class="text-danger">*</span></label>
                        <select name="dish_id" class="form-select form-select-lg" id="dishSelect" required>
                            <option value="">Choose a dish...</option>
                            @foreach($dishes as $dish)
                                @php $stockCheck = $dish->checkStockAvailability(1); @endphp
                                <option value="{{ $dish->id }}"
                                        data-price="{{ $dish->price }}"
                                        data-possible="{{ $stockCheck['possible'] ? '1' : '0' }}"
                                        {{ (old('dish_id') == $dish->id || request('dish_id') == $dish->id) ? 'selected' : '' }}>
                                    {{ $dish->name }} — ৳{{ number_format($dish->price, 2) }}
                                    @if(!$stockCheck['possible']) [⚠️ Low Ingredients] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Quantity / Portions <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" id="qtyMinus">-</button>
                                <input type="number" name="quantity" id="quantityInput" class="form-control text-center fw-bold" min="1" max="50" value="{{ old('quantity', 1) }}" required>
                                <button class="btn btn-outline-secondary" type="button" id="qtyPlus">+</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Unit Price</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="text" id="unitPriceDisplay" class="form-control bg-light" value="0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer / Table Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Table 4, less spicy, takeaway" value="{{ old('notes') }}">
                    </div>

                    <!-- Total Amount Display Banner -->
                    <div class="p-3 rounded bg-light border d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold text-muted">Total Sale Amount:</span>
                        <span class="fs-4 fw-bold text-success" id="totalAmountDisplay">৳0.00</span>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn">
                        <i class="fa-solid fa-check-circle me-2"></i>Complete Sale &amp; Deduct Stock
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Live Recipe & Stock Deduction Preview -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-list-check me-2"></i>Automatic Ingredient Deduction Preview</div>
            <div class="card-body">
                <div id="recipePreviewEmpty" class="text-center py-4 text-muted">
                    <i class="fa-solid fa-utensils fa-2x mb-2 opacity-25"></i>
                    <p class="small mb-0">Select a dish to preview the exact ingredients that will be deducted from inventory.</p>
                </div>

                <div id="recipePreviewContent" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-uppercase text-muted" style="font-size:0.7rem;">Ingredients to Deduct</span>
                        <span class="small text-muted" id="portionMultiplierText">1 portion</span>
                    </div>

                    <ul class="list-group list-group-flush" id="recipeItemsList"></ul>

                    <div id="stockWarningNotice" class="alert alert-danger mt-3 small" style="display:none;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <strong>Warning:</strong> One or more ingredients have insufficient usable stock. This transaction will be rejected!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dishRecipesMap = {
    @foreach($dishes as $dish)
        {{ $dish->id }}: [
            @foreach($dish->recipeIngredients as $ri)
            {
                name: "{{ addslashes($ri->ingredient->name) }}",
                quantity: {{ (float)$ri->quantity }},
                unit: "{{ $ri->ingredient->unit }}",
                available: {{ (float)$ri->ingredient->usable_stock }}
            },
            @endforeach
        ],
    @endforeach
};

document.addEventListener('DOMContentLoaded', function() {
    const dishSelect = document.getElementById('dishSelect');
    const quantityInput = document.getElementById('quantityInput');
    const unitPriceDisplay = document.getElementById('unitPriceDisplay');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');
    const recipePreviewEmpty = document.getElementById('recipePreviewEmpty');
    const recipePreviewContent = document.getElementById('recipePreviewContent');
    const recipeItemsList = document.getElementById('recipeItemsList');
    const portionMultiplierText = document.getElementById('portionMultiplierText');
    const stockWarningNotice = document.getElementById('stockWarningNotice');
    const submitBtn = document.getElementById('submitBtn');

    function updateCalculations() {
        const selectedOption = dishSelect.options[dishSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            recipePreviewEmpty.style.display = 'block';
            recipePreviewContent.style.display = 'none';
            unitPriceDisplay.value = '0.00';
            totalAmountDisplay.textContent = '৳0.00';
            return;
        }

        const price = parseFloat(selectedOption.dataset.price) || 0;
        const qty = parseInt(quantityInput.value) || 1;
        const dishId = parseInt(selectedOption.value);
        const recipes = dishRecipesMap[dishId] || [];

        unitPriceDisplay.value = price.toFixed(2);
        totalAmountDisplay.textContent = '৳' + (price * qty).toFixed(2);

        recipePreviewEmpty.style.display = 'none';
        recipePreviewContent.style.display = 'block';
        portionMultiplierText.textContent = `${qty} portion${qty > 1 ? 's' : ''}`;

        recipeItemsList.innerHTML = '';
        let hasShortage = false;

        recipes.forEach(item => {
            const needed = (item.quantity * qty);
            const available = item.available;
            const shortage = needed > available;

            if (shortage) hasShortage = true;

            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center px-0 py-2 small';
            li.innerHTML = `
                <div>
                    <div class="fw-semibold ${shortage ? 'text-danger' : 'text-dark'}">${item.name}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Available: ${available} ${item.unit}</div>
                </div>
                <div class="text-end">
                    <span class="fw-bold ${shortage ? 'text-danger' : 'text-primary'}">-${needed.toFixed(2)} ${item.unit}</span>
                    ${shortage ? '<div class="text-danger fw-bold" style="font-size:0.7rem;">SHORTAGE</div>' : ''}
                </div>
            `;
            recipeItemsList.appendChild(li);
        });

        if (hasShortage) {
            stockWarningNotice.style.display = 'block';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-danger');
            submitBtn.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>Insufficient Stock - Cannot Complete';
        } else {
            stockWarningNotice.style.display = 'none';
            submitBtn.classList.add('btn-success');
            submitBtn.classList.remove('btn-danger');
            submitBtn.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i>Complete Sale & Deduct Stock';
        }
    }

    dishSelect.addEventListener('change', updateCalculations);
    quantityInput.addEventListener('input', updateCalculations);

    document.getElementById('qtyPlus').addEventListener('click', function() {
        quantityInput.value = parseInt(quantityInput.value || 1) + 1;
        updateCalculations();
    });

    document.getElementById('qtyMinus').addEventListener('click', function() {
        const val = parseInt(quantityInput.value || 1);
        if (val > 1) {
            quantityInput.value = val - 1;
            updateCalculations();
        }
    });

    if (dishSelect.value) {
        updateCalculations();
    }
});
</script>
@endpush
