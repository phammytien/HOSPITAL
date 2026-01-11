<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'product_code' => 'YT001',
                'product_name' => 'Máy đo huyết áp',
                'category_id' => 1,
                'unit' => 'Cái',
                'unit_price' => 1500000,
                'stock_quantity' => 10,
                'description' => 'Thiết bị y tế',
                'created_at' => now(),
            ],
            [
                'product_code' => 'YT002',
                'product_name' => 'Nhiệt kế điện tử',
                'category_id' => 1,
                'unit' => 'Cái',
                'unit_price' => 300000,
                'stock_quantity' => 20,
                'description' => 'Thiết bị y tế',
                'created_at' => now(),
            ],
            [
                'product_code' => 'VP001',
                'product_name' => 'Giấy A4',
                'category_id' => 2,
                'unit' => 'Ream',
                'unit_price' => 75000,
                'stock_quantity' => 100,
                'description' => 'Văn phòng',
                'created_at' => now(),
            ],
            [
                'product_code' => 'CNTT001',
                'product_name' => 'Máy tính để bàn',
                'category_id' => 3,
                'unit' => 'Bộ',
                'unit_price' => 15000000,
                'stock_quantity' => 5,
                'description' => 'Thiết bị CNTT',
                'created_at' => now(),
            ],
        ]);
    }
}
