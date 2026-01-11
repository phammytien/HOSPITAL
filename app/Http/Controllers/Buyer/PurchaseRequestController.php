<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\PurchaseRequestWorkflow;
use App\Models\Notification; // Added Import

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['department', 'requester', 'purchaseOrder'])
            ->orderBy('created_at', 'desc');

        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('period') && $request->period != '') {
            $query->where('period', $request->period);
        }

        $requests = $query->paginate(10);
        $departments = \App\Models\Department::all();
        // Get unique periods for filter
        $periods = PurchaseRequest::select('period')->distinct()->orderBy('period', 'desc')->pluck('period');

        return view('buyer.requests.index', compact('requests', 'departments', 'periods'));
    }

    public function show($id)
    {
        $request = PurchaseRequest::with(['items.product', 'department', 'requester'])->findOrFail($id);

        // Calculate total amount
        $totalAmount = $request->items->sum(function ($item) {
            return $item->quantity * $item->expected_price;
        });

        return view('buyer.requests.partials.detail_modal', compact('request', 'totalAmount'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:SUBMITTED,APPROVED,REJECTED,PROCESSING,PAID,COMPLETED,CANCELLED',
            'note' => 'nullable|string',
            'rejection_reason' => 'nullable|string|required_if:status,REJECTED'
        ]);

        $purchaseRequest = PurchaseRequest::findOrFail($id);
        $oldStatus = $purchaseRequest->status;
        $newStatus = $request->status;

        // Strict Status Transitions
        $validTransitions = [
            'SUBMITTED' => ['APPROVED', 'REJECTED'],
            'APPROVED' => ['PROCESSING'],
            'PROCESSING' => ['PAID', 'COMPLETED'],
            'REJECTED' => [],
            'PAID' => [],
            'COMPLETED' => [],
            'CANCELLED' => [],
        ];

        // Specific rule: Cannot change from REJECTED to anything
        if ($oldStatus == 'REJECTED') {
            return redirect()->back()->with('error', 'Không thể chuyển đổi trạng thái khi yêu cầu đã bị Từ chối.');
        }

        // Generic Valid Transition Check
        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            return redirect()->back()->with('error', "Không thể chuyển trạng thái từ $oldStatus sang $newStatus. Trạng thái chỉ được phép cập nhật theo quy trình.");
        }

        DB::transaction(function () use ($purchaseRequest, $newStatus, $oldStatus, $request) {
            $purchaseRequest->status = $newStatus;
            $purchaseRequest->note = $request->note ?? $purchaseRequest->note; // Optional: update note
            $purchaseRequest->save();

            // Auto-create Purchase Order if status is PROCESSING
            if ($newStatus == 'PROCESSING' && !$purchaseRequest->purchaseOrder) {
                $totalAmount = $purchaseRequest->items->sum(function ($item) {
                    return $item->quantity * $item->expected_price;
                });

                $orderCode = 'PO_' . now()->format('Ymd') . '_' . $purchaseRequest->id;

                $purchaseOrder = \App\Models\PurchaseOrder::create([
                    'order_code' => $orderCode,
                    'purchase_request_id' => $purchaseRequest->id,
                    'department_id' => $purchaseRequest->department_id,
                    'approved_by' => auth()->id(),
                    'order_date' => now(),
                    // expected_delivery_date will be updated later by Buyer
                    'total_amount' => $totalAmount,
                    'status' => 'CREATED', // Initial status for Order
                    'is_delete' => 0
                ]);

                // Create Order Items
                foreach ($purchaseRequest->items as $item) {
                    \App\Models\PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->expected_price,
                        'is_delete' => 0
                    ]);
                }
            }

            // Log Workflow
            PurchaseRequestWorkflow::create([
                'purchase_request_id' => $purchaseRequest->id,
                'action_by' => auth()->id(),
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'action_note' => $request->note ?? "Chuyển trạng thái thủ công",
                'action_time' => now()
            ]);

            // Save Feedback if Note or Rejection Reason is present
            $feedbackContent = $request->rejection_reason ?? $request->note;
            if ($feedbackContent) {
                \App\Models\PurchaseFeedback::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'feedback_by' => auth()->id(),
                    'feedback_content' => $feedbackContent,
                    'feedback_date' => now(),
                    'is_delete' => 0
                ]);
            }
        });

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function approve($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);

        if ($purchaseRequest->status !== 'SUBMITTED') {
            return redirect()->back()->with('error', 'Only submitted requests can be approved.');
        }

        DB::transaction(function () use ($purchaseRequest) {
            $oldStatus = $purchaseRequest->status;

            // Update Request Status
            $purchaseRequest->status = 'APPROVED';
            $purchaseRequest->save();

            // Log workflow
            PurchaseRequestWorkflow::create([
                'purchase_request_id' => $purchaseRequest->id,
                'action_by' => Auth::id(),
                'from_status' => $oldStatus,
                'to_status' => 'APPROVED',
                'action_note' => 'Đã được bộ phận mua hàng phê duyệt',
                'action_time' => now(),
            ]);

            // Auto-create Purchase Order upon Approval
            if (!$purchaseRequest->purchaseOrder) {
                $totalAmount = $purchaseRequest->items->sum(function ($item) {
                    return $item->quantity * $item->expected_price;
                });

                $orderCode = 'PO_' . now()->format('Ymd') . '_' . $purchaseRequest->id;

                $purchaseOrder = \App\Models\PurchaseOrder::create([
                    'order_code' => $orderCode,
                    'purchase_request_id' => $purchaseRequest->id,
                    'department_id' => $purchaseRequest->department_id,
                    'approved_by' => Auth::id(),
                    'order_date' => now(),
                    'total_amount' => $totalAmount,
                    'status' => 'CREATED',
                    'is_delete' => 0
                ]);

                foreach ($purchaseRequest->items as $item) {
                    \App\Models\PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->expected_price,
                        'is_delete' => 0,
                        'status' => 'PENDING' // Default status
                    ]);
                }
            }

            // Optional: If we ever want to log "Approved" as feedback, we can do it here.
            // For now, since "Quick Approve" has no note, we don't force a feedback entry unless desired.

            // Create Notification
            Notification::create([
                'title' => 'Admin bình luận',
                'message' => "Yêu cầu #{$purchaseRequest->request_code} đã được phê duyệt",
                'type' => 'info',
                'target_role' => 'department', // Target the requester's role or department? For now 'department' seems appropriate as per user context
                'created_by' => Auth::id(),
            ]);

            // Notification for Order Creation
            if (isset($purchaseOrder)) {
                Notification::create([
                    'title' => 'Đơn hàng mới',
                    'message' => "Đơn hàng #{$purchaseOrder->order_code} đã được tạo cho yêu cầu #{$purchaseRequest->request_code}",
                    'type' => 'success',
                    'target_role' => 'department',
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Purchase request approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $purchaseRequest = PurchaseRequest::findOrFail($id);

        if ($purchaseRequest->status !== 'SUBMITTED') {
            return redirect()->back()->with('error', 'Only submitted requests can be rejected.');
        }

        DB::transaction(function () use ($purchaseRequest, $request) {
            $oldStatus = $purchaseRequest->status;

            $purchaseRequest->status = 'REJECTED';
            $purchaseRequest->save();

            PurchaseFeedback::create([
                'purchase_request_id' => $purchaseRequest->id,
                'feedback_by' => Auth::id(),
                'feedback_content' => $request->reason,
                'feedback_date' => now(),
            ]);

            // Log workflow
            PurchaseRequestWorkflow::create([
                'purchase_request_id' => $purchaseRequest->id,
                'action_by' => Auth::id(),
                'from_status' => $oldStatus,
                'to_status' => 'REJECTED',
                'action_note' => $request->reason,
                'action_time' => now(),
            ]);
            // Create Notification
            Notification::create([
                'title' => 'Yêu cầu bị từ chối',
                'message' => "Yêu cầu #{$purchaseRequest->request_code} đã bị từ chối. Lý do: {$request->reason}",
                'type' => 'error', // using 'error' type for rejection (mapped to red icon usually)
                'target_role' => 'department',
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Purchase request rejected.');
    }

    public function compare($id)
    {
        $currentRequest = PurchaseRequest::with(['items.product.category', 'department'])->findOrFail($id);

        // Determine previous quarter
        $parts = explode('_', $currentRequest->period);
        if (count($parts) === 2) {
            $year = intval($parts[0]);
            $quarter = intval(str_replace('Q', '', $parts[1]));

            $prevYear = $quarter == 1 ? $year - 1 : $year;
            $prevQuarter = $quarter == 1 ? 4 : $quarter - 1;
            $prevPeriod = "{$prevYear}_Q{$prevQuarter}";
        } else {
            $prevPeriod = '';
        }

        // 1. Current Request Data by Category
        $currentData = [];
        $currentTotal = 0;
        foreach ($currentRequest->items as $item) {
            $catName = $item->product && $item->product->category ? $item->product->category->category_name : 'Khác';
            if (!isset($currentData[$catName])) {
                $currentData[$catName] = 0;
            }
            $amount = $item->quantity * $item->expected_price;
            $currentData[$catName] += $amount;
            $currentTotal += $amount;
        }

        // 2. Previous Quarter Data by Category (for this Department, Approved)
        $previousRequests = PurchaseRequest::where('department_id', $currentRequest->department_id)
            ->where('period', $prevPeriod)
            ->where('status', 'APPROVED')
            ->with(['items.product.category'])
            ->get();

        $previousData = [];
        $previousTotal = 0;
        foreach ($previousRequests as $req) {
            foreach ($req->items as $item) {
                $catName = $item->product && $item->product->category ? $item->product->category->category_name : 'Khác';
                if (!isset($previousData[$catName])) {
                    $previousData[$catName] = 0;
                }
                $amount = $item->quantity * $item->expected_price;
                $previousData[$catName] += $amount;
                $previousTotal += $amount;
            }
        }

        // 3. Merge Data for Comparison
        $categories = array_unique(array_merge(array_keys($currentData), array_keys($previousData)));
        $comparison = [];
        foreach ($categories as $cat) {
            $comparison[] = [
                'category' => $cat,
                'current_amount' => $currentData[$cat] ?? 0,
                'previous_amount' => $previousData[$cat] ?? 0,
                'diff' => ($currentData[$cat] ?? 0) - ($previousData[$cat] ?? 0)
            ];
        }

        // 4. Flatten Previous Items for Display
        $previousItems = [];
        foreach ($previousRequests as $req) {
            foreach ($req->items as $item) {
                $previousItems[] = [
                    'product_name' => $item->product->product_name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->expected_price, // Using expected_price as proxy for historical price in request
                    'total' => $item->quantity * $item->expected_price,
                    'request_code' => $req->request_code
                ];
            }
        }

        // 5. Get Rejection Reason if applicable
        $rejectionReason = null;
        if ($currentRequest->status == 'REJECTED') {
            $feedback = \App\Models\PurchaseFeedback::where('purchase_request_id', $currentRequest->id)
                ->latest('feedback_date')
                ->first();
            $rejectionReason = $feedback ? $feedback->feedback_content : null;
        }

        // 6. Budget Analysis (ADDED from TH Project)
        $budgetLimit = $currentRequest->department->budget_amount ?? 0;

        // Count all requests for this Department in this Period (excluding REJECTED, including current)
        $periodRequests = PurchaseRequest::where('department_id', $currentRequest->department_id)
            ->where('period', $currentRequest->period)
            ->where('status', '!=', 'REJECTED')
            ->where('is_delete', false) // Assuming functionality similar to TH where is_delete exists or we check deleted_at if SoftDeletes
            ->with(['items'])
            ->get();

        $accumulatedTotal = 0;
        foreach ($periodRequests as $req) {
            foreach ($req->items as $item) {
                $accumulatedTotal += $item->quantity * $item->expected_price;
            }
        }

        $isOverBudget = ($budgetLimit > 0 && $accumulatedTotal > $budgetLimit);
        $budgetUsagePercent = $budgetLimit > 0 ? ($accumulatedTotal / $budgetLimit) * 100 : 0;

        return response()->json([
            'current_period' => $currentRequest->period,
            'current_total' => $currentTotal,
            'previous_period' => $prevPeriod,
            'previous_total' => $previousTotal,
            'department' => $currentRequest->department->department_name,
            'comparison' => $comparison,
            'items' => $currentRequest->items,
            'previous_items' => $previousItems,
            'status' => $currentRequest->status,
            'rejection_reason' => $rejectionReason,
            // Added Budget fields
            'budget_limit' => $budgetLimit,
            'accumulated_total' => $accumulatedTotal,
            'is_over_budget' => $isOverBudget,
            'budget_usage_percent' => $budgetUsagePercent
        ]);
    }
}
