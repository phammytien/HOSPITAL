<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchase_orders')->insert([
            // Order 1: Completed, from Request 1
            [
                'order_code' => 'PO_2024_NOI_01',
                'purchase_request_id' => 1,
                'department_id' => 1,
                'approved_by' => 2,
                'order_date' => '2024-03-15',
                'total_amount' => 10500000,
                'status' => 'COMPLETED',
                'created_at' => now(),
            ],
            // Order 2: Delivered, from Request 2
            [
                'order_code' => 'PO_2024_NOI_02',
                'purchase_request_id' => 2,
                'department_id' => 1,
                'approved_by' => 2,
                'order_date' => '2024-04-10',
                'total_amount' => 5500000,
                'status' => 'DELIVERED',
                'created_at' => now(),
            ],
            // Order 3: Created (new order), from Request 1
            [
                'order_code' => 'PO_2024_NOI_03',
                'purchase_request_id' => 1,
                'department_id' => 1,
                'approved_by' => 2,
                'order_date' => '2024-03-20',
                'total_amount' => 30000000,
                'status' => 'CREATED',
                'created_at' => now(),
            ],
            // Order 4: Ordered (confirmed), from Request 2
            [
                'order_code' => 'PO_2024_NOI_04',
                'purchase_request_id' => 2,
                'department_id' => 1,
                'approved_by' => 2,
                'order_date' => '2024-04-12',
                'total_amount' => 4500000,
                'status' => 'ORDERED',
                'created_at' => now(),
            ],
            // Order 5: Cancelled
            [
                'order_code' => 'PO_2024_NGOAI_01',
                'purchase_request_id' => 3,
                'department_id' => 2,
                'approved_by' => 2,
                'order_date' => '2024-02-15',
                'total_amount' => 15000000,
                'status' => 'CANCELLED',
                'created_at' => now(),
            ],
            // Order 6: Delivered (waiting for department confirmation) - Khoa Ngoại
            [
                'order_code' => 'PO_2024_NGOAI_02',
                'purchase_request_id' => 3,
                'department_id' => 2,
                'approved_by' => 2,
                'order_date' => '2024-05-20',
                'total_amount' => 8500000,
                'status' => 'DELIVERED',
                'created_at' => now(),
            ],
        ]);
    }
}
