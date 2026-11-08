<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientBatch;
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
        // Clear existing data
        WasteReport::query()->delete();
        StockReconciliationItem::query()->delete();
        StockReconciliation::query()->delete();
        IngredientBatch::query()->delete();
        Ingredient::query()->delete();
        User::query()->delete();

        // ── Users ──
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

        // ── Ingredients ──
        $ingredients = [
            ['name' => 'Chicken Breast',    'unit' => 'kg',     'current_stock' => 45.50,  'reorder_threshold' => 10.00, 'cost_per_unit' => 320.00,  'category' => 'Meat'],
            ['name' => 'Basmati Rice',      'unit' => 'kg',     'current_stock' => 120.00, 'reorder_threshold' => 25.00, 'cost_per_unit' => 85.00,   'category' => 'Grains'],
            ['name' => 'Tomatoes',          'unit' => 'kg',     'current_stock' => 8.50,   'reorder_threshold' => 10.00, 'cost_per_unit' => 60.00,   'category' => 'Vegetables'],
            ['name' => 'Onions',            'unit' => 'kg',     'current_stock' => 35.00,  'reorder_threshold' => 10.00, 'cost_per_unit' => 40.00,   'category' => 'Vegetables'],
            ['name' => 'Cooking Oil',       'unit' => 'liters', 'current_stock' => 18.50,  'reorder_threshold' => 5.00,  'cost_per_unit' => 150.00,  'category' => 'Oils'],
            ['name' => 'All-Purpose Flour', 'unit' => 'kg',     'current_stock' => 55.00,  'reorder_threshold' => 15.00, 'cost_per_unit' => 65.00,   'category' => 'Grains'],
            ['name' => 'Whole Milk',        'unit' => 'liters', 'current_stock' => 3.50,   'reorder_threshold' => 10.00, 'cost_per_unit' => 90.00,   'category' => 'Dairy'],
            ['name' => 'Butter',            'unit' => 'kg',     'current_stock' => 12.00,  'reorder_threshold' => 3.00,  'cost_per_unit' => 450.00,  'category' => 'Dairy'],
            ['name' => 'Ground Beef',       'unit' => 'kg',     'current_stock' => 22.00,  'reorder_threshold' => 8.00,  'cost_per_unit' => 380.00,  'category' => 'Meat'],
            ['name' => 'Bell Peppers',      'unit' => 'kg',     'current_stock' => 6.00,   'reorder_threshold' => 5.00,  'cost_per_unit' => 120.00,  'category' => 'Vegetables'],
        ];

        $createdIngredients = [];
        foreach ($ingredients as $data) {
            $createdIngredients[] = Ingredient::create(array_merge($data, ['restaurant_id' => 1]));
        }

        // ── Batches ──
        $batch1 = IngredientBatch::create([
            'ingredient_id'     => $createdIngredients[0]->id, // Chicken
            'batch_number'      => 'BATCH-CHK-001',
            'quantity'          => 50.00,
            'remaining_quantity'=> 45.50,
            'expiry_date'       => now()->addDays(5),
            'cost_per_unit'     => 320.00,
            'supplier'          => 'Dhaka Fresh Farms',
        ]);

        $batch2 = IngredientBatch::create([
            'ingredient_id'     => $createdIngredients[6]->id, // Milk
            'batch_number'      => 'BATCH-MLK-001',
            'quantity'          => 20.00,
            'remaining_quantity'=> 3.50,
            'expiry_date'       => now()->addDays(2),
            'cost_per_unit'     => 90.00,
            'supplier'          => 'City Dairy Ltd.',
        ]);

        $batch3 = IngredientBatch::create([
            'ingredient_id'     => $createdIngredients[2]->id, // Tomatoes
            'batch_number'      => 'BATCH-TOM-001',
            'quantity'          => 15.00,
            'remaining_quantity'=> 8.50,
            'expiry_date'       => now()->subDays(1), // Already expired
            'cost_per_unit'     => 60.00,
            'supplier'          => 'Green Valley Produce',
        ]);

        // ── Stock Reconciliations ──

        // Reconciliation 1: Pending
        $recon1 = StockReconciliation::create([
            'restaurant_id' => 1,
            'submitted_by'  => $staff1->id,
            'status'        => 'pending',
            'notes'         => 'End-of-week physical count — storage room A and B.',
        ]);

        StockReconciliationItem::create([
            'reconciliation_id' => $recon1->id,
            'ingredient_id'     => $createdIngredients[0]->id,
            'recorded_stock'    => 45.50,
            'physical_count'    => 43.00,
            'adjustment_reason' => 'Minor spillage during prep',
        ]);
        StockReconciliationItem::create([
            'reconciliation_id' => $recon1->id,
            'ingredient_id'     => $createdIngredients[1]->id,
            'recorded_stock'    => 120.00,
            'physical_count'    => 122.50,
            'adjustment_reason' => 'Extra delivery not logged',
        ]);
        StockReconciliationItem::create([
            'reconciliation_id' => $recon1->id,
            'ingredient_id'     => $createdIngredients[3]->id,
            'recorded_stock'    => 35.00,
            'physical_count'    => 35.00,
            'adjustment_reason' => null,
        ]);

        // Reconciliation 2: Approved
        $recon2 = StockReconciliation::create([
            'restaurant_id' => 1,
            'submitted_by'  => $staff2->id,
            'status'        => 'approved',
            'notes'         => 'Monthly audit reconciliation.',
            'manager_notes' => 'Looks accurate. Approved.',
            'approved_by'   => $manager->id,
            'approved_at'   => now()->subDays(3),
        ]);

        StockReconciliationItem::create([
            'reconciliation_id' => $recon2->id,
            'ingredient_id'     => $createdIngredients[4]->id,
            'recorded_stock'    => 20.00,
            'physical_count'    => 18.50,
            'adjustment_reason' => 'Usage not recorded in system',
        ]);

        // Reconciliation 3: Rejected
        $recon3 = StockReconciliation::create([
            'restaurant_id' => 1,
            'submitted_by'  => $staff1->id,
            'status'        => 'rejected',
            'notes'         => 'Quick spot check.',
            'manager_notes' => 'Count seems inaccurate. Please recount and resubmit.',
            'approved_by'   => $manager->id,
            'approved_at'   => now()->subDays(5),
        ]);

        StockReconciliationItem::create([
            'reconciliation_id' => $recon3->id,
            'ingredient_id'     => $createdIngredients[5]->id,
            'recorded_stock'    => 55.00,
            'physical_count'    => 20.00,
            'adjustment_reason' => 'Large discrepancy',
        ]);

        // ── Waste Reports ──

        // Waste 1: Pending
        WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $createdIngredients[2]->id, // Tomatoes
            'batch_id'       => $batch3->id,
            'reported_by'    => $staff1->id,
            'quantity'       => 3.00,
            'reason'         => 'expired',
            'description'    => 'Tomatoes from batch BATCH-TOM-001 are past expiry date and showing mold.',
            'estimated_loss' => 180.00,
            'status'         => 'pending',
        ]);

        // Waste 2: Approved
        WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $createdIngredients[0]->id, // Chicken
            'batch_id'       => $batch1->id,
            'reported_by'    => $staff2->id,
            'quantity'       => 2.50,
            'reason'         => 'damaged',
            'description'    => 'Dropped during transfer from cold storage. Contaminated with floor contact.',
            'estimated_loss' => 800.00,
            'status'         => 'approved',
            'manager_notes'  => 'Verified. Stock deducted.',
            'approved_by'    => $manager->id,
            'approved_at'    => now()->subDays(2),
        ]);

        // Waste 3: Pending
        WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $createdIngredients[6]->id, // Milk
            'batch_id'       => $batch2->id,
            'reported_by'    => $staff1->id,
            'quantity'       => 1.50,
            'reason'         => 'spoiled',
            'description'    => 'Milk left out of refrigeration for 4+ hours during power outage.',
            'estimated_loss' => 135.00,
            'status'         => 'pending',
        ]);

        // Waste 4: Rejected
        WasteReport::create([
            'restaurant_id'  => 1,
            'ingredient_id'  => $createdIngredients[1]->id, // Rice
            'batch_id'       => null,
            'reported_by'    => $staff2->id,
            'quantity'       => 10.00,
            'reason'         => 'other',
            'description'    => 'Cannot locate this stock.',
            'estimated_loss' => 850.00,
            'status'         => 'rejected',
            'manager_notes'  => 'Quantity too high without evidence. Please investigate further.',
            'approved_by'    => $manager->id,
            'approved_at'    => now()->subDays(4),
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('   Manager login: manager@restaurant.com / password');
        $this->command->info('   Staff login:   staff1@restaurant.com / password');
    }
}
