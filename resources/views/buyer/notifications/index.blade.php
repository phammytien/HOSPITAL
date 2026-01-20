@extends('layouts.buyer')

@section('title', 'Quản lý Thông báo')
@section('header_title', 'Quản lý Thông báo')

@section('content')


    <div class="space-y-6">
        {{-- Tab Navigation --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button onclick="switchTab('list')" id="tab-list"
                    class="tab-button flex-1 px-6 py-4 text-center font-semibold transition bg-blue-50 text-blue-600 border-b-2 border-blue-600">
                    <i class="fas fa-list mr-2"></i>
                    Danh sách thông báo
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $stats['total'] }}</span>
                </button>
                <button onclick="switchTab('create')" id="tab-create"
                    class="tab-button flex-1 px-6 py-4 text-center font-semibold transition text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tạo thông báo mới
                </button>
            </div>
        </div>

        {{-- Tab Content: List --}}
        <div id="content-list" class="tab-content">
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                            <h3 class="text-3xl font-bold text-orange-600">{{ number_format($stats['unread']) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Read --}}
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Đã đọc</p>
                            <h3 class="text-3xl font-bold text-green-600">{{ number_format($stats['read']) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl p-4 border border-gray-200 mb-6">
                <form method="GET" action="{{ route('buyer.notifications.index') }}" id="filterForm"
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

                    {{-- Type Filter Dropdown --}}
                    <div class="relative">
                        <button type="button" onclick="toggleLocalDropdown('typeDropdown')"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white hover:bg-gray-50 transition flex items-center gap-2 min-w-[150px]">
                            <i class="fas fa-filter text-gray-500"></i>
                            <span id="typeLabel">
                                @if(request('type') == 'info') Thông tin
                                @elseif(request('type') == 'important') Quan trọng
                                @elseif(request('type') == 'warning') Cảnh báo
                                @elseif(request('type') == 'error') Lỗi
                                @else Tất cả loại
                                @endif
                            </span>
                            <i class="fas fa-chevron-down text-gray-400 ml-auto"></i>
                        </button>
                        <div id="typeDropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="javascript:void(0)" onclick="selectNotificationType('', 'Tất cả loại')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ !request('type') ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-list mr-2"></i> Tất cả loại
                            </a>
                            <a href="javascript:void(0)" onclick="selectNotificationType('info', 'Thông tin')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request('type') == 'info' ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-info-circle mr-2"></i> Thông tin
                            </a>
                            <a href="javascript:void(0)" onclick="selectNotificationType('important', 'Quan trọng')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request('type') == 'important' ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-star mr-2"></i> Quan trọng
                            </a>
                            <a href="javascript:void(0)" onclick="selectNotificationType('warning', 'Cảnh báo')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request('type') == 'warning' ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Cảnh báo
                            </a>
                            <a href="javascript:void(0)" onclick="selectNotificationType('error', 'Lỗi')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request('type') == 'error' ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-exclamation-circle mr-2"></i> Lỗi
                            </a>
                        </div>
                        <input type="hidden" name="type" id="typeInput" value="{{ request('type') }}">
                    </div>

                    {{-- Status Filter Dropdown --}}
                    <div class="relative">
                        <button type="button" onclick="toggleLocalDropdown('statusDropdown')"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white hover:bg-gray-50 transition flex items-center gap-2 min-w-[180px]">
                            <i class="fas fa-check-circle text-gray-500"></i>
                            <span id="statusLabel">
                                @if(request('is_read') === '0') Chưa đọc
                                @elseif(request('is_read') === '1') Đã đọc
                                @else Tất cả trạng thái
                                @endif
                            </span>
                            <i class="fas fa-chevron-down text-gray-400 ml-auto"></i>
                        </button>
                        <div id="statusDropdown"
                            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="javascript:void(0)" onclick="selectReadStatus('', 'Tất cả trạng thái')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ !request('is_read') ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-list mr-2"></i> Tất cả trạng thái
                            </a>
                            <a href="javascript:void(0)" onclick="selectReadStatus('0', 'Chưa đọc')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request('is_read') === '0' ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-envelope mr-2"></i> Chưa đọc
                            </a>
                            <a href="javascript:void(0)" onclick="selectReadStatus('1', 'Đã đọc')"
                                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request('is_read') === '1' ? 'bg-blue-50 text-blue-600' : '' }}">
                                <i class="fas fa-check-circle mr-2"></i> Đã đọc
                            </a>
                        </div>
                        <input type="hidden" name="is_read" id="statusInput" value="{{ request('is_read') }}">
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
                                'important' => ['icon' => 'star', 'color' => 'purple', 'bg' => 'purple-50', 'badge' => 'QUAN TRỌNG'],
                            ];
                            $config = $icons[$notification->type] ?? $icons['info'];
                        @endphp
                        <div class="notification-item p-6 hover:bg-gray-50 transition border-l-4 border-{{ $config['color'] }}-500 {{ !$notification->is_read ? 'bg-blue-50' : '' }} cursor-pointer"
                            data-id="{{ $notification->id }}" data-title="{{ $notification->title }}"
                            data-message="{{ $notification->message }}" data-type="{{ $notification->type }}"
                            data-is-read="{{ $notification->is_read ? 'true' : 'false' }}">
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="relative flex-shrink-0">
                                    <div class="w-12 h-12 bg-{{ $config['bg'] }} rounded-lg flex items-center justify-center">
                                        <i class="fas fa-{{ $config['icon'] }} text-{{ $config['color'] }}-600 text-xl"></i>
                                    </div>
                                    @if(!$notification->is_read)
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full ring-2 ring-white">
                                        </div>
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
                                    <p class="text-gray-600 text-sm mb-3 {{ !$notification->is_read ? 'font-medium' : '' }}">
                                        {{ $notification->message }}</p>
                                    <div
                                        class="flex items-center gap-4 text-xs {{ !$notification->is_read ? 'text-gray-600 font-medium' : 'text-gray-500' }}">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-user"></i>
                                            Người gửi: {{ $notification->createdBy->full_name ?? 'Hệ thống' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-building"></i>
                                            Đối tượng: {{ $notification->target_role === 'ADMIN' ? 'Admin' : 'Khoa/Phòng' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            {{ \App\Helpers\TimeHelper::formatNotificationTime($notification->created_at) }}
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

        {{-- Tab Content: Create --}}
        <div id="content-create" class="tab-content hidden">
            <div class="bg-white rounded-xl p-8 border border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                    Tạo thông báo mới
                </h3>
                <form method="POST" action="{{ route('buyer.notifications.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tiêu đề thông báo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nhập tiêu đề thông báo...">
                        </div>

                        {{-- Message --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nội dung <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" rows="6" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nhập nội dung thông báo..."></textarea>
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại thông báo <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($notificationTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Target Role --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Đối tượng nhận <span class="text-red-500">*</span>
                            </label>
                            <select name="target_role" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ADMIN">Admin</option>
                                <option value="DEPARTMENT" selected>Khoa/Phòng</option>
                            </select>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="switchTab('list')"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-times mr-2"></i>
                            Hủy
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Gửi thông báo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tab Content: Edit --}}
        <div id="content-edit" class="tab-content hidden">
            <div class="bg-white rounded-xl p-8 border border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Chỉnh sửa thông báo
                </h3>
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tiêu đề <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editTitle" name="title" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nội dung <span class="text-red-500">*</span>
                            </label>
                            <textarea id="editMessage" name="message" rows="6" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại thông báo <span class="text-red-500">*</span>
                            </label>
                            <select id="editType" name="type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($notificationTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Đối tượng nhận <span class="text-red-500">*</span>
                            </label>
                            <select id="editTargetRole" name="target_role" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ADMIN">Admin</option>
                                <option value="DEPARTMENT">Khoa/Phòng</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="switchTab('list')"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-times mr-2"></i>
                            Hủy
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Dropdown toggle functions for Buyer Notification Filters
        function toggleLocalDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const allDropdowns = document.querySelectorAll('[id$="Dropdown"]');

            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== dropdownId) {
                    d.classList.add('hidden');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }

        function selectNotificationType(value, label) {
            document.getElementById('typeInput').value = value;
            document.getElementById('typeLabel').textContent = label;
            document.getElementById('typeDropdown').classList.add('hidden');
            document.getElementById('filterForm').submit();
        }

        function selectReadStatus(value, label) {
            document.getElementById('statusInput').value = value;
            document.getElementById('statusLabel').textContent = label;
            document.getElementById('statusDropdown').classList.add('hidden');
            document.getElementById('filterForm').submit();
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('[onclick^="toggleLocalDropdown"]') && !e.target.closest('[id$="Dropdown"]')) {
                document.querySelectorAll('[id$="Dropdown"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Tab switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-blue-50', 'text-blue-600', 'border-b-2', 'border-blue-600');
                button.classList.add('text-gray-600', 'hover:bg-gray-50');
            });

            // Show selected tab
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('bg-blue-50', 'text-blue-600', 'border-b-2', 'border-blue-600');
            activeButton.classList.remove('text-gray-600', 'hover:bg-gray-50');
        }

        // Edit notification
        function editNotification(id, title, message, type, targetRole) {
            // Set form action
            document.getElementById('editForm').action = `{{ url('buyer/notifications') }}/${id}`;

            // Fill form fields
            document.getElementById('editTitle').value = title;
            document.getElementById('editMessage').value = message;
            document.getElementById('editType').value = type;
            document.getElementById('editTargetRole').value = targetRole || 'DEPARTMENT';

            // Switch to edit tab
            switchTab('edit');

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Click event delegation for notification items
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                const notificationItem = e.target.closest('.notification-item');
                if (notificationItem) {
                    const id = notificationItem.dataset.id;
                    const title = notificationItem.dataset.title;
                    const message = notificationItem.dataset.message;
                    const type = notificationItem.dataset.type;
                    const isRead = notificationItem.dataset.isRead === 'true';

                    // Call the layout's built-in showNotificationDetail function
                    // This function handles displaying the modal and marking as read
                    if (typeof showNotificationDetail === 'function') {
                        showNotificationDetail(id, title, message, type, isRead);
                    } else {
                        console.error('showNotificationDetail function not found in layout');
                    }
                }
            });
        });

    </script>
@endsection