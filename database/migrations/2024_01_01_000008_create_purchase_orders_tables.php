<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('target_stock', 10, 2)->default(50.00)->after('reorder_threshold');
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->string('supplier')->default('Default Supplier');
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->boolean('is_auto_draft')->default(false);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->text('manager_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained();
            $table->decimal('current_stock', 10, 2);
            $table->decimal('reorder_threshold', 10, 2);
            $table->decimal('target_stock', 10, 2);
            $table->decimal('quantity', 10, 2); // proposed order quantity
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('target_stock');
        });
    }
};
