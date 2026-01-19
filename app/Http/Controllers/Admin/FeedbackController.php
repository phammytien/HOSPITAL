<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseFeedback;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display all feedback
     */
    public function index(Request $request)
    {
        $query = PurchaseFeedback::with(['feedbackBy', 'purchaseOrder'])
            ->where('is_delete', false);

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
            $query->whereHas('feedbackBy', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(5);

        $stats = [
            'total' => PurchaseFeedback::where('is_delete', false)->count(),
            'pending' => PurchaseFeedback::where('is_delete', false)->where('status', 'PENDING')->count(),
            'resolved' => PurchaseFeedback::where('is_delete', false)->where('status', '!=', 'PENDING')->count(),
        ];

        $departments = Department::where('is_delete', false)->orderBy('department_name')->get();

        return view('admin.feedback.index', compact('feedbacks', 'stats', 'departments'));
    }

    /**
     * Show detailed feedback
     */
    public function show($id)
    {
        $feedback = PurchaseFeedback::with(['feedbackBy', 'purchaseOrder.items.product'])
            ->where('is_delete', false)
            ->findOrFail($id);

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Reply to feedback
     */
    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'response' => 'required|string'
        ]);

        try {
            $feedback = PurchaseFeedback::where('is_delete', false)->findOrFail($id);
            
            $feedback->update([
                'admin_response' => $validated['response'],
                'response_time' => now(),
            ]);

            return back()->with('success', 'Đã gửi phản hồi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi gửi phản hồi!');
        }
    }

    /**
     * Mark feedback as resolved
     */
    public function resolve($id)
    {
        try {
            $feedback = PurchaseFeedback::where('is_delete', false)->findOrFail($id);
            
            $feedback->update([
                'status' => 'RESOLVED',
                'resolved_at' => now(),
            ]);

            return back()->with('success', 'Đã đánh dấu là đã giải quyết!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }
}
