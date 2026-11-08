<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\Sale;
use App\Models\SaleDeduction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    /**
     * Feature 1: Recipe-based inventory deduction.
     * Records a dish sale and automatically deducts the ingredient quantities.
     * Rejects the transaction if usable stock is insufficient.
     */
    public function recordSale(int $dishId, int $quantity, int $recordedBy, ?string $notes = null): Sale
    {
        $dish = Dish::with('recipeIngredients.ingredient.batches')->findOrFail($dishId);

        if ($dish->recipeIngredients->isEmpty()) {
            throw new RuntimeException("Dish '{$dish->name}' does not have any recipe ingredients defined.");
        }

        // Validate stock availability
        $availability = $dish->checkStockAvailability($quantity);
        if (!$availability['possible']) {
            $shortageList = [];
            foreach ($availability['shortages'] as $s) {
                $shortageList[] = "{$s['ingredient']}: needed {$s['needed']} {$s['unit']}, available {$s['available']} {$s['unit']} (shortage: {$s['shortage']} {$s['unit']})";
            }
            throw new RuntimeException("Sale rejected due to insufficient usable stock: " . implode('; ', $shortageList));
        }

        return DB::transaction(function () use ($dish, $quantity, $recordedBy, $notes) {
            $totalAmount = (float)$dish->price * $quantity;

            $sale = Sale::create([
                'dish_id'      => $dish->id,
                'quantity'     => $quantity,
                'unit_price'   => $dish->price,
                'total_amount' => $totalAmount,
                'recorded_by'  => $recordedBy,
                'notes'        => $notes,
            ]);

            // Deduct each recipe ingredient
            foreach ($dish->recipeIngredients as $recipeItem) {
                $ingredient = $recipeItem->ingredient;
                $deductQty = (float)$recipeItem->quantity * $quantity;

                // Create audit log of deduction
                SaleDeduction::create([
                    'sale_id'           => $sale->id,
                    'ingredient_id'     => $ingredient->id,
                    'quantity_deducted' => $deductQty,
                ]);

                // Atomically deduct from current stock
                $ingredient->current_stock = max(0, (float)$ingredient->current_stock - $deductQty);
                $ingredient->save();

                // Also deduct from earliest active batch (FIFO)
                $remainingToDeduct = $deductQty;
                $batches = $ingredient->batches()
                    ->where('remaining_quantity', '>', 0)
                    ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    $take = min((float)$batch->remaining_quantity, $remainingToDeduct);
                    $batch->remaining_quantity -= $take;
                    $batch->save();
                    $remainingToDeduct -= $take;
                }
            }

            return $sale->load(['dish', 'deductions.ingredient']);
        });
    }

    /**
     * Get recent sales with dish and recorder.
     */
    public function getRecentSales(int $limit = 10)
    {
        return Sale::with(['dish', 'recorder', 'deductions.ingredient'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
