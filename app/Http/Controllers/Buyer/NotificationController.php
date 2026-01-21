<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Helpers\NotificationHelper;
use App\Services\DocumentParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $query = Notification::query();

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by status (only filter if a specific value is selected)
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $notifications = $query->orderBy('id', 'desc')->paginate(5);

        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'read' => Notification::where('is_read', true)->count(),
        ];

        // Get notification types from database
        $notificationTypes = NotificationHelper::getNotificationTypes();
        $targetRoles = NotificationHelper::getTargetRoles();

        return view('buyer.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'targetRoles'));
    }

    /**
     * Show create notification form
     */
    public function create()
    {
        return view('buyer.notifications.create');
    }

    /**
     * Store a new notification
     * Target role is fixed to DEPARTMENT for buyer role
     */
    public function store(Request $request)
    {
        // Get valid types from database
        $validTypes = implode(',', array_keys(NotificationHelper::getNotificationTypes()));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => "required|in:$validTypes",
            'target_role' => 'required|in:ADMIN,DEPARTMENT',
        ]);

        try {
            Notification::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'target_role' => $validated['target_role'],
                'created_by' => Auth::id(),
            ]);

            return back()->with('success', 'Tạo thông báo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi tạo thông báo!');
        }
    }

    /**
     * Update notification
     * Target role remains fixed to DEPARTMENT
     */
    public function update(Request $request, $id)
    {
        // Get valid types from database
        $validTypes = implode(',', array_keys(NotificationHelper::getNotificationTypes()));

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => "required|in:$validTypes",
            'target_role' => 'required|in:ADMIN,DEPARTMENT',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_role' => $request->target_role,
        ]);

        return redirect()->route('buyer.notifications.index')->with('success', 'Cập nhật thông báo thành công!');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->update(['is_read' => true]);

            return back()->with('success', 'Đã đánh dấu là đã đọc!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where(function ($query) {
            $query->where('target_role', 'BUYER')
                ->orWhere('target_role', 'ALL');
        })
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả đã đọc'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->delete();

            return back()->with('success', 'Xóa thông báo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Upload document and extract notification data
     */
    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            ]);

            $file = $request->file('document');
            $parser = new DocumentParserService();

            $data = $parser->extractNotificationData($file);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'File đã được phân tích thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể phân tích file: ' . $e->getMessage()
            ], 400);
        }
    }
}
