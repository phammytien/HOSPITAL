<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseFeedback;
use App\Models\Notification;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DeliveryTrackingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'CREATED'); // Default to CREATED (Mới tạo)

        if ($status === 'FEEDBACK') {
            // Logic from FeedbackController
            $query = PurchaseFeedback::with(['feedbackBy', 'purchaseOrder'])
                ->where('is_delete', false)
                ->whereNotNull('purchase_order_id')
                ->whereIn('id', function ($q) {
                    $q->select(DB::raw('MIN(id)'))
                        ->from('purchase_feedbacks')
                        ->where('is_delete', false)
                        ->whereNotNull('rating')
                        ->whereNotNull('purchase_order_id')
                        ->groupBy('purchase_order_id');
                });

            if ($request->has('department_id') && $request->department_id != '') {
                $query->whereHas('feedbackBy', function ($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        } else {
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

            // Apply status filter
            if ($status === 'CREATED') {
                $query->whereIn('status', ['CREATED', 'PENDING']);
            } elseif ($status === 'CANCELLED') {
                $query->whereIn('status', ['CANCELLED', 'REJECTED']);
            } else {
                $query->where('status', $status);
            }

            $orders = $query->paginate(10);
        }
        $departments = \App\Models\Department::all();
        $periods = \App\Models\PurchaseRequest::select('period')->distinct()->orderBy('period', 'desc')->pluck('period');

        // Get counts for each tab with filters applied
        $countQuery = PurchaseOrder::query();
        if ($request->has('department_id') && $request->department_id != '') {
            $countQuery->where('department_id', $request->department_id);
        }
        if ($request->has('period') && $request->period != '') {
            $countQuery->whereHas('purchaseRequest', function ($q) use ($request) {
                $q->where('period', $request->period);
            });
        }

        $allCounts = $countQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'CREATED' => ($allCounts['CREATED'] ?? 0) + ($allCounts['PENDING'] ?? 0),
            'ORDERED' => $allCounts['ORDERED'] ?? 0,
            'DELIVERING' => $allCounts['DELIVERING'] ?? 0,
            'DELIVERED' => $allCounts['DELIVERED'] ?? 0,
            'COMPLETED' => $allCounts['COMPLETED'] ?? 0,
            'CANCELLED' => ($allCounts['CANCELLED'] ?? 0) + ($allCounts['REJECTED'] ?? 0),
        ];

        return view('buyer.tracking.index', compact('orders', 'departments', 'periods', 'status', 'counts'));
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
        $success = $this->performStatusUpdate($order, $request->status, $request->expected_delivery_date);

        if (!$success) {
            return redirect()->back()->with('error', 'Không thể chuyển lùi trạng thái đơn hàng.');
        }

        return redirect()->back()->with('success', 'Cập nhật tiến độ giao hàng thành công.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:purchase_orders,id',
            'status' => 'required|in:CREATED,ORDERED,PENDING,DELIVERING,DELIVERED,COMPLETED,CANCELLED',
            'expected_delivery_date' => 'nullable|date',
        ]);

        $orders = PurchaseOrder::whereIn('id', $request->order_ids)->get();
        $count = 0;

        foreach ($orders as $order) {
            if ($this->performStatusUpdate($order, $request->status, $request->expected_delivery_date)) {
                $count++;
            }
        }

        if ($count === 0 && count($request->order_ids) > 0) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào hợp lệ để chuyển đổi trạng thái.');
        }

        return redirect()->back()->with('success', "Đã cập nhật trạng thái thành công cho {$count} đơn hàng.");
    }

    private function getStatusRank(string $status): int
    {
        return match ($status) {
            'CREATED' => 1,
            'PENDING' => 2,
            'ORDERED' => 3,
            'DELIVERING' => 4,
            'DELIVERED' => 5,
            'COMPLETED' => 6,
            'CANCELLED' => 0, // Terminal but low rank for transition logic
            'REJECTED' => 0,
            default => 0
        };
    }

    private function performStatusUpdate(PurchaseOrder $order, string $status, ?string $expectedDeliveryDate = null)
    {
        $oldStatus = $order->status;
        $oldRank = $this->getStatusRank($oldStatus);

        if ($expectedDeliveryDate) {
            $order->expected_delivery_date = $expectedDeliveryDate;
        }

        $newRank = $this->getStatusRank($status);

        // Enforce forward-only transition (except for setting expected delivery date which might stay at the same status)
        // We allow same rank if expectedDeliveryDate is being set for the first time
        if ($newRank < $oldRank && !in_array($status, ['CANCELLED', 'REJECTED'])) {
            return false;
        }

        // Update timestamps based on status
        $now = now();
        if ($status == 'ORDERED' && !$order->ordered_at) {
            $order->ordered_at = $now;
        } elseif ($status == 'DELIVERING' && !$order->shipping_at) {
            $order->shipping_at = $now;
        } elseif ($status == 'DELIVERED' && !$order->delivered_at) {
            $order->delivered_at = $now;
        } elseif ($status == 'COMPLETED' && !$order->completed_at) {
            $order->completed_at = $now;
        }

        $order->status = $status;
        $order->save();

        if ($status == 'DELIVERED') {
            $order->items()->update(['status' => 'DELIVERED']);

            // Notification: Delivered to Warehouse (Wrapped in try-catch)
            try {
                \App\Models\Notification::create([
                    'title' => 'Vật tư đã giao',
                    'message' => "Đơn hàng #{$order->order_code} đã về kho",
                    'type' => 'info',
                    'target_role' => 'department',
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Notification creation failed for Order #{$order->id}: " . $e->getMessage());
            }

        } elseif ($status == 'ORDERED') {
            // Update ALL items to ORDERED when the whole order is confirmed
            $order->items()->update(['status' => 'ORDERED']);

            // Notification: Order Confirmed by Buyer (Wrapped in try-catch)
            try {
                \App\Models\Notification::create([
                    'title' => 'Đơn hàng đã duyệt',
                    'message' => "Bộ phận mua hàng đã xác nhận đơn #{$order->order_code}",
                    'type' => 'success',
                    'target_role' => 'department',
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Notification creation failed for Order #{$order->id}: " . $e->getMessage());
            }
        } elseif ($status == 'DELIVERING') {
            // Update ALL items to DELIVERING
            $order->items()->update(['status' => 'DELIVERING']);

            // Notification: Shipping started (Wrapped in try-catch)
            try {
                \App\Models\Notification::create([
                    'title' => 'Đơn hàng đang giao',
                    'message' => "Đơn hàng #{$order->order_code} đang trên đường giao",
                    'type' => 'info',
                    'target_role' => 'department',
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Notification creation failed for Order #{$order->id}: " . $e->getMessage());
            }
        }

        if ($status == 'PENDING') {
            // Update items to PENDING
            $order->items()->update(['status' => 'PENDING']);

            // Notification: Order Confirmed/Pending by Buyer (Wrapped in try-catch)
            try {
                \App\Models\Notification::create([
                    'title' => 'Đơn hàng chờ xử lý',
                    'message' => "Bộ phận mua hàng đã xác nhận ngày giao cho đơn #{$order->order_code}" . ($order->expected_delivery_date ? ": " . \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') : ""),
                    'type' => 'info',
                    'target_role' => 'department',
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Notification creation failed for Order #{$order->id}: " . $e->getMessage());
            }
        }

        if ($status == 'COMPLETED') {
            // Keep items status updated if needed
            $order->items()->update(['status' => 'COMPLETED']);

            $purchaseRequest = $order->purchaseRequest;
            if (!$purchaseRequest && $order->purchase_request_id) {
                $purchaseRequest = \App\Models\PurchaseRequest::find($order->purchase_request_id);
            }

            if ($purchaseRequest) {
                $purchaseRequest->status = 'COMPLETED';
                $purchaseRequest->save();
            }

            // Notification: Order Completed (Wrapped in try-catch)
            try {
                \App\Models\Notification::create([
                    'title' => 'Đơn hàng hoàn tất',
                    'message' => "Đơn hàng #{$order->order_code} đã hoàn tất quy trình",
                    'type' => 'success',
                    'target_role' => 'department',
                    'created_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Notification creation failed for Order #{$order->id}: " . $e->getMessage());
            }
        }

        return true;
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
