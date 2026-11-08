@extends('layouts.app')

@section('title', 'Dishes & Recipes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dishes &amp; Recipes</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fa-solid fa-bowl-food me-2 text-primary"></i>Dishes &amp; Recipes</h1>
        <p>Define dish recipes with precise ingredient deductions per portion.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-success">
            <i class="fa-solid fa-cash-register me-1"></i> Record Sale (POS)
        </a>
        <a href="{{ route('dishes.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> New Dish &amp; Recipe
        </a>
    </div>
</div>

<div class="row g-4">
    @forelse($dishes as $dish)
        @php $stockCheck = $dish->checkStockAvailability(1); @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $dish->name }}</h6>
                        <small class="text-muted">{{ $dish->category ?? 'Main Course' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="fs-6 fw-bold text-success">৳{{ number_format($dish->price, 2) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ $dish->description ?: 'No description provided.' }}</p>

                    <h6 class="small fw-bold text-muted text-uppercase mb-2" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="fa-solid fa-receipt me-1"></i>Recipe Ingredients (Per Serving):
                    </h6>
                    <ul class="list-group list-group-flush mb-3">
                        @foreach($dish->recipeIngredients as $ri)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 small">
                                <span>{{ $ri->ingredient->name ?? 'Ingredient' }}</span>
                                <span class="fw-semibold text-primary">{{ $ri->quantity }} {{ $ri->ingredient->unit ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <div class="small">
                            Stock Status:
                            @if($stockCheck['possible'])
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Ready to prepare</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Low Ingredients</span>
                            @endif
                        </div>
                        <a href="{{ route('sales.create') }}?dish_id={{ $dish->id }}" class="btn btn-sm btn-outline-success">
                            <i class="fa-solid fa-cash-register me-1"></i> Sell
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card text-center py-5">
                <i class="fa-solid fa-bowl-food fa-3x text-muted opacity-25 mb-3"></i>
                <h5>No Dishes Created Yet</h5>
                <p class="text-muted small">Create dishes with recipe ingredient ratios to enable automatic deduction during sales.</p>
                <div class="mt-2">
                    <a href="{{ route('dishes.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i> Create First Dish
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
