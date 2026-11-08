<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockReconciliation;
use App\Models\WasteReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_ingredients'      => Ingredient::count(),
            'low_stock'              => Ingredient::whereRaw('current_stock <= reorder_threshold')->count(),
            'pending_reconciliations'=> StockReconciliation::where('status', 'pending')->count(),
            'pending_waste_reports'  => WasteReport::where('status', 'pending')->count(),
            'total_waste_loss'       => WasteReport::where('status', 'approved')->sum('estimated_loss'),
        ];

        $recentReconciliations = StockReconciliation::with('submitter')
            ->latest()->limit(5)->get();

        $recentWasteReports = WasteReport::with(['ingredient', 'reporter'])
            ->latest()->limit(5)->get();

        $lowStockIngredients = Ingredient::whereRaw('current_stock <= reorder_threshold')
            ->limit(5)->get();

        return view('dashboard', compact(
            'stats',
            'recentReconciliations',
            'recentWasteReports',
            'lowStockIngredients'
        ));
    }
}
