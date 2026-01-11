<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('123456');

        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password_hash' => $password,
                'full_name' => 'Quản trị hệ thống',
                'role' => 'ADMIN',
                'department_id' => NULL,
                'phone_number' => '0900000001',
                'email' => 'admin@hospital.vn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'buyer01',
                'password_hash' => $password,
                'full_name' => 'Nhân viên mua hàng',
                'role' => 'BUYER',
                'department_id' => NULL,
                'phone_number' => '0900000002',
                'email' => 'buyer@hospital.vn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'noi01',
                'password_hash' => $password,
                'full_name' => 'NV Khoa Nội',
                'role' => 'DEPARTMENT',
                'department_id' => 1,
                'phone_number' => '0900000003',
                'email' => 'noi@hospital.vn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ngoai01',
                'password_hash' => $password,
                'full_name' => 'NV Khoa Ngoại',
                'role' => 'DEPARTMENT',
                'department_id' => 2,
                'phone_number' => '0900000004',
                'email' => 'ngoai@hospital.vn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
