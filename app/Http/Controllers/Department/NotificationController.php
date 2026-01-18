<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for Department (read-only)
     */
    public function index(Request $request)
    {
        $userRole = Auth::user()->role;

        $query = Notification::where(function ($q) use ($userRole) {
            $q->where('target_role', $userRole)
                ->orWhere('target_role', 'ALL')
                ->orWhereNull('target_role');
        });

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $notifications = $query->orderBy('id', 'desc')->paginate(10);

        $stats = [
            'total' => Notification::where(function ($q) use ($userRole) {
                $q->where('target_role', $userRole)
                    ->orWhere('target_role', 'ALL')
                    ->orWhereNull('target_role');
            })->count(),
            'unread' => Notification::where(function ($q) use ($userRole) {
                $q->where('target_role', $userRole)
                    ->orWhere('target_role', 'ALL')
                    ->orWhereNull('target_role');
            })->where('is_read', false)->count(),
        ];

        return view('department.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->update(['is_read' => true]);

            return response()->json(['success' => true, 'message' => 'Đã đánh dấu đã đọc']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $userRole = Auth::user()->role;

            Notification::where(function ($q) use ($userRole) {
                $q->where('target_role', $userRole)
                    ->orWhere('target_role', 'ALL')
                    ->orWhereNull('target_role');
            })->where('is_read', false)->update(['is_read' => true]);

            return response()->json(['success' => true, 'message' => 'Đã đánh dấu tất cả đã đọc']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }
}
