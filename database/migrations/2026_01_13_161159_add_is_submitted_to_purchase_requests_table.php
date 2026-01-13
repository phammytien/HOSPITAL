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
        if (!Schema::hasColumn('purchase_requests', 'is_submitted')) {
            Schema::table('purchase_requests', function (Blueprint $table) {
                $table->boolean('is_submitted')->default(false)->after('status')->comment('Đã gửi yêu cầu hay chưa (false=Draft, true=Submitted)');
            });
        }

        if (!Schema::hasColumn('purchase_request_items', 'is_submitted')) {
            Schema::table('purchase_request_items', function (Blueprint $table) {
                $table->boolean('is_submitted')->default(false)->after('reason')->comment('Đã gửi hay chưa');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('is_submitted');
        });

        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->dropColumn('is_submitted');
        });
    }
};
