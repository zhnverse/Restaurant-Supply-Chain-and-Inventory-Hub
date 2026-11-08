@extends('layouts.app')

@section('title', 'Create Dish & Recipe')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dishes.index') }}" class="text-muted text-decoration-none">Dishes</a></li>
    <li class="breadcrumb-item active">New Dish</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Create Dish &amp; Recipe Definition</h1>
    <p>Define the dish name, selling price, and the exact ingredients needed per serving.</p>
</div>

<form method="POST" action="{{ route('dishes.store') }}" id="dishForm">
    @csrf

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><i class="fa-solid fa-bowl-rice me-2"></i>Dish Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Dish Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Chicken Biryani" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Main Course, Appetizer, Dessert" value="{{ old('category') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Selling Price (BDT) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.01" min="0.01" name="price" class="form-control" placeholder="350.00" value="{{ old('price') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Portion description or preparation notes...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="fa-solid fa-scale-balanced me-2"></i>Recipe Ingredients (Per Portion)</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addIngredientRow">
                        <i class="fa-solid fa-plus me-1"></i> Add Ingredient
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0" id="recipeTable">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th style="width:160px;">Quantity</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="recipeRows">
                            <tr class="recipe-row">
                                <td>
                                    <select name="recipes[0][ingredient_id]" class="form-select ingredient-select" required>
                                        <option value="">Select ingredient...</option>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" data-unit="{{ $ing->unit }}">
                                                {{ $ing->name }} (Available: {{ $ing->usable_stock }} {{ $ing->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0.01" name="recipes[0][quantity]" class="form-control" placeholder="0.25" required>
                                        <span class="input-group-text unit-display">unit</span>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="{{ route('dishes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1"></i> Save Dish &amp; Recipe
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const recipeRows = document.getElementById('recipeRows');
    const addBtn = document.getElementById('addIngredientRow');

    function bindUnitChange(select) {
        select.addEventListener('change', function() {
            const unit = this.options[this.selectedIndex].dataset.unit || 'unit';
            this.closest('tr').querySelector('.unit-display').textContent = unit;
        });
    }

    document.querySelectorAll('.ingredient-select').forEach(bindUnitChange);

    addBtn.addEventListener('click', function() {
        const firstRow = recipeRows.querySelector('tr');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
            input.name = `recipes[${rowIndex}][quantity]`;
        });

        const select = newRow.querySelector('select');
        select.name = `recipes[${rowIndex}][ingredient_id]`;
        select.selectedIndex = 0;
        newRow.querySelector('.unit-display').textContent = 'unit';

        const removeBtn = newRow.querySelector('.remove-row');
        removeBtn.disabled = false;
        removeBtn.addEventListener('click', function() {
            newRow.remove();
        });

        bindUnitChange(select);
        recipeRows.appendChild(newRow);
        rowIndex++;
    });
});
</script>
@endpush
