<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('files')->insert([
            [
                'file_name' => 'bao_gia_may_do_huyet_ap.pdf',
                'file_path' => '/uploads/bao_gia_yt001.pdf',
                'file_type' => 'pdf',
                'related_table' => 'purchase_requests',
                'related_id' => 1,
                'uploaded_by' => 2,
                'uploaded_at' => now(),
            ],
            [
                'file_name' => 'hoa_don_po_2024_noi_01.pdf',
                'file_path' => '/uploads/po_2024_noi_01.pdf',
                'file_type' => 'pdf',
                'related_table' => 'purchase_orders',
                'related_id' => 1,
                'uploaded_by' => 2,
                'uploaded_at' => now(),
            ],
        ]);
    }
}
