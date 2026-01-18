<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create automatic database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if auto-backup is enabled
        $autoBackupEnabled = DB::table('system_settings')
            ->where('key', 'auto_backup_enabled')
            ->value('value');

        if ($autoBackupEnabled !== '1') {
            $this->info('Auto-backup is disabled. Skipping...');
            return 0;
        }

        $this->info('Starting automatic database backup...');

        try {
            $filename = 'auto_backup_' . date('Ymd_His') . '.sql';
            $backupPath = storage_path('app/backups');
            
            // Create backups directory if it doesn't exist
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            // Get database credentials from .env
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD', '');

            // Get mysqldump path
            $mysqldump = $this->getMySQLPath('mysqldump');

            // Build mysqldump command
            $command = sprintf(
                '"%s" -h%s -P%s -u%s',
                str_replace('"', '', $mysqldump),
                $host,
                $port,
                $username
            );

            // Only add password flag if password is not empty
            if (!empty($password)) {
                $command .= ' -p"' . $password . '"';
            }

            $command .= sprintf(
                ' "%s" > "%s" 2>&1',
                $database,
                $fullPath
            );

            // Execute command
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMsg = implode("\n", $output);
                Log::error('Auto-backup failed: ' . $errorMsg);
                
                // Update backup status
                $this->updateBackupStatus('failed', $errorMsg);
                
                $this->error('Backup failed: ' . $errorMsg);
                return 1;
            }

            // Check if file was created and has content
            if (!File::exists($fullPath) || File::size($fullPath) == 0) {
                Log::error('Auto-backup file is empty or not created');
                $this->updateBackupStatus('failed', 'Backup file is empty');
                $this->error('Backup file is empty or not created');
                return 1;
            }

            // Get file size
            $fileSize = File::size($fullPath);

            // Update backup status
            $this->updateBackupStatus('success', 'Backup created successfully: ' . $filename);

            Log::info('Auto-backup created successfully: ' . $filename . ' (' . $this->formatBytes($fileSize) . ')');
            $this->info('Backup created successfully: ' . $filename . ' (' . $this->formatBytes($fileSize) . ')');

            return 0;
        } catch (\Exception $e) {
            Log::error('Auto-backup exception: ' . $e->getMessage());
            $this->updateBackupStatus('failed', $e->getMessage());
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Find MySQL binary path on Windows
     */
    private function getMySQLPath($binary = 'mysqldump')
    {
        // Common MySQL installation paths on Windows
        $possiblePaths = [
            'C:\\xampp\\mysql\\bin\\' . $binary . '.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\' . $binary . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\' . $binary . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\' . $binary . '.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\' . $binary . '.exe',
        ];

        // Check if binary is in PATH
        exec('where ' . $binary . ' 2>nul', $output, $returnVar);
        if ($returnVar === 0 && !empty($output)) {
            return $binary; // Found in PATH
        }

        // Check common installation paths
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Fallback to just the binary name
        return $binary;
    }

    /**
     * Update backup status in system_settings
     */
    private function updateBackupStatus($status, $message = '')
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'last_backup_time'],
            [
                'value' => now()->toDateTimeString(),
                'description' => 'Last automatic backup time',
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'last_backup_status'],
            [
                'value' => $status,
                'description' => 'Last automatic backup status: ' . $message,
                'updated_at' => now(),
                'created_at' => now()
            ]
        );
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
