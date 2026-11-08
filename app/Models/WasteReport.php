<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'ingredient_id',
        'batch_id',
        'reported_by',
        'quantity',
        'reason',
        'description',
        'estimated_loss',
        'status',
        'manager_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'quantity'       => 'decimal:2',
        'estimated_loss' => 'decimal:2',
        'approved_at'    => 'datetime',
    ];

    const REASONS = [
        'damaged'      => 'Damaged',
        'spoiled'      => 'Spoiled',
        'expired'      => 'Expired',
        'contaminated' => 'Contaminated',
        'other'        => 'Other',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function batch()
    {
        return $this->belongsTo(IngredientBatch::class, 'batch_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? ucfirst($this->reason);
    }
}
