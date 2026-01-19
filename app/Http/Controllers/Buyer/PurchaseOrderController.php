<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        // Display orders with status 'PAID'
        $query = PurchaseOrder::with(['items.product', 'department', 'approver', 'purchaseRequest.workflows.actionBy', 'purchaseRequest.feedbacks.user'])
            ->orderBy('order_date', 'desc');

        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('period') && $request->period != '') {
            $query->whereHas('purchaseRequest', function ($q) use ($request) {
                $q->where('period', $request->period);
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'IN_PROGRESS') {
                $query->whereIn('status', ['CREATED', 'ORDERED', 'DELIVERING', 'DELIVERED']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $orders = $query->paginate(10);
        
        $departments = \App\Models\Department::all();
        // Get unique periods from Purchase Requests to filter orders by the original request period
        // Or we could derive quarter from order_date, but using Request Period is consistent with the Request list.
        $periods = \App\Models\PurchaseRequest::select('period')->distinct()->orderBy('period', 'desc')->pluck('period');

        return view('buyer.requests.orders', compact('orders', 'departments', 'periods'));
    }
    
    public function getOrderDetails($id)
    {
        try {
            $order = PurchaseOrder::with([
                'items.product.category', 
                'department', 
                'purchaseRequest.requester',
                'purchaseRequest.workflows.actionBy'
            ])->findOrFail($id);
            
            // Get status text
            $statusText = 'N/A';
            if (function_exists('getStatusText')) {
                $statusText = getStatusText($order->status);
            } else {
                // Fallback status mapping
                $statusMap = [
                    'PENDING' => 'Chờ duyệt',
                    'APPROVED' => 'Đã duyệt',
                    'ORDERED' => 'Đã đặt hàng',
                    'DELIVERING' => 'Đang giao',
                    'DELIVERED' => 'Đã giao',
                    'COMPLETED' => 'Hoàn thành',
                    'CANCELLED' => 'Đã hủy'
                ];
                $statusText = $statusMap[$order->status] ?? $order->status;
            }
            
            // Get workflows/history
            $workflows = [];
            if ($order->purchaseRequest && $order->purchaseRequest->workflows) {
                $workflows = $order->purchaseRequest->workflows->map(function($workflow) {
                    return [
                        'action' => $workflow->to_status,
                        'action_text' => $this->getActionText($workflow->to_status),
                        'action_by' => $workflow->actionBy ? $workflow->actionBy->full_name : 'N/A',
                        'action_role' => $this->getActionRole($workflow->to_status),
                        'created_at' => $workflow->action_time ? $workflow->action_time->format('d/m/Y H:i') : 'N/A',
                        'note' => $workflow->action_note
                    ];
                })->sortByDesc('created_at')->values();
            }
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'request_code' => $order->purchaseRequest->request_code ?? null,
                    'department_name' => $order->department->department_name ?? 'N/A',
                    'order_date' => $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A',
                    'status' => $order->status,
                    'status_text' => $statusText,
                    'total_amount' => $order->total_amount,
                    'note' => $order->purchaseRequest->note ?? 'Không có ghi chú',
                    'items' => $order->items->map(function($item) {
                        return [
                            'product_name' => $item->product->product_name ?? 'N/A',
                            'category_name' => $item->product->category->category_name ?? 'N/A',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->quantity * $item->unit_price
                        ];
                    }),
                    'workflows' => $workflows
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getActionText($action)
    {
        $actionMap = [
            'PENDING' => 'Gửi yêu cầu',
            'APPROVED' => 'Đã phê duyệt',
            'REJECTED' => 'Từ chối',
            'ORDERED' => 'Đã đặt hàng',
            'DELIVERING' => 'Đang giao hàng',
            'DELIVERED' => 'Đã giao hàng',
            'COMPLETED' => 'Hoàn thành',
            'CANCELLED' => 'Đã hủy',
            'DRAFT' => 'Khởi tạo yêu cầu'
        ];
        return $actionMap[$action] ?? $action;
    }
    
    private function getActionRole($action)
    {
        $roleMap = [
            'PENDING' => 'Người gửi',
            'APPROVED' => 'Trưởng khoa',
            'REJECTED' => 'Trưởng khoa',
            'ORDERED' => 'Chuyên viên mua sắm',
            'DELIVERING' => 'Nhà cung cấp',
            'DELIVERED' => 'Nhà cung cấp',
            'COMPLETED' => 'Chuyên viên mua sắm',
            'CANCELLED' => 'Người hủy',
            'DRAFT' => 'Người tạo'
        ];
        return $roleMap[$action] ?? 'Người thực hiện';
    }
}
