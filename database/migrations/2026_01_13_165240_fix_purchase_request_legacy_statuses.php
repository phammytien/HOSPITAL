<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update DRAFT requests: is_submitted = 0, status = NULL
        DB::table('purchase_requests')
            ->where('status', 'DRAFT')
            ->update([
                'is_submitted' => false,
                'status' => null
            ]);

        // Update SUBMITTED and PROCESSING requests: is_submitted = 1, status = NULL
        DB::table('purchase_requests')
            ->whereIn('status', ['SUBMITTED', 'PROCESSING'])
            ->update([
                'is_submitted' => true,
                'status' => null
            ]);

        // Also ensure purchase_request_items are consistent (though logic mostly relies on parenet)
        // Update items based on parent is_submitted status if needed, but the main driver is purchase_requests table
        // We can update items to match parent just in case
        DB::statement("
            UPDATE purchase_request_items 
            JOIN purchase_requests ON purchase_request_items.purchase_request_id = purchase_requests.id
            SET purchase_request_items.is_submitted = purchase_requests.is_submitted
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way migration, cannot easily revert without complex logic
    }
};
