<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Mail;
use App\Mail\ITSupportMail;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    public function show()
    {
        $departments = Department::all();
        $errorTypes = [
            'Không thể đăng nhập',
            'Quên mật khẩu nhưng không nhận được email',
            'Lỗi hiển thị giao diện',
            'Hệ thống chạy chậm',
            'Không thể tạo yêu cầu mua hàng',
            'Khác (Vui lòng mô tả chi tiết)'
        ];

        return view('support.contact', compact('departments', 'errorTypes'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'department_id' => 'required|exists:departments,id',
            'error_type' => 'required|string',
            'new_error_name' => 'nullable|string|required_if:error_type,new_error',
            'description' => 'required|string',
        ]);

        $data = $request->only(['name', 'email', 'department_id', 'error_type', 'description', 'new_error_name']);
        
        if ($data['error_type'] === 'new_error') {
            $data['error_type'] = $data['new_error_name'];
        }

        $department = Department::find($data['department_id']);
        $data['department_name'] = $department ? $department->department_name : 'N/A';

        try {
            Mail::to('thanhthaotp02019@gmail.com')->send(new ITSupportMail($data));
        } catch (\Exception $e) {
            Log::error('IT Support Mail Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Có lỗi xảy ra khi gửi email. Vui lòng liên hệ trực tiếp.']);
        }

        return back()->with('status', 'Yêu cầu hỗ trợ đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }
}
