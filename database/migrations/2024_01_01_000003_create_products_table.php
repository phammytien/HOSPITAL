<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 50)->unique()->nullable();
            $table->string('product_name', 255);
            $table->foreignId('category_id')->nullable()->constrained('product_categories');
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_price', 18, 2)->nullable();
            $table->decimal('stock_quantity', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->timestamp('created_at')->useCurrent();
            // Schema for products: created_at but no updated_at.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
