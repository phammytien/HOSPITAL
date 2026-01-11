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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 50)->unique()->nullable();
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('order_date')->nullable();
            $table->decimal('total_amount', 18, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->boolean('is_delete')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
