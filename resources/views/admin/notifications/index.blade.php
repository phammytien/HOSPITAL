@extends('layouts.admin')

@section('title', 'Quản lý Thông báo')
@section('header_title', 'Quản lý Thông báo')

@section('content')

    <div class="space-y-6">
        {{-- Tab Navigation --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex border-b border-gray-200">
                <a href="{{ route('admin.notifications', ['tab' => 'received']) }}" id="tab-received"
                    class="tab-button flex-1 px-6 py-4 text-center font-semibold transition {{ $tab === 'received' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-inbox mr-2"></i>
                    Thông báo nhận được
                    <span
                        class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $stats['received_total'] }}</span>
                </a>
                <a href="{{ route('admin.notifications', ['tab' => 'sent']) }}" id="tab-sent"
                    class="tab-button flex-1 px-6 py-4 text-center font-semibold transition {{ $tab === 'sent' ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Thông báo đã gửi
                    <span
                        class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $stats['sent_total'] }}</span>
                </a>
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
                {{-- Tab Specific Total --}}
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tổng {{ $tab === 'sent' ? 'đã gửi' : 'đã nhận' }}</p>
                            <h3 class="text-3xl font-bold text-gray-900">
                                {{ number_format($tab === 'sent' ? $stats['sent_total'] : $stats['received_total']) }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas {{ $tab === 'sent' ? 'fa-paper-plane' : 'fa-inbox' }} text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                @if($tab === 'received')
                    {{-- Unread --}}
                    <div class="bg-white rounded-xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Chưa đọc</p>
                                <h3 class="text-3xl font-bold text-orange-600">{{ number_format($stats['received_unread']) }}
                                </h3>
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
                                <h3 class="text-3xl font-bold text-green-600">{{ number_format($stats['received_read']) }}</h3>
                            </div>
                            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 mb-6 shadow-sm">
                <form method="GET" action="{{ route('admin.notifications') }}" id="filterForm"
                    class="flex items-center gap-4">
                    <span class="text-blue-900/40 font-bold text-sm tracking-widest uppercase ml-2">Lọc:</span>

                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <select name="type" onchange="document.getElementById('filterForm').submit()"
                        class="px-6 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 font-medium shadow-sm cursor-pointer transition-all">
                        <option value="">-- Tất cả loại --</option>
                        @foreach($notificationTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if($tab === 'received')
                        <select name="is_read" onchange="document.getElementById('filterForm').submit()"
                            class="px-6 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 font-medium shadow-sm cursor-pointer transition-all">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
                            <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Đã đọc</option>
                        </select>
                    @endif

                    {{-- Clear Filter --}}
                    <a href="{{ route('admin.notifications') }}"
                        class="px-8 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 text-sm font-bold rounded-xl transition-all shadow-sm flex items-center justify-center min-w-[120px]">
                        Xóa lọc
                    </a>
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
                            $config = $icons[trim($notification->type)] ?? $icons['info'];
                        @endphp
                        <div class="notification-item p-6 hover:bg-gray-50 transition border-l-4 border-{{ $config['color'] }}-500 {{ (!$notification->is_read && $tab === 'received') ? 'bg-blue-50' : '' }} cursor-pointer"
                            data-id="{{ $notification->id }}" data-title="{{ $notification->title }}"
                            data-message="{{ $notification->message }}" data-type="{{ $notification->type }}"
                            data-attachment="{{ $notification->attachment ? url($notification->attachment->file_path) : '' }}"
                            data-is-read="{{ ($notification->is_read || $tab === 'sent') ? 'true' : 'false' }}">
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="relative flex-shrink-0">
                                    <div class="w-12 h-12 bg-{{ $config['bg'] }} rounded-lg flex items-center justify-center">
                                        <i class="fas fa-{{ $config['icon'] }} text-{{ $config['color'] }}-600 text-xl"></i>
                                    </div>
                                    @if(!$notification->is_read && $tab === 'received')
                                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full ring-2 ring-white">
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <h3
                                            class="{{ (!$notification->is_read && $tab === 'received') ? 'font-bold' : 'font-semibold' }} text-gray-900">
                                            {{ $notification->title }}</h3>
                                        <span
                                            class="px-3 py-1 bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700 rounded-full text-xs font-semibold whitespace-nowrap">
                                            {{ $config['badge'] }}
                                        </span>
                                    </div>
                                    <p
                                        class="text-gray-600 text-sm mb-3 {{ (!$notification->is_read && $tab === 'received') ? 'font-medium' : '' }}">
                                        {{ $notification->message }}</p>
                                    <div
                                        class="flex items-center gap-4 text-xs {{ (!$notification->is_read && $tab === 'received') ? 'text-gray-600 font-medium' : 'text-gray-500' }}">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-user"></i>
                                            Người gửi: {{ $notification->createdBy->full_name ?? 'Hệ thống' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-building"></i>
                                            Đối tượng:
                                            {{ \App\Helpers\NotificationHelper::getRoleLabel($notification->target_role) }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            {{ $notification->created_at->format('d/m/Y') }}
                                        </span>
                                        @if($notification->attachment)
                                            <span class="flex items-center gap-1 text-red-600 font-bold">
                                                <i class="fas fa-file-pdf"></i>
                                                Đính kèm PDF
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions for Sent notifications --}}
                                @if($tab === 'sent')
                                    <div class="flex items-center gap-2">
                                        <button onclick="event.stopPropagation(); openEditModal({
                                            id: {{ $notification->id }},
                                            title: '{{ addslashes($notification->title) }}',
                                            message: '{{ addslashes($notification->message) }}',
                                            type: '{{ $notification->type }}',
                                            target: '{{ $notification->target_role }}',
                                            fileName: '{{ $notification->attachment ? $notification->attachment->file_name : "" }}'
                                        })" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-all border border-blue-100 shadow-sm"
                                            title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="event.stopPropagation(); confirmDelete({{ $notification->id }})"
                                            class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-all border border-red-100 shadow-sm"
                                            title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500">
                            <i class="fas fa-filter text-4xl mb-3 text-gray-300"></i>
                            @if(request('type') != '' && request('is_read') === '')
                                <p class="text-orange-600 font-medium">Vui lòng chọn thêm "Trạng thái" để hiển thị kết quả theo
                                    loại.</p>
                                <p class="text-xs text-gray-400 mt-2">(Tính năng lọc loại yêu cầu đi kèm với trạng thái cụ thể)</p>
                            @else
                                <p>Không có thông báo nào</p>
                            @endif
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
                <form method="POST" action="{{ route('admin.notifications.store') }}" enctype="multipart/form-data">
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
                                <option value="ALL">Tất cả người dùng</option>
                                <option value="BUYER">Nhân viên mua hàng</option>
                                <option value="DEPARTMENT">Khoa/Phòng</option>
                            </select>
                        </div>

                        {{-- Attachment --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Đính kèm File PDF (Tùy chọn)
                            </label>
                            <div class="relative group">
                                <label id="dropZoneCreate"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition-all cursor-pointer">
                                    <div id="uploadPlaceholderCreate"
                                        class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i
                                            class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-blue-500 mb-2"></i>
                                        <p class="text-sm text-gray-500"><span class="font-bold">Click để tải lên</span>
                                            hoặc kéo thả file</p>
                                        <p class="text-xs text-gray-400 mt-1">PDF (Tối đa 5MB)</p>
                                    </div>
                                    <div id="fileInfoCreate"
                                        class="hidden flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                        <p id="fileNameCreate" class="text-sm font-bold text-gray-700"></p>
                                        <p class="text-xs text-blue-600 mt-1">Click để thay đổi file khác</p>
                                    </div>
                                    <input type="file" name="attachment" id="attachmentCreate" accept=".pdf" class="hidden"
                                        onchange="handleFileSelect(this, 'Create')">
                                </label>
                            </div>
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
    </div>

    {{-- Notification Detail Modal --}}
    <div id="notificationPageDetailModal" class="fixed inset-0 z-[10060] hidden overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-md"
                onclick="closeNotificationDetail()" aria-hidden="true"></div>

            <div
                class="relative inline-block w-full max-w-7xl my-4 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-white/20">
                {{-- Header Bar --}}
                <div
                    class="absolute top-0 left-0 right-0 h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 z-20">
                    <div class="flex items-center gap-4">
                        <div id="modalIconContainer_new"
                            class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <div id="modalBadge"
                                class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold tracking-widest uppercase mb-0.5 bg-blue-100 text-blue-700">
                            </div>
                            <h2 id="modalTitle" class="text-xl font-bold text-gray-900 truncate max-w-md lg:max-w-2xl"></h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a id="modalDownloadLink" href="#" target="_blank"
                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 hover:text-blue-600 transition-all border border-gray-200 shadow-sm"
                            title="Mở trong cửa sổ mới">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <div class="w-px h-6 bg-gray-200 mx-1"></div>
                        <button onclick="closeNotificationDetail()"
                            class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 transition-all shadow-lg shadow-gray-200">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row h-[90vh] pt-20">
                    {{-- Left Side: Content (Narrower) --}}
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
                                        <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-0.5">Đối
                                            tượng nhận</p>
                                        <p id="modalRecipient" class="font-bold text-gray-800"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Message Content --}}
                            <div class="relative">
                                <div class="absolute -left-4 top-0 bottom-0 w-1 bg-blue-500 rounded-full opacity-20"></div>
                                <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-4">Nội dung chi
                                    tiết</h4>
                                <div class="prose prose-sm prose-blue max-w-none">
                                    <p id="modalMessage"
                                        class="text-gray-600 leading-relaxed text-base whitespace-pre-wrap font-medium"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: PDF Viewer (Wider) --}}
                    <div id="pdfViewerArea"
                        class="flex-1 bg-gray-100 relative overflow-hidden hidden transition-all duration-500">
                        {{-- Premium Frame for PDF --}}
                        <div class="absolute inset-0 bg-gray-500 shadow-inner flex flex-col">
                            <iframe id="pdfIframe" src="" class="flex-1 w-full h-full border-none shadow-2xl"></iframe>
                        </div>

                        {{-- Empty State for PDF --}}
                        <div id="pdfPlaceholder"
                            class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-50 hidden">
                            <div
                                class="w-24 h-24 rounded-3xl bg-gray-100 flex items-center justify-center mb-4 transition-transform hover:scale-110 duration-300">
                                <i class="fas fa-file-pdf text-4xl opacity-20"></i>
                            </div>
                            <p class="font-bold text-gray-400 uppercase tracking-widest text-xs">Không có tài liệu đính kèm
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Notification Modal --}}
    <div id="editNotificationModal" class="fixed inset-0 z-[10060] hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="bg-white px-8 pt-8 pb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Chỉnh sửa thông báo</h3>
                    <form method="POST" action="" id="editForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề</label>
                                <input type="text" name="title" id="editTitle" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung</label>
                                <textarea name="message" id="editMessage" rows="5" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Loại</label>
                                    <select name="type" id="editType" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                        @foreach($notificationTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Đối tượng</label>
                                    <select name="target_role" id="editTarget" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                        <option value="ALL">Tất cả người dùng</option>
                                        <option value="BUYER">Nhân viên mua hàng</option>
                                        <option value="DEPARTMENT">Khoa/Phòng</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Thay đổi File PDF (Tùy
                                    chọn)</label>
                                <label
                                    class="relative flex items-center gap-3 px-4 py-3 border-2 border-dashed border-blue-100 rounded-xl bg-blue-50/50 hover:bg-blue-50 transition-all cursor-pointer group">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="fas fa-file-upload"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="fileNameEdit" class="text-sm font-bold text-blue-700 truncate text-center">
                                            Chưa chọn file mới</p>
                                        <p class="text-[10px] text-blue-400 uppercase tracking-widest font-bold">Click để
                                            chọn file PDF</p>
                                    </div>
                                    <input type="file" name="attachment" id="attachmentEdit" accept=".pdf" class="hidden"
                                        onchange="handleFileSelect(this, 'Edit')">
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-8">
                            <button type="button" onclick="closeEditModal()"
                                class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all uppercase tracking-wider text-sm">
                                Hủy bỏ
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 uppercase tracking-wider text-sm">
                                Cập nhật thông báo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Form (Hidden) --}}
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

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

        // View notification detail
        let currentNotificationId = null;
        let currentNotificationWasRead = false;

        // Add event delegation for notification items
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                const notificationItem = e.target.closest('.notification-item');
                if (notificationItem) {
                    // Only open detail modal if not clicking on edit/delete buttons
                    if (!e.target.closest('button')) {
                        const data = notificationItem.dataset;
                        viewNotificationDetail({
                            id: data.id,
                            title: data.title,
                            message: data.message,
                            type: data.type,
                            isRead: data.isRead === 'true',
                            sender: notificationItem.querySelector('.fa-user').parentElement.textContent.replace('Người gửi:', '').trim(),
                            recipient: notificationItem.querySelector('.fa-building').parentElement.textContent.replace('Đối tượng:', '').trim(),
                            time: notificationItem.querySelector('.fa-clock').parentElement.textContent.trim(),
                            attachment: data.attachment
                        });
                    }
                }
            });
        });

        function viewNotificationDetail(data) {
            currentNotificationId = data.id;
            currentNotificationWasRead = data.isRead;

            // Config based on type
            const configs = {
                'important': { icon: 'fa-star', color: 'purple', badge: 'QUAN TRỌNG' },
                'error': { icon: 'fa-exclamation-circle', color: 'red', badge: 'KHẨN CẤP' },
                'warning': { icon: 'fa-exclamation-triangle', color: 'orange', badge: 'CẢNH BÁO' },
                'info': { icon: 'fa-info-circle', color: 'blue', badge: 'THÔNG TIN' }
            };
            const config = configs[data.type.trim()] || configs['info'];

            // Set content
            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalMessage').textContent = data.message;
            document.getElementById('modalSender').textContent = data.sender;
            document.getElementById('modalRecipient').textContent = data.recipient;
            document.getElementById('modalTime').textContent = data.time;

            // Set Badge
            const badgeEl = document.getElementById('modalBadge');
            badgeEl.textContent = config.badge;
            badgeEl.className = `inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black tracking-widest uppercase mb-1 bg-${config.color}-100 text-${config.color}-700`;

            // Update Icon Color
            const iconContainer = document.getElementById('modalIconContainer_new');
            iconContainer.className = `w-10 h-10 rounded-xl bg-${config.color}-50 text-${config.color}-600 flex items-center justify-center shadow-sm border border-${config.color}-100`;
            iconContainer.innerHTML = `<i class="fas ${config.icon} text-lg"></i>`;

            // Handle PDF Attachment and Layout Split
            const pdfArea = document.getElementById('pdfViewerArea');
            const pdfIframe = document.getElementById('pdfIframe');
            const contentPane = document.getElementById('contentPane');
            const downloadLink = document.getElementById('modalDownloadLink');

            if (data.attachment) {
                pdfArea.classList.remove('hidden');
                pdfIframe.src = data.attachment;
                downloadLink.classList.remove('hidden');
                downloadLink.href = data.attachment;
                contentPane.classList.remove('lg:w-full');
                contentPane.classList.add('lg:w-[35%]');
            } else {
                pdfArea.classList.add('hidden');
                pdfIframe.src = '';
                downloadLink.classList.add('hidden');
                contentPane.classList.remove('lg:w-[35%]');
                contentPane.classList.add('lg:w-full');
            }

            // Show modal
            const modal = document.getElementById('notificationPageDetailModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Lock scroll
        }

        function closeNotificationDetail() {
            const modal = document.getElementById('notificationPageDetailModal');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Unlock scroll

            // Mark as read if it wasn't read before
            if (currentNotificationId && !currentNotificationWasRead) {
                fetch(`/admin/notifications/${currentNotificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    // Reload page to update UI and stats
                    window.location.reload();
                });
            }

            // Reset tracking
            currentNotificationId = null;
            currentNotificationWasRead = false;
        }

        // Edit function
        function openEditModal(data) {
            const modal = document.getElementById('editNotificationModal');
            const form = modal.querySelector('form');

            // Set form action based on ID
            form.action = `/admin/notifications/${data.id}`;

            // Set values
            modal.querySelector('#editTitle').value = data.title;
            modal.querySelector('#editMessage').value = data.message;
            modal.querySelector('#editType').value = data.type;
            modal.querySelector('#editTarget').value = data.target;

            // Show old file name if exists
            const fileLabel = document.getElementById('fileNameEdit');
            if (data.fileName) {
                fileLabel.textContent = 'File hiện tại: ' + data.fileName;
                fileLabel.classList.add('text-blue-700');
            } else {
                fileLabel.textContent = 'Chưa có file đính kèm';
                fileLabel.classList.remove('text-blue-700');
            }

            // Clear the file input
            document.getElementById('attachmentEdit').value = '';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            const modal = document.getElementById('editNotificationModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Delete function
        function confirmDelete(id) {
            if (confirm('Bạn có chắc chắn muốn xóa thông báo này? Hành động này không thể hoàn tác.')) {
                const form = document.getElementById('deleteForm');
                form.action = `/admin/notifications/${id}`;
                form.submit();
            }
        }

        // File Selection Handler
        function handleFileSelect(input, type) {
            const fileName = input.files[0] ? input.files[0].name : '';
            const nameDisplay = document.getElementById('fileName' + type);
            const placeholder = document.getElementById('uploadPlaceholder' + type);
            const fileInfo = document.getElementById('fileInfo' + type);

            if (fileName) {
                if (nameDisplay) nameDisplay.textContent = fileName;
                if (placeholder) placeholder.classList.add('hidden');
                if (fileInfo) fileInfo.classList.remove('hidden');

                // Visual feedback for edit modal
                if (type === 'Edit') {
                    document.getElementById('fileNameEdit').textContent = 'Đã chọn: ' + fileName;
                }
            } else {
                if (placeholder) placeholder.classList.remove('hidden');
                if (fileInfo) fileInfo.classList.add('hidden');
                if (type === 'Edit') {
                    document.getElementById('fileNameEdit').textContent = 'Chưa chọn file mới';
                }
            }
        }
    </script>
@endsection
