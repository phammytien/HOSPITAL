<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'description' => 'Bật/tắt bảo trì (0 = tắt, 1 = bật)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Hệ thống đang bảo trì. Vui lòng quay lại sau.',
                'description' => 'Nội dung thông báo bảo trì',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('system_settings')->insert($settings);
    }
}
