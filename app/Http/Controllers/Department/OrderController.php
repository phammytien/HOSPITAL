<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseFeedback;
use App\Models\Notification;

class OrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Assuming user has department_id or related department
        $departmentId = $user->department_id;

        $orders = PurchaseOrder::with(['purchaseRequest'])
            ->where('department_id', $departmentId) // Filter by Department
            ->where('is_delete', 0)
            ->when(request('status') && request('status') != 'all', function ($query) {
                return $query->where('status', request('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('department.orders.index', compact('orders'));
    }

    public function confirm(Request $request, $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        // Allow confirmation only when order is DELIVERED (arrived at warehouse)
        if ($order->status == 'DELIVERED') {
            $order->status = 'COMPLETED';
            $order->completed_at = now();
            // $order->approved_by = auth()->id(); // REMOVED: Do not overwrite original approver
            $order->save();

            // Save Feedback & Rating
            if ($request->filled('rating') || $request->filled('feedback_content')) {
                \App\Models\PurchaseFeedback::create([
                    'purchase_request_id' => $order->purchase_request_id,
                    'purchase_order_id' => $order->id,
                    'feedback_by' => auth()->id(),
                    'feedback_content' => $request->feedback_content ?? 'Xác nhận nhận hàng',
                    'rating' => $request->rating,
                    'status' => 'PENDING',
                    'feedback_date' => now(),
                ]);
            }

            // Sync PurchaseRequest status
            $purchaseRequest = $order->purchaseRequest;
            if ($purchaseRequest) {
                // User requested Request status to be COMPLETED
                $purchaseRequest->status = 'COMPLETED';
                $purchaseRequest->save();
            }

            // User requested Items status to be PAID
            $order->items()->update(['status' => 'PAID']);

            // Log workflow
            \App\Models\PurchaseRequestWorkflow::create([
                'purchase_request_id' => $order->purchase_request_id,
                'action_by' => auth()->id(),
                'from_status' => 'DELIVERED',
                'to_status' => 'COMPLETED',
                'action_note' => 'Khoa đã xác nhận nhận hàng' . ($request->filled('rating') ? " (Đánh giá: {$request->rating} sao)" : ''),
                'action_time' => now(),
            ]);

            // Notification: Order Completed (Department Confirmed)
            Notification::create([
                'title' => 'Đơn hàng đã hoàn thành',
                'message' => "Khoa {$order->department->department_name} đã xác nhận nhận hàng đơn #{$order->order_code}.",
                'type' => 'success',
                'target_role' => 'buyer', // Buyer should know. Also maybe Admin.
                'created_by' => auth()->id(),
            ]);

            // Auto-update Department Inventory
            // Find or create Warehouse for this department
            $warehouse = \App\Models\Warehouse::firstOrCreate(
                ['department_id' => $order->department_id],
                [
                    'warehouse_code' => 'WH-' . strtoupper($order->department->code ?? 'DEPT' . $order->department_id),
                    'warehouse_name' => 'Kho ' . ($order->department->department_name ?? 'Khoa Phòng'),
                    'location' => 'Tại khoa',
                ]
            );

            // Update Inventory for each item
            foreach ($order->items as $item) {
                // 1. Update Snapshot Inventory
                $inventory = \App\Models\Inventory::firstOrCreate(
                    ['warehouse_id' => $warehouse->id, 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );

                $inventory->quantity += $item->quantity;
                $inventory->save();

                // 2. Log to Warehouse Inventory (Traceability)
                \App\Models\WarehouseInventory::create([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $item->product_id,
                    'transaction_type' => 'IMPORT',
                    'quantity' => $item->quantity,
                    'related_order_id' => $order->id,
                    'related_request_id' => $order->purchase_request_id, // Link to Request
                    'performed_by' => auth()->id(),
                    'note' => 'Nhập kho từ đơn hàng #' . $order->order_code,
                ]);
            }

            return redirect()->back()->with('success', 'Đã xác nhận đơn hàng thành công và cập nhật kho.');
        }

        return redirect()->back()->with('error', 'Chỉ có thể xác nhận đơn hàng đã về kho.');
    }

    public function reject(Request $request, $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        if ($order->status == 'DELIVERED') {
            // Process rejection reason
            $reasonOption = $request->input('reason_option');
            $otherReason = $request->input('other_reason');
            $wrongItems = $request->input('wrong_items', []);

            // Build detailed reason text
            if ($reasonOption === 'wrong_product') {
                $reason = 'Sai sản phẩm';
                if (!empty($wrongItems)) {
                    $itemNames = \App\Models\PurchaseOrderItem::whereIn('id', $wrongItems)
                        ->with('product')
                        ->get()
                        ->pluck('product.product_name')
                        ->toArray();
                    $reason .= ': ' . implode(', ', $itemNames);
                }
            } else {
                $reason = $otherReason ?? 'Lý do khác';
            }

            $order->status = 'REJECTED';
            $order->save();

            // Update order items status to REJECTED
            $order->items()->update(['status' => 'REJECTED']);

            // Sync PurchaseRequest status if applicable
            $purchaseRequest = $order->purchaseRequest;
            if ($purchaseRequest) {
                $purchaseRequest->status = 'REJECTED';
                $purchaseRequest->save();
            }

            // Save feedback with detailed reason
            \App\Models\PurchaseFeedback::create([
                'purchase_request_id' => $order->purchase_request_id,
                'purchase_order_id' => $order->id,
                'feedback_by' => auth()->id(),
                'feedback_content' => $reason,
                'rating' => 1, // Low rating for rejected orders
                'status' => 'PENDING',
                'feedback_date' => now(),
            ]);

            // Log workflow
            \App\Models\PurchaseRequestWorkflow::create([
                'purchase_request_id' => $order->purchase_request_id,
                'action_by' => auth()->id(),
                'from_status' => 'DELIVERED',
                'to_status' => 'REJECTED',
                'action_note' => 'Từ chối nhận hàng: ' . $reason,
                'action_time' => now(),
            ]);

            // Notification to Buyer
            Notification::create([
                'title' => 'Đơn hàng bị từ chối',
                'message' => "Khoa {$order->department->department_name} đã từ chối đơn hàng #{$order->order_code}. Lý do: {$reason}",
                'type' => 'error',
                'target_role' => 'buyer',
                'related_id' => $order->id,
                'related_type' => 'PurchaseOrder',
            ]);

            return redirect()->back()->with('success', 'Đã từ chối đơn hàng.');
        }

        return redirect()->back()->with('error', 'Chỉ có thể từ chối đơn hàng đã về kho.');
    }
}
