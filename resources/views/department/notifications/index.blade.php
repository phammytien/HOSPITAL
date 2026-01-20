@extends('layouts.department')

@section('title', 'Thông báo')
@section('header_title', 'Thông báo')

@section('content')
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
                <span>Đánh dấu tất cả đã đọc</span>
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
                            {{ number_format($stats['total'] - $stats['unread']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Tabs --}}
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
            </div>
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

        {{-- Notifications List --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    @php
                        $icons = [
                            'error' => ['icon' => 'exclamation-circle', 'color' => 'red', 'bg' => 'red-50', 'badge' => 'KHẨN CẤP'],
                            'warning' => ['icon' => 'exclamation-triangle', 'color' => 'orange', 'bg' => 'orange-50', 'badge' => 'CẢNH BÁO'],
                            'info' => ['icon' => 'info-circle', 'color' => 'blue', 'bg' => 'blue-50', 'badge' => 'THÔNG TIN'],
                            'success' => ['icon' => 'check-circle', 'color' => 'green', 'bg' => 'green-50', 'badge' => 'THÀNH CÔNG'],
                        ];
                        $config = $icons[$notification->type] ?? $icons['info'];
                    @endphp
                    <div class="p-6 hover:bg-gray-50 transition border-l-4 border-{{ $config['color'] }}-500 cursor-pointer {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                        onclick="openNotificationModal({{ $notification->id }}, '{{ addslashes($notification->title) }}', '{{ addslashes($notification->message) }}', '{{ $config['badge'] }}', '{{ $config['color'] }}', {{ $notification->is_read ? 'true' : 'false' }})">

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
                                        {{ $notification->title }}</h3>
                                    <span
                                        class="px-3 py-1 bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                        {{ $config['badge'] }}
                                    </span>
                                </div>
                                {{-- Truncated message --}}
                                <p
                                    class="text-gray-600 text-sm mb-3 line-clamp-2 {{ !$notification->is_read ? 'font-medium' : '' }}">
                                    {{ $notification->message }}</p>
                                <div
                                    class="flex items-center gap-4 text-xs {{ !$notification->is_read ? 'text-gray-600 font-medium' : 'text-gray-500' }}">
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-user"></i>
                                        Người gửi: {{ $notification->createdBy->full_name ?? 'Hệ thống' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'N/A' }}
                                    </span>
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

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Notification Detail Modal --}}
    <div id="notificationModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
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
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeNotificationModal()"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openNotificationModal(id, title, message, badge, color, isRead) {
                // Set modal content
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalMessage').textContent = message;

                const badgeEl = document.getElementById('modalBadge');
                badgeEl.textContent = badge;
                badgeEl.className = `px-3 py-1 rounded-full text-xs font-semibold bg-${color}-100 text-${color}-700`;

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