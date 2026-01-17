<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryController extends Controller
{
    /**
     * Display purchase history
     */
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['department', 'requester', 'items.product'])
            ->where('purchase_requests.is_delete', false)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID']);

        // Apply filters (department, searching, etc.)
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('month_from')) {
            $query->whereDate('created_at', '>=', $request->month_from . '-01');
        }

        if ($request->filled('month_to')) {
            $dateTo = \Carbon\Carbon::parse($request->month_to)->endOfMonth();
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('request_code', 'like', '%' . $request->search . '%')
                    ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        // Clone query for stats BEFORE pagination
        $statsQuery = clone $query;

        $totalSpent = $statsQuery->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        $totalRequests = (clone $query)->count();

        // Calculate Pending separately (filtered by same criteria but different status)
        $pendingQuery = PurchaseRequest::where('purchase_requests.is_delete', false)
            ->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW']);

        if ($request->filled('department_id')) {
            $pendingQuery->where('department_id', $request->department_id);
        }
        if ($request->filled('month_from')) {
            $pendingQuery->whereDate('created_at', '>=', $request->month_from . '-01');
        }
        if ($request->filled('month_to')) {
            $dateTo = \Carbon\Carbon::parse($request->month_to)->endOfMonth();
            $pendingQuery->whereDate('created_at', '<=', $dateTo);
        }

        $pendingCount = $pendingQuery->count();

        $history = $query->orderByRaw('YEAR(created_at) DESC')
            ->orderByRaw('QUARTER(created_at) DESC')
            ->orderBy('department_id', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        $departments = Department::where('is_delete', false)->orderBy('department_name')->get();

        return view('admin.history.index', compact('history', 'totalSpent', 'totalRequests', 'pendingCount', 'departments'));
    }



    /**
     * Show request detail
     */
    public function show($id)
    {
        $request = PurchaseRequest::with([
            'items.product.category',
            'department',
            'requester',
            'workflows.actionBy'
        ])
            ->where('is_delete', false)
            ->findOrFail($id);

        $totalAmount = $request->items->sum(function ($item) {
            return $item->quantity * $item->expected_price;
        });

        return view('admin.history.show', compact('request', 'totalAmount'));
    }

    /**
     * Export history to Excel
     */
    public function export(Request $request)
    {
        $filename = 'Lich_su_mua_hang_' . date('Y_m_d_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PurchaseHistoryExport(
                $request->department_id,
                $request->search,
                $request->month_from,
                $request->month_to
            ),
            $filename
        );
    }
}
