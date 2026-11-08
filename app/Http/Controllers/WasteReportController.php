<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\WasteReport;
use App\Services\WasteReportService;
use Illuminate\Http\Request;

class WasteReportController extends Controller
{
    public function __construct(private WasteReportService $service) {}

    public function index(Request $request)
    {
        $status = $request->get('status');
        $query  = WasteReport::with(['ingredient', 'reporter', 'approver', 'batch']);

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $reports     = $query->latest()->paginate(15);
        $stats       = $this->service->getStats();
        $currentStatus = $status;

        return view('waste-reports.index', compact('reports', 'stats', 'currentStatus'));
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        $batches     = IngredientBatch::with('ingredient')->orderBy('expiry_date')->get();
        $reasons     = WasteReport::REASONS;

        return view('waste-reports.create', compact('ingredients', 'batches', 'reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id'  => 'required|exists:ingredients,id',
            'batch_id'       => 'nullable|exists:ingredient_batches,id',
            'quantity'       => 'required|numeric|min:0.01',
            'reason'         => 'required|in:damaged,spoiled,expired,contaminated,other',
            'description'    => 'nullable|string|max:1000',
            'estimated_loss' => 'nullable|numeric|min:0',
        ]);

        // Validate quantity doesn't exceed current stock
        $ingredient = Ingredient::findOrFail($request->ingredient_id);
        if ($request->quantity > $ingredient->current_stock) {
            return back()
                ->withErrors(['quantity' => "Quantity cannot exceed current stock ({$ingredient->current_stock} {$ingredient->unit})."])
                ->withInput();
        }

        $report = $this->service->createReport($request->all(), Auth::id() ?? (\App\Models\User::first()->id ?? 1));

        return redirect()
            ->route('waste-reports.show', $report)
            ->with('success', 'Waste report submitted successfully. Awaiting manager approval.');
    }

    public function show(WasteReport $wasteReport)
    {
        $wasteReport->load(['ingredient', 'batch', 'reporter', 'approver']);
        return view('waste-reports.show', compact('wasteReport'));
    }

    public function approve(Request $request, int $id)
    {
        $request->validate(['manager_notes' => 'nullable|string|max:1000']);
        $report = WasteReport::findOrFail($id);

        try {
            $this->service->approve($report, Auth::id() ?? (\App\Models\User::first()->id ?? 1), $request->manager_notes);
            return redirect()
                ->route('waste-reports.show', $report)
                ->with('success', "Waste report approved. {$report->quantity} {$report->ingredient->unit} deducted from stock.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['manager_notes' => 'nullable|string|max:1000']);
        $report = WasteReport::findOrFail($id);

        try {
            $this->service->reject($report, Auth::id() ?? (\App\Models\User::first()->id ?? 1), $request->manager_notes);
            return redirect()
                ->route('waste-reports.show', $report)
                ->with('warning', 'Waste report rejected. Stock levels unchanged.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
