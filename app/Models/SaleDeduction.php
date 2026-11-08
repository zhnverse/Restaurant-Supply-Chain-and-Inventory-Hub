<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'ingredient_id',
        'quantity_deducted',
    ];

    protected $casts = [
        'quantity_deducted' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
