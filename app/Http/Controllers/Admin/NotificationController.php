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
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'read' => Notification::where('is_read', true)->count(),
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

            // Helpers for message construction
            $types = [
                'info' => 'Thông tin',
                'success' => 'Thành công',
                'warning' => 'Cảnh báo',
                'error' => 'Khẩn cấp'
            ];
            $roles = [
                'ALL' => 'tất cả người dùng',
                'ADMIN' => 'quản trị viên',
                'BUYER' => 'nhân viên mua hàng',
                'DEPARTMENT' => 'khoa/phòng',
                null => 'tất cả người dùng'
            ];

            $typeLabel = $types[$validated['type']] ?? 'Thông tin';
            $roleLabel = $roles[$validated['target_role'] ?? 'ALL'] ?? 'người dùng';
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

        // Helpers for message construction
        $types = [
            'info' => 'Thông tin',
            'success' => 'Thành công',
            'warning' => 'Cảnh báo',
            'error' => 'Khẩn cấp'
        ];
        $roles = [
            'ALL' => 'tất cả người dùng',
            'ADMIN' => 'quản trị viên',
            'BUYER' => 'nhân viên mua hàng',
            'DEPARTMENT' => 'khoa/phòng'
        ];

        $typeLabel = $types[$request->type] ?? 'Thông tin';
        $roleLabel = $roles[$request->target_role] ?? 'người dùng';
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

            $types = [
                'info' => 'Thông tin',
                'success' => 'Thành công',
                'warning' => 'Cảnh báo',
                'error' => 'Khẩn cấp'
            ];
            $roles = [
                'ALL' => 'tất cả người dùng',
                'ADMIN' => 'quản trị viên',
                'BUYER' => 'nhân viên mua hàng',
                'DEPARTMENT' => 'khoa/phòng',
                null => 'tất cả người dùng'
            ];

            $roleLabel = $roles[$role] ?? 'người dùng';

            $msg = "Đã xóa thông tin gửi đến $roleLabel với tiêu đề \"$title\"";

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra!');
        }
    }
}
