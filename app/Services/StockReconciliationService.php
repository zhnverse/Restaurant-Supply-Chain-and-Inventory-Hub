<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockReconciliation;
use App\Models\StockReconciliationItem;
use Illuminate\Support\Facades\DB;

class StockReconciliationService
{
    /**
     * Create a new physical stock reconciliation submission.
     */
    public function createReconciliation(array $data, int $submittedBy): StockReconciliation
    {
        return DB::transaction(function () use ($data, $submittedBy) {
            $reconciliation = StockReconciliation::create([
                'restaurant_id' => 1,
                'submitted_by'  => $submittedBy,
                'status'        => 'pending',
                'notes'         => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $ingredientId => $item) {
                $ingredient = Ingredient::findOrFail($ingredientId);

                StockReconciliationItem::create([
                    'reconciliation_id' => $reconciliation->id,
                    'ingredient_id'     => $ingredientId,
                    'recorded_stock'    => $ingredient->current_stock,
                    'physical_count'    => $item['physical_count'],
                    'adjustment_reason' => $item['reason'] ?? null,
                ]);
            }

            return $reconciliation->load('items.ingredient', 'submitter');
        });
    }

    /**
     * Approve reconciliation: update each ingredient's stock by the difference.
     */
    public function approve(StockReconciliation $reconciliation, int $approverId, ?string $notes = null): void
    {
        if (!$reconciliation->isPending()) {
            throw new \RuntimeException('Only pending reconciliations can be approved.');
        }

        DB::transaction(function () use ($reconciliation, $approverId, $notes) {
            foreach ($reconciliation->items as $item) {
                $ingredient = $item->ingredient;
                $difference = (float)$item->physical_count - (float)$item->recorded_stock;

                // Update stock: add the difference (positive = surplus, negative = shortage)
                $ingredient->current_stock = max(0, $ingredient->current_stock + $difference);
                $ingredient->save();
            }

            $reconciliation->update([
                'status'        => 'approved',
                'approved_by'   => $approverId,
                'approved_at'   => now(),
                'manager_notes' => $notes,
            ]);
        });
    }

    /**
     * Reject reconciliation without changing stock.
     */
    public function reject(StockReconciliation $reconciliation, int $approverId, ?string $notes = null): void
    {
        if (!$reconciliation->isPending()) {
            throw new \RuntimeException('Only pending reconciliations can be rejected.');
        }

        $reconciliation->update([
            'status'        => 'rejected',
            'approved_by'   => $approverId,
            'approved_at'   => now(),
            'manager_notes' => $notes,
        ]);
    }

    /**
     * Get dashboard stats.
     */
    public function getStats(): array
    {
        return [
            'total'    => StockReconciliation::count(),
            'pending'  => StockReconciliation::where('status', 'pending')->count(),
            'approved' => StockReconciliation::where('status', 'approved')->count(),
            'rejected' => StockReconciliation::where('status', 'rejected')->count(),
        ];
    }
}
