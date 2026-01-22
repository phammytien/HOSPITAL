<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Department as DepartmentModel;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho khoa/phòng ban
     */
    public function index()
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Thống kê các yêu cầu (chỉ dựa vào status, không dùng is_submitted)
        $stats = [
            // Đang chờ duyệt = SUBMITTED, PENDING, or NULL status with is_submitted=true
            'pending' => PurchaseRequest::where('department_id', $departmentId)
                ->where(function ($q) {
                    $q->whereIn('status', ['SUBMITTED', 'PENDING'])
                        ->orWhere(function ($sub) {
                            $sub->whereNull('status')->where('is_submitted', true);
                        });
                })
                ->where('is_delete', false)
                ->count(),

            // Đã được duyệt = APPROVED
            'approved' => PurchaseRequest::where('department_id', $departmentId)
                ->where('status', 'APPROVED')
                ->where('is_delete', false)
                ->count(),

            // Đã hoàn thành = COMPLETED hoặc PAID
            'completed' => PurchaseRequest::where('department_id', $departmentId)
                ->whereIn('status', ['COMPLETED', 'PAID'])
                ->where('is_delete', false)
                ->count(),

            // Bị từ chối
            'rejected' => PurchaseRequest::where('department_id', $departmentId)
                ->where('status', 'REJECTED')
                ->where('is_delete', false)
                ->count(),

            // Nháp
            'draft' => PurchaseRequest::where('department_id', $departmentId)
                ->whereIn('status', ['DRAFT', null])
                ->where('is_delete', false)
                ->count(),
        ];

        // Tính tổng số vật tư đã nhận (từ các đơn COMPLETED/PAID)
        $totalItems = PurchaseRequestItem::whereHas('purchaseRequest', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId)
                ->whereIn('status', ['COMPLETED', 'PAID'])
                ->where('is_delete', false);
        })->sum('quantity');

        // Lấy danh sách vật tư đã nhận để hiển thị trong popup
        $receivedItems = PurchaseRequestItem::with(['product', 'purchaseRequest'])
            ->whereHas('purchaseRequest', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId)
                    ->whereIn('status', ['COMPLETED', 'PAID'])
                    ->where('is_delete', false);
            })
            ->where('is_delete', false)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Lấy danh sách yêu cầu đang xử lý (Chờ duyệt, Đã duyệt)
        $activeRequests = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->where(function ($q) {
                $q->whereIn('status', ['SUBMITTED', 'PENDING', 'APPROVED'])
                    ->orWhere(function ($sub) {
                        $sub->whereNull('status')->where('is_submitted', true);
                    });
            })
            ->with(['items.product', 'requester'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Lấy lịch sử yêu cầu (Hoàn thành, Từ chối)
        $requestHistory = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->whereIn('status', ['COMPLETED', 'PAID', 'REJECTED', 'CANCELLED'])
            ->with(['items.product', 'requester'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Lấy thông tin ngân sách của khoa
        $department = DepartmentModel::find($departmentId);

        // Xác định thời gian Quý hiện tại
        $currentMonth = date('n');
        $currentYear = date('Y');
        $quarterStartMonth = floor(($currentMonth - 1) / 3) * 3 + 1;
        $quarterStartDate = \Carbon\Carbon::create($currentYear, $quarterStartMonth, 1)->startOfDay();
        $quarterEndDate = $quarterStartDate->copy()->addMonths(3)->subDay()->endOfDay();

        // Tính tổng đã sử dụng (APPROVED & PAID) trong Quý hiện tại
        $usedBudget = PurchaseRequest::where('purchase_requests.department_id', $departmentId)
            ->whereIn('purchase_requests.status', ['APPROVED', 'COMPLETED', 'PAID'])
            ->where('purchase_requests.is_delete', false)
            ->whereBetween('purchase_requests.created_at', [$quarterStartDate, $quarterEndDate])
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        // Tính tổng đang chờ duyệt (SUBMITTED) trong Quý hiện tại
        $pendingBudget = PurchaseRequest::where('purchase_requests.department_id', $departmentId)
            ->where('purchase_requests.status', 'SUBMITTED')
            ->where('purchase_requests.is_delete', false)
            ->whereBetween('purchase_requests.created_at', [$quarterStartDate, $quarterEndDate])
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        // Lấy 5 phản hồi gần đây nhất liên quan đến khoa (từ các đơn hàng của khoa)
        // Group by purchase_order_id để lấy tin mới nhất của mỗi đơn
        // Lấy 5 phản hồi gần đây nhất liên quan đến khoa (từ các đơn hàng của khoa)
        // Group by purchase_order_id để lấy tin mới nhất của mỗi đơn
        $recentFeedbacks = \App\Models\PurchaseFeedback::with(['purchaseOrder', 'feedbackBy'])
            ->whereHas('purchaseOrder', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->where('is_delete', false)
            ->whereNotNull('rating') // Chỉ lấy feedback có đánh giá (thread starter)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('purchase_order_id')
            ->take(10);

        return view('dashboard.department', compact(
            'stats',
            'totalItems',
            'receivedItems',
            'activeRequests',
            'requestHistory',
            'department',
            'usedBudget',
            'pendingBudget',
            'recentFeedbacks' // Changed from latestNotifications
        ));
    }

    /**
     * Lấy danh sách đơn hàng trong tháng hiện tại
     */
    public function getMonthOrders()
    {
        $departmentId = Auth::user()->department_id;

        $orders = \App\Models\PurchaseOrder::where('department_id', $departmentId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->withCount('items') // Count items
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'created_at_formatted' => $order->created_at->format('d/m/Y H:i'),
                    'items_count' => $order->items_count,
                    'status' => $order->status,
                    'status_label' => $this->getOrderStatusLabel($order->status),
                    'status_class' => $this->getOrderStatusClass($order->status),
                ];
            });

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    private function getOrderStatusLabel($status)
    {
        $labels = [
            'ORDERED' => 'Đã đặt hàng',
            'DELIVERING' => 'Đang giao',
            'DELIVERED' => 'Đã giao',
            'COMPLETED' => 'Hoàn thành',
            'CANCELLED' => 'Đã hủy',
        ];
        return $labels[$status] ?? $status;
    }

    private function getOrderStatusClass($status)
    {
        $classes = [
            'ORDERED' => 'bg-yellow-100 text-yellow-800',
            'DELIVERING' => 'bg-blue-100 text-blue-800',
            'DELIVERED' => 'bg-indigo-100 text-indigo-800',
            'COMPLETED' => 'bg-green-100 text-green-800',
            'CANCELLED' => 'bg-red-100 text-red-800',
        ];
        return $classes[$status] ?? 'bg-gray-100 text-gray-800';
    }
}
