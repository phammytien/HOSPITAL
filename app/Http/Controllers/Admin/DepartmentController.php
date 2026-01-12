<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\DepartmentHeadAccountCreated;

class DepartmentController extends Controller
{
    public function index()
    {
        $users = User::with('department')
            ->where('is_delete', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $departments = Department::where('is_delete', false)
            ->withCount('users')
            ->with(['purchaseOrders' => function($query) {
                $query->where('is_delete', false)
                      ->where('status', '!=', 'CANCELLED');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalDepartments = $departments->count();
        $totalEmployees = $users->count();
        $totalBudget = $departments->sum('budget_amount');

        // Add calculated fields to departments
        $departments->each(function($dept) {
            $dept->used_budget = $dept->purchaseOrders->sum('total_amount');
            $dept->usage_percent = $dept->budget_amount > 0 
                ? round(($dept->used_budget / $dept->budget_amount) * 100) 
                : 0;
            
            // Format time difference
            $dept->last_updated = $dept->updated_at->diffForHumans();
        });
        
        return view('admin.departments', compact('users', 'departments', 'totalDepartments', 'totalEmployees', 'totalBudget'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required',
            'description' => 'nullable',
            'budget_amount' => 'required|numeric|min:0',
            'budget_period' => 'nullable',
            'head_name' => 'required',
            'head_email' => 'required|email|unique:users,email',
        ]);

        // Auto-generate department_code from department_name
        $departmentCode = $this->generateDepartmentCode($request->department_name);

        // Create Department
        $department = Department::create([
            'department_code' => $departmentCode,
            'department_name' => $request->department_name,
            'description' => $request->description,
            'budget_amount' => $request->budget_amount,
            'budget_period' => $request->budget_period,
            'is_delete' => false,
        ]);

        // Generate random password
        $password = Str::random(8);

        // Create User for Department Head
        $user = User::create([
            'username' => strtolower($request->head_email), // Use lowercase email as username
            'email' => strtolower($request->head_email), // Save email in lowercase
            'password_hash' => Hash::make($password),
            'full_name' => $request->head_name,
            'role' => 'DEPARTMENT', // Role must be uppercase
            'department_id' => $department->id,
            'is_active' => true,
            'is_delete' => false,
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new DepartmentHeadAccountCreated(
                $user->email,
                $password,
                $department->department_name
            ));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send department head email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thêm khoa/phòng thành công! Email đã được gửi đến trưởng khoa.'
        ]);
    }

    /**
     * Generate department code from department name
     * Example: "Khoa Nội" -> "KHOA_NOI"
     */
    private function generateDepartmentCode($name)
    {
        // Convert Vietnamese to ASCII
        $name = $this->removeVietnameseAccents($name);
        
        // Replace spaces with underscores and convert to uppercase
        $code = strtoupper(str_replace(' ', '_', trim($name)));
        
        // Check if code already exists, if yes, append number
        $originalCode = $code;
        $counter = 1;
        while (Department::where('department_code', $code)->where('is_delete', false)->exists()) {
            $code = $originalCode . '_' . $counter;
            $counter++;
        }
        
        return $code;
    }

    /**
     * Remove Vietnamese accents from string
     */
    private function removeVietnameseAccents($str)
    {
        $vietnameseMap = [
            'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'đ' => 'd',
            'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        ];
        
        return strtr(mb_strtolower($str), $vietnameseMap);
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        return response()->json($department);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_code' => 'required|unique:departments,department_code,' . $id,
            'department_name' => 'required',
            'description' => 'nullable',
            'budget_amount' => 'required|numeric|min:0',
            'budget_period' => 'nullable',
        ]);

        $department = Department::findOrFail($id);
        $department->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật khoa/phòng thành công!'
        ]);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->update(['is_delete' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Xóa khoa/phòng thành công!'
        ]);
    }

    public function getUserLogs(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        // Demo Data Seeding (if empty)
        if ($user->auditLogs()->count() === 0) {
             // 1. Success Login
             $user->auditLogs()->create([
                'action' => 'Đăng nhập thành công',
                'description' => 'Người dùng đăng nhập vào hệ thống bệnh viện qua cổng web.',
                'ip_address' => '127.0.0.1',
                'device_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subMinutes(5)
            ]);

            // 2. Update Profile with Diff
            $user->auditLogs()->create([
                'action' => 'Cập nhật hồ sơ',
                'description' => 'Thay đổi số điện thoại cá nhân từ 090*** sang 098***.',
                'ip_address' => '192.168.1.45',
                'device_agent' => 'Chrome (Windows 11)',
                'old_values' => [
                    'phone_number' => '0901234567',
                    'email' => 'nguyevana@hospital.vn',
                    'address' => 'Phường 1, TP. Cao Lãnh'
                ],
                'new_values' => [
                    'phone_number' => '0987654321',
                    'email' => 'nguyevana.new@hospital.vn',
                    'address' => 'Phường 2, TP. Cao Lãnh'
                ],
                'created_at' => now()->subMinutes(15)
            ]);

            // 3. Failed Login
            $user->auditLogs()->create([
                'action' => 'Đăng nhập thất bại',
                'description' => 'Sai mật khẩu 3 lần liên tiếp tại cổng Admin.',
                'ip_address' => '14.232.160.12',
                'device_agent' => 'Safari (iPhone 14)',
                'created_at' => now()->subHours(2)
            ]);
        }

        // Get pagination parameters
        $page = $request->input('page', 1);
        $perPage = 4; // 4 items per page as requested
        
        // Get total count
        $total = $user->auditLogs()->count();
        
        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $logs = $user->auditLogs()
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                    'action' => $log->action,
                    'description' => $log->description,
                    'ip_address' => $log->ip_address,
                    'device_agent' => $log->device_agent,
                    
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ]);
    }

    private function seedDummyLogs($user) 
    {
        $actions = [
            ['action' => 'Đăng nhập thành công', 'desc' => 'Hệ thống quản lý trung tâm', 'ip' => '192.168.1.45', 'device' => 'Chrome (Windows 11)'],
            ['action' => 'Cập nhật hồ sơ', 'desc' => 'Thay đổi số điện thoại cá nhân', 'ip' => '192.168.1.45', 'device' => 'Chrome (Windows 11)'],
            ['action' => 'Đăng nhập thất bại', 'desc' => 'Sai mật khẩu 3 lần liên tiếp', 'ip' => '14.232.160.12', 'device' => 'Safari (iPhone 14)'],
            ['action' => 'Phê duyệt đơn hàng', 'desc' => 'Phê duyệt vật tư y tế #MED-8871', 'ip' => '192.168.1.45', 'device' => 'Chrome (Windows 11)'],
            ['action' => 'Xuất báo cáo', 'desc' => 'Báo cáo tài chính tháng 12/2025', 'ip' => '192.168.1.45', 'device' => 'Chrome (Windows 11)'],
        ];

        foreach ($actions as $index => $item) {
            $user->auditLogs()->create([
                'action' => $item['action'],
                'description' => $item['desc'],
                'ip_address' => $item['ip'],
                'device_agent' => $item['device'],
                'created_at' => now()->subDays($index)->subHours(rand(1, 5)),
            ]);
        }
    }
}
