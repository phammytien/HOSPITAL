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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 50)->unique()->nullable();
            $table->foreignId('department_id')->constrained('departments');
            $table->string('period', 20);
            $table->foreignId('requested_by')->nullable()->constrained('users');
            $table->string('status', 50)->nullable(); // CHECK constraint usually not added in Laravel migration builder directly but can be valid.
            $table->text('note')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
