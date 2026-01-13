<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\PurchaseRequest;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load work scope data for Department
        $workScope = [
            'total_requests' => PurchaseRequest::where('department_id', $user->department_id)->count(),
            'pending_requests' => PurchaseRequest::where('department_id', $user->department_id)
                ->where('status', 'PENDING')->count(),
            'department_name' => $user->department->department_name ?? 'N/A',
            'permissions' => [
                'Xử lý yêu cầu mua hàng',
                'Xác nhận đơn hàng',
            ],
        ];

        return view('department.profile.index', compact('user', 'workScope'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->full_name = $request->full_name;
        $user->phone_number = $request->phone_number;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công.');
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Mật khẩu hiện tại không đúng!');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('success', 'Đổi mật khẩu thành công!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Vui lòng kiểm tra lại thông tin!')->withErrors($e->errors());
        }
    }
}
