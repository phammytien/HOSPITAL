<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\Department;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get unique periods for filter
        $periods = PurchaseRequest::select('period')
            ->distinct()
            ->orderBy('period', 'desc')
            ->pluck('period');

        // Get filter parameters
        $departmentId = $request->get('department_id');
        $status = $request->get('status');
        $selectedPeriod = $request->get('period'); // Only use if explicitly provided

        // Build query for purchase orders
        $query = PurchaseOrder::with(['department', 'purchaseRequest'])
            ->where('is_delete', false)
            ->whereIn('status', ['COMPLETED', 'CANCELLED']);

        // Filter duplicates: Keep only the latest order for each request
        $latestIds = PurchaseOrder::select(DB::raw('MAX(id)'))
            ->where('is_delete', false)
            ->whereNotNull('purchase_request_id')
            ->groupBy('purchase_request_id');

        $query->whereIn('id', $latestIds);

        // Filter by period (Join with purchase_requests) - only if provided
        if ($selectedPeriod) {
            $query->whereHas('purchaseRequest', function ($q) use ($selectedPeriod) {
                $q->where('period', $selectedPeriod);
            });
        }

        // Apply filters
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Get paginated orders (we'll keep variable name $requests for the view to minimize changes)
        $requests = $query->orderBy('order_date', 'desc')->paginate(15);

        // Get all departments for filter dropdown
        $departments = Department::orderBy('department_name')->get();

        // Get Available Statuses (Restricted)
        $statuses = ['COMPLETED', 'CANCELLED'];

        // Calculate statistics based on filtered data from PurchaseOrder
        $stats = $this->calculateOrderStatistics($selectedPeriod, $departmentId);

        return view('buyer.reports.index', compact(
            'requests',
            'departments',
            'stats',
            'departmentId',
            'status',
            'statuses',
            'periods',
            'selectedPeriod'
        ));
    }

    private function calculateOrderStatistics($period = null, $departmentId = null)
    {
        $query = PurchaseOrder::where('is_delete', false);

        // Filter duplicates: Keep only the latest order for each request
        $latestIds = PurchaseOrder::select(DB::raw('MAX(id)'))
            ->where('is_delete', false)
            ->whereNotNull('purchase_request_id')
            ->groupBy('purchase_request_id');

        $query->whereIn('id', $latestIds);

        if ($period) {
            $query->whereHas('purchaseRequest', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $totalOrders = (clone $query)->count();
        $approvedOrders = (clone $query)->where('status', 'APPROVED')->count();
        $completedOrders = (clone $query)->where('status', 'COMPLETED')->count();
        $cancelledOrders = (clone $query)->where('status', 'CANCELLED')->count();
        $totalAmount = (clone $query)->where('status', 'COMPLETED')->sum('total_amount');

        return [
            'total_requests' => $totalOrders,
            'approved_requests' => $approvedOrders,
            'completed_requests' => $completedOrders,
            'cancelled_requests' => $cancelledOrders,
            'total_budget' => $totalAmount,
            'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0,
        ];
    }

    private function calculateStatistics($year, $months = null)
    {
        $query = PurchaseRequest::whereYear('created_at', $year);

        if ($months) {
            $query->whereIn(DB::raw('MONTH(created_at)'), $months);
        }

        $totalRequests = $query->count();
        $pendingRequests = (clone $query)->where('status', 'PENDING')->count();
        $approvedRequests = (clone $query)->where('status', 'APPROVED')->count();
        $rejectedRequests = (clone $query)->where('status', 'REJECTED')->count();

        // Calculate total budget from approved orders
        $orderQuery = PurchaseOrder::whereYear('created_at', $year);
        if ($months) {
            $orderQuery->whereIn(DB::raw('MONTH(created_at)'), $months);
        }

        $totalBudget = $orderQuery->where('status', '!=', 'CANCELLED')
            ->sum('total_amount');

        return [
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests,
            'approved_requests' => $approvedRequests,
            'rejected_requests' => $rejectedRequests,
            'total_budget' => $totalBudget,
            'approval_rate' => $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0,
        ];
    }

    private function getBudgetTrendData($year, $months)
    {
        $data = PurchaseOrder::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', $year)
            ->whereIn(DB::raw('MONTH(created_at)'), $months)
            ->where('status', '!=', 'CANCELLED')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill in missing months with 0 for the quarter
        $monthsData = [];
        foreach ($months as $m) {
            $monthsData[$m] = 0;
        }

        foreach ($data as $item) {
            $monthsData[$item->month] = $item->total;
        }

        // Create labels for the quarter months
        $monthNames = [
            1 => 'Tháng 1',
            2 => 'Tháng 2',
            3 => 'Tháng 3',
            4 => 'Tháng 4',
            5 => 'Tháng 5',
            6 => 'Tháng 6',
            7 => 'Tháng 7',
            8 => 'Tháng 8',
            9 => 'Tháng 9',
            10 => 'Tháng 10',
            11 => 'Tháng 11',
            12 => 'Tháng 12'
        ];

        $labels = [];
        foreach ($months as $m) {
            $labels[] = $monthNames[$m];
        }

        return [
            'labels' => $labels,
            'data' => array_values($monthsData)
        ];
    }

    private function getEquipmentByCategoryData($year, $months = null)
    {
        $query = DB::table('purchase_request_items')
            ->join('purchase_requests', 'purchase_request_items.purchase_request_id', '=', 'purchase_requests.id')
            ->join('products', 'purchase_request_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.category_name',
                DB::raw('SUM(purchase_request_items.quantity) as total_quantity')
            )
            ->whereYear('purchase_requests.created_at', $year);

        if ($months) {
            $query->whereIn(DB::raw('MONTH(purchase_requests.created_at)'), $months);
        }

        $data = $query->groupBy('product_categories.id', 'product_categories.category_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('category_name')->toArray(),
            'data' => $data->pluck('total_quantity')->toArray()
        ];
    }

    private function getRequestsByDepartmentData($year, $months = null)
    {
        $query = PurchaseRequest::select(
            'departments.department_name',
            DB::raw('COUNT(*) as total')
        )
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->whereYear('purchase_requests.created_at', $year);

        if ($months) {
            $query->whereIn(DB::raw('MONTH(purchase_requests.created_at)'), $months);
        }

        $data = $query->groupBy('departments.id', 'departments.department_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('department_name')->toArray(),
            'data' => $data->pluck('total')->toArray()
        ];
    }

    public function exportQuarterlyReport(Request $request)
    {
        $period = $request->get('period');
        $departmentId = $request->get('department_id');
        $status = $request->get('status');

        // Build query for purchase orders (matching the index page)
        $query = PurchaseOrder::with(['department', 'purchaseRequest.items.product'])
            ->where('is_delete', false)
            ->whereIn('status', ['COMPLETED', 'CANCELLED']);

        // Apply period filter
        if ($period) {
            $query->whereHas('purchaseRequest', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        // Apply department filter
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('department_id')->orderBy('order_date', 'desc')->get();

        // Filter duplicate orders for the same request
        $orders = $orders->unique('purchase_request_id');

        // Group by department
        $ordersByDepartment = $orders->groupBy('department_id');

        //     $periodName = $period ?: 'Tat_ca';
        //     $filename = "Bao_cao_{$periodName}_" . date('YmdHis') . ".xlsx";

        //     return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\QuarterlyReportExport($ordersByDepartment, $periodName), $filename);
        // }
        // ✅ CHUẨN HÓA TÊN
        $periodKey = $period ?: 'Tat_ca';   // filename
        $periodLabel = $period ?: 'TẤT CẢ';   // hiển thị

        $filename = "Bao_cao_{$periodKey}_" . date('YmdHis') . ".xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QuarterlyReportExport($ordersByDepartment, $periodLabel),
            $filename
        );
    }

    public function exportPDF(Request $request)
    {
        $period = $request->get('period');
        $departmentId = $request->get('department_id');
        $status = $request->get('status');

        // Build query for purchase orders (matching the index page)
        $query = PurchaseOrder::with(['department', 'purchaseRequest.items.product'])
            ->where('is_delete', false)
            ->whereIn('status', ['COMPLETED', 'CANCELLED']);

        // Apply period filter
        if ($period) {
            $query->whereHas('purchaseRequest', function ($q) use ($period) {
                $q->where('period', $period);
            });
        }

        // Apply department filter
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('department_id')->orderBy('order_date', 'desc')->get();

        // Filter duplicate orders for the same request
        $orders = $orders->unique('purchase_request_id');

        // Group by department
        $ordersByDepartment = $orders->groupBy('department_id');

        // Get statistics based on filtered data
        $stats = $this->calculateOrderStatistics($period, $departmentId);

        // Generate PDF
        $pdf = \PDF::loadView('buyer.reports.pdf_report', [
            'requestsByDepartment' => $ordersByDepartment,
            'stats' => $stats,
            'period' => $period,
            'periodName' => $period ?: 'Tất cả'
        ]);

        $periodName = $period ?: 'Tat_ca';
        $filename = "Bao_cao_{$periodName}_" . date('YmdHis') . ".pdf";

        return $pdf->download($filename);
    }


    private function getQuarterlyData($year, $months)
    {
        // Summary statistics
        $requestQuery = PurchaseRequest::whereYear('created_at', $year)
            ->whereIn(DB::raw('MONTH(created_at)'), $months);

        $totalRequests = $requestQuery->count();
        $approvedRequests = (clone $requestQuery)->where('status', 'APPROVED')->count();
        $rejectedRequests = (clone $requestQuery)->where('status', 'REJECTED')->count();
        $pendingRequests = (clone $requestQuery)->where('status', 'PENDING')->count();

        $orderQuery = PurchaseOrder::whereYear('created_at', $year)
            ->whereIn(DB::raw('MONTH(created_at)'), $months)
            ->where('status', '!=', 'CANCELLED');

        $totalBudget = $orderQuery->sum('total_amount');

        // Monthly budget breakdown
        $monthlyBudget = PurchaseOrder::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total_amount'),
            DB::raw('COUNT(*) as order_count')
        )
            ->whereYear('created_at', $year)
            ->whereIn(DB::raw('MONTH(created_at)'), $months)
            ->where('status', '!=', 'CANCELLED')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Budget by department
        $departmentBudget = DB::table('purchase_requests')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->leftJoin('purchase_orders', 'purchase_requests.id', '=', 'purchase_orders.purchase_request_id')
            ->select(
                'departments.department_name',
                DB::raw('COUNT(DISTINCT purchase_requests.id) as request_count'),
                DB::raw('COALESCE(SUM(purchase_orders.total_amount), 0) as total_amount')
            )
            ->whereYear('purchase_requests.created_at', $year)
            ->whereIn(DB::raw('MONTH(purchase_requests.created_at)'), $months)
            ->groupBy('departments.id', 'departments.department_name')
            ->orderByDesc('total_amount')
            ->get();

        // Equipment by category
        $categorySummary = DB::table('purchase_request_items')
            ->join('purchase_requests', 'purchase_request_items.purchase_request_id', '=', 'purchase_requests.id')
            ->join('products', 'purchase_request_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.category_name',
                DB::raw('SUM(purchase_request_items.quantity) as total_quantity'),
                DB::raw('SUM(purchase_request_items.quantity * products.unit_price) as total_amount')
            )
            ->whereYear('purchase_requests.created_at', $year)
            ->whereIn(DB::raw('MONTH(purchase_requests.created_at)'), $months)
            ->groupBy('product_categories.id', 'product_categories.category_name')
            ->orderByDesc('total_amount')
            ->get();

        // Top products
        $topProducts = DB::table('purchase_request_items')
            ->join('purchase_requests', 'purchase_request_items.purchase_request_id', '=', 'purchase_requests.id')
            ->join('products', 'purchase_request_items.product_id', '=', 'products.id')
            ->select(
                'products.product_name',
                'products.product_code',
                'products.unit_price',
                DB::raw('SUM(purchase_request_items.quantity) as total_quantity'),
                DB::raw('SUM(purchase_request_items.quantity * products.unit_price) as total_amount')
            )
            ->whereYear('purchase_requests.created_at', $year)
            ->whereIn(DB::raw('MONTH(purchase_requests.created_at)'), $months)
            ->groupBy('products.id', 'products.product_name', 'products.product_code', 'products.unit_price')
            ->orderByDesc('total_quantity')
            ->limit(20)
            ->get();

        return [
            'summary' => [
                'total_requests' => $totalRequests,
                'approved_requests' => $approvedRequests,
                'rejected_requests' => $rejectedRequests,
                'pending_requests' => $pendingRequests,
                'approval_rate' => $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0,
                'total_budget' => $totalBudget,
            ],
            'monthly_budget' => $monthlyBudget,
            'department_budget' => $departmentBudget,
            'category_summary' => $categorySummary,
            'top_products' => $topProducts,
        ];
    }
}
