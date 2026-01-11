<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchase_order_items')->insert([
            // Order 1 items
            [
                'purchase_order_id' => 1,
                'product_id' => 1,
                'quantity' => 5,
                'unit_price' => 1500000,
                'status' => 'PENDING',
            ],
            [
                'purchase_order_id' => 1,
                'product_id' => 2,
                'quantity' => 10,
                'unit_price' => 300000,
                'status' => 'PENDING',
            ],
            // Order 2 items
            [
                'purchase_order_id' => 2,
                'product_id' => 3, // Giấy A4
                'quantity' => 20,
                'unit_price' => 75000,
                'status' => 'PENDING',
            ],
            // Order 3 items
            [
                'purchase_order_id' => 3,
                'product_id' => 4, // Máy tính
                'quantity' => 2,
                'unit_price' => 15000000,
                'status' => 'PENDING',
            ],
            // Order 4 items
            [
                'purchase_order_id' => 4,
                'product_id' => 1,
                'quantity' => 3,
                'unit_price' => 1500000,
                'status' => 'PENDING',
            ],
            // Order 5 items
            [
                'purchase_order_id' => 5,
                'product_id' => 2, // Nhiệt kế
                'quantity' => 20,
                'unit_price' => 300000,
                'status' => 'PENDING',
            ],
            // Order 6 items
            [
                'purchase_order_id' => 6,
                'product_id' => 1, // Máy đo huyết áp
                'quantity' => 4,
                'unit_price' => 1500000,
                'status' => 'PENDING',
            ],
            [
                'purchase_order_id' => 6,
                'product_id' => 3, // Giấy A4
                'quantity' => 30,
                'unit_price' => 75000,
                'status' => 'PENDING',
            ],
        ]);
    }
}
