<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductProposal;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load work scope data for Admin
        $workScope = [
            'total_users' => User::where('is_delete', false)->count(),
            'total_products' => Product::where('is_delete', false)->count(),
            'pending_proposals' => ProductProposal::where('status', 'CREATED')->count(),
            'permissions' => [
                'Quản lý người dùng & phân quyền',
                'Quản lý sản phẩm & danh mục',
                'Duyệt đề xuất sản phẩm',
                'Quản lý nhà cung cấp',
            ],
        ];

        return view('admin.profile.index', compact('user', 'workScope'));
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
