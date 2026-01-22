<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentOrderController extends Controller
{
    // Danh sách đơn hàng cần xác nhận
    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $query = PurchaseOrder::where('department_id', $departmentId) // Giả định PurchaseOrder có department_id
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'UNRATED') {
                $query->whereIn('status', ['COMPLETED', 'REJECTED'])
                    ->whereDoesntHave('feedbacks');
            } else {
                $query->where('status', $request->status);
            }
        }

        $orders = $query->paginate(10);

        return view('department.orders.index', compact('orders'));
    }

    // Xác nhận đã nhận hàng thanh toán
    public function confirm($id)
    {
        $order = PurchaseOrder::findOrFail($id);

        // Status flow: DELIVERED -> COMPLETED
        if ($order->status == 'DELIVERED') {
            $order->status = 'COMPLETED';
            $order->completed_at = now();
            $order->save();
            return redirect()->back()->with('success', 'Đã xác nhận nhận hàng thành công');
        }

        return redirect()->back()->with('error', 'Trạng thái đơn hàng không hợp lệ để xác nhận');
    }

    // Từ chối đơn hàng
    public function cancel($id)
    {
        $order = PurchaseOrder::findOrFail($id);

        if ($order->status == 'CREATED') {
            $order->status = 'CANCELLED'; // Hoặc REJECTED tùy enum
            $order->save();
            return redirect()->back()->with('success', 'Đã từ chối đơn hàng');
        }

        return redirect()->back()->with('error', 'Trạng thái đơn hàng không hợp lệ');
    }
}
