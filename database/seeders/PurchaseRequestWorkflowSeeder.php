<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchase_request_workflows')->insert([
            [
                'purchase_request_id' => 1,
                'action_by' => 2,
                'from_status' => 'SUBMITTED',
                'to_status' => 'APPROVED',
                'action_note' => 'Phù hợp ngân sách, duyệt mua',
                'action_time' => now(),
            ],
            [
                'purchase_request_id' => 3,
                'action_by' => 2,
                'from_status' => 'SUBMITTED',
                'to_status' => 'REJECTED',
                'action_note' => 'Vượt ngân sách cho phép',
                'action_time' => now(),
            ],
        ]);
    }
}
