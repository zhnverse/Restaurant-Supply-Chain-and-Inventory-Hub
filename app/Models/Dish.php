<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'is_active',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Check if there is enough usable stock to prepare the given quantity of this dish.
     * Returns ['possible' => bool, 'shortages' => array]
     */
    public function checkStockAvailability(int $dishQuantity = 1): array
    {
        $shortages = [];
        $possible = true;

        foreach ($this->recipeIngredients as $recipeItem) {
            $ingredient = $recipeItem->ingredient;
            $needed = (float)$recipeItem->quantity * $dishQuantity;
            $available = $ingredient->usable_stock;

            if ($available < $needed) {
                $possible = false;
                $shortages[] = [
                    'ingredient' => $ingredient->name,
                    'needed' => $needed,
                    'available' => $available,
                    'shortage' => $needed - $available,
                    'unit' => $ingredient->unit,
                ];
            }
        }

        return [
            'possible' => $possible,
            'shortages' => $shortages,
        ];
    }
}
