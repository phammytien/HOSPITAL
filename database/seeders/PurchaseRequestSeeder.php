<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchase_requests')->insert([
            [
                'request_code' => 'REQ_2024_Q1_NOI',
                'department_id' => 1,
                'period' => '2024_Q1',
                'requested_by' => 3,
                'status' => 'APPROVED',
                'note' => 'Mua sắm quý 1',
                'created_at' => Carbon::create(2024, 2, 15, 9, 0, 0),
                'updated_at' => Carbon::create(2024, 2, 15, 9, 30, 0),
            ],
            [
                'request_code' => 'REQ_2024_Q2_NOI',
                'department_id' => 1,
                'period' => '2024_Q2',
                'requested_by' => 3,
                'status' => 'SUBMITTED',
                'note' => 'Mua sắm quý 2',
                'created_at' => Carbon::create(2024, 5, 20, 14, 0, 0),
                'updated_at' => Carbon::create(2024, 5, 20, 14, 0, 0),
            ],
            [
                'request_code' => 'REQ_2024_Q1_NGOAI',
                'department_id' => 2,
                'period' => '2024_Q1',
                'requested_by' => 4,
                'status' => 'REJECTED',
                'note' => 'Vượt ngân sách',
                'created_at' => Carbon::create(2024, 3, 10, 10, 0, 0),
                'updated_at' => Carbon::create(2024, 3, 11, 8, 0, 0),
            ],
        ]);
    }
}
