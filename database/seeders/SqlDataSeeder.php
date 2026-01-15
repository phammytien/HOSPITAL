<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SqlDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path('database/sql_insert_data.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            // Execute raw SQL
            // Using DB::unprepared to handle multiple statements
            DB::unprepared($sql);

            $this->command->info('SQL Data seeded successfully from ' . $path);
        } else {
            $this->command->error('File not found: ' . $path);
        }
    }
}
