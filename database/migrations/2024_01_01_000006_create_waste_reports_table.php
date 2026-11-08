<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->default(1);
            $table->foreignId('ingredient_id')->constrained('ingredients');
            $table->foreignId('batch_id')->nullable()->constrained('ingredient_batches')->nullOnDelete();
            $table->foreignId('reported_by')->constrained('users');
            $table->decimal('quantity', 10, 2);
            $table->enum('reason', ['damaged', 'spoiled', 'expired', 'contaminated', 'other']);
            $table->text('description')->nullable();
            $table->decimal('estimated_loss', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('manager_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_reports');
    }
};
