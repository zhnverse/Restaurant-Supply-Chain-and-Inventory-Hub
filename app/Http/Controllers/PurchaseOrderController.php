<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use App\Services\ReorderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private PurchaseOrderService $poService,
        private ReorderService $reorderService
    ) {}

    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = PurchaseOrder::with(['items.ingredient', 'generator', 'approver']);

        if ($status && in_array($status, ['draft', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15);
        $stats = $this->poService->getStats();
        $lowStockIngredients = $this->reorderService->getLowStockIngredients();

        return view('purchase-orders.index', compact('orders', 'stats', 'status', 'lowStockIngredients'));
    }

    /**
     * Feature 3: Trigger automatic reorder drafting.
     */
    public function autoDraft(Request $request)
    {
        $userId = Auth::id() ?? (\App\Models\User::first()->id ?? 1);
        $draft = $this->reorderService->generateAutomaticDraft($userId);

        if (!$draft) {
            return redirect()->route('purchase-orders.index')
                ->with('info', 'No new reorder drafts needed. Usable stock for all ingredients is currently above thresholds, or existing draft purchase orders already cover them.');
        }

        return redirect()->route('purchase-orders.show', $draft)
            ->with('success', "Draft purchase order #{$draft->po_number} generated with {$draft->items->count()} low-stock ingredient(s). Awaiting manager approval.");
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('purchase-orders.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier' => 'required|string|max:255',
            'notes'    => 'nullable|string|max:1000',
            'items'    => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_cost'     => 'required|numeric|min:0',
        ]);

        $userId = Auth::id() ?? (\App\Models\User::first()->id ?? 1);
        $po = $this->poService->createManualOrder($request->all(), $userId);

        return redirect()->route('purchase-orders.show', $po)
            ->with('success', "Draft purchase order #{$po->po_number} created successfully.");
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.ingredient', 'generator', 'approver']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Feature 4: Purchase order approval with quantity adjustments.
     */
    public function approve(Request $request, int $id)
    {
        $request->validate([
            'manager_notes' => 'nullable|string|max:1000',
            'items'         => 'nullable|array',
            'items.*.quantity' => 'nullable|numeric|min:0.01',
        ]);

        try {
            $approverId = Auth::id() ?? (\App\Models\User::first()->id ?? 1);
            $po = $this->poService->approveOrder(
                $id,
                $approverId,
                $request->manager_notes,
                $request->input('items', [])
            );

            return redirect()->route('purchase-orders.show', $po)
                ->with('success', "Purchase order #{$po->po_number} approved! Order is now authorized for supplier fulfillment.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Feature 4: Purchase order rejection.
     */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'manager_notes' => 'required|string|max:1000',
        ]);

        try {
            $approverId = Auth::id() ?? (\App\Models\User::first()->id ?? 1);
            $po = $this->poService->rejectOrder($id, $approverId, $request->manager_notes);

            return redirect()->route('purchase-orders.show', $po)
                ->with('warning', "Purchase order #{$po->po_number} rejected. Order status set to rejected.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
