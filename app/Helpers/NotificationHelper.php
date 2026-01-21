<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NotificationHelper
{
    /**
     * Get available notification types from database enum
     * 
     * @return array
     */
    public static function getNotificationTypes()
    {
        try {
            // Get enum values from notifications table
            $type = DB::select("SHOW COLUMNS FROM notifications WHERE Field = 'type'")[0]->Type;
            
            // Extract enum values using regex
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $enum = explode("','", $matches[1]);
            
            // Map to Vietnamese labels according to user preference
            $labels = [
                'info' => 'THÔNG TIN',
                'success' => 'QUAN TRỌNG',
                'warning' => 'CẢNH BÁO',
                'error' => 'KHẨN CẤP',
            ];
            
            $result = [];
            foreach ($enum as $value) {
                $trimmedValue = trim($value);
                // Map to Vietnamese labels according to user preference
                if (isset($labels[$trimmedValue])) {
                    $result[$trimmedValue] = $labels[$trimmedValue];
                }
            }
            
            // Ensure we at least have the core types if enum extraction is weird or empty
            if (empty($result)) {
                $result = $labels;
            }
            
            return $result;
        } catch (\Exception $e) {
            // Fallback to default types
            return [
                'info' => 'THÔNG TIN',
                'success' => 'QUAN TRỌNG',
                'warning' => 'CẢNH BÁO',
                'error' => 'KHẨN CẤP',
            ];
        }
    }
    
    /**
     * Get notification type label
     * 
     * @param string $type
     * @return string
     */
    public static function getTypeLabel($type)
    {
        $types = self::getNotificationTypes();
        return $types[$type] ?? ucfirst($type);
    }
    
    /**
     * Get target role labels
     * 
     * @return array
     */
    public static function getTargetRoles()
    {
        return [
            'ALL' => 'Tất cả người dùng',
            'ADMIN' => 'Quản trị viên',
            'BUYER' => 'Nhân viên mua hàng',
            'DEPARTMENT' => 'Khoa/Phòng',
        ];
    }
    
    /**
     * Get target role label
     * 
     * @param string|null $role
     * @return string
     */
    public static function getRoleLabel($role)
    {
        $roles = self::getTargetRoles();
        $roleUpper = strtoupper($role ?? '');
        return $roles[$roleUpper] ?? ($roles[$role] ?? $roles['ALL']);
    }
}
