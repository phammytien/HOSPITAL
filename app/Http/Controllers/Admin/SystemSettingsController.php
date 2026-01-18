<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SystemSettingsController extends Controller
{
    public function index()
    {
        return view('admin.system_settings');
    }

    /**
     * Get deleted data from a specific table
     */
    public function getDeletedData($table)
    {
        // Validate table name to prevent SQL injection
        $allowedTables = [
            'products' => 'Sản phẩm',
            'product_categories' => 'Danh mục',
            'departments' => 'Khoa phòng',
            'purchase_orders' => 'Đơn hàng',
            'purchase_requests' => 'Yêu cầu mua hàng',
        ];

        if (!array_key_exists($table, $allowedTables)) {
            return response()->json(['success' => false, 'message' => 'Bảng không hợp lệ'], 400);
        }

        try {
            $data = DB::table($table)
                ->where('is_delete', true)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'table_name' => $allowedTables[$table]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Restore a single deleted item
     */
    public function restoreItem(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'id' => 'required|integer'
        ]);

        $allowedTables = ['products', 'product_categories', 'departments', 'purchase_orders', 'purchase_requests'];

        if (!in_array($request->table, $allowedTables)) {
            return response()->json(['success' => false, 'message' => 'Bảng không hợp lệ'], 400);
        }

        try {
            DB::table($request->table)
                ->where('id', $request->id)
                ->update(['is_delete' => false]);

            return response()->json(['success' => true, 'message' => 'Khôi phục thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Restore multiple items
     */
    public function restoreBulk(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'ids' => 'required|array'
        ]);

        $allowedTables = ['products', 'product_categories', 'departments', 'purchase_orders', 'purchase_requests'];

        if (!in_array($request->table, $allowedTables)) {
            return response()->json(['success' => false, 'message' => 'Bảng không hợp lệ'], 400);
        }

        try {
            DB::table($request->table)
                ->whereIn('id', $request->ids)
                ->update(['is_delete' => false]);

            return response()->json(['success' => true, 'message' => 'Khôi phục ' . count($request->ids) . ' mục thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Permanently delete items
     */
    public function permanentDelete(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'ids' => 'required|array'
        ]);

        $allowedTables = ['products', 'product_categories', 'departments', 'purchase_orders', 'purchase_requests'];

        if (!in_array($request->table, $allowedTables)) {
            return response()->json(['success' => false, 'message' => 'Bảng không hợp lệ'], 400);
        }

        try {
            DB::table($request->table)
                ->whereIn('id', $request->ids)
                ->where('is_delete', true) // Only delete items that are already soft deleted
                ->delete();

            return response()->json(['success' => true, 'message' => 'Xóa vĩnh viễn ' . count($request->ids) . ' mục thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
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
                return $path; // Quote path for Windows
            }
        }

        // Fallback to just the binary name (might work if in PATH)
        return $binary;
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {
            $filename = 'backup_' . date('Ymd_His') . '.sql';
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

            // Build mysqldump command with proper handling for empty password
            // For Windows, we need to properly quote the path
            $command = sprintf(
                '"%s" -h%s -P%s -u%s',
                str_replace('"', '', $mysqldump), // Remove quotes if already present
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

            // Log the command for debugging (without password)
            $logCommand = str_replace($password, '****', $command);
            \Log::info('Executing backup command: ' . $logCommand);

            // Execute command
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                // Get error details from output
                $errorMsg = implode("\n", $output);
                \Log::error('Backup failed with return code: ' . $returnVar);
                \Log::error('Error output: ' . $errorMsg);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Không thể tạo backup. Chi tiết lỗi: ' . ($errorMsg ?: 'Không có thông tin lỗi chi tiết. Vui lòng kiểm tra log.')
                ], 500);
            }

            // Check if file was created and has content
            if (!File::exists($fullPath) || File::size($fullPath) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup được tạo nhưng rỗng. Vui lòng kiểm tra quyền truy cập database.'
                ], 500);
            }

            // Get file size
            $fileSize = File::size($fullPath);

            return response()->json([
                'success' => true,
                'message' => 'Tạo backup thành công!',
                'filename' => $filename,
                'size' => $this->formatBytes($fileSize)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get list of backup files
     */
    public function getBackupList()
    {
        try {
            $backupPath = storage_path('app/backups');
            
            if (!File::exists($backupPath)) {
                return response()->json(['success' => true, 'backups' => []]);
            }

            $files = File::files($backupPath);
            $backups = [];

            foreach ($files as $file) {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'created_at' => date('d/m/Y H:i:s', $file->getMTime())
                ];
            }

            // Sort by creation time (newest first)
            usort($backups, function($a, $b) {
                return $b['size_bytes'] <=> $a['size_bytes'];
            });

            return response()->json(['success' => true, 'backups' => $backups]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!File::exists($filePath)) {
            abort(404, 'File không tồn tại');
        }

        return response()->download($filePath);
    }

    /**
     * Restore database from backup
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        try {
            $filePath = storage_path('app/backups/' . $request->filename);

            if (!File::exists($filePath)) {
                return response()->json(['success' => false, 'message' => 'File backup không tồn tại'], 404);
            }

            // Get database credentials
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD', '');

            // Get mysql path
            $mysql = $this->getMySQLPath('mysql');

            // Build mysql import command with proper handling for empty password
            $command = sprintf(
                '%s -h %s -P %s -u %s',
                $mysql,
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username)
            );

            // Only add password flag if password is not empty
            if (!empty($password)) {
                $command .= ' -p' . escapeshellarg($password);
            }

            $command .= sprintf(
                ' %s < %s 2>&1',
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            // Execute command
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMsg = implode("\n", $output);
                \Log::error('Restore failed: ' . $errorMsg);
                \Log::error('Command: ' . $command);
                return response()->json(['success' => false, 'message' => 'Không thể restore database. Vui lòng kiểm tra file backup và cấu hình database. Chi tiết: ' . $errorMsg], 500);
            }

            return response()->json(['success' => true, 'message' => 'Restore database thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        try {
            $filePath = storage_path('app/backups/' . $filename);

            if (!File::exists($filePath)) {
                return response()->json(['success' => false, 'message' => 'File không tồn tại'], 404);
            }

            File::delete($filePath);

            return response()->json(['success' => true, 'message' => 'Xóa file backup thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get maintenance mode settings
     */
    public function getMaintenanceSettings()
    {
        try {
            $maintenanceMode = DB::table('system_settings')
                ->where('key', 'maintenance_mode')
                ->first();
            
            $maintenanceMessage = DB::table('system_settings')
                ->where('key', 'maintenance_message')
                ->first();

            return response()->json([
                'success' => true,
                'maintenance_mode' => $maintenanceMode ? (int)$maintenanceMode->value : 0,
                'maintenance_message' => $maintenanceMessage ? $maintenanceMessage->value : 'Hệ thống đang bảo trì'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update maintenance mode status
     */
    public function updateMaintenanceMode(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);

        try {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'maintenance_mode'],
                [
                    'value' => $request->status ? '1' : '0',
                    'description' => 'Trạng thái chế độ bảo trì hệ thống',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            $statusText = $request->status ? 'bật' : 'tắt';
            return response()->json([
                'success' => true,
                'message' => "Đã {$statusText} chế độ bảo trì!"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update maintenance message
     */
    public function updateMaintenanceMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'maintenance_message'],
                [
                    'value' => $request->message,
                    'description' => 'Thông báo hiển thị khi hệ thống bảo trì',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông báo bảo trì thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get automatic backup settings
     */
    public function getBackupSettings()
    {
        try {
            $autoBackupEnabled = DB::table('system_settings')
                ->where('key', 'auto_backup_enabled')
                ->first();
            
            $autoBackupInterval = DB::table('system_settings')
                ->where('key', 'auto_backup_interval')
                ->first();

            return response()->json([
                'success' => true,
                'auto_backup_enabled' => $autoBackupEnabled ? (int)$autoBackupEnabled->value : 0,
                'auto_backup_interval' => $autoBackupInterval ? (int)$autoBackupInterval->value : 30
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update automatic backup settings
     */
    public function updateBackupSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'interval' => 'required|integer|min:10'
        ]);

        try {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'auto_backup_enabled'],
                [
                    'value' => $request->enabled ? '1' : '0',
                    'description' => 'Trạng thái backup tự động',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            DB::table('system_settings')->updateOrInsert(
                ['key' => 'auto_backup_interval'],
                [
                    'value' => (string)$request->interval,
                    'description' => 'Khoảng thời gian backup tự động (giây)',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            $statusText = $request->enabled ? 'bật' : 'tắt';
            return response()->json([
                'success' => true,
                'message' => "Đã {$statusText} backup tự động! Khoảng thời gian: {$request->interval} giây"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get backup status
     */
    public function getBackupStatus()
    {
        try {
            $lastBackupTime = DB::table('system_settings')
                ->where('key', 'last_backup_time')
                ->first();
            
            $lastBackupStatus = DB::table('system_settings')
                ->where('key', 'last_backup_status')
                ->first();

            return response()->json([
                'success' => true,
                'last_backup_time' => $lastBackupTime ? $lastBackupTime->value : null,
                'last_backup_status' => $lastBackupStatus ? $lastBackupStatus->value : 'unknown',
                'last_backup_message' => $lastBackupStatus ? $lastBackupStatus->description : ''
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload and import backup file
     */
    public function uploadBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql|max:102400' // Max 100MB
        ]);

        try {
            $file = $request->file('backup_file');
            $filename = 'uploaded_' . date('Ymd_His') . '_' . $file->getClientOriginalName();
            $backupPath = storage_path('app/backups');
            
            // Create backups directory if it doesn't exist
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Move uploaded file to backups directory
            $file->move($backupPath, $filename);

            return response()->json([
                'success' => true,
                'message' => 'Upload file backup thành công!',
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
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
