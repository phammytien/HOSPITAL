<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Notification;

class DeliveryTrackingController extends Controller
{
    public function index(Request $request)
    {
        // Display orders that are in "Tracking" workflow (e.g. not just Created, or maybe all?)
        // User said "move this delivery process function... create another category".
        // Let's list all orders but focus on tracking status.

        $query = PurchaseOrder::with(['department', 'purchaseRequest'])
            ->orderBy('created_at', 'desc');

        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('period') && $request->period != '') {
            $query->whereHas('purchaseRequest', function ($q) use ($request) {
                $q->where('period', $request->period);
            });
        }

        // Filter by status if needed?
        // Maybe default to showing active orders?
        // For now, list all like the original Orders page, but the view will be different (focused on tracking).

        $orders = $query->paginate(10);
        $departments = \App\Models\Department::all();
        $periods = \App\Models\PurchaseRequest::select('period')->distinct()->orderBy('period', 'desc')->pluck('period');

        return view('buyer.tracking.index', compact('orders', 'departments', 'periods'));
    }

    public function show($id)
    {
        $order = PurchaseOrder::with(['department', 'approver', 'items.product', 'purchaseRequest'])->findOrFail($id);

        $steps = [
            'CREATED' => ['label' => 'Mới tạo', 'icon' => 'fa-file-invoice'],
            'ORDERED' => ['label' => 'Đã đặt hàng', 'icon' => 'fa-shopping-cart'],
            'DELIVERING' => ['label' => 'Đang giao', 'icon' => 'fa-truck'],
            'DELIVERED' => ['label' => 'Đã nhận hàng', 'icon' => 'fa-box-open'],
            'COMPLETED' => ['label' => 'Hoàn tất', 'icon' => 'fa-check-circle']
        ];

        // Calculate progress
        $statusOrder = array_keys($steps);
        $currentIndex = array_search($order->status, $statusOrder);

        if ($order->status == 'CANCELLED') {
            $progress = 0;
        } else {
            $progress = ($currentIndex !== false) ? ($currentIndex / (count($steps) - 1)) * 100 : 0;
        }

        return view('buyer.tracking.show', compact('order', 'steps', 'progress'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expected_delivery_date' => 'nullable|date',
            'status' => 'required|in:CREATED,ORDERED,PENDING,DELIVERING,DELIVERED,COMPLETED,CANCELLED',
        ]);

        $order = PurchaseOrder::findOrFail($id);

        if ($request->filled('expected_delivery_date')) {
            $order->expected_delivery_date = $request->expected_delivery_date;
            // When buyer confirms date, if status is CREATED, move to PENDING (Chờ xử lý) as requested
            if ($order->status == 'CREATED') {
                $order->status = 'PENDING';
                $request->merge(['status' => 'PENDING']); // Override request status for downstream logic
            }
        }

        // Update timestamps based on status
        $now = now();
        if ($request->status == 'ORDERED' && !$order->ordered_at) {
            $order->ordered_at = $now;
        } elseif ($request->status == 'DELIVERING' && !$order->shipping_at) {
            $order->shipping_at = $now;
        } elseif ($request->status == 'DELIVERED' && !$order->delivered_at) {
            $order->delivered_at = $now;
        } elseif ($request->status == 'COMPLETED' && !$order->completed_at) {
            $order->completed_at = $now;
        }

        $order->status = $request->status;
        $order->save();

        if ($request->status == 'DELIVERED') {
            $order->items()->update(['status' => 'DELIVERED']);

            // Notification: Delivered to Warehouse
            Notification::create([
                'title' => 'Vật tư đã giao',
                'message' => "Đơn hàng #{$order->order_code} đã về kho",
                'type' => 'info', // Icon matching truck/delivery
                'target_role' => 'department',
                'created_by' => auth()->id(),
            ]);

        } elseif ($request->status == 'ORDERED') {
            $order->items()->where('status', 'PENDING')->update(['status' => 'ORDERED']);

            // Notification: Order Confirmed by Buyer
            Notification::create([
                'title' => 'Đơn hàng đã duyệt',
                'message' => "Bộ phận mua hàng đã xác nhận đơn #{$order->order_code}",
                'type' => 'success',
                'target_role' => 'department',
                'created_by' => auth()->id(),
            ]);
        }

        // 1. If Order is ORDERED (Đã đặt hàng) -> Request becomes PROCESSING (Đang xử lý)
        if ($request->status == 'PENDING') {
            $purchaseRequest = $order->purchaseRequest;
            // Fallback: try to find by ID if relation not loaded
            if (!$purchaseRequest && $order->purchase_request_id) {
                $purchaseRequest = \App\Models\PurchaseRequest::find($order->purchase_request_id);
            }

            // Also update Items to PENDING
            $order->items()->where('status', 'PENDING')->update(['status' => 'PENDING']);

            // Notification: Order Confirmed/Pending by Buyer
            Notification::create([
                'title' => 'Đơn hàng chờ xử lý',
                'message' => "Bộ phận mua hàng đã xác nhận ngày giao cho đơn #{$order->order_code}: " . \Carbon\Carbon::parse($request->expected_delivery_date)->format('d/m/Y'),
                'type' => 'info',
                'target_role' => 'department',
                'created_by' => auth()->id(),
            ]);
        }

        // 2. If Order is COMPLETED or PAID -> Request becomes PAID
        // Note: The user said COMPLETED triggers PAID for request. 
        // But if Buyer sets it to PAID manually here, we should probably also sync it.
        if ($request->status == 'COMPLETED') {
            $purchaseRequest = $order->purchaseRequest;
            // Fallback
            if (!$purchaseRequest && $order->purchase_request_id) {
                $purchaseRequest = \App\Models\PurchaseRequest::find($order->purchase_request_id);
            }

            if ($purchaseRequest) {
                $purchaseRequest->status = 'COMPLETED';
                $purchaseRequest->save();
                session()->flash('info', "Đã cập nhật trạng thái Yêu cầu #{$purchaseRequest->request_code} sang Hoàn thành.");
            }
        }

        return redirect()->back()->with('success', 'Cập nhật tiến độ giao hàng thành công.');
    }

    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $item = PurchaseOrderItem::findOrFail($itemId);
        $item->status = $request->status;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái sản phẩm thành công']);
    }
}
