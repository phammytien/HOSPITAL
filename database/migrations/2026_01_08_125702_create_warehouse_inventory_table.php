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
        Schema::create('warehouse_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_id');
            $table->string('transaction_type', 20); // IMPORT | EXPORT
            $table->decimal('quantity', 10, 2);
            $table->unsignedBigInteger('related_order_id')->nullable();
            $table->unsignedBigInteger('related_request_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('warehouses')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            $table->foreign('related_order_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('set null');

            $table->foreign('related_request_id')
                  ->references('id')
                  ->on('purchase_requests')
                  ->onDelete('set null');

            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  ->onDelete('set null');

            $table->foreign('performed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_inventory');
    }
};
