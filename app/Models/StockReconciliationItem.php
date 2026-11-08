<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReconciliationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_id',
        'ingredient_id',
        'recorded_stock',
        'physical_count',
        'adjustment_reason',
    ];

    protected $casts = [
        'recorded_stock' => 'decimal:2',
        'physical_count' => 'decimal:2',
        'difference'     => 'decimal:2',
    ];

    // difference is a generated/computed column in SQLite we calculate manually
    public function getDifferenceAttribute(): float
    {
        return (float)$this->physical_count - (float)$this->recorded_stock;
    }

    public function reconciliation()
    {
        return $this->belongsTo(StockReconciliation::class, 'reconciliation_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function hasSurplus(): bool  { return $this->getDifferenceAttribute() > 0; }
    public function hasShortage(): bool { return $this->getDifferenceAttribute() < 0; }
    public function isBalanced(): bool  { return $this->getDifferenceAttribute() == 0; }
}
