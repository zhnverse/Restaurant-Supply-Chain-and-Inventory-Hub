<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $window = (int)$request->get('window', 7); // configurable warning window in days

        $query = IngredientBatch::with('ingredient');

        if ($status === 'expired') {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now()->toDateString());
        } elseif ($status === 'critical') {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now()->toDateString())
                ->where('expiry_date', '<=', now()->addDays(3)->toDateString());
        } elseif ($status === 'warning') {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now()->toDateString())
                ->where('expiry_date', '<=', now()->addDays($window)->toDateString());
        } elseif ($status === 'fresh') {
            $query->where(function ($q) use ($window) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now()->addDays($window)->toDateString());
            });
        }

        $batches = $query->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC')
            ->paginate(15);

        // Calculate statistics
        $allBatches = IngredientBatch::all();
        $expiredCount = $allBatches->filter(fn($b) => $b->isExpired())->count();
        $criticalCount = $allBatches->filter(fn($b) => $b->days_remaining >= 0 && $b->days_remaining <= 3)->count();
        $warningCount = $allBatches->filter(fn($b) => $b->days_remaining >= 0 && $b->days_remaining <= $window)->count();

        // Calculate total stock vs usable stock across all ingredients
        $ingredients = Ingredient::with('batches')->get();
        $totalRecordedStock = $ingredients->sum('current_stock');
        $totalUsableStock = $ingredients->sum('usable_stock');
        $totalExpiredQuantity = max(0, $totalRecordedStock - $totalUsableStock);

        return view('batches.index', compact(
            'batches',
            'status',
            'window',
            'expiredCount',
            'criticalCount',
            'warningCount',
            'totalRecordedStock',
            'totalUsableStock',
            'totalExpiredQuantity'
        ));
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('batches.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'batch_number'  => 'required|string|unique:ingredient_batches,batch_number|max:100',
            'quantity'      => 'required|numeric|min:0.01',
            'expiry_date'   => 'nullable|date',
            'cost_per_unit' => 'required|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $ingredient = Ingredient::findOrFail($request->ingredient_id);

            IngredientBatch::create([
                'ingredient_id'      => $ingredient->id,
                'batch_number'       => $request->batch_number,
                'quantity'           => $request->quantity,
                'remaining_quantity' => $request->quantity,
                'expiry_date'        => $request->expiry_date,
                'cost_per_unit'      => $request->cost_per_unit,
                'supplier'           => $request->supplier,
            ]);

            // Add batch quantity to ingredient stock
            $ingredient->current_stock += (float)$request->quantity;
            $ingredient->save();
        });

        return redirect()->route('batches.index')
            ->with('success', "Batch '{$request->batch_number}' registered and stock updated.");
    }
}
