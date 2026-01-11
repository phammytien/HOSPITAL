<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchase_request_items')->insert([
            [
                'purchase_request_id' => 1,
                'product_id' => 1,
                'quantity' => 5,
                'expected_price' => 1500000,
                'reason' => 'Thay thế thiết bị cũ',
                'created_at' => now(),
            ],
            [
                'purchase_request_id' => 1,
                'product_id' => 2,
                'quantity' => 10,
                'expected_price' => 300000,
                'reason' => 'Bổ sung thiết bị',
                'created_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'product_id' => 1,
                'quantity' => 8,
                'expected_price' => 1500000,
                'reason' => 'Tăng số lượng bệnh nhân',
                'created_at' => now(),
            ],
            [
                'purchase_request_id' => 3,
                'product_id' => 4,
                'quantity' => 3,
                'expected_price' => 15000000,
                'reason' => 'Trang bị phòng mổ',
                'created_at' => now(),
            ],
        ]);
    }
}
