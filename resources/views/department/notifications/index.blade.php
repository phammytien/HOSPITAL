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
            'success' => $allDeptNotifications->where('type', 'success')->count(),
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
                <a href="{{ route('department.notifications.index', ['type' => 'success']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'success' ? 'bg-green-50 text-green-600 border-b-2 border-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-check-circle mr-2"></i>
                    Thành công
                    <span
                        class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $typeCounts['success'] }}</span>
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
                            'success' => ['icon' => 'check-circle', 'color' => 'green', 'bg' => 'green-50', 'badge' => 'THÀNH CÔNG'],
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
                        data-notification-url="{{ $modalRedirectUrl }}" onclick="openNotificationModalFromData(this)">

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
                            </div>
                            @if($isUrgent && $orderId && $order && $order->status == 'DELIVERED')
                                <div class="mt-4">
                                    <a href="{{ route('department.dept_orders.show', $orderId) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-xs font-black rounded-lg hover:bg-blue-700 transition shadow-md uppercase tracking-wider">
                                        <i class="fas fa-truck-fast"></i>
                                        Xác nhận đã nhận hàng ngay
                                    </a>
                                </div>
                            @elseif($isUrgent && $orderId && $order && $order->status == 'COMPLETED')
                                <div class="mt-4">
                                    <span
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 text-xs font-bold rounded-lg border border-green-200">
                                        <i class="fas fa-check-circle"></i>
                                        THÀNH CÔNG
                                    </span>
                                </div>
                            @endif
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

        {{-- Notification Detail Modal --}}
        <div id="notificationModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                onclick="event.stopPropagation()">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 id="modalTitle" class="text-xl font-bold text-gray-900"></h3>
                    <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <div class="mb-4">
                        <span id="modalBadge" class="px-3 py-1 rounded-full text-xs font-semibold"></span>
                    </div>
                    <p id="modalMessage" class="text-gray-700 leading-relaxed break-words whitespace-normal"></p>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <a id="modalActionBtn" href="#"
                        class="hidden px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold">
                        Đi nhận hàng
                    </a>
                    <button onclick="closeNotificationModal()"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Đóng
                    </button>
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

                    openNotificationModal(id, title, message, badge, color, isRead, redirectUrl);
                }

                function openNotificationModal(id, title, message, badge, color, isRead, redirectUrl = '') {
                    // Set modal content
                    document.getElementById('modalTitle').textContent = title;
                    document.getElementById('modalMessage').innerHTML = message;

                    const badgeEl = document.getElementById('modalBadge');
                    badgeEl.textContent = badge;
                    badgeEl.className = `px-3 py-1 rounded-full text-xs font-semibold bg-${color}-100 text-${color}-700`;

                    // Handle action button
                    const actionBtn = document.getElementById('modalActionBtn');
                    if (redirectUrl) {
                        if (redirectUrl === '#completed') {
                            actionBtn.href = 'javascript:void(0)';
                            actionBtn.className = 'hidden px-6 py-2 bg-green-100 text-green-700 rounded-lg border border-green-200 font-bold pointer-events-none';
                            actionBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>THÀNH CÔNG';
                            actionBtn.classList.remove('hidden');
                        } else {
                            actionBtn.href = redirectUrl;
                            actionBtn.className = 'px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold';
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