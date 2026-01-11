<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory')->insert([
            [
                'warehouse_id' => 1, // Kho Khoa Nội
                'product_id' => 1, // Máy đo huyết áp
                'quantity' => 7.00, // 10 nhập - 3 xuất = 7
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 1,
                'product_id' => 2, // Nhiệt kế điện tử
                'quantity' => 15.00, // 20 nhập - 5 xuất = 15
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 2, // Kho Khoa Ngoại
                'product_id' => 4, // Máy tính để bàn
                'quantity' => 5.00, // 5 nhập
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 3, // Kho CNTT
                'product_id' => 3, // Giấy A4
                'quantity' => 100.00, // 100 nhập
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 4, // Kho Trung Tâm
                'product_id' => 1,
                'quantity' => 0.00, // Chưa có hàng
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 4,
                'product_id' => 2,
                'quantity' => 0.00,
                'updated_at' => now(),
            ],
        ]);
    }
}
