<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\PurchaseFeedback;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Display all feedback for Buyer
     */
    public function index(Request $request)
    {
        // Only fetch ONE feedback per Order to avoid duplicates
        $query = PurchaseFeedback::with(['feedbackBy', 'purchaseOrder'])
            ->where('is_delete', false)
            ->whereNotNull('purchase_order_id')
            ->whereIn('id', function ($q) {
                $q->select(DB::raw('MIN(id)'))
                    ->from('purchase_feedbacks')
                    ->where('is_delete', false)
                    ->whereNotNull('rating')
                    ->whereNotNull('purchase_order_id')
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

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total' => PurchaseFeedback::where('is_delete', false)->whereNotNull('rating')->whereNotNull('purchase_order_id')->distinct('purchase_order_id')->count('purchase_order_id'),
            'pending' => PurchaseFeedback::where('is_delete', false)->whereNotNull('rating')->whereNotNull('purchase_order_id')->where('status', 'PENDING')->distinct('purchase_order_id')->count('purchase_order_id'),
            'resolved' => PurchaseFeedback::where('is_delete', false)->whereNotNull('rating')->whereNotNull('purchase_order_id')->where('status', '!=', 'PENDING')->distinct('purchase_order_id')->count('purchase_order_id'),
        ];

        $departments = Department::where('is_delete', false)->orderBy('department_name')->get();

        return view('buyer.feedback.index', compact('feedbacks', 'stats', 'departments'));
    }

    /**
     * Reply to feedback (Same logic as Admin)
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

            if (!empty($validated['response'])) {
                // We create a NEW feedback record for the response to keep history
                // or update the existing one? Admin logic updates admin_response.
                // Let's stick to updating existing record if it's the target, 
                // but actually the chat UI uses store() in Department\FeedbackController.
                // For Buyer/Admin specific "Reply" form:
                $feedback->update([
                    'admin_response' => $validated['response'],
                    'response_time' => now(),
                ]);
            }

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
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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

    /**
     * Get messages for an order (AJAX) - Duplicate of Department logic but for Buyer
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
                'user_name' => $feedback->feedbackBy ? $feedback->feedbackBy->full_name : 'Khoa phòng',
                'avatar' => $feedback->feedbackBy->avatar ?? null,
                'time' => $feedback->created_at->format('H:i d/m/Y'),
                'is_current_user' => false, // Buyer is viewing
                'rating' => $feedback->rating,
            ];

            // Tin nhắn trả lời của Admin (nếu có)
            if (!empty($feedback->admin_response)) {
                $messages[] = [
                    'id' => $feedback->id . '_admin',
                    'type' => 'admin',
                    'content' => $feedback->admin_response,
                    'user_name' => 'Buyer/Admin',
                    'avatar' => null,
                    'time' => $feedback->response_time ? \Carbon\Carbon::parse($feedback->response_time)->format('H:i d/m/Y') : '',
                    'is_current_user' => true, // Buyer behaves like admin here
                    'rating' => null,
                ];
            }
        }

        // Check if the buyer can reply: 
        // 1. Conversation not resolved
        // 2. Latest message is from Department (admin_response is NULL)
        $latestFeedback = PurchaseFeedback::where('purchase_order_id', $orderId)
            ->where('is_delete', false)
            ->latest()
            ->first();

        $isResolved = $latestFeedback && $latestFeedback->status === 'RESOLVED';
        $canReply = !$isResolved && ($latestFeedback && $latestFeedback->admin_response === null);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'existing_rating' => $existingRating,
            'can_reply' => $canReply,
            'is_resolved' => $isResolved
        ]);
    }

    /**
     * Store a reply message via AJAX (Replier is Buyer/Admin)
     */
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
            'resolve' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Find the latest pending feedback for this order to attach the response to
            $feedback = PurchaseFeedback::where('purchase_order_id', $orderId)
                ->where('is_delete', false)
                ->where('status', 'PENDING')
                ->whereNull('admin_response')
                ->latest()
                ->first();

            // If no pending feedback found, find the latest feedback for this order
            if (!$feedback) {
                $feedback = PurchaseFeedback::where('purchase_order_id', $orderId)
                    ->where('is_delete', false)
                    ->latest()
                    ->first();
            }

            if (!$feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phản hồi nào để trả lời cho đơn hàng này.'
                ], 404);
            }

            // Update with admin response if content is provided
            if ($request->filled('content')) {
                // Check if already replied
                if ($feedback->admin_response !== null && !$request->resolve) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã gửi phản hồi rồi. Vui lòng đợi Khoa phòng trả lời.'
                    ], 400);
                }

                $feedback->update([
                    'admin_response' => $request->input('content'),
                    'response_time' => now(),
                ]);
            }

            // Resolve if requested
            if ($request->resolve) {
                PurchaseFeedback::where('purchase_order_id', $orderId)
                    ->where('is_delete', false)
                    ->update([
                        'status' => 'RESOLVED',
                        'resolved_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi phản hồi thành công!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
