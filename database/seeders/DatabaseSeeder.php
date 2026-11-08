<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RecipeIngredient;
use App\Models\Sale;
use App\Models\SaleDeduction;
use App\Models\StockReconciliation;
use App\Models\StockReconciliationItem;
use App\Models\User;
use App\Models\WasteReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Clear Tables ──
        SaleDeduction::query()->delete();
        Sale::query()->delete();
        RecipeIngredient::query()->delete();
        Dish::query()->delete();
        PurchaseOrderItem::query()->delete();
        PurchaseOrder::query()->delete();
        WasteReport::query()->delete();
        StockReconciliationItem::query()->delete();
        StockReconciliation::query()->delete();
        IngredientBatch::query()->delete();
        Ingredient::query()->delete();
        User::query()->delete();

        // ── 1. Users ──
        $manager = User::create([
            'name'              => 'Sarah Ahmed',
            'email'             => 'manager@restaurant.com',
            'password'          => Hash::make('password'),
            'role'              => 'manager',
            'restaurant_id'     => 1,
            'email_verified_at' => now(),
        ]);

        $staff1 = User::create([
            'name'              => 'Rahim Mia',
            'email'             => 'staff1@restaurant.com',
            'password'          => Hash::make('password'),
            'role'              => 'staff',
            'restaurant_id'     => 1,
            'email_verified_at' => now(),
        ]);

        $staff2 = User::create([
            'name'              => 'Karim Khan',
            'email'             => 'staff2@restaurant.com',
            'password'          => Hash::make('password'),
            'role'              => 'staff',
            'restaurant_id'     => 1,
            'email_verified_at' => now(),
        ]);

        // ── 2. Ingredients ──
        $ingredientsData = [
            ['name' => 'Chicken Breast',    'unit' => 'kg',     'current_stock' => 45.50,  'reorder_threshold' => 15.00, 'target_stock' => 60.00, 'cost_per_unit' => 320.00,  'category' => 'Meat'],
            ['name' => 'Basmati Rice',      'unit' => 'kg',     'current_stock' => 120.00, 'reorder_threshold' => 30.00, 'target_stock' => 150.00,'cost_per_unit' => 85.00,   'category' => 'Grains'],
            ['name' => 'Tomatoes',          'unit' => 'kg',     'current_stock' => 8.50,   'reorder_threshold' => 12.00, 'target_stock' => 25.00, 'cost_per_unit' => 60.00,   'category' => 'Vegetables'],
            ['name' => 'Onions',            'unit' => 'kg',     'current_stock' => 35.00,  'reorder_threshold' => 10.00, 'target_stock' => 40.00, 'cost_per_unit' => 40.00,   'category' => 'Vegetables'],
            ['name' => 'Cooking Oil',       'unit' => 'liters', 'current_stock' => 18.50,  'reorder_threshold' => 8.00,  'target_stock' => 30.00, 'cost_per_unit' => 150.00,  'category' => 'Oils'],
            ['name' => 'All-Purpose Flour', 'unit' => 'kg',     'current_stock' => 55.00,  'reorder_threshold' => 15.00, 'target_stock' => 60.00, 'cost_per_unit' => 65.00,   'category' => 'Grains'],
            ['name' => 'Whole Milk',        'unit' => 'liters', 'current_stock' => 4.00,   'reorder_threshold' => 10.00, 'target_stock' => 20.00, 'cost_per_unit' => 90.00,   'category' => 'Dairy'],
            ['name' => 'Butter',            'unit' => 'kg',     'current_stock' => 12.00,  'reorder_threshold' => 5.00,  'target_stock' => 15.00, 'cost_per_unit' => 450.00,  'category' => 'Dairy'],
            ['name' => 'Ground Beef',       'unit' => 'kg',     'current_stock' => 22.00,  'reorder_threshold' => 10.00, 'target_stock' => 30.00, 'cost_per_unit' => 380.00,  'category' => 'Meat'],
            ['name' => 'Bell Peppers',      'unit' => 'kg',     'current_stock' => 6.00,   'reorder_threshold' => 8.00,  'target_stock' => 15.00, 'cost_per_unit' => 120.00,  'category' => 'Vegetables'],
        ];

        $ingredients = [];
        foreach ($ingredientsData as $d) {
            $ingredients[$d['name']] = Ingredient::create(array_merge($d, ['restaurant_id' => 1]));
        }

        // ── 3. Batches (F2) ──
        // Chicken: Fresh batch
        $batchChicken = IngredientBatch::create([
            'ingredient_id'      => $ingredients['Chicken Breast']->id,
            'batch_number'       => 'BATCH-CHK-001',
            'quantity'           => 50.00,
            'remaining_quantity' => 45.50,
            'expiry_date'        => now()->addDays(12),
            'cost_per_unit'      => 320.00,
            'supplier'           => 'Dhaka Fresh Farms',
        ]);

        // Milk: Critical expiry (2 days left)
        $batchMilk = IngredientBatch::create([
            'ingredient_id'      => $ingredients['Whole Milk']->id,
            'batch_number'       => 'BATCH-MLK-001',
            'quantity'           => 10.00,
            'remaining_quantity' => 4.00,
            'expiry_date'        => now()->addDays(2),
            'cost_per_unit'      => 90.00,
            'supplier'           => 'City Dairy Ltd.',
        ]);

        // Tomatoes: EXPIRED batch (expired 1 day ago, remaining 5 kg)
        // This will demonstrate F2: 5 kg is excluded from usable stock!
        $batchTomatoes = IngredientBatch::create([
            'ingredient_id'      => $ingredients['Tomatoes']->id,
            'batch_number'       => 'BATCH-TOM-EXPIRED',
            'quantity'           => 10.00,
            'remaining_quantity' => 5.00,
            'expiry_date'        => now()->subDays(1),
            'cost_per_unit'      => 60.00,
            'supplier'           => 'Green Valley Produce',
        ]);

        // Beef: Fresh batch
        IngredientBatch::create([
            'ingredient_id'      => $ingredients['Ground Beef']->id,
            'batch_number'       => 'BATCH-BEEF-001',
            'quantity'           => 25.00,
            'remaining_quantity' => 22.00,
            'expiry_date'        => now()->addDays(20),
            'cost_per_unit'      => 380.00,
            'supplier'           => 'Bengal Meat Distributors',
        ]);

        // ── 4. Dishes & Recipes (F1) ──
        $biryani = Dish::create([
            'name'        => 'Chicken Biryani',
            'category'    => 'Main Course',
            'price'       => 380.00,
            'is_active'   => true,
            'description' => 'Aromatic basmati rice cooked with tender spiced chicken breast and caramelized onions.',
        ]);
        RecipeIngredient::create(['dish_id' => $biryani->id, 'ingredient_id' => $ingredients['Chicken Breast']->id, 'quantity' => 0.25, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $biryani->id, 'ingredient_id' => $ingredients['Basmati Rice']->id,   'quantity' => 0.20, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $biryani->id, 'ingredient_id' => $ingredients['Onions']->id,         'quantity' => 0.10, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $biryani->id, 'ingredient_id' => $ingredients['Cooking Oil']->id,    'quantity' => 0.04, 'unit' => 'liters']);

        $butterChicken = Dish::create([
            'name'        => 'Butter Chicken Curry',
            'category'    => 'Curry',
            'price'       => 420.00,
            'is_active'   => true,
            'description' => 'Rich creamy curry made with chicken breast, tomato reduction, and fresh dairy butter.',
        ]);
        RecipeIngredient::create(['dish_id' => $butterChicken->id, 'ingredient_id' => $ingredients['Chicken Breast']->id, 'quantity' => 0.30, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $butterChicken->id, 'ingredient_id' => $ingredients['Butter']->id,         'quantity' => 0.05, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $butterChicken->id, 'ingredient_id' => $ingredients['Whole Milk']->id,     'quantity' => 0.08, 'unit' => 'liters']);
        RecipeIngredient::create(['dish_id' => $butterChicken->id, 'ingredient_id' => $ingredients['Tomatoes']->id,       'quantity' => 0.15, 'unit' => 'kg']);

        $beefBurger = Dish::create([
            'name'        => 'Classic Gourmet Beef Burger',
            'category'    => 'Burgers',
            'price'       => 320.00,
            'is_active'   => true,
            'description' => 'Handcrafted 100% seasoned beef patty with fresh sliced onions and artisan bun.',
        ]);
        RecipeIngredient::create(['dish_id' => $beefBurger->id, 'ingredient_id' => $ingredients['Ground Beef']->id, 'quantity' => 0.20, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $beefBurger->id, 'ingredient_id' => $ingredients['Onions']->id,      'quantity' => 0.05, 'unit' => 'kg']);
        RecipeIngredient::create(['dish_id' => $beefBurger->id, 'ingredient_id' => $ingredients['All-Purpose Flour']->id, 'quantity' => 0.08, 'unit' => 'kg']);

        // ── 5. Sample Sales & Deductions (F1) ──
        $sale1 = Sale::create([
            'dish_id'      => $biryani->id,
            'quantity'     => 2,
            'unit_price'   => 380.00,
            'total_amount' => 760.00,
            'recorded_by'  => $staff1->id,
            'notes'        => 'Table 3 dinner order',
            'created_at'   => now()->subHours(3),
        ]);
        SaleDeduction::create(['sale_id' => $sale1->id, 'ingredient_id' => $ingredients['Chicken Breast']->id, 'quantity_deducted' => 0.50]);
        SaleDeduction::create(['sale_id' => $sale1->id, 'ingredient_id' => $ingredients['Basmati Rice']->id,   'quantity_deducted' => 0.40]);
        SaleDeduction::create(['sale_id' => $sale1->id, 'ingredient_id' => $ingredients['Onions']->id,         'quantity_deducted' => 0.20]);
        SaleDeduction::create(['sale_id' => $sale1->id, 'ingredient_id' => $ingredients['Cooking Oil']->id,    'quantity_deducted' => 0.08]);

        // ── 6. Purchase Orders (F3 & F4) ──
        // Draft Auto-Order for low items (Tomatoes, Whole Milk, Bell Peppers)
        $poDraft = PurchaseOrder::create([
            'po_number'     => 'PO-' . now()->format('Ymd') . '-0001',
            'supplier'      => 'Central Food Supplies Ltd.',
            'status'        => 'draft',
            'is_auto_draft' => true,
            'total_amount'  => 3690.00,
            'generated_by'  => $manager->id,
            'notes'         => 'System auto-drafted replenishment for ingredients below threshold.',
            'created_at'    => now()->subHours(5),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $poDraft->id,
            'ingredient_id'     => $ingredients['Tomatoes']->id,
            'current_stock'     => 3.50, // usable stock
            'reorder_threshold' => 12.00,
            'target_stock'      => 25.00,
            'quantity'          => 21.50,
            'unit_cost'         => 60.00,
            'total_cost'        => 1290.00,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $poDraft->id,
            'ingredient_id'     => $ingredients['Whole Milk']->id,
            'current_stock'     => 4.00,
            'reorder_threshold' => 10.00,
            'target_stock'      => 20.00,
            'quantity'          => 16.00,
            'unit_cost'         => 90.00,
            'total_cost'        => 1440.00,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $poDraft->id,
            'ingredient_id'     => $ingredients['Bell Peppers']->id,
            'current_stock'     => 6.00,
            'reorder_threshold' => 8.00,
            'target_stock'      => 15.00,
            'quantity'          => 8.00,
            'unit_cost'         => 120.00,
            'total_cost'        => 960.00,
        ]);

        // Approved PO (F4)
        $poApproved = PurchaseOrder::create([
            'po_number'     => 'PO-' . now()->subDays(2)->format('Ymd') . '-0002',
            'supplier'      => 'Dhaka Fresh Farms',
            'status'        => 'approved',
            'is_auto_draft' => false,
            'total_amount'  => 9600.00,
            'generated_by'  => $staff1->id,
            'notes'         => 'Weekly chicken breast restocking.',
            'manager_notes' => 'Approved. Delivery scheduled for Tuesday.',
            'approved_by'   => $manager->id,
            'approved_at'   => now()->subDays(1),
            'created_at'    => now()->subDays(2),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $poApproved->id,
            'ingredient_id'     => $ingredients['Chicken Breast']->id,
            'current_stock'     => 15.50,
            'reorder_threshold' => 15.00,
            'target_stock'      => 60.00,
            'quantity'          => 30.00,
            'unit_cost'         => 320.00,
            'total_cost'        => 9600.00,
        ]);

        // ── 7. Stock Reconciliations (F5) ──
        $recon1 = StockReconciliation::create([
            'restaurant_id' => 1,
            'submitted_by'  => $staff1->id,
            'status'        => 'pending',
            'notes'         => 'Weekend physical inventory audit.',
        ]);
        StockReconciliationItem::create(['reconciliation_id' => $recon1->id, 'ingredient_id' => $ingredients['Chicken Breast']->id, 'recorded_stock' => 45.50, 'physical_count' => 43.50, 'adjustment_reason' => 'Trimming spillage']);
        StockReconciliationItem::create(['reconciliation_id' => $recon1->id, 'ingredient_id' => $ingredients['Basmati Rice']->id,   'recorded_stock' => 120.00, 'physical_count' => 121.50, 'adjustment_reason' => 'Over-delivery sack']);

        $recon2 = StockReconciliation::create([
            'restaurant_id' => 1,
            'submitted_by'  => $staff2->id,
            'status'        => 'approved',
            'notes'         => 'Month-end storage audit.',
            'manager_notes' => 'Verified. Stock adjustments posted.',
            'approved_by'   => $manager->id,
            'approved_at'   => now()->subDays(3),
        ]);
        StockReconciliationItem::create(['reconciliation_id' => $recon2->id, 'ingredient_id' => $ingredients['Butter']->id, 'recorded_stock' => 12.00, 'physical_count' => 11.50, 'adjustment_reason' => 'Kitchen usage not logged']);

        // ── 8. Waste Reports (F6) ──
        WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $ingredients['Tomatoes']->id,
            'batch_id'       => $batchTomatoes->id,
            'reported_by'    => $staff1->id,
            'quantity'       => 3.00,
            'reason'         => 'expired',
            'description'    => 'Past expiry date from expired tomato batch.',
            'estimated_loss' => 180.00,
            'status'         => 'pending',
        ]);

        WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $ingredients['Chicken Breast']->id,
            'batch_id'       => $batchChicken->id,
            'reported_by'    => $staff2->id,
            'quantity'       => 2.00,
            'reason'         => 'damaged',
            'description'    => 'Fell from tray during transfer.',
            'estimated_loss' => 640.00,
            'status'         => 'approved',
            'manager_notes'  => 'Verified and deducted.',
            'approved_by'    => $manager->id,
            'approved_at'    => now()->subDay(),
        ]);

        $this->command->info('✅ Full Database Seeded Successfully!');
        $this->command->info('   Features Seeded: F1 (Recipes & Sales), F2 (Batches & Expiry), F3 & F4 (POs & Approvals), F5 (Reconciliation), F6 (Waste)');
        $this->command->info('   Manager: manager@restaurant.com / password');
        $this->command->info('   Staff:   staff1@restaurant.com / password');
    }
}
