<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Sale;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function index()
    {
        $sales = Sale::with(['dish', 'recorder', 'deductions.ingredient'])
            ->latest()
            ->paginate(15);

        $totalRevenue = Sale::sum('total_amount');
        $totalOrders = Sale::count();

        return view('sales.index', compact('sales', 'totalRevenue', 'totalOrders'));
    }

    public function create()
    {
        $dishes = Dish::where('is_active', true)
            ->with(['recipeIngredients.ingredient.batches'])
            ->get();

        return view('sales.create', compact('dishes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dish_id'  => 'required|exists:dishes,id',
            'quantity' => 'required|integer|min:1|max:100',
            'notes'    => 'nullable|string|max:500',
        ]);

        try {
            $userId = Auth::id() ?? (\App\Models\User::first()->id ?? 1);
            $sale = $this->saleService->recordSale(
                $request->dish_id,
                $request->quantity,
                $userId,
                $request->notes
            );

            return redirect()->route('sales.index')
                ->with('success', "Order recorded successfully! {$sale->quantity}x {$sale->dish->name} prepared and required ingredients automatically deducted from inventory.");
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['stock_error' => $e->getMessage()]);
        }
    }
}
