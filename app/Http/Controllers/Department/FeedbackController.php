<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\PurchaseFeedback;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Get messages for an order (AJAX)
     */
    public function getMessages($orderId)
    {
        $feedbacks = PurchaseFeedback::with(['feedbackBy'])
            ->where('purchase_order_id', $orderId)
            ->where('is_delete', false)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get existing rating from the first feedback if any
        $existingRating = $feedbacks->whereNotNull('rating')->first()->rating ?? null;

        $messages = [];

        foreach ($feedbacks as $feedback) {
            // Tin nhắn của Department (User)
            $messages[] = [
                'id' => $feedback->id,
                'type' => 'user',
                'content' => $feedback->feedback_content,
                'user_name' => $feedback->feedbackBy->full_name ?? 'Unknown',
                'avatar' => $feedback->feedbackBy->avatar ?? null,
                'time' => $feedback->created_at->format('H:i d/m/Y'),
                'is_current_user' => $feedback->feedback_by == Auth::id(),
                'rating' => $feedback->rating,
            ];

            // Tin nhắn trả lời của Admin (nếu có)
            if (!empty($feedback->admin_response)) {
                $messages[] = [
                    'id' => $feedback->id . '_admin',
                    'type' => 'admin',
                    'content' => $feedback->admin_response,
                    'user_name' => 'Admin',
                    'avatar' => null,
                    'time' => $feedback->response_time ? \Carbon\Carbon::parse($feedback->response_time)->format('H:i d/m/Y') : '',
                    'is_current_user' => false,
                    'rating' => null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'existing_rating' => $existingRating
        ]);
    }

    /**
     * Store new feedback message
     */
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
            'action' => 'nullable|string|in:good,issue'
        ]);

        $order = PurchaseOrder::where('department_id', Auth::user()->department_id)
            ->findOrFail($orderId);

        $isResolved = PurchaseFeedback::where('purchase_order_id', $orderId)
            ->where('status', 'RESOLVED')
            ->exists();

        // If trying to add new feedback to a resolved thread via the dashboard/list without checking resolved status
        // (Though UI should prevent this, safety check)
        if ($isResolved && !$request->action) { // Relax check for existing threads if just replying? 
            // Actually, if it's resolved, standard rule is no more replies unless reopened.
            // But let's stick to the current logic: if resolved, return 403.
        }

        if ($isResolved) {
            return response()->json([
                'success' => false,
                'message' => 'Phản hồi đã được giải quyết, không thể gửi thêm tin nhắn'
            ], 403);
        }

        // Check for existing rating to persist it
        $existingRating = PurchaseFeedback::where('purchase_order_id', $orderId)
            ->whereNotNull('rating')
            ->orderBy('created_at', 'asc')
            ->value('rating');

        $feedback = new PurchaseFeedback();
        $feedback->purchase_order_id = $orderId;
        $feedback->feedback_by = Auth::id();
        $feedback->feedback_content = $request->content;

        // Use existing rating if available, otherwise use provided rating
        $feedback->rating = $existingRating ?? $request->rating;

        // Set status based on action
        if ($request->action == 'good') {
            $feedback->status = 'RESOLVED';
        } else {
            $feedback->status = 'PENDING';
        }

        if ($feedback->status == 'RESOLVED') {
            $feedback->resolved_at = now();
        }

        $feedback->save();

        // If 'good' action, sync resolving status to all entries in this order
        if ($request->action == 'good') {
            PurchaseFeedback::where('purchase_order_id', $orderId)
                ->update(['status' => 'RESOLVED', 'resolved_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi phản hồi',
        ]);
    }
}
