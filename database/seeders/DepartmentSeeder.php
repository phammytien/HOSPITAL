<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'department_code' => 'KHOA_NOI',
                'department_name' => 'Khoa Nội',
                'description' => 'Điều trị nội khoa',
                'budget_amount' => 500000000,
                'budget_period' => '2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_code' => 'KHOA_NGOAI',
                'department_name' => 'Khoa Ngoại',
                'description' => 'Phẫu thuật',
                'budget_amount' => 700000000,
                'budget_period' => '2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_code' => 'PHONG_CNTT',
                'department_name' => 'Phòng CNTT',
                'description' => 'Công nghệ thông tin',
                'budget_amount' => 300000000,
                'budget_period' => '2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
