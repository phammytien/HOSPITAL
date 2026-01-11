<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('department.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            // 'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)], // Usually email is managed by admin
        ]);

        $user->full_name = $request->full_name;
        $user->phone_number = $request->phone_number;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Debug logging
        \Log::info('Password change attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'current_password_provided' => $request->current_password,
            'password_hash_in_db' => $user->password,
            'hash_check_result' => Hash::check($request->current_password, $user->password),
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Mật khẩu hiện tại không đúng. User: ' . $user->email);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công.');
    }
}
