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
        'cost_per_unit',
        'category',
    ];

    protected $casts = [
        'current_stock'     => 'decimal:2',
        'reorder_threshold' => 'decimal:2',
        'cost_per_unit'     => 'decimal:2',
    ];

    public function batches()
    {
        return $this->hasMany(IngredientBatch::class);
    }

    public function reconciliationItems()
    {
        return $this->hasMany(StockReconciliationItem::class);
    }

    public function wasteReports()
    {
        return $this->hasMany(WasteReport::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->reorder_threshold;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) return 'out_of_stock';
        if ($this->current_stock <= $this->reorder_threshold) return 'low';
        return 'ok';
    }
}
