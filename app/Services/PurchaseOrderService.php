<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseOrderService
{
    /**
     * Feature 4: Purchase-order approval.
     * Managers review, adjust quantities, approve, or reject reorder drafts.
     */
    public function approveOrder(int $orderId, int $approverId, ?string $managerNotes = null, array $adjustments = []): PurchaseOrder
    {
        $po = PurchaseOrder::with('items')->findOrFail($orderId);

        if (!$po->isDraft()) {
            throw new RuntimeException("Only draft purchase orders can be approved.");
        }

        return DB::transaction(function () use ($po, $approverId, $managerNotes, $adjustments) {
            // Apply quantity adjustments if any provided by manager
            $totalAmount = 0;
            foreach ($po->items as $item) {
                if (isset($adjustments[$item->id]['quantity'])) {
                    $newQty = max(0.01, (float)$adjustments[$item->id]['quantity']);
                    $item->quantity = $newQty;
                    $item->total_cost = round($newQty * (float)$item->unit_cost, 2);
                    $item->save();
                }
                $totalAmount += (float)$item->total_cost;
            }

            $po->update([
                'status'        => 'approved',
                'total_amount'  => $totalAmount,
                'approved_by'   => $approverId,
                'approved_at'   => now(),
                'manager_notes' => $managerNotes ?: 'Approved by manager for supplier dispatch.',
            ]);

            return $po->fresh(['items.ingredient', 'approver']);
        });
    }

    /**
     * Reject a purchase order draft.
     */
    public function rejectOrder(int $orderId, int $approverId, ?string $managerNotes = null): PurchaseOrder
    {
        $po = PurchaseOrder::findOrFail($orderId);

        if (!$po->isDraft()) {
            throw new RuntimeException("Only draft purchase orders can be rejected.");
        }

        $po->update([
            'status'        => 'rejected',
            'approved_by'   => $approverId,
            'approved_at'   => now(),
            'manager_notes' => $managerNotes ?: 'Rejected by manager.',
        ]);

        return $po->fresh(['items.ingredient', 'approver']);
    }

    /**
     * Create manual purchase order draft.
     */
    public function createManualOrder(array $data, int $userId): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $userId) {
            $poCount = PurchaseOrder::count() + 1;
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . str_pad($poCount, 4, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'po_number'     => $poNumber,
                'supplier'      => $data['supplier'] ?? 'Central Food Supplies Ltd.',
                'status'        => 'draft',
                'is_auto_draft' => false,
                'total_amount'  => 0,
                'generated_by'  => $userId,
                'notes'         => $data['notes'] ?? null,
            ]);

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                if (empty($item['quantity']) || $item['quantity'] <= 0) continue;

                $unitCost = (float)($item['unit_cost'] ?? 50.00);
                $qty = (float)$item['quantity'];
                $lineTotal = round($qty * $unitCost, 2);
                $totalAmount += $lineTotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'ingredient_id'     => $item['ingredient_id'],
                    'current_stock'     => $item['current_stock'] ?? 0,
                    'reorder_threshold' => $item['reorder_threshold'] ?? 10,
                    'target_stock'      => $item['target_stock'] ?? 50,
                    'quantity'          => $qty,
                    'unit_cost'         => $unitCost,
                    'total_cost'        => $lineTotal,
                ]);
            }

            $po->update(['total_amount' => $totalAmount]);

            return $po->fresh(['items.ingredient']);
        });
    }

    public function getStats(): array
    {
        return [
            'total'    => PurchaseOrder::count(),
            'draft'    => PurchaseOrder::where('status', 'draft')->count(),
            'approved' => PurchaseOrder::where('status', 'approved')->count(),
            'rejected' => PurchaseOrder::where('status', 'rejected')->count(),
            'total_approved_amount' => PurchaseOrder::where('status', 'approved')->sum('total_amount'),
        ];
    }
}
