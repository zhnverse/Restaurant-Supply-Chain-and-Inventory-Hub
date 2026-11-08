<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockReconciliation;
use App\Services\StockReconciliationService;
use Illuminate\Http\Request;

class StockReconciliationController extends Controller
{
    public function __construct(private StockReconciliationService $service) {}

    public function index(Request $request)
    {
        $status = $request->get('status');
        $query  = StockReconciliation::with(['submitter', 'approver', 'items']);

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $reconciliations = $query->latest()->paginate(15);
        $stats           = $this->service->getStats();
        $currentStatus   = $status;

        return view('stock-reconciliations.index', compact('reconciliations', 'stats', 'currentStatus'));
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('stock-reconciliations.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notes'                     => 'nullable|string|max:1000',
            'items'                     => 'required|array|min:1',
            'items.*.physical_count'    => 'required|numeric|min:0',
            'items.*.reason'            => 'nullable|string|max:500',
        ]);

        // Filter out items not submitted
        $items = collect($request->items)
            ->filter(fn($item) => isset($item['physical_count']) && $item['physical_count'] !== '')
            ->toArray();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Please enter at least one physical count.'])->withInput();
        }

        $reconciliation = $this->service->createReconciliation(
            ['notes' => $request->notes, 'items' => $items],
            auth()->id() ?? 1
        );

        return redirect()
            ->route('stock-reconciliations.show', $reconciliation)
            ->with('success', 'Stock reconciliation submitted successfully. Awaiting manager approval.');
    }

    public function show(StockReconciliation $stockReconciliation)
    {
        $stockReconciliation->load(['submitter', 'approver', 'items.ingredient']);
        return view('stock-reconciliations.show', compact('stockReconciliation'));
    }

    public function approve(Request $request, int $id)
    {
        $request->validate(['manager_notes' => 'nullable|string|max:1000']);
        $reconciliation = StockReconciliation::findOrFail($id);

        try {
            $this->service->approve($reconciliation, auth()->id() ?? 1, $request->manager_notes);
            return redirect()
                ->route('stock-reconciliations.show', $reconciliation)
                ->with('success', 'Reconciliation approved. Stock levels have been updated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['manager_notes' => 'nullable|string|max:1000']);
        $reconciliation = StockReconciliation::findOrFail($id);

        try {
            $this->service->reject($reconciliation, auth()->id() ?? 1, $request->manager_notes);
            return redirect()
                ->route('stock-reconciliations.show', $reconciliation)
                ->with('warning', 'Reconciliation rejected. Stock levels unchanged.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
