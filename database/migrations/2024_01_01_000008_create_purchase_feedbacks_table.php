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
        Schema::create('purchase_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->foreignId('feedback_by')->nullable()->constrained('users');
            $table->text('feedback_content');
            $table->integer('rating')->nullable();
            $table->string('status', 50)->default('PENDING');
            $table->text('admin_response')->nullable();
            $table->timestamp('response_time')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('feedback_date')->useCurrent();
            $table->boolean('is_delete')->default(false);
            $table->timestamps();

            $table->foreign('purchase_order_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_feedbacks');
    }
};
