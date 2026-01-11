<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Explicitly check user and password hash
        $user = User::where('email', $request->email)->first();
        
        Log::info('Login Attempt', ['email' => $request->email, 'user_found' => $user ? true : false]);

        if ($user && Hash::check($request->password, $user->password_hash)) {
            
            // Check if active
            if (!$user->is_active) {
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendNewPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
        }

        // Generate random password
        $newPassword = \Illuminate\Support\Str::random(10);
        
        // Update user password
        $user->password_hash = Hash::make($newPassword);
        $user->email_verified_at = now();
        $user->save();

        // Send email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\NewPasswordMail($newPassword));
        } catch (\Exception $e) {
             Log::error('Mail Error: ' . $e->getMessage());
             return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng liên hệ IT.']);
        }

        return back()->with('status', 'Mật khẩu mới đã được gửi vào email của bạn.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'ADMIN':
                return redirect()->route('admin.dashboard');
            case 'BUYER':
                return redirect()->route('buyer.dashboard');
            case 'DEPARTMENT':
                return redirect()->route('department.dashboard');
            default:
                Auth::logout();
                return back()->withErrors(['email' => 'Vai trò không hợp lệ.']);
        }
    }
}