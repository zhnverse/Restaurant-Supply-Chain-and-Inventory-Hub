<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;

class ReorderService
{
    /**
     * Feature 3: Automatic reorder drafting.
     * Checks usable stock against threshold.
     * Calculates quantity needed to reach target stock.
     * Generates a draft purchase order.
     * Prevents duplicate drafts for ingredients already in a pending draft.
     */
    public function generateAutomaticDraft(?int $userId = null): ?PurchaseOrder
    {
        // 1. Find all ingredients that have active drafts already
        $ingredientsWithDrafts = PurchaseOrderItem::whereHas('purchaseOrder', function ($q) {
            $q->where('status', 'draft');
        })->pluck('ingredient_id')->toArray();

        // 2. Find ingredients where usable stock <= threshold
        $ingredients = Ingredient::all();
        $itemsToOrder = [];

        foreach ($ingredients as $ingredient) {
            // Prevent duplicate draft: skip if already in an active draft
            if (in_array($ingredient->id, $ingredientsWithDrafts)) {
                continue;
            }

            $usable = $ingredient->usable_stock;
            $threshold = (float)$ingredient->reorder_threshold;
            $target = (float)$ingredient->target_stock;

            if ($usable <= $threshold) {
                $needed = max(1, $target - $usable);
                $unitCost = (float)$ingredient->cost_per_unit > 0 ? (float)$ingredient->cost_per_unit : 50.00;
                $totalCost = round($needed * $unitCost, 2);

                $itemsToOrder[] = [
                    'ingredient_id'     => $ingredient->id,
                    'current_stock'     => $usable,
                    'reorder_threshold' => $threshold,
                    'target_stock'      => $target,
                    'quantity'          => $needed,
                    'unit_cost'         => $unitCost,
                    'total_cost'        => $totalCost,
                ];
            }
        }

        if (empty($itemsToOrder)) {
            return null; // No ingredients need reordering or all are already drafted
        }

        return DB::transaction(function () use ($itemsToOrder, $userId) {
            $poCount = PurchaseOrder::count() + 1;
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . str_pad($poCount, 4, '0', STR_PAD_LEFT);
            $totalAmount = array_sum(array_column($itemsToOrder, 'total_cost'));

            $po = PurchaseOrder::create([
                'po_number'     => $poNumber,
                'supplier'      => 'Central Food Supplies Ltd.',
                'status'        => 'draft',
                'is_auto_draft' => true,
                'total_amount'  => $totalAmount,
                'generated_by'  => $userId,
                'notes'         => 'Automatically generated draft based on low usable inventory thresholds.',
            ]);

            foreach ($itemsToOrder as $item) {
                PurchaseOrderItem::create(array_merge($item, [
                    'purchase_order_id' => $po->id,
                ]));
            }

            return $po->load('items.ingredient');
        });
    }

    /**
     * Get list of ingredients currently low on usable stock.
     */
    public function getLowStockIngredients()
    {
        return Ingredient::with('batches')->get()->filter(function ($ing) {
            return $ing->isLowStock();
        });
    }
}
