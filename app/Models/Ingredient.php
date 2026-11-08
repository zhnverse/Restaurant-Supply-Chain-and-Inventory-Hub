<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'unit',
        'current_stock',
        'reorder_threshold',
        'target_stock',
        'cost_per_unit',
        'category',
    ];

    protected $casts = [
        'current_stock'     => 'decimal:2',
        'reorder_threshold' => 'decimal:2',
        'target_stock'      => 'decimal:2',
        'cost_per_unit'     => 'decimal:2',
    ];

    public function batches()
    {
        return $this->hasMany(IngredientBatch::class);
    }

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'recipe_ingredients');
    }

    public function reconciliationItems()
    {
        return $this->hasMany(StockReconciliationItem::class);
    }

    public function wasteReports()
    {
        return $this->hasMany(WasteReport::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Feature 2: Exclude expired stock from usable inventory.
     * Usable stock = current_stock - (sum of remaining_quantity in expired batches).
     */
    public function getUsableStockAttribute(): float
    {
        $expiredStock = $this->batches()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->sum('remaining_quantity');

        return max(0, (float)$this->current_stock - (float)$expiredStock);
    }

    /**
     * Check if usable stock is at or below reorder threshold.
     */
    public function isLowStock(): bool
    {
        return $this->usable_stock <= (float)$this->reorder_threshold;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->usable_stock <= 0) return 'out_of_stock';
        if ($this->usable_stock <= (float)$this->reorder_threshold) return 'low';
        return 'ok';
    }
}
