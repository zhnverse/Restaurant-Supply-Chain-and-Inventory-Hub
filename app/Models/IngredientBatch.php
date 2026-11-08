<?php

namespace App\Models;

use Carbon\Carbon;
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
        if (!$this->expiry_date || $this->isExpired()) {
            return false;
        }

        return $this->expiry_date->diffInDays(now()->startOfDay(), false) <= 0
            && abs($this->expiry_date->diffInDays(now()->startOfDay())) <= $days;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->expiry_date) return null;
        return (int)now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false);
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) return 'no_expiry';
        $days = $this->days_remaining;

        if ($days < 0) return 'expired';
        if ($days <= 3) return 'critical';
        if ($days <= 7) return 'warning';
        return 'fresh';
    }
}
