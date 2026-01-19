<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseFeedback;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $pendingOrderCount = PurchaseOrder::where('status', 'PENDING')->count();
        $approvedMonthCount = PurchaseRequest::where('status', 'APPROVED')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $completedCount = PurchaseOrder::where('status', 'COMPLETED')->count();
        $rejectedCount = PurchaseRequest::where('status', 'REJECTED')->count();

        // Recent Requests (last 5)
        $recentRequests = PurchaseRequest::with(['department', 'requester'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Feedbacks (last 3)
        $recentFeedbacks = PurchaseFeedback::with(['user', 'purchaseRequest.department', 'purchaseOrder.purchaseRequest.department'])
            ->latest('feedback_date')
            ->take(3)
            ->get();

        // Notifications
        $notifications = Notification::latest()
            ->take(5)
            ->get();

        // Departments for Filter
        $departments = \App\Models\Department::all();

        // Available Years
        $availableYears = PurchaseOrder::selectRaw('YEAR(order_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($availableYears))
            $availableYears = [now()->year];

        // Filter inputs
        $spendingDeptId = $request->get('spending_dept_id');
        $quantityDeptId = $request->get('quantity_dept_id');

        $spendingYear = $request->get('spending_year', $availableYears[0]);
        $quantityYear = $request->get('quantity_year', $availableYears[0]);

        // Chart 1: Spending Trend (Quarterly)
        $spendingQuery = PurchaseOrder::selectRaw('QUARTER(order_date) as quarter, SUM(total_amount) as total')
            ->whereYear('order_date', $spendingYear);

        if ($spendingDeptId) {
            $spendingQuery->where('department_id', $spendingDeptId);
        }

        $spendingData = $spendingQuery->groupBy('quarter')
            ->orderBy('quarter')
            ->pluck('total', 'quarter')
            ->toArray();

        $spendingChartData = [];
        for ($i = 1; $i <= 4; $i++) {
            $spendingChartData[] = $spendingData[$i] ?? 0;
        }

        // Chart 2: Quantity per Quarter
        $quantityQuery = \Illuminate\Support\Facades\DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->selectRaw('QUARTER(purchase_orders.order_date) as quarter, SUM(purchase_order_items.quantity) as total_qty')
            ->whereYear('purchase_orders.order_date', $quantityYear);

        if ($quantityDeptId) {
            $quantityQuery->where('purchase_orders.department_id', $quantityDeptId);
        }

        $quarterlyData = $quantityQuery->groupBy('quarter')
            ->pluck('total_qty', 'quarter')
            ->toArray();

        $quantityChartData = [];
        for ($i = 1; $i <= 4; $i++) {
            $quantityChartData[] = $quarterlyData[$i] ?? 0;
        }

        return view('dashboard.buyer', compact(
            'pendingOrderCount',
            'approvedMonthCount',
            'completedCount',
            'rejectedCount',
            'recentRequests',
            'recentFeedbacks',
            'spendingChartData',
            'quantityChartData',
            'departments',
            'availableYears',
            'spendingDeptId',
            'quantityDeptId',
            'spendingYear',
            'spendingYear',
            'quantityYear',
            'notifications' // Added variable
        ));
    }
}
