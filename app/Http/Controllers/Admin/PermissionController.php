<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display user role management page
     */
    public function index()
    {
        // Get all users grouped by role
        $users = User::where('is_delete', false)
            ->orderBy('role')
            ->orderBy('full_name')
            ->get();
        
        // Group users by role
        $usersByRole = $users->groupBy('role');
        
        // Get role statistics
        $roleStats = [
            'ADMIN' => User::where('role', 'ADMIN')->where('is_delete', false)->count(),
            'BUYER' => User::where('role', 'BUYER')->where('is_delete', false)->count(),
            'DEPARTMENT' => User::where('role', 'DEPARTMENT')->where('is_delete', false)->count(),
        ];
        
        // Define role permissions/capabilities
        $roleCapabilities = [
            'ADMIN' => [
                'Quản lý toàn bộ hệ thống',
                'Quản lý sản phẩm và danh mục',
                'Quản lý khoa phòng và nhân viên',
                'Xem và quản lý đơn hàng',
                'Xem lịch sử mua hàng',
                'Quản lý thông báo',
                'Xem phản hồi',
                'Cài đặt hệ thống',
                'Quản lý phân quyền',
            ],
            'BUYER' => [
                'Xem dashboard mua hàng',
                'Xem danh sách sản phẩm',
                'Quản lý yêu cầu mua hàng',
                'Phê duyệt/từ chối yêu cầu',
                'So sánh giá sản phẩm',
                'Quản lý đơn hàng',
                'Theo dõi giao hàng',
                'Xem và xuất báo cáo',
                'Cài đặt cá nhân',
            ],
            'DEPARTMENT' => [
                'Xem dashboard khoa phòng',
                'Xem danh mục sản phẩm',
                'Tạo yêu cầu mua hàng',
                'Quản lý yêu cầu của khoa',
                'Xem lịch sử yêu cầu',
                'Xác nhận đơn hàng',
                'Cài đặt cá nhân',
            ],
        ];
        
        return view('admin.permissions', compact('users', 'usersByRole', 'roleStats', 'roleCapabilities'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $userId)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:ADMIN,BUYER,DEPARTMENT',
        ]);

        $user = User::findOrFail($userId);
        $oldRole = $user->role;
        $newRole = $validated['role'];

        // Không cho phép chuyển từ BUYER hoặc DEPARTMENT sang ADMIN
        if ($oldRole !== 'ADMIN' && $newRole === 'ADMIN') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chuyển vai trò sang Quản trị viên từ vai trò khác'
            ], 403);
        }

        // Không cho phép thay đổi vai trò của ADMIN
        if ($oldRole === 'ADMIN') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thay đổi vai trò của Quản trị viên'
            ], 403);
        }

        $user->role = $newRole;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Đã thay đổi quyền của {$user->full_name} từ {$oldRole} sang {$newRole}",
            'user' => $user
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Không cho phép khóa tài khoản ADMIN
        if ($user->role === 'ADMIN') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể khóa tài khoản Quản trị viên'
            ], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_active 
                ? "Đã kích hoạt tài khoản {$user->full_name}" 
                : "Đã vô hiệu hóa tài khoản {$user->full_name}",
            'user' => $user
        ]);
    }

    /**
     * Get role description
     */
    public function getRoleInfo($role)
    {
        $roleInfo = [
            'ADMIN' => [
                'name' => 'Quản trị viên',
                'description' => 'Có toàn quyền quản lý hệ thống, bao gồm quản lý người dùng, sản phẩm, đơn hàng và cài đặt.',
                'icon' => 'fa-user-shield',
                'color' => 'blue',
            ],
            'BUYER' => [
                'name' => 'Nhân viên mua hàng',
                'description' => 'Quản lý yêu cầu mua hàng, phê duyệt đơn hàng, theo dõi giao hàng và tạo báo cáo.',
                'icon' => 'fa-shopping-cart',
                'color' => 'green',
            ],
            'DEPARTMENT' => [
                'name' => 'Nhân viên khoa phòng',
                'description' => 'Tạo và quản lý yêu cầu mua hàng cho khoa phòng, xác nhận đơn hàng.',
                'icon' => 'fa-hospital',
                'color' => 'purple',
            ],
        ];

        return response()->json($roleInfo[$role] ?? null);
    }
}
