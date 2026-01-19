@extends('layouts.buyer')

@section('title', 'Quản lý Thông báo')
@section('header_title', 'Quản lý Thông báo')

@section('content')

{{-- Toast Notification --}}
@if(session('success'))
<div id="toast" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[100] bg-white rounded-xl shadow-2xl border border-green-200 p-6 min-w-[400px]">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-gray-900 mb-1">Thành công!</h4>
            <p class="text-gray-600 text-sm">{{ session('success') }}</p>
        </div>
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div id="toast" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[100] bg-white rounded-xl shadow-2xl border border-red-200 p-6 min-w-[400px]">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-gray-900 mb-1">Lỗi!</h4>
            <p class="text-gray-600 text-sm">{{ session('error') }}</p>
        </div>
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

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
            <form method="GET" action="{{ route('buyer.notifications.index') }}" id="filterForm" class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Tìm theo tiêu đề, nội dung..." 
                               onchange="document.getElementById('filterForm').submit()"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <select name="type" onchange="document.getElementById('filterForm').submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả loại</option>
                    <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Thông tin</option>
                    <option value="important" {{ request('type') == 'important' ? 'selected' : '' }}>Quan trọng</option>
                    <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Cảnh báo</option>
                    <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Lỗi</option>
                </select>
                <select name="is_read" onchange="document.getElementById('filterForm').submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
                    <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Đã đọc</option>
                </select>
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
                     data-id="{{ $notification->id }}"
                     data-title="{{ $notification->title }}"
                     data-message="{{ $notification->message }}"
                     data-type="{{ $notification->type }}"
                     data-is-read="{{ $notification->is_read ? 'true' : 'false' }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="relative flex-shrink-0">
                            <div class="w-12 h-12 bg-{{ $config['bg'] }} rounded-lg flex items-center justify-center">
                                <i class="fas fa-{{ $config['icon'] }} text-{{ $config['color'] }}-600 text-xl"></i>
                            </div>
                            @if(!$notification->is_read)
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full ring-2 ring-white"></div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <h3 class="{{ !$notification->is_read ? 'font-bold' : 'font-semibold' }} text-gray-900">{{ $notification->title }}</h3>
                                <span class="px-3 py-1 bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                    {{ $config['badge'] }}
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mb-3 {{ !$notification->is_read ? 'font-medium' : '' }}">{{ $notification->message }}</p>
                            <div class="flex items-center gap-4 text-xs {{ !$notification->is_read ? 'text-gray-600 font-medium' : 'text-gray-500' }}">
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

// View notification detail
let currentNotificationId = null;
let currentNotificationWasRead = false;

// Add event delegation for notification items
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        if (notificationItem) {
            const id = notificationItem.dataset.id;
            const title = notificationItem.dataset.title;
            const message = notificationItem.dataset.message;
            const type = notificationItem.dataset.type;
            const isRead = notificationItem.dataset.isRead === 'true';
            
            viewNotificationDetail(id, title, message, type, isRead);
        }
    });
});

function viewNotificationDetail(id, title, message, type, isRead) {
    currentNotificationId = id;
    currentNotificationWasRead = isRead;
    
    // Create modal if it doesn't exist
    let modal = document.getElementById('notificationDetailModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'notificationDetailModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center">
                        <i class="fas fa-bell mr-2 text-blue-600"></i> Chi tiết thông báo
                    </h3>
                    <button onclick="closeNotificationDetail()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="mb-4">
                        <span id="modalNotificationBadge" class="px-3 py-1 rounded-full text-xs font-semibold"></span>
                    </div>
                    <h4 id="modalNotificationTitle" class="text-xl font-bold text-gray-900 mb-4"></h4>
                    <p id="modalNotificationMessage" class="text-gray-700 leading-relaxed whitespace-pre-wrap"></p>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                    <button onclick="closeNotificationDetail()" class="px-5 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium transition-colors">Đóng</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Set content
    document.getElementById('modalNotificationTitle').textContent = title;
    document.getElementById('modalNotificationMessage').textContent = message;
    
    // Set badge based on type
    const badgeConfig = {
        'important': { text: 'QUAN TRỌNG', class: 'bg-purple-100 text-purple-700' },
        'error': { text: 'LỖI', class: 'bg-red-100 text-red-700' },
        'warning': { text: 'CẢNH BÁO', class: 'bg-yellow-100 text-yellow-700' },
        'info': { text: 'THÔNG TIN', class: 'bg-blue-100 text-blue-700' }
    };
    const config = badgeConfig[type] || badgeConfig['info'];
    const badgeEl = document.getElementById('modalNotificationBadge');
    badgeEl.textContent = config.text;
    badgeEl.className = `px-3 py-1 rounded-full text-xs font-semibold ${config.class}`;
    
    // Show modal
    modal.classList.remove('hidden');
}

function closeNotificationDetail() {
    const modal = document.getElementById('notificationDetailModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // Mark as read if it wasn't read before
    if (currentNotificationId && !currentNotificationWasRead) {
        fetch(`/buyer/notifications/${currentNotificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            // Reload page to update UI
            setTimeout(() => {
                window.location.reload();
            }, 300);
        });
    }
    
    // Reset current notification tracking
    currentNotificationId = null;
    currentNotificationWasRead = false;
}

// Toast functions
function closeToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }
}

// Auto-hide toast after 3 seconds
if (document.getElementById('toast')) {
    setTimeout(() => {
        closeToast();
    }, 3000);
}
</script>
@endsection
