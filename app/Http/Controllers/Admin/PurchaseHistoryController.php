<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PurchaseHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseHistoryController extends Controller
{
    /**
     * Display purchase history
     */
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['department', 'requester', 'items.product'])
            ->where('is_delete', false)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID']);

        // Filter by department
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('request_code', 'like', '%' . $request->search . '%')
                    ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        $history = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate statistics
        $totalSpent = PurchaseRequest::where('purchase_requests.is_delete', false)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID'])
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('quantity * expected_price'));

        $totalRequests = PurchaseRequest::where('is_delete', false)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID'])
            ->count();

        $departments = Department::where('is_delete', false)->orderBy('department_name')->get();

        return view('admin.history.index', compact('history', 'totalSpent', 'totalRequests', 'departments'));
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
        $query = PurchaseRequest::with(['department', 'requester', 'items.product'])
            ->where('is_delete', false)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID']);

        // Apply same filters as index
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('request_code', 'like', '%' . $request->search . '%')
                    ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        $history = $query->orderBy('created_at', 'desc')->get();

        $filename = 'lich_su_mua_hang_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new PurchaseHistoryExport($history, $request->all()), 
            $filename
        );
    }
}
