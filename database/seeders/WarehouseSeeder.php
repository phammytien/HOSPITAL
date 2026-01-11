<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('warehouses')->insert([
            [
                'warehouse_code' => 'WH_NOI',
                'warehouse_name' => 'Kho Khoa Nội',
                'location' => 'Tầng 2, Tòa A',
                'department_id' => 1, // Khoa Nội
                'is_delete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_code' => 'WH_NGOAI',
                'warehouse_name' => 'Kho Khoa Ngoại',
                'location' => 'Tầng 3, Tòa B',
                'department_id' => 2, // Khoa Ngoại
                'is_delete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_code' => 'WH_CNTT',
                'warehouse_name' => 'Kho Phòng CNTT',
                'location' => 'Tầng 1, Tòa C',
                'department_id' => 3, // Phòng CNTT
                'is_delete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_code' => 'WH_CENTRAL',
                'warehouse_name' => 'Kho Trung Tâm',
                'location' => 'Tầng hầm, Tòa A',
                'department_id' => 1, // Quản lý bởi Khoa Nội
                'is_delete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
