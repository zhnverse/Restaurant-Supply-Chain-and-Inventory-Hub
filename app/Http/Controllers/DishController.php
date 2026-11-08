<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DishController extends Controller
{
    public function index()
    {
        $dishes = Dish::with(['recipeIngredients.ingredient.batches'])->latest()->get();
        return view('dishes.index', compact('dishes'));
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('dishes.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'nullable|string|max:100',
            'price'         => 'required|numeric|min:0.01',
            'description'   => 'nullable|string|max:1000',
            'recipes'       => 'required|array|min:1',
            'recipes.*.ingredient_id' => 'required|exists:ingredients,id',
            'recipes.*.quantity'      => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $dish = Dish::create([
                'name'        => $request->name,
                'category'    => $request->category,
                'price'       => $request->price,
                'description' => $request->description,
                'is_active'   => true,
            ]);

            foreach ($request->recipes as $item) {
                if (empty($item['ingredient_id']) || empty($item['quantity'])) continue;

                $ingredient = Ingredient::find($item['ingredient_id']);
                RecipeIngredient::create([
                    'dish_id'       => $dish->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity'      => $item['quantity'],
                    'unit'          => $ingredient ? $ingredient->unit : 'unit',
                ]);
            }
        });

        return redirect()->route('dishes.index')
            ->with('success', "Dish '{$request->name}' and recipe created successfully.");
    }
}
