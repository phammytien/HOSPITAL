<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add 'important' to the enum type
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('info', 'success', 'warning', 'error', 'important') DEFAULT 'info'");
        
        // Step 2: Update all existing 'success' records to 'important'
        DB::table('notifications')
            ->where('type', 'success')
            ->update(['type' => 'important']);
        
        // Step 3: Remove 'success' from the enum type
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('info', 'warning', 'error', 'important') DEFAULT 'info'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add 'success' back to the enum
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('info', 'success', 'warning', 'error', 'important') DEFAULT 'info'");
        
        // Step 2: Update all 'important' records back to 'success'
        DB::table('notifications')
            ->where('type', 'important')
            ->update(['type' => 'success']);
        
        // Step 3: Remove 'important' from the enum type
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info'");
    }
};
