<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['department', 'approver', 'items.product'])
            ->where('is_delete', false);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'like', '%' . $request->search . '%')
                    ->orWhere('supplier_name', 'like', '%' . $request->search . '%');
            });
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => PurchaseOrder::where('is_delete', false)->count(),
            'pending' => PurchaseOrder::where('is_delete', false)->where('status', 'PENDING')->count(),
            'processing' => PurchaseOrder::where('is_delete', false)->where('status', 'PROCESSING')->count(),
            'completed' => PurchaseOrder::where('is_delete', false)->where('status', 'COMPLETED')->count(),
            'cancelled' => PurchaseOrder::where('is_delete', false)->where('status', 'CANCELLED')->count(),
        ];

        $departments = Department::where('is_delete', false)->orderBy('department_name')->get();

        return view('admin.orders.index', compact('orders', 'stats', 'departments'));
    }

    /**
     * Display the specified purchase order
     */
    public function show($id)
    {
        $order = PurchaseOrder::with([
            'department',
            'approver',
            'items.product.category',
            'purchaseRequest.requester'
        ])
            ->where('is_delete', false)
            ->findOrFail($id);

        // Use total_amount from database directly
        $totalAmount = $order->total_amount;

        return view('admin.orders.show', compact('order', 'totalAmount'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:PENDING,APPROVED,PROCESSING,DELIVERED,PAID,COMPLETED,CANCELLED',
            'note' => 'nullable|string'
        ]);

        try {
            $order = PurchaseOrder::where('is_delete', false)->findOrFail($id);
            
            $order->update([
                'status' => $validated['status'],
                'admin_note' => $validated['note'] ?? $order->admin_note
            ]);

            return redirect()
                ->route('admin.orders.show', $id)
                ->with('success', 'Cập nhật trạng thái đơn hàng thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái!');
        }
    }
}
