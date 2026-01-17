<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
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

        return view('admin.notifications.index', compact('notifications', 'stats'));
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
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

            return back()->with('success', 'Tạo thông báo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi tạo thông báo!');
        }
    }

    /**
     * Update notification
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'target_role' => 'required|string',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_role' => $request->target_role,
        ]);

        return redirect()->route('admin.notifications')->with('success', 'Cập nhật thông báo thành công!');
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
            $notification->delete();

            return back()->with('success', 'Xóa thông báo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }
}
