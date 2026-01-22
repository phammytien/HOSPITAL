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
        $tab = $request->get('tab', 'received');
        $query = Notification::query();

        if ($tab === 'received') {
            // Notifications received by admin
            $query->where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role');
            })->where('created_by', '!=', Auth::id());
        } else {
            // Notifications sent by admin
            $query->where('created_by', Auth::id());
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by status (only for received)
        if ($tab === 'received' && $request->has('is_read') && $request->is_read !== '') {
            $query->where('is_read', $request->is_read);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'received_total' => Notification::where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role');
            })->where('created_by', '!=', Auth::id())->count(),
            
            'received_unread' => Notification::where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role');
            })->where('created_by', '!=', Auth::id())->where('is_read', false)->count(),
            
            'received_read' => Notification::where(function($q) {
                $q->where('target_role', 'ADMIN')
                  ->orWhere('target_role', 'ALL')
                  ->orWhereNull('target_role');
            })->where('created_by', '!=', Auth::id())->where('is_read', true)->count(),
            
            'sent_total' => Notification::where('created_by', Auth::id())->count(),
        ];

        // Get notification types from database
        $notificationTypes = NotificationHelper::getNotificationTypes();

        return view('admin.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'tab'));
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
            'attachment' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        try {
            $notification = Notification::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'target_role' => $validated['target_role'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Handle file attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('notifications', $fileName, 'public');
                
                $notification->attachment()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'storage/' . $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'related_table' => 'notifications',
                    'related_id' => $notification->id,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now(),
                    'is_delete' => false,
                ]);
            }

            // Get labels from helper
            $typeLabel = NotificationHelper::getTypeLabel($validated['type']);
            $roleLabel = NotificationHelper::getRoleLabel($validated['target_role'] ?? 'ALL');
            $title = $validated['title'];

            $msg = "Đã tạo thông báo gửi đến $roleLabel với tiêu đề \"$title\"";

            return redirect()->route('admin.notifications', ['tab' => 'sent'])->with('success', $msg);
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
            'attachment' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_role' => $request->target_role,
        ]);

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($notification->attachment) {
                \Storage::disk('public')->delete(str_replace('storage/', '', $notification->attachment->file_path));
                $notification->attachment->delete();
            }
            
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('notifications', $fileName, 'public');
            
            $notification->attachment()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => 'storage/' . $filePath,
                'file_type' => $file->getClientOriginalExtension(),
                'related_table' => 'notifications',
                'related_id' => $notification->id,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'is_delete' => false,
            ]);
        }

        // Get labels from helper
        $typeLabel = NotificationHelper::getTypeLabel($request->type);
        $roleLabel = NotificationHelper::getRoleLabel($request->target_role);
        $title = $request->title;

        $msg = "Đã cập nhật thông báo gửi đến $roleLabel với tiêu đề \"$title\"";

        return redirect()->route('admin.notifications', ['tab' => 'sent'])->with('success', $msg);
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
