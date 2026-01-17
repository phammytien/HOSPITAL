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

        // Date filter (Month/Year)
        if ($request->filled('filter_month') && $request->filled('filter_year')) {
            $query->whereYear('order_date', $request->filter_year)
                ->whereMonth('order_date', $request->filter_month);
        } elseif ($request->filled('filter_month')) {
            $query->whereMonth('order_date', $request->filter_month);
        } elseif ($request->filled('filter_year')) {
            $query->whereYear('order_date', $request->filter_year);
        } elseif ($request->filled('month')) {
            $date = \Carbon\Carbon::parse($request->month);
            $query->whereYear('order_date', $date->year)
                ->whereMonth('order_date', $date->month);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => PurchaseOrder::where('is_delete', false)->count(),
            'pending' => PurchaseOrder::where('is_delete', false)->where('status', 'PENDING')->count(),

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
            'status' => 'required|in:CREATED,PENDING,ORDERED,DELIVERING,DELIVERED,COMPLETED,CANCELLED,REJECTED',
            'note' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $order = PurchaseOrder::where('is_delete', false)->findOrFail($id);
            $now = now();

            // Prepare update data
            $updateData = [
                'status' => $validated['status'],
                'admin_note' => $validated['note'] ?? $order->admin_note
            ];

            // Record timestamps based on status
            if ($validated['status'] == 'ORDERED' && !$order->ordered_at) {
                $updateData['ordered_at'] = $now;
            } elseif ($validated['status'] == 'DELIVERING' && !$order->shipping_at) {
                $updateData['shipping_at'] = $now;
            } elseif ($validated['status'] == 'DELIVERED' && !$order->delivered_at) {
                $updateData['delivered_at'] = $now;
            } elseif ($validated['status'] == 'COMPLETED' && !$order->completed_at) {
                $updateData['completed_at'] = $now;
            }

            $order->update($updateData);

            // Sync all items to the same status
            $order->items()->update(['status' => $validated['status']]);

            // If COMPLETED, also sync the Purchase Request status
            if ($validated['status'] == 'COMPLETED') {
                $purchaseRequest = $order->purchaseRequest;
                if ($purchaseRequest) {
                    $purchaseRequest->update(['status' => 'COMPLETED']);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.orders.show', $id)
                ->with('success', 'Cập nhật trạng thái đơn hàng và sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
