@extends('layouts.department')

@section('title', 'Tổng quan')
@section('page-title', 'Xin chào, ' . Auth::user()->full_name . ' 👋')
@section('page-subtitle', 'Quản lý các yêu cầu mua sắm và theo dõi tình trạng đơn hàng của bạn.')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Đang chờ duyệt -->
            <div class="stat-card bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">Đang chờ duyệt</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
            </div>

            <!-- Đã được duyệt -->
            <div class="stat-card bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">Đã được duyệt</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
            </div>

            <!-- Đã hoàn thành -->
            <div class="stat-card bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flag-checkered text-teal-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">Đã hoàn thành</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
            </div>

            <!-- Vật tư đã nhận -->
            <div class="stat-card bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <button onclick="openReceivedItemsModal()" class="text-xs text-blue-600 hover:text-blue-700 font-medium hover:underline">
                        Xem <i class="fas fa-external-link-alt ml-1"></i>
                    </button>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">Vật tư đã nhận</h3>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totalItems, 0, ',', '.') }}</p>
            </div>

            <!-- Đã sử dụng ngân sách -->
            <div class="stat-card bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-purple-600 font-medium">
                        {{ $department && $department->budget_amount > 0 ? number_format((($usedBudget + $pendingBudget) / $department->budget_amount) * 100, 0) : 0 }}%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">Đã sử dụng</h3>
                <div class="flex flex-col">
                    <p class="text-xl font-bold text-gray-900 leading-tight">
                        {{ number_format($usedBudget, 0, ',', '.') }}đ
                    </p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        / {{ $department ? number_format($department->budget_amount, 0, ',', '.') : 0 }}đ
                    </p>
                </div>
                @if($pendingBudget > 0)
                    <div class="mt-2 text-xs text-orange-500 font-medium">
                        <i class="fas fa-clock mr-1"></i> Chờ duyệt:
                        {{ number_format($pendingBudget, 0, ',', '.') }}đ
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Yêu cầu gần đây -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Yêu cầu mua hàng</h3>
                        <p class="text-sm text-gray-500">Danh sách các yêu cầu gần đây của bạn</p>
                    </div>
                    <a href="{{ route('department.requests.index') }}"
                        class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="p-6">
                    @if($recentRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentRequests as $request)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span
                                                class="text-blue-600 font-bold text-sm">#{{ substr($request->request_code, -3) }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $request->request_code }}</h4>
                                            <p class="text-sm text-gray-500">
                                                {{ $request->items->count() }} sản phẩm •
                                                {{ $request->created_at?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @if($request->status == 'DRAFT')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Nháp</span>
                                        @elseif($request->status == 'SUBMITTED' || $request->status == 'PENDING' || $request->status == null)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Chờ duyệt</span>
                                        @elseif($request->status == 'APPROVED')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Đã duyệt</span>
                                        @elseif($request->status == 'COMPLETED' || $request->status == 'PAID')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-700">Hoàn thành</span>
                                        @elseif($request->status == 'REJECTED')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Từ chối</span>
                                        @endif

                                        <a href="{{ route('department.requests.show', $request->id) }}"
                                            class="text-gray-400 hover:text-blue-600">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Chưa có yêu cầu nào</p>
                            <a href="{{ route('department.requests.create') }}"
                                class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Tạo yêu cầu mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cập nhật mới nhất -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Cập nhật mới nhất</h3>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($latestNotifications as $notification)
                        @php
                            $config = match($notification->type) {
                                'error' => ['icon' => 'exclamation-circle', 'color' => 'red', 'bg' => 'red-50'],
                                'warning' => ['icon' => 'exclamation-triangle', 'color' => 'orange', 'bg' => 'orange-50'],
                                'important' => ['icon' => 'star', 'color' => 'purple', 'bg' => 'purple-50'],
                                default => ['icon' => 'info-circle', 'color' => 'blue', 'bg' => 'blue-50'],
                            };
                        @endphp
                        <div class="flex space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition" 
                             onclick="showNotifyModal('{{ $notification->id }}', '{{ addslashes($notification->title) }}', '{{ addslashes(strip_tags($notification->message)) }}', '{{ $notification->type }}', {{ $notification->is_read ? 'true' : 'false' }})">
                            <div class="w-8 h-8 bg-{{ $config['bg'] }} rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $config['icon'] }} text-{{ $config['color'] }}-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm text-gray-900 truncate">{{ $notification->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ strip_tags($notification->message) }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500">
                            <i class="far fa-bell-slash text-2xl mb-2"></i>
                            <p class="text-xs">Chưa có cập nhật nào</p>
                        </div>
                    @endforelse
                </div>
            <!-- Help Card -->
                    <div class="m-6 p-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white">
                        <h4 class="font-bold mb-2">Cần hỗ trợ gì?</h4>
                        <p class="text-sm text-teal-50 mb-4">Đội ngũ hỗ trợ luôn sẵn sàng giúp bạn giải đáp mọi thắc mắc.</p>
                        <button
                                onclick="openSupportModal()"
                                class="w-full bg-white text-blue-600 font-semibold py-2 rounded-lg hover:bg-blue-50 transition">
                            Liên hệ ngay
                        </button>
                    </div>
            </div>

            
        </div>


    </div>

<!-- Popup Modal: Vật tư đã nhận -->
<div id="receivedItemsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-3xl w-full max-h-[85vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-500 to-blue-600">
            <div>
                <h3 class="text-lg font-bold text-white">Danh sách vật tư đã nhận</h3>
                <p class="text-sm text-blue-100">Tổng: {{ number_format($totalItems, 0, ',', '.') }} sản phẩm</p>
            </div>
            <button onclick="closeReceivedItemsModal()" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(85vh-140px)]">
            @if(isset($receivedItems) && $receivedItems->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Số lượng</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ĐVT</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mã đơn</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($receivedItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $item->product->product_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->product->product_code ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-700">
                                    {{ number_format($item->quantity, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $item->product->unit ?? 'Cái' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-500">{{ $item->purchaseRequest->request_code ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <p>Chưa có vật tư nào được nhận</p>
                </div>
            @endif
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end bg-gray-50">
            <button onclick="closeReceivedItemsModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Đóng
            </button>
        </div>
    </div>
</div>

<!-- Popup Modal: Liên hệ IT Support -->
<div id="supportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-xl w-full max-h-[90vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-500 to-blue-600">
            <div>
                <h3 class="text-lg font-bold text-white">Liên hệ bộ phận IT</h3>
                <p class="text-sm text-blue-100">Gửi yêu cầu hỗ trợ kỹ thuật</p>
            </div>
            <button onclick="closeSupportModal()" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6">
            <form action="{{ route('support.send') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Name & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="name" value="{{ Auth::user()->full_name }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email liên hệ</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50" required>
                    </div>
                </div>

                <!-- Department (visible, read-only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ban / Khoa / Phòng</label>
                    <input type="text" value="{{ $department->department_name ?? 'Chưa xác định' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                    <input type="hidden" name="department_id" value="{{ Auth::user()->department_id }}">
                </div>
                
                <!-- Error Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vấn đề gặp phải</label>
                    <select name="error_type" id="supportErrorType" onchange="checkSupportErrorType(this)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">-- Chọn loại lỗi --</option>
                        <option value="Không thể đăng nhập">Không thể đăng nhập</option>
                        <option value="Quên mật khẩu nhưng không nhận được email">Quên mật khẩu nhưng không nhận được email</option>
                        <option value="Lỗi hiển thị giao diện">Lỗi hiển thị giao diện</option>
                        <option value="Hệ thống chạy chậm">Hệ thống chạy chậm</option>
                        <option value="Không thể tạo yêu cầu mua hàng">Không thể tạo yêu cầu mua hàng</option>
                        <option value="new_error">Lỗi mới phát sinh (Nhập tên lỗi mới)</option>
                        <option value="Khác (Vui lòng mô tả chi tiết)">Khác (Vui lòng mô tả chi tiết)</option>
                    </select>
                </div>

                <!-- New Error Input (Hidden by default) -->
                <div id="supportNewErrorDiv" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tên lỗi mới</label>
                    <input type="text" name="new_error_name" id="supportNewErrorName" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Nhập tên lỗi mới...">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mô tả chi tiết</label>
                    <textarea name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                              placeholder="Mô tả chi tiết sự cố bạn đang gặp phải..." required></textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeSupportModal()" 
                            class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openReceivedItemsModal() {
    document.getElementById('receivedItemsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReceivedItemsModal() {
    document.getElementById('receivedItemsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('receivedItemsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceivedItemsModal();
    }
});

// Support Modal Functions
function openSupportModal() {
    document.getElementById('supportModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSupportModal() {
    document.getElementById('supportModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function checkSupportErrorType(select) {
    const newErrorDiv = document.getElementById('supportNewErrorDiv');
    const newErrorInput = document.getElementById('supportNewErrorName');
    if (select.value === 'new_error') {
        newErrorDiv.classList.remove('hidden');
        newErrorInput.required = true;
    } else {
        newErrorDiv.classList.add('hidden');
        newErrorInput.required = false;
    }
}

// Close support modal when clicking outside
document.getElementById('supportModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeSupportModal();
    }
});
</script>
@endpush
@endsection