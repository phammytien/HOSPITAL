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
        Schema::create('product_proposals', function (Blueprint $table) {
            $table->id();

            // Product Information
            $table->string('product_name')->comment('Tên vật tư');
            $table->text('description')->nullable()->comment('Mô tả');

            // Initiator
            $table->unsignedBigInteger('department_id')->comment('Khoa đề xuất');
            $table->unsignedBigInteger('created_by')->comment('Người tạo (User)');

            // Status and Workflow
            $table->string('status', 20)->default('PENDING')->comment('PENDING, CREATED, APPROVED, REJECTED');
            $table->text('rejection_reason')->nullable()->comment('Lý do từ chối');

            // Roles processing the proposal
            $table->unsignedBigInteger('buyer_id')->nullable()->comment('Người đề xuất lên (Buyer)');
            $table->unsignedBigInteger('approver_id')->nullable()->comment('Người duyệt (Admin)');

            // Buyer Input Fields
            $table->string('product_code', 50)->nullable()->comment('Mã vật tư');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Danh mục');
            $table->string('unit', 50)->nullable()->comment('Đơn vị tính');
            $table->decimal('unit_price', 18, 2)->nullable()->comment('Giá');
            // $table->decimal('stock_quantity', 10, 2)->nullable()->comment('Tồn kho');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('Nhà cung cấp');
            // $table->string('image')->nullable()->comment('Hình ảnh');

            // Final Product Link
            $table->unsignedBigInteger('product_id')->nullable()->comment('Sản phẩm chính thức sau khi duyệt');

            $table->boolean('is_delete')->default(false)->comment('Xóa mềm');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_proposals');
    }
};
