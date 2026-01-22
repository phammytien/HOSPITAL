<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log for authenticated users
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log user activity to audit_logs table
     */
    private function logActivity($request, $response)
    {
        try {
            // Skip logging for certain routes that already have their own logging
            $skipRoutes = [
                'login',              // Login is already logged in AuthController
                'logout',             // Logout is already logged in AuthController
                'password.email',     // Password reset
                'password.request',   // Password reset request
                'profile.password',   // Password change (already logged separately)
                'notifications.read', // Just marking as read
                'notifications.read-all', // Just marking as read
                'settings.audit-logs',    // Don't log when viewing audit logs
                'settings.users',         // Just fetching user list
                'settings.deleted',       // Just viewing deleted data
                'settings.backup.list',   // Just viewing backup list
                'settings.backup.status', // Just checking backup status
                'settings.maintenance',   // Just viewing maintenance settings
            ];

            $routeName = $request->route() ? $request->route()->getName() : null;
            $path = $request->path();
            
            // Skip specific exact paths
            $skipPaths = [
                'password/email',
                'password/reset',
            ];
            
            // Check exact path match
            if (in_array($path, $skipPaths)) {
                return;
            }
            
            // Check route name contains skip keywords
            if ($routeName) {
                foreach ($skipRoutes as $skip) {
                    if (str_contains($routeName, $skip)) {
                        return;
                    }
                }
            }

            // Only log POST, PUT, PATCH, DELETE methods (actions that change data)
            if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                return;
            }

            // Only log successful responses (2xx status codes)
            // Exception: Login redirects (302) are considered success
            if (($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) && $response->getStatusCode() !== 302) {
                return;
            }

            $user = Auth::user();
            
            // If logging in, user might not be set in request yet, but Auth::user() should have it after login
            if (!$user && $path === 'login') {
                 // Try to get user from session or guard if possible, but usually middleware runs after response so Auth::user() should work if login successful
                 // If not, we might miss login log or have no user_id
            }
            
            if (!$user) return; // Cannot log without user

            $action = $this->determineAction($request);
            $description = $this->generateDescription($request, $action);

            // Get old and new values for UPDATE actions
            $oldValues = null;
            $newValues = null;

            if ($request->method() === 'PUT' || $request->method() === 'PATCH') {
                $oldValues = json_encode($request->old ?? []);
                $newValues = json_encode($request->except(['_token', '_method', 'password', 'password_confirmation']));
            } elseif ($request->method() === 'POST') {
                $newValues = json_encode($request->except(['_token', 'password', 'password_confirmation']));
            }

            DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'device_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the application if logging fails
            \Log::error('Audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine the action type based on the request
     */
    private function determineAction($request)
    {
        $method = $request->method();
        $routeName = $request->route() ? $request->route()->getName() : '';
        $path = $request->path();

        // Check path first for authentication actions
        if ($path === 'login') return 'Đăng nhập thành công';
        if ($path === 'logout') return 'Đăng xuất';

        // Check route name first for specific actions
        if ($routeName) {
            // Request/Order specific actions
            if (str_contains($routeName, 'approve')) return 'Duyệt yêu cầu';
            if (str_contains($routeName, 'reject')) return 'Từ chối yêu cầu';
            if (str_contains($routeName, 'submit')) return 'Gửi yêu cầu';
            if (str_contains($routeName, 'withdraw')) return 'Rút lại yêu cầu';
            if (str_contains($routeName, 'confirm')) return 'Xác nhận đơn hàng';
            
            // Status updates
            if (str_contains($routeName, 'update-status') || str_contains($routeName, 'status')) {
                return 'Cập nhật trạng thái';
            }
            
            // Inventory actions
            if (str_contains($routeName, 'inventory')) {
                if (str_contains($routeName, 'sync')) return 'Đồng bộ tồn kho';
                if (str_contains($routeName, 'initialize')) return 'Khởi tạo tồn kho';
                if (str_contains($routeName, 'quick-action')) return 'Thao tác tồn kho';
            }
            
            // Proposal actions
            if (str_contains($routeName, 'proposal')) {
                if ($method === 'POST') return 'Tạo đề xuất sản phẩm';
                if ($method === 'PUT' || $method === 'PATCH') return 'Cập nhật đề xuất';
            }
            
            // CRUD operations
            if (str_contains($routeName, 'store')) {
                return 'Thêm ' . $this->getResourceNameVN($routeName);
            }
            if (str_contains($routeName, 'update')) {
                return 'Cập nhật ' . $this->getResourceNameVN($routeName);
            }
            if (str_contains($routeName, 'destroy')) {
                return 'Xóa ' . $this->getResourceNameVN($routeName);
            }
        }

        // Fallback to method-based action
        switch ($method) {
            case 'POST':
                return 'Tạo mới';
            case 'PUT':
            case 'PATCH':
                return 'Cập nhật';
            case 'DELETE':
                return 'Xóa';
            default:
                return 'Thao tác';
        }
    }

    /**
     * Get resource name in Vietnamese from route
     */
    private function getResourceNameVN($routeName)
    {
        // Extract resource name from route
        // e.g., "admin.products.store" -> "sản phẩm"
        $parts = explode('.', $routeName);
        
        if (count($parts) >= 2) {
            $resource = $parts[count($parts) - 2];
            
            // Map English resource names to Vietnamese
            $resourceMap = [
                'products' => 'sản phẩm',
                'product' => 'sản phẩm',
                'categories' => 'danh mục',
                'category' => 'danh mục',
                'departments' => 'khoa/phòng',
                'department' => 'khoa/phòng',
                'suppliers' => 'nhà cung cấp',
                'supplier' => 'nhà cung cấp',
                'orders' => 'đơn hàng',
                'order' => 'đơn hàng',
                'requests' => 'yêu cầu',
                'request' => 'yêu cầu',
                'users' => 'người dùng',
                'user' => 'người dùng',
                'notifications' => 'thông báo',
                'notification' => 'thông báo',
                'feedback' => 'phản hồi',
                'proposals' => 'đề xuất',
                'proposal' => 'đề xuất',
                'inventory' => 'tồn kho',
            ];
            
            return $resourceMap[$resource] ?? $resource;
        }

        return 'dữ liệu';
    }

    /**
     * Generate human-readable description
     */
    private function generateDescription($request, $action)
    {
        // Since action is already in Vietnamese, just return it
        // You can add more context here if needed
        return $action;
    }
}
