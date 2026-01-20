<?php

namespace App\Http\Controllers\Admin;

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

        // Refined visibility permissions for Admin:
        // 1. Notifications targeted to ADMIN
        // 2. Notifications targeted to ALL (null or 'ALL')
        // 3. Notifications created by the current user (Admin sent them)
        $query->where(function($q) {
            $q->where('target_role', 'ADMIN')
              ->orWhere('target_role', 'ALL')
              ->orWhereNull('target_role')
              ->orWhere('created_by', Auth::id());
        });

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('is_read') && $request->is_read !== '') {
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
            'total' => Notification::where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role')
                  ->orWhere('created_by', Auth::id());
            })->count(),
            'unread' => Notification::where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role')
                  ->orWhere('created_by', Auth::id());
            })->where('is_read', false)->count(),
            'read' => Notification::where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role')
                  ->orWhere('created_by', Auth::id());
            })->where('is_read', true)->count(),
        ];

        // Get notification types from database
        $notificationTypes = NotificationHelper::getNotificationTypes();
        $targetRoles = NotificationHelper::getTargetRoles();

        return view('admin.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'targetRoles'));
    }

    /**
     * Show create notification form
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a new notification
     */
    public function store(Request $request)
    {
        // Get valid types from database
        $validTypes = implode(',', array_keys(NotificationHelper::getNotificationTypes()));
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => "required|in:$validTypes",
            'target_role' => 'nullable|string',
        ]);

        try {
            Notification::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'target_role' => $validated['target_role'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Get labels from helper
            $typeLabel = NotificationHelper::getTypeLabel($validated['type']);
            $roleLabel = NotificationHelper::getRoleLabel($validated['target_role'] ?? 'ALL');
            $title = $validated['title'];

            $msg = "Đã tạo thông tin gửi đến $roleLabel với tiêu đề \"$title\"";

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi gửi thông báo!');
        }
    }

    /**
     * Update notification
     */
    public function update(Request $request, $id)
    {
        // Get valid types from database
        $validTypes = implode(',', array_keys(NotificationHelper::getNotificationTypes()));
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => "required|in:$validTypes",
            'target_role' => 'required|string',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_role' => $request->target_role,
        ]);

        // Get labels from helper
        $typeLabel = NotificationHelper::getTypeLabel($request->type);
        $roleLabel = NotificationHelper::getRoleLabel($request->target_role);
        $title = $request->title;

        $msg = "Đã cập nhật thông tin gửi đến $roleLabel với tiêu đề \"$title\"";

        return redirect()->route('admin.notifications')->with('success', $msg);
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
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            // Capture data before delete for message
            $title = $notification->title;
            $type = $notification->type;
            $role = $notification->target_role;

            $notification->delete();

            // Get label from helper
            $roleLabel = NotificationHelper::getRoleLabel($role);

            $msg = "Đã xóa thông tin gửi đến $roleLabel với tiêu đề \"$title\"";

            return back()->with('success', $msg);
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
