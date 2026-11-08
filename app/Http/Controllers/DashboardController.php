<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockReconciliation;
use App\Models\WasteReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::with('batches')->get();
        $lowStockIngredients = $ingredients->filter(fn($ing) => $ing->isLowStock());

        $batches = IngredientBatch::all();
        $expiredBatches = $batches->filter(fn($b) => $b->isExpired());
        $expiringSoonBatches = $batches->filter(fn($b) => $b->days_remaining >= 0 && $b->days_remaining <= 7);

        $stats = [
            'total_ingredients'      => $ingredients->count(),
            'low_stock'              => $lowStockIngredients->count(),
            'expired_batches'        => $expiredBatches->count(),
            'expiring_soon_batches'  => $expiringSoonBatches->count(),
            'total_dishes'           => Dish::count(),
            'total_sales'            => Sale::count(),
            'total_revenue'          => Sale::sum('total_amount'),
            'draft_purchase_orders'  => PurchaseOrder::where('status', 'draft')->count(),
            'pending_reconciliations'=> StockReconciliation::where('status', 'pending')->count(),
            'pending_waste_reports'  => WasteReport::where('status', 'pending')->count(),
            'total_waste_loss'       => WasteReport::where('status', 'approved')->sum('estimated_loss'),
        ];

        $recentSales = Sale::with('dish', 'recorder')->latest()->limit(5)->get();
        $recentReconciliations = StockReconciliation::with('submitter')->latest()->limit(5)->get();
        $recentWasteReports = WasteReport::with(['ingredient', 'reporter'])->latest()->limit(5)->get();
        $recentPOs = PurchaseOrder::latest()->limit(5)->get();

        return view('dashboard', compact(
            'stats',
            'lowStockIngredients',
            'expiringSoonBatches',
            'recentSales',
            'recentReconciliations',
            'recentWasteReports',
            'recentPOs'
        ));
    }
}
