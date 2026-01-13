<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load work scope data
        // Note: purchase_requests doesn't have buyer_id, buyers process all requests
        $workScope = [
            'total_requests' => PurchaseRequest::whereNotNull('id')->count(),
            'pending_requests' => PurchaseRequest::where('status', 'PENDING')->count(),
            'total_orders' => PurchaseOrder::whereNotNull('id')->count(),
            'permissions' => [
                'Xử lý yêu cầu mua hàng',
                'Theo dõi & Quản lý đơn hàng',
            ],
        ];

        return view('buyer.profile.index', compact('user', 'workScope'));
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
