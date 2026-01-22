@extends('layouts.department')

@section('title', 'Thông báo')
@section('header_title', 'Thông báo')

@section('content')
    @php
        $user = Auth::user();
        $dept = $user->department;
        $deptSlug = $dept ? $dept->slug : '';

        // Function to check if notification belongs to this department
        $belongsToDept = function ($n) use ($deptSlug) {
            if (preg_match('/#(PO|REQ)_[0-9]{4}_Q[1-4]_([A-Z0-9_]+)_[0-9]+/', $n->message, $matches)) {
                $codeDept = $matches[2];
                return $codeDept === $deptSlug;
            }
            return true; // General messages shown to all
        };

        // Recalculate stats for the current department
        // We need the base query for all notifications for this role
        $allDeptNotifications = \App\Models\Notification::where(function ($q) {
            $q->where('target_role', 'DEPARTMENT')
                ->orWhere('target_role', 'ALL')
                ->orWhereNull('target_role');
        })->get()->filter($belongsToDept);

        $totalFiltered = $allDeptNotifications->count();
        $filteredUnread = $allDeptNotifications->where('is_read', false)->count();

        $typeCounts = [
            'info' => $allDeptNotifications->where('type', 'info')->count(),
            'warning' => $allDeptNotifications->where('type', 'warning')->count(),
            'important' => $allDeptNotifications->where('type', 'important')->count(),
        ];
    @endphp
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Thông báo Hệ thống</h1>
                <p class="text-gray-600 mt-1">Xem các thông báo, cảnh báo và sự kiện quan trọng</p>
            </div>
            <button onclick="markAllAsRead()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-check-double"></i>
                <span>Đánh dấu tất cả đã đọc ({{ $filteredUnread }})</span>
            </button>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Notifications --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tổng thông báo</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Unread --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Chưa đọc</p>
                        <h3 class="text-3xl font-bold text-red-600">{{ number_format($stats['unread']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-red-600 text-xl"></i>
                    </div>
                </div>
                @if($stats['unread'] > 0)
                    <p class="text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span>Có thông báo mới</span>
                    </p>
                @endif
            </div>

            {{-- Read --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Đã đọc</p>
                        <h3 class="text-3xl font-bold text-green-600">
                            {{ number_format($stats['total'] - $stats['unread']) }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Tabs (Commented out as redundant)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <a href="{{ route('department.notifications.index') }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ !request('type') ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-list mr-2"></i>
                    Tất cả
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $stats['total'] }}</span>
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'info']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'info' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-info-circle mr-2"></i>
                    Thông tin
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'warning']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'warning' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Cảnh báo
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'success']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'success' ? 'bg-green-50 text-green-600 border-b-2 border-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-check-circle mr-2"></i>
                    Thành công
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'important']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'important' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-star mr-2"></i>
                    Quan trọng
                </a>
            </div>
        </div>
        --}}

        {{-- Filters --}}
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <form method="GET" action="{{ route('department.notifications.index') }}" id="filterForm"
                class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Tìm theo tiêu đề, nội dung..."
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    {{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }} of
                    {{ $notifications->total() }}
                </div>
            </form>
        </div>

        {{-- Status Tabs --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <a href="{{ route('department.notifications.index') }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ !request('type') ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-list mr-2"></i>
                    Tất cả
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $totalFiltered }}</span>
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'info']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'info' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-info-circle mr-2"></i>
                    Thông tin
                    <span
                        class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $typeCounts['info'] }}</span>
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'warning']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'warning' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Cảnh báo
                    <span
                        class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $typeCounts['warning'] }}</span>
                </a>
                <a href="{{ route('department.notifications.index', ['type' => 'important']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'important' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-star mr-2"></i>
                    Quan trọng
                    <span
                        class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $typeCounts['important'] }}</span>
                </a>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Pagination at top --}}


            <div class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    @php
                        if (!$belongsToDept($notification))
                            continue;
                    @endphp
                    @php
                        $icons = [
                            'error' => ['icon' => 'exclamation-circle', 'color' => 'red', 'bg' => 'red-50', 'badge' => 'KHẨN CẤP'],
                            'warning' => ['icon' => 'exclamation-triangle', 'color' => 'orange', 'bg' => 'orange-50', 'badge' => 'CẢNH BÁO'],
                            'info' => ['icon' => 'info-circle', 'color' => 'blue', 'bg' => 'blue-50', 'badge' => 'THÔNG TIN'],
                            'important' => ['icon' => 'star', 'color' => 'purple', 'bg' => 'purple-50', 'badge' => 'QUAN TRỌNG'],
                        ];
                        $config = $icons[$notification->type] ?? $icons['info'];
                    @endphp
                    @php
                        $isUrgent = ($notification->title == 'Vật tư đã giao');
                        $orderId = null;
                        $order = null;
                        // Pre-process message to add links to PO codes
                        // This searches for #PO... patterns. To link them correctly, we need to find the order ID.
                        // We can use a callback to query the ID for each match.
                        $processedMessage = preg_replace_callback('/#(PO_[A-Z0-9_]+)/', function ($matches) {
                            $code = $matches[1];
                            $ord = \App\Models\PurchaseOrder::where('order_code', $code)->first();
                            if ($ord) {
                                $url = route('department.dept_orders.show', $ord->id);
                                return '<a href="' . $url . '" class="text-blue-600 hover:underline font-bold">#' . $code . '</a>';
                            }
                            return '#' . $code;
                        }, $notification->message);

                        // Check for Urgent/Main Order Logic (existing)
                        if ($isUrgent && preg_match('/#(PO_[A-Z0-9_]+)/', $notification->message, $matches)) {
                            $orderCode = $matches[1];
                            $order = \App\Models\PurchaseOrder::where('order_code', $orderCode)->first();
                            if ($order)
                                $orderId = $order->id;
                        }

                        $modalRedirectUrl = '';
                        if ($isUrgent && $orderId && $order) {
                            if ($order->status == 'COMPLETED') {
                                $modalRedirectUrl = '#completed';
                            } elseif ($order->status == 'DELIVERED') {
                                $modalRedirectUrl = route('department.dept_orders.show', $orderId);
                            }
                        } elseif ($orderId) {
                            $modalRedirectUrl = route('department.dept_orders.show', $orderId);
                        }
                    @endphp
                    <div class="p-6 transition border-l-4 {{ $isUrgent ? 'border-blue-600 bg-blue-50/50 shadow-inner' : 'border-' . $config['color'] . '-500 hover:bg-gray-50' }} cursor-pointer {{ !$notification->is_read && !$isUrgent ? 'bg-blue-50' : '' }}"
                        data-notification-id="{{ $notification->id }}"
                        data-notification-title="{{ htmlspecialchars($notification->title, ENT_QUOTES) }}"
                        data-notification-message="{{ $processedMessage }}" data-notification-badge="{{ $config['badge'] }}"
                        data-notification-color="{{ $config['color'] }}"
                        data-notification-read="{{ $notification->is_read ? 'true' : 'false' }}"
                        data-notification-attachment="{{ $notification->attachment ? asset($notification->attachment->file_path) : '' }}"
                        data-notification-sender="{{ $notification->createdBy->full_name ?? 'Hệ thống' }}"
                        data-notification-recipient="{{ \App\Helpers\NotificationHelper::getRoleLabel($notification->target_role) }}"
                        data-notification-time="{{ $notification->created_at->format('d/m/Y') }}"
                        data-notification-url="{{ $modalRedirectUrl }}" onclick="openNotificationModalFromData(this)">

                        <!-- <div class="p-6 hover:bg-gray-50 transition border-l-4 border-{{ $config['color'] }}-500 cursor-pointer {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                                                                                        onclick="openNotificationModal({{ $notification->id }}, '{{ addslashes($notification->title) }}', '{{ addslashes($notification->message) }}', '{{ $config['badge'] }}', '{{ $config['color'] }}', {{ $notification->is_read ? 'true' : 'false' }})"> -->

                        <div class="flex items-start gap-4">
                            {{-- Icon with blue dot --}}
                            <div class="relative flex-shrink-0">
                                <div class="w-12 h-12 bg-{{ $config['bg'] }} rounded-lg flex items-center justify-center">
                                    <i class="fas fa-{{ $config['icon'] }} text-{{ $config['color'] }}-600 text-xl"></i>
                                </div>
                                {{-- Blue dot for unread --}}
                                @if(!$notification->is_read)
                                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full ring-2 ring-white"></div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <h3 class="{{ !$notification->is_read ? 'font-bold' : 'font-semibold' }} text-gray-900">
                                        {{ $notification->title }}
                                    </h3>
                                    <span
                                        class="px-3 py-1 bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                        {{ $config['badge'] }}
                                    </span>
                                </div>
                                {{-- Truncated message --}}
                                <p
                                    class="text-gray-600 text-sm mb-3 line-clamp-2 {{ !$notification->is_read ? 'font-medium' : '' }}">
                                    {{ $notification->message }}
                                </p>

                                @if($isUrgent && $orderId && $order && $order->status == 'DELIVERED')
                                    <div class="mt-2">
                                        <a href="{{ route('department.dept_orders.show', $orderId) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-xs font-black rounded-lg hover:bg-blue-700 transition shadow-md uppercase tracking-wider">
                                            <i class="fas fa-truck-fast"></i>
                                            Xác nhận đã nhận hàng ngay
                                        </a>
                                    </div>
                                @elseif($isUrgent && $orderId && $order && $order->status == 'COMPLETED')
                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded border border-green-200 uppercase whitespace-nowrap">
                                            <i class="fas fa-check-circle"></i> THÀNH CÔNG
                                        </span>
                                    </div>
                                @endif

                                <div class="mt-2 text-[11px] text-gray-500 font-medium flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span>
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'N/A' }}
                                        </span>
                                        @if($notification->attachment)
                                            <span class="flex items-center gap-1 text-red-600 font-bold">
                                                <i class="fas fa-file-pdf"></i>
                                                Đính kèm PDF
                                            </span>
                                        @endif
                                    </div>
                                    @if($notification->attachment)
                                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                        <p>Không có thông báo nào</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination at bottom --}}
            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                    <div class="text-sm text-gray-600">
                        Hiển thị {{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }} trong tổng
                        {{ $notifications->total() }} thông báo
                    </div>
                    <div>
                        {{ $notifications->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Notification Detail Modal (Upgraded) --}}
        <div id="notificationModal" class="fixed inset-0 z-[10060] hidden overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-md"
                    onclick="closeNotificationModal()" aria-hidden="true"></div>

                <div
                    class="relative inline-block w-full max-w-7xl my-4 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-white/20">
                    {{-- Header Bar --}}
                    <div
                        class="absolute top-0 left-0 right-0 h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 z-20">
                        <div class="flex items-center gap-4">
                            <div id="modalIconContainer"
                                class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <div id="modalBadge"
                                    class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold tracking-widest uppercase mb-0.5 bg-blue-100 text-blue-700">
                                </div>
                                <h2 id="modalTitle" class="text-xl font-bold text-gray-900 truncate max-w-md lg:max-w-2xl">
                                </h2>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a id="modalDownloadLink" href="#" target="_blank"
                                class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 hover:text-blue-600 transition-all border border-gray-200 shadow-sm"
                                title="Mở trong cửa sổ mới">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <div class="w-px h-6 bg-gray-200 mx-1"></div>
                            <button onclick="closeNotificationModal()"
                                class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 transition-all shadow-lg shadow-gray-200">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row h-[90vh] pt-20">
                        {{-- Left Side: Content --}}
                        <div id="contentPane"
                            class="w-full lg:w-[35%] border-r border-gray-100 bg-white overflow-y-auto custom-scrollbar transition-all duration-300">
                            <div class="p-8">
                                {{-- Metadata Section --}}
                                <div class="space-y-4 mb-8">
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50/50 rounded-2xl border border-gray-100/50 shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-blue-500">
                                                <i class="far fa-user text-lg"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-0.5">
                                                    Người gửi</p>
                                                <p id="modalSender" class="font-bold text-gray-800"></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-0.5">
                                                Thời gian</p>
                                            <p id="modalTime" class="font-bold text-gray-800"></p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center gap-3 p-4 bg-gray-50/50 rounded-2xl border border-gray-100/50 shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-orange-500">
                                            <i class="far fa-building text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-0.5">
                                                Đối tượng nhận</p>
                                            <p id="modalRecipient" class="font-bold text-gray-800"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Message Content --}}
                                <div class="relative">
                                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-blue-500 rounded-full opacity-20">
                                    </div>
                                    <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-4">Nội dung chi
                                        tiết</h4>
                                    <div class="prose prose-sm prose-blue max-w-none">
                                        <div id="modalMessage"
                                            class="text-gray-600 leading-relaxed text-base whitespace-pre-wrap font-medium">
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Button (if any) --}}
                                <div class="mt-8 pt-6 border-t border-gray-100">
                                    <a id="modalActionBtn" href="#"
                                        class="hidden w-full px-6 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all font-bold text-center shadow-lg shadow-blue-100">
                                        Đi tới liên kết
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Right Side: PDF Viewer --}}
                        <div id="pdfViewerArea"
                            class="flex-1 bg-gray-100 relative overflow-hidden hidden transition-all duration-500">
                            <div class="absolute inset-0 bg-gray-500 shadow-inner flex flex-col">
                                <iframe id="pdfIframe" src="" class="flex-1 w-full h-full border-none shadow-2xl"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function openNotificationModalFromData(element) {
                    const id = element.dataset.notificationId;
                    const title = element.dataset.notificationTitle;
                    const message = element.dataset.notificationMessage;
                    const badge = element.dataset.notificationBadge;
                    const color = element.dataset.notificationColor;
                    const isRead = element.dataset.notificationRead === 'true';
                    const redirectUrl = element.dataset.notificationUrl || '';
                    const attachment = element.dataset.notificationAttachment || '';
                    const sender = element.dataset.notificationSender || '';
                    const recipient = element.dataset.notificationRecipient || '';
                    const time = element.dataset.notificationTime || '';

                    openNotificationModal(id, title, message, badge, color, isRead, redirectUrl, attachment, sender, recipient, time);
                }

                function openNotificationModal(id, title, message, badge, color, isRead, redirectUrl = '', attachment = '', sender = '', recipient = '', time = '') {
                    // Set modal content
                    document.getElementById('modalTitle').textContent = title;
                    document.getElementById('modalMessage').innerHTML = message;
                    
                    // Set metadata content
                    document.getElementById('modalSender').textContent = sender;
                    document.getElementById('modalRecipient').textContent = recipient;
                    document.getElementById('modalTime').textContent = time;

                    const badgeEl = document.getElementById('modalBadge');
                    badgeEl.textContent = badge;
                    badgeEl.className = `inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black tracking-widest uppercase mb-1 bg-${color}-100 text-${color}-700`;

                    // Update Icon Color
                    const iconContainer = document.getElementById('modalIconContainer');
                    iconContainer.className = `w-10 h-10 rounded-xl bg-${color}-50 text-${color}-600 flex items-center justify-center shadow-sm border border-${color}-100`;

                    // Handle PDF Attachment
                    const pdfArea = document.getElementById('pdfViewerArea');
                    const pdfIframe = document.getElementById('pdfIframe');
                    const contentPane = document.getElementById('contentPane');
                    const downloadLink = document.getElementById('modalDownloadLink');

                    if (attachment) {
                        pdfArea.classList.remove('hidden');
                        pdfIframe.src = attachment + '#toolbar=1&navpanes=0&view=FitH';
                        downloadLink.classList.remove('hidden');
                        downloadLink.href = attachment;
                        contentPane.classList.remove('lg:w-full');
                        contentPane.classList.add('lg:w-[35%]');
                    } else {
                        pdfArea.classList.add('hidden');
                        pdfIframe.src = '';
                        downloadLink.classList.add('hidden');
                        contentPane.classList.remove('lg:w-[35%]');
                        contentPane.classList.add('lg:w-full');
                    }

                    // Handle action button
                    const actionBtn = document.getElementById('modalActionBtn');
                    if (redirectUrl) {
                        if (redirectUrl === '#completed') {
                            actionBtn.href = 'javascript:void(0)';
                            actionBtn.className = 'w-full block px-6 py-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 font-bold text-center pointer-events-none';
                            actionBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>THÀNH CÔNG';
                            actionBtn.classList.remove('hidden');
                        } else {
                            actionBtn.href = redirectUrl;
                            actionBtn.className = 'w-full block px-6 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all font-bold text-center shadow-lg shadow-blue-100';
                            actionBtn.innerHTML = 'Đi nhận hàng';
                            actionBtn.classList.remove('hidden');
                        }
                    } else {
                        actionBtn.classList.add('hidden');
                    }

                    // Show modal
                    document.getElementById('notificationModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    // Mark as read if unread
                    if (!isRead) {
                        fetch(`{{ url('department/notifications') }}/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(() => {
                            // Reload page to update UI
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        });
                    }
                }

                function closeNotificationModal() {
                    document.getElementById('notificationModal').classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }

                function markAllAsRead() {
                    fetch('{{ route("department.notifications.read-all") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        window.location.reload();
                    });
                }

                // Close modals when clicking outside
                document.getElementById('notificationModal')?.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeNotificationModal();
                    }
                });
            </script>
        @endpush
@endsection