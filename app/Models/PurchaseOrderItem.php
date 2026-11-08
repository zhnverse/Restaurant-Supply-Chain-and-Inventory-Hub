<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'ingredient_id',
        'current_stock',
        'reorder_threshold',
        'target_stock',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'reorder_threshold' => 'decimal:2',
        'target_stock' => 'decimal:2',
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
