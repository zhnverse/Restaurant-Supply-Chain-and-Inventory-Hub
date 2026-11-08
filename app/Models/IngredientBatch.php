<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'batch_number',
        'quantity',
        'remaining_quantity',
        'expiry_date',
        'cost_per_unit',
        'supplier',
    ];

    protected $casts = [
        'expiry_date'        => 'date',
        'quantity'           => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
        'cost_per_unit'      => 'decimal:2',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function wasteReports()
    {
        return $this->hasMany(WasteReport::class, 'batch_id');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->expiry_date && !$this->isExpired()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }
}
