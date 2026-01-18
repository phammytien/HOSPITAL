<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Department as DepartmentModel;
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
            // Đang chờ duyệt = SUBMITTED
            'pending' => PurchaseRequest::where('department_id', $departmentId)
                ->where('status', 'SUBMITTED')
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

        // Lấy danh sách yêu cầu gần đây (không hiển thị nháp - chỉ hiển thị đã gửi trở lên)
        $recentRequests = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->where('is_submitted', true) // Chỉ lấy các yêu cầu đã gửi
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

        return view('dashboard.department', compact(
            'stats',
            'totalItems',
            'receivedItems',
            'recentRequests',
            'department',
            'usedBudget',
            'pendingBudget'
        ));
    }
}
