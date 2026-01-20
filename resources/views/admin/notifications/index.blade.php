@extends('layouts.admin')

@section('title', 'Thông báo Hệ thống')
@section('page-title', 'Thông báo Hệ thống')
@section('header_title', 'Thông báo Hệ thống')

@section('page-subtitle', 'Quản lý các cảnh báo, lỗi, bảo trì và sự kiện quan trọng trong hệ thống')

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
        <div id="toast"
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[100] bg-white rounded-xl shadow-2xl border border-red-200 p-6 min-w-[400px]">
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
        {{-- Header with Create Button --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mt-1">Quản lý các cảnh báo, lỗi, bảo trì và sự kiện quan trọng trong hệ thống</p>
            </div>
            <div class="flex gap-2">
                <!-- <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-cog"></i>
                    <span>Cấu hình</span>
                </button> -->
                <button onclick="toggleCreateForm()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Tạo thông báo mới</span>
                </button>
            </div>
        </div>

        {{-- Create Form (Hidden by default) --}}
        <div id="createForm" class="hidden bg-white rounded-xl p-8 border border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Tạo thông báo mới</h3>

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 mb-6">
                <button type="button" onclick="switchTab('manual')" id="manualTab"
                    class="tab-button px-6 py-3 font-medium text-blue-600 border-b-2 border-blue-600">
                    <i class="fas fa-edit mr-2"></i>
                    Nhập thủ công
                </button>
                <button type="button" onclick="switchTab('upload')" id="uploadTab"
                    class="tab-button px-6 py-3 font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                    Upload file
                </button>
            </div>

            {{-- Manual Form Tab --}}
            <div id="manualFormTab">
                <form method="POST" action="{{ route('admin.notifications.store') }}" id="manualNotificationForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tiêu đề thông báo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="manualTitle" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nhập tiêu đề thông báo...">
                        </div>

                        {{-- Message --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nội dung <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" id="manualMessage" rows="4" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nhập nội dung thông báo..."></textarea>
                        </div>

                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại thông báo <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="manualType" required
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
                            <select name="target_role" id="manualTargetRole" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ALL">Tất cả người dùng</option>
                                <option value="BUYER">Nhân viên mua hàng</option>
                                <option value="DEPARTMENT">Khoa/Phòng</option>
                            </select>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="toggleCreateForm()"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
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

            {{-- Upload File Tab --}}
            <div id="uploadFormTab" class="hidden">
                {{-- Upload Zone --}}
                <div id="uploadZone"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition cursor-pointer">
                    <input type="file" id="documentFile" accept=".pdf,.doc,.docx" class="hidden">
                    <div id="uploadPrompt">
                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                        <p class="text-lg font-medium text-gray-700 mb-2">Kéo thả file vào đây hoặc click để chọn</p>
                        <p class="text-sm text-gray-500">Hỗ trợ: PDF, Word (.doc, .docx) - Tối đa 5MB</p>
                    </div>
                    <div id="uploadProgress" class="hidden">
                        <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
                        <p class="text-gray-700">Đang phân tích file...</p>
                    </div>
                </div>

                {{-- Extracted Data Form (Hidden initially) --}}
                <form method="POST" action="{{ route('admin.notifications.store') }}" id="extractedNotificationForm"
                    class="hidden mt-6">
                    @csrf

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2 text-green-700">
                            <i class="fas fa-check-circle"></i>
                            <span class="font-medium">File đã được phân tích thành công!</span>
                        </div>
                        <p class="text-sm text-green-600 mt-1">Bạn có thể chỉnh sửa nội dung trước khi gửi thông báo.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tiêu đề thông báo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="extractedTitle" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nội dung <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" id="extractedMessage" rows="6" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại thông báo <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="extractedType" required
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
                            <select name="target_role" id="extractedTargetRole" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ALL">Tất cả người dùng</option>
                                <option value="BUYER">Nhân viên mua hàng</option>
                                <option value="DEPARTMENT">Khoa/Phòng</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="resetUploadForm()"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-redo mr-2"></i>
                            Upload lại
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

            {{-- Unread Notifications --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Chưa đọc</p>
                        <h3 class="text-3xl font-bold text-orange-600">{{ number_format($stats['unread']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Read Notifications --}}
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

        {{-- Status Tabs --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <a href="{{ route('admin.notifications') }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ !request('type') ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-list mr-2"></i>
                    Tất cả
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $stats['total'] }}</span>
                </a>
                <a href="{{ route('admin.notifications', ['type' => 'info']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'info' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-info-circle mr-2"></i>
                    Thông tin
                </a>
                <a href="{{ route('admin.notifications', ['type' => 'warning']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'warning' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Cảnh báo
                </a>
                <a href="{{ route('admin.notifications', ['type' => 'error']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'error' ? 'bg-red-50 text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-times-circle mr-2"></i>
                    Khẩn cấp
                </a>
                <a href="{{ route('admin.notifications', ['type' => 'important']) }}"
                    class="flex-1 px-6 py-4 text-center font-semibold transition {{ request('type') == 'important' ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-star mr-2"></i>
                    Quan trọng
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <form method="GET" action="{{ route('admin.notifications') }}" id="filterForm" class="flex items-center gap-4">
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
                @forelse($notifications as $index => $notification)
                    @php
                        $icons = [
                            'error' => ['icon' => 'exclamation-circle', 'color' => 'red', 'bg' => 'red-50', 'badge' => 'KHẨN CẤP'],
                            'warning' => ['icon' => 'chart-line', 'color' => 'orange', 'bg' => 'orange-50', 'badge' => 'CẢNH BÁO'],
                            'info' => ['icon' => 'info-circle', 'color' => 'blue', 'bg' => 'blue-50', 'badge' => 'THÔNG TIN'],
                            'important' => ['icon' => 'star', 'color' => 'purple', 'bg' => 'purple-50', 'badge' => 'QUAN TRỌNG'],
                        ];
                        $config = $icons[$notification->type] ?? $icons['info'];
                    @endphp
                    <div class="p-6 hover:bg-gray-50 transition border-l-4 border-{{ $config['color'] }}-500 cursor-pointer {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                        onclick="openNotificationModal({{ $notification->id }}, {{ Js::from($notification->title) }}, {{ Js::from($notification->message) }}, {{ Js::from($config['badge']) }}, {{ Js::from($config['color']) }}, {{ $notification->is_read ? 'true' : 'false' }})">

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
                                        <i class="far fa-building"></i>
                                        @php
                                            $roleNames = [
                                                'ALL' => 'Tất cả',
                                                'ADMIN' => 'Quản trị viên',
                                                'BUYER' => 'Nhân viên mua hàng',
                                                'DEPARTMENT' => 'Khoa/Phòng',
                                                'staff' => 'Nhân viên',
                                                'department_head' => 'Trưởng khoa',
                                                'buyer' => 'Người mua hàng'
                                            ];
                                            $displayRole = $roleNames[$notification->target_role] ?? ($notification->target_role ?? 'Tất cả');
                                        @endphp
                                        Đối tượng nhận: {{ $displayRole }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        {{ \App\Helpers\TimeHelper::formatNotificationTime($notification->created_at) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                <button
                                    onclick="openEditModal({{ $notification->id }}, {{ Js::from($notification->title) }}, {{ Js::from($notification->message) }}, {{ Js::from($notification->type) }}, {{ Js::from($notification->target_role) }})"
                                    class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

    {{-- Edit Notification Modal --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Chỉnh sửa thông báo</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tiêu đề <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editTitle" name="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nội dung <span class="text-red-500">*</span>
                        </label>
                        <textarea id="editMessage" name="message" rows="4" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
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
                                <option value="ALL">Tất cả người dùng</option>
                                <option value="BUYER">Nhân viên mua hàng</option>
                                <option value="DEPARTMENT">Khoa/Phòng</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Hủy
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
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
                    fetch(`{{ url('admin/notifications') }}/${id}/read`, {
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

            function openEditModal(id, title, message, type, targetRole) {
                // Set form action
                document.getElementById('editForm').action = `{{ url('admin/notifications') }}/${id}`;

                // Fill form fields
                document.getElementById('editTitle').value = title;
                document.getElementById('editMessage').value = message;
                document.getElementById('editType').value = type;
                document.getElementById('editTargetRole').value = targetRole;

                // Show modal
                document.getElementById('editModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modals when clicking outside
            document.getElementById('notificationModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeNotificationModal();
                }
            });

            document.getElementById('editModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            function toggleCreateForm() {
                const form = document.getElementById('createForm');
                form.classList.toggle('hidden');

                // Scroll to form if showing
                if (!form.classList.contains('hidden')) {
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

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

            // ===== FILE UPLOAD FUNCTIONALITY =====

            // Tab switching
            function switchTab(tab) {
                const manualTab = document.getElementById('manualTab');
                const uploadTab = document.getElementById('uploadTab');
                const manualFormTab = document.getElementById('manualFormTab');
                const uploadFormTab = document.getElementById('uploadFormTab');

                if (tab === 'manual') {
                    manualTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                    manualTab.classList.remove('text-gray-500');
                    uploadTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                    uploadTab.classList.add('text-gray-500');

                    manualFormTab.classList.remove('hidden');
                    uploadFormTab.classList.add('hidden');
                } else {
                    uploadTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                    uploadTab.classList.remove('text-gray-500');
                    manualTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                    manualTab.classList.add('text-gray-500');

                    uploadFormTab.classList.remove('hidden');
                    manualFormTab.classList.add('hidden');
                }
            }

            // File upload handling
            const uploadZone = document.getElementById('uploadZone');
            const documentFile = document.getElementById('documentFile');

            if (uploadZone && documentFile) {
                // Click to select file
                uploadZone.addEventListener('click', () => {
                    documentFile.click();
                });

                // Drag and drop
                uploadZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadZone.classList.add('border-blue-500', 'bg-blue-50');
                });

                uploadZone.addEventListener('dragleave', () => {
                    uploadZone.classList.remove('border-blue-500', 'bg-blue-50');
                });

                uploadZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadZone.classList.remove('border-blue-500', 'bg-blue-50');

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        documentFile.files = files;
                        handleFileUpload(files[0]);
                    }
                });

                // File input change
                documentFile.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleFileUpload(e.target.files[0]);
                    }
                });
            }

            function handleFileUpload(file) {
                // Validate file type
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Chỉ hỗ trợ file PDF và Word (.doc, .docx)');
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File không được lớn hơn 5MB');
                    return;
                }

                // Show progress
                document.getElementById('uploadPrompt').classList.add('hidden');
                document.getElementById('uploadProgress').classList.remove('hidden');

                // Upload via AJAX
                const formData = new FormData();
                formData.append('document', file);

                fetch('{{ route("admin.notifications.upload") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fill form with extracted data
                            document.getElementById('extractedTitle').value = data.data.title;
                            document.getElementById('extractedMessage').value = data.data.message;
                            document.getElementById('extractedType').value = data.data.type;

                            // Show extracted form
                            document.getElementById('uploadZone').classList.add('hidden');
                            document.getElementById('extractedNotificationForm').classList.remove('hidden');
                        } else {
                            alert('Lỗi: ' + data.message);
                            resetUploadForm();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi upload file');
                        resetUploadForm();
                    });
            }

            function resetUploadForm() {
                document.getElementById('uploadZone').classList.remove('hidden');
                document.getElementById('extractedNotificationForm').classList.add('hidden');
                document.getElementById('uploadPrompt').classList.remove('hidden');
                document.getElementById('uploadProgress').classList.add('hidden');
                document.getElementById('documentFile').value = '';
            }

        </script>
    @endpush
@endsection