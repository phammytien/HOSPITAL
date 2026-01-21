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
        $userId = Auth::id();
        $tab = $request->get('tab', 'received');

        $query = Notification::with(['createdBy', 'attachment']);

        if ($tab === 'sent') {
            $query->where('created_by', $userId);
        } else {
            // Received: role matches AND not created by me
            $query->where(function ($q) {
                $q->whereRaw('UPPER(target_role) IN (?, ?)', ['BUYER', 'ALL'])
                    ->orWhereNull('target_role');
            })->where('created_by', '!=', $userId);
        }

        $type = $request->get('type');
        $isRead = $request->get('is_read');

        // Filter by type
        if ($type != '') {
            $query->whereRaw('TRIM(type) = ?', [$type]);
        }

        // Filter by status (only applies to received)
        if ($tab === 'received' && $isRead !== '' && $isRead !== null) {
            $query->where('is_read', (bool) $isRead);
        }

        $notifications = $query->orderBy('id', 'desc')->paginate(10);

        // Stats for the view
        $receivedQuery = Notification::where(function ($q) {
            $q->whereRaw('UPPER(target_role) IN (?, ?)', ['BUYER', 'ALL'])
                ->orWhereNull('target_role');
        })->where('created_by', '!=', $userId);

        $sentQuery = Notification::where('created_by', $userId);

        $stats = [
            'received_total' => (clone $receivedQuery)->count(),
            'received_unread' => (clone $receivedQuery)->where('is_read', false)->count(),
            'sent_total' => (clone $sentQuery)->count(),
        ];
        $stats['received_read'] = $stats['received_total'] - $stats['received_unread'];

        // Get notification types from database
        $notificationTypes = NotificationHelper::getNotificationTypes();
        $targetRoles = NotificationHelper::getTargetRoles();

        return view('buyer.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'targetRoles', 'tab'));
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
            $notification = Notification::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'target_role' => $validated['target_role'],
                'created_by' => Auth::id(),
            ]);

            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('notifications', $fileName, 'public');

                \App\Models\File::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'storage/' . $path,
                    'file_type' => $file->getClientMimeType(),
                    'related_table' => 'notifications',
                    'related_id' => $notification->id,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now(),
                    'is_delete' => false
                ]);
            }

            return redirect()->route('buyer.notifications.index', ['tab' => 'sent'])->with('success', 'Tạo thông báo thành công!');
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
        $notification = Notification::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        // Get valid types from database
        $validTypes = implode(',', array_keys(NotificationHelper::getNotificationTypes()));

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => "required|in:$validTypes",
            'target_role' => 'required|in:ADMIN,DEPARTMENT,ALL',
        ]);

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_role' => $request->target_role,
        ]);

        // Handle file update
        if ($request->hasFile('attachment')) {
            // Soft delete old file
            \App\Models\File::where('related_table', 'notifications')
                ->where('related_id', $id)
                ->update(['is_delete' => true]);

            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('notifications', $fileName, 'public');

            \App\Models\File::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => 'storage/' . $path,
                'file_type' => $file->getClientMimeType(),
                'related_table' => 'notifications',
                'related_id' => $notification->id,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'is_delete' => false
            ]);
        }

        return redirect()->route('buyer.notifications.index', ['tab' => 'sent'])->with('success', 'Cập nhật thông báo thành công!');
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
        $userId = Auth::id();
        Notification::where(function ($query) {
            $query->whereRaw('UPPER(target_role) IN (?, ?)', ['BUYER', 'ALL'])
                ->orWhereNull('target_role');
        })
            ->where('created_by', '!=', $userId)
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
            $notification = Notification::where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();

            // Soft delete attached file
            \App\Models\File::where('related_table', 'notifications')
                ->where('related_id', $id)
                ->update(['is_delete' => true]);

            $notification->delete();

            return redirect()->route('buyer.notifications.index', ['tab' => 'sent'])->with('success', 'Xóa thông báo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra hoặc bạn không có quyền xóa thông báo này!');
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
