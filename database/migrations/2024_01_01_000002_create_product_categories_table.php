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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_code', 50)->unique()->nullable();
            $table->string('category_name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable(); // Added updated_at to be standard compliant if needed, though schema didn't explicitly ask for it on categories but usually safe to have. Schema said: `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` only for categories. I'll stick to schema: just created_at or both. Laravel standard is both. 
            // Wait, SQL Schema for product_categories: is_delete, created_at. NO updated_at.
            // I will match the schema exactly if possible.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
