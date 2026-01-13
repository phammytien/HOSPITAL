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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->full_name = $request->full_name;
        $user->phone_number = $request->phone_number;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'Avatar_' . $user->username . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images/avatars');

            // Move file to public/images/avatars
            $file->move($destinationPath, $fileName);

            // Save relative path to DB
            $filePath = 'images/avatars/' . $fileName;

            // Save to files table - FIXED: Do not set user->avatar
            \App\Models\File::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => 'avatar',
                'related_table' => 'users',
                'related_id' => $user->id,
                'uploaded_by' => $user->id,
                'uploaded_at' => now(),
                'is_delete' => false,
            ]);

            // Optional: Save to files table if needed (as per request)
            // assuming there is a File model or using DB facade
            // \App\Models\File::create([...]); 
            // For now, updating the user's avatar column is the critical part for display.
        }

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
