<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('ordered_at')->nullable()->after('order_date');
            $table->timestamp('shipping_at')->nullable()->after('ordered_at');
            $table->timestamp('delivered_at')->nullable()->after('shipping_at');
            $table->timestamp('completed_at')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['ordered_at', 'shipping_at', 'delivered_at', 'completed_at']);
        });
    }
};
