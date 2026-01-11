<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. Filter Logic ---
        $periodType = $request->input('period_type', 'year'); // Default to year
        $deptId = $request->input('department_id');
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Determine Date Range based on Selected Year
        $baseDate = Carbon::createFromDate($selectedYear, Carbon::now()->month, Carbon::now()->day);
        
        // If viewing current year, stick to "now", otherwise jump to that year
        // But for month/quarter calculation, we need to respect the selected year.
        
        if ($periodType == 'month') {
            // If period is month, it means "This Month" of the Selected Year
            // Or usually "Month" filter implies selecting a specific month? 
            // For simplicity in this layout (which only has Month/Quarter/Year type selector), 
            // we assume it implies "Current Month of Selected Year"
            $startDate = $baseDate->copy()->startOfMonth();
            $endDate = $baseDate->copy()->endOfMonth();
        } elseif ($periodType == 'quarter') {
            $startDate = $baseDate->copy()->startOfQuarter();
            $endDate = $baseDate->copy()->endOfQuarter();
        } elseif ($periodType == 'year') {
            $startDate = $baseDate->copy()->startOfYear();
            $endDate = $baseDate->copy()->endOfYear();
        } else {
            $startDate = $baseDate->copy()->startOfMonth();
            $endDate = $baseDate->copy()->endOfMonth();
        }

        // Calculate Previous Period for Comparison
        $diffInDays = $startDate->diffInDays($endDate) + 1;
        $prevStartDate = $startDate->copy()->subDays($diffInDays);
        $prevEndDate = $endDate->copy()->subDays($diffInDays);

        // --- 2. Advanced Statistics ---
        
        // Total Spending (Current vs Previous) - Based on PAID Purchase Orders
        $totalValue = $this->calculateSpending($startDate, $endDate, $deptId);
        $prevTotalValue = $this->calculateSpending($prevStartDate, $prevEndDate, $deptId);
        $spendingGrowth = $prevTotalValue > 0 ? (($totalValue - $prevTotalValue) / $prevTotalValue) * 100 : 100;

        // Stats Cards Counts (Apply Filters where appropriate, or keep global)
        // Note: Usually "Pending Requests" is a global operational metric, not historical.
        // But "Approved This Month" should probably respect the filter if possible, 
        // or we keep them as "Quick Stats" distinct from the "Analytical Stats".
        // Let's keep specific card logic consistent with their labels.
        
        $pendingRequests = PurchaseRequest::where('status', 'SUBMITTED')
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->count();
            
        $approvedThisPeriod = PurchaseRequest::where('status', 'APPROVED')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->count();
            
        $prevApprovedThisPeriod = PurchaseRequest::where('status', 'APPROVED')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->count();
            
        $approvedGrowth = $prevApprovedThisPeriod > 0 ? (($approvedThisPeriod - $prevApprovedThisPeriod) / $prevApprovedThisPeriod) * 100 : 100;

        $newToday = PurchaseRequest::whereDate('created_at', today())->count();
        $totalProducts = Product::where('is_delete', false)->count();
        $lowStock = Product::where('is_delete', false)->where('stock_quantity', '<', 10)->count();

        // --- 3. Charts Data ---

        // Chart 1: Spending Trend (Line Chart)
        $trendData = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('status', ['PAID', 'COMPLETED', 'DELIVERED'])
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->select(
                DB::raw('DATE(order_date) as date'), 
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Chart 2: Spending by Department (Bar Chart)
        $deptSpending = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('status', ['PAID', 'COMPLETED', 'DELIVERED'])
            ->join('departments', 'purchase_orders.department_id', '=', 'departments.id')
            ->select('departments.department_name', DB::raw('SUM(purchase_orders.total_amount) as total'))
            ->groupBy('departments.department_name')
            ->orderByDesc('total')
            ->get();

        // Chart 3: Spending by Category (Pie Chart)
        $categorySpending = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->whereBetween('purchase_orders.order_date', [$startDate, $endDate])
            ->whereIn('purchase_orders.status', ['PAID', 'COMPLETED', 'DELIVERED'])
            ->when($deptId, fn($q) => $q->where('purchase_orders.department_id', $deptId))
            ->select('product_categories.category_name', DB::raw('SUM(purchase_order_items.amount) as total'))
            ->groupBy('product_categories.category_name')
            ->get();

        // Prepare Chart JSON
        $chartData = [
            'trend' => [
                'labels' => $trendData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray(),
                'data' => $trendData->pluck('total')->toArray()
            ],
            'departments' => [
                'labels' => $deptSpending->pluck('department_name')->toArray(),
                'data' => $deptSpending->pluck('total')->toArray()
            ],
            'categories' => [
                'labels' => $categorySpending->pluck('category_name')->toArray(),
                'data' => $categorySpending->pluck('total')->toArray()
            ]
        ];

        // --- 4. Other Data ---
        $recentRequests = PurchaseRequest::with('department')
            ->where('is_delete', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $departments = Department::where('is_delete', false)->get(); // For dropdown

        // Recent Activities logic remains same (simplified for brevity, can just fetch latest for now)
        $recentActivities = $this->getRecentActivities();

        // Prepare available years for dropdown
        $availableYears = range(2020, 2030);

        return view('dashboard.admin', compact(
            'periodType', 'deptId', 'selectedYear', 'availableYears', // Filter State
            'pendingRequests', 'newToday',
            'approvedThisPeriod', 'approvedGrowth',
            'totalValue', 'spendingGrowth', 'prevTotalValue',
            'totalProducts', 'lowStock',
            'recentRequests', 'departments',
            'chartData', 'recentActivities'
        ));
    }

    private function calculateSpending($start, $end, $deptId = null)
    {
        return PurchaseOrder::whereBetween('order_date', [$start, $end])
            ->whereIn('status', ['PAID', 'COMPLETED', 'DELIVERED'])
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->sum('total_amount');
    }

    private function getRecentActivities()
    {
        $activities = collect();
        
        $latestRequests = PurchaseRequest::with(['department', 'user'])
            ->where('is_delete', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($latestRequests as $request) {
            $activities->push([
                'type' => $request->status == 'APPROVED' ? 'success' : 'info',
                'icon_color' => $request->status == 'APPROVED' ? 'green' : 'blue',
                'message' => $request->status == 'APPROVED' 
                    ? "Đơn hàng <span class=\"text-green-600 font-medium font-bold\">#{$request->request_code}</span> đã được phê duyệt."
                    : ($request->user ? $request->user->full_name : 'Người dùng') . " đã tạo yêu cầu nhập kho mới.",
                'time' => $request->created_at ? $request->created_at->diffForHumans() : 'Vừa xong'
            ]);
        }
        return $activities;
    }

    public function getPurchaseRequestDetails($id)
    {
        $request = PurchaseRequest::with(['department', 'items.product', 'user', 'workflows.actionBy'])
            ->findOrFail($id);
        
        $items = $request->items->map(function($item) {
            return [
                'product_name' => $item->product->product_name ?? 'N/A',
                'product_code' => $item->product->product_code ?? 'N/A', // SKU
                'product_image' => getProductImage($item->product_id),
                'quantity' => $item->quantity,
                'unit_price' => $item->product->unit_price ?? 0,
                'unit' => $item->product->unit ?? 'Cái',
            ];
        });
        
        $totalAmount = $items->sum(function($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        // Format Date Helper
        $formatDate = function($date) {
            return $date ? Carbon::parse($date)->format('d/m/Y H:i') : null;
        };

        $workflows = $request->workflows->map(function($flow) use ($formatDate) {
            return [
                'status' => $flow->to_status,
                'status_text' => $this->getStatusText($flow->to_status),
                'user_name' => $flow->actionBy->full_name ?? 'Hệ thống',
                'action_time' => $formatDate($flow->action_time),
                'note' => $flow->action_note
            ];
        })->sortByDesc('action_time')->values();        
        
        return response()->json([
            'request_code' => $request->request_code,
            'status' => $request->status,
            'department_name' => $request->department->department_name ?? 'N/A',
            'created_at' => $request->created_at ? $request->created_at->format('d/m/Y') : 'N/A',
            'note' => $request->note,
            'items' => $items,
            'total_amount' => $totalAmount,
            'workflows' => $workflows
        ]);
    }

    private function getStatusText($status) {
        $texts = [
            'APPROVED' => 'Đã duyệt',
            'SUBMITTED' => 'Chờ duyệt',
            'REJECTED' => 'Từ chối',
            'COMPLETED' => 'Hoàn thành',
            'PENDING' => 'Chờ xử lý',
            'CANCELLED' => 'Đã hủy',
            'DRAFT' => 'Nháp'
        ];
        return $texts[$status] ?? $status;
    }
}
