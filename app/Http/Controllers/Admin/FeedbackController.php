<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseFeedback;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Display all feedback
     */
    public function index(Request $request)
    {
        // Only fetch ONE feedback per Order (the first one with unique ID per group) to avoid duplicates
        // We use a subquery to find the distinct IDs
        $query = PurchaseFeedback::with(['feedbackBy', 'purchaseOrder'])
            ->where('is_delete', false)
            ->whereIn('id', function ($q) {
                $q->select(DB::raw('MIN(id)'))
                    ->from('purchase_feedbacks')
                    ->where('is_delete', false)
                    ->whereNotNull('rating') // Ensure we target the "Rated" threads
                    ->groupBy('purchase_order_id');
            });

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'RESOLVED') {
                $query->where('status', '!=', 'PENDING');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id != '') {
            $query->whereHas('feedbackBy', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(5);

        // Stats based on unique Purchase Orders with Feedback
        $stats = [
            'total' => PurchaseFeedback::where('is_delete', false)->whereNotNull('rating')->distinct('purchase_order_id')->count('purchase_order_id'),
            'pending' => PurchaseFeedback::where('is_delete', false)->whereNotNull('rating')->where('status', 'PENDING')->distinct('purchase_order_id')->count('purchase_order_id'),
            'resolved' => PurchaseFeedback::where('is_delete', false)->whereNotNull('rating')->where('status', '!=', 'PENDING')->distinct('purchase_order_id')->count('purchase_order_id'),
        ];

        $departments = Department::where('is_delete', false)->orderBy('department_name')->get();

        return view('admin.feedback.index', compact('feedbacks', 'stats', 'departments'));
    }

    /**
     * Show detailed feedback
     */
    public function show($id)
    {
        $currentFeedback = PurchaseFeedback::with(['feedbackBy', 'purchaseOrder.items.product'])
            ->where('is_delete', false)
            ->findOrFail($id);

        // Load conversation history for this order
        $feedbacks = PurchaseFeedback::with(['feedbackBy'])
            ->where('purchase_order_id', $currentFeedback->purchase_order_id)
            ->where('is_delete', false)
            ->orderBy('created_at', 'asc')
            ->get();

        // Identify the feedback to reply to:
        // Always show the reply section if the whole thread is not resolved, 
        // using the latest message as the target.
        $isResolved = $feedbacks->where('status', 'RESOLVED')->count() > 0;
        $replyTarget = !$isResolved ? $feedbacks->last() : null;

        return view('admin.feedback.show', compact('currentFeedback', 'feedbacks', 'replyTarget', 'isResolved'));
    }

    /**
     * Reply to feedback
     */
    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'response' => 'nullable|string',
            'resolve' => 'nullable|boolean'
        ]);

        try {
            $feedback = PurchaseFeedback::where('is_delete', false)->findOrFail($id);
            $orderId = $feedback->purchase_order_id;

            DB::beginTransaction();

            // 1. If there's a response, update the feedback record
            if (!empty($validated['response'])) {
                $feedback->update([
                    'admin_response' => $validated['response'],
                    'response_time' => now(),
                ]);
            }

            // 2. If 'resolve' is checked, mark ALL feedbacks for this order as RESOLVED
            if (!empty($validated['resolve'])) {
                PurchaseFeedback::where('purchase_order_id', $orderId)
                    ->where('is_delete', false)
                    ->update([
                        'status' => 'RESOLVED',
                        'resolved_at' => now(),
                    ]);
            }

            DB::commit();

            $msg = !empty($validated['resolve']) ? 'Đã phản hồi và giải quyết thành công!' : 'Đã gửi phản hồi thành công!';
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' + $e->getMessage());
        }
    }

    /**
     * Mark feedback as resolved
     */
    public function resolve($id)
    {
        try {
            $feedback = PurchaseFeedback::where('is_delete', false)->findOrFail($id);
            $orderId = $feedback->purchase_order_id;

            // Sync status: Mark ALL feedbacks for this order as RESOLVED
            PurchaseFeedback::where('purchase_order_id', $orderId)
                ->where('is_delete', false)
                ->update([
                    'status' => 'RESOLVED',
                    'resolved_at' => now(),
                ]);

            return back()->with('success', 'Đã đánh dấu là đã giải quyết!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }
}
