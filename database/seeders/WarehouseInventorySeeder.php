<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('warehouse_inventory')->insert([
            // IMPORT transactions
            [
                'warehouse_id' => 1, // Kho Khoa Nội
                'product_id' => 1, // Máy đo huyết áp
                'transaction_type' => 'IMPORT',
                'quantity' => 10.00,
                'related_order_id' => null,
                'related_request_id' => null,
                'supplier_id' => 1, // ABC Medical
                'performed_by' => 1, // Admin
                'note' => 'Nhập kho ban đầu - Máy đo huyết áp',
                'is_delete' => false,
                'created_at' => '2024-03-16 08:00:00',
                'updated_at' => '2024-03-16 08:00:00',
            ],
            [
                'warehouse_id' => 1,
                'product_id' => 2, // Nhiệt kế điện tử
                'transaction_type' => 'IMPORT',
                'quantity' => 20.00,
                'related_order_id' => null,
                'related_request_id' => null,
                'supplier_id' => 1,
                'performed_by' => 1,
                'note' => 'Nhập kho ban đầu - Nhiệt kế điện tử',
                'is_delete' => false,
                'created_at' => '2024-03-16 08:00:00',
                'updated_at' => '2024-03-16 08:00:00',
            ],
            [
                'warehouse_id' => 2, // Kho Khoa Ngoại
                'product_id' => 4, // Máy tính để bàn
                'transaction_type' => 'IMPORT',
                'quantity' => 5.00,
                'related_order_id' => null,
                'related_request_id' => null,
                'supplier_id' => 2,
                'performed_by' => 1,
                'note' => 'Nhập thiết bị CNTT cho khoa',
                'is_delete' => false,
                'created_at' => '2024-06-19 09:00:00',
                'updated_at' => '2024-06-19 09:00:00',
            ],
            // EXPORT transactions
            [
                'warehouse_id' => 1,
                'product_id' => 1,
                'transaction_type' => 'EXPORT',
                'quantity' => 3.00,
                'related_order_id' => null,
                'related_request_id' => null,
                'supplier_id' => null,
                'performed_by' => 3, // NV Khoa Nội
                'note' => 'Xuất kho cho khoa sử dụng',
                'is_delete' => false,
                'created_at' => '2024-04-15 10:00:00',
                'updated_at' => '2024-04-15 10:00:00',
            ],
            [
                'warehouse_id' => 1,
                'product_id' => 2,
                'transaction_type' => 'EXPORT',
                'quantity' => 5.00,
                'related_order_id' => null,
                'related_request_id' => null,
                'supplier_id' => null,
                'performed_by' => 3,
                'note' => 'Xuất kho cho khoa sử dụng',
                'is_delete' => false,
                'created_at' => '2024-04-15 10:00:00',
                'updated_at' => '2024-04-15 10:00:00',
            ],
            [
                'warehouse_id' => 3, // Kho CNTT
                'product_id' => 3, // Giấy A4
                'transaction_type' => 'IMPORT',
                'quantity' => 100.00,
                'related_order_id' => null,
                'related_request_id' => null,
                'supplier_id' => 3, // DEF Office
                'performed_by' => 1,
                'note' => 'Nhập văn phòng phẩm',
                'is_delete' => false,
                'created_at' => '2024-03-23 08:00:00',
                'updated_at' => '2024-03-23 08:00:00',
            ],
        ]);
    }
}
