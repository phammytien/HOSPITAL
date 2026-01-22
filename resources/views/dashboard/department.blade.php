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
                    <button onclick="openReceivedItemsModal()"
                        class="text-xs text-blue-600 hover:text-blue-700 font-medium hover:underline">
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
            <!-- Left Column: Active & History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Yêu cầu gần đây -->
                <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Yêu cầu mua hàng</h3>
                        <p class="text-sm text-gray-500">Danh sách các yêu cầu gần đây của bạn</p>
                    </div>
                    <div class="flex items-center gap-3">
                       
                        <a href="{{ route('department.requests.index') }}"
                            class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if(isset($activeRequests) && $activeRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($activeRequests as $request)
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
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Nháp</span>
                                        @elseif($request->status == 'SUBMITTED' || $request->status == 'PENDING' || $request->status == null)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Chờ
                                                duyệt</span>
                                        @elseif($request->status == 'APPROVED')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Đã
                                                duyệt</span>
                                        @elseif($request->status == 'COMPLETED' || $request->status == 'PAID')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-700">Hoàn
                                                thành</span>
                                        @elseif($request->status == 'REJECTED')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Từ
                                                chối</span>
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

                <!-- Lịch sử yêu cầu -->
                <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Lịch sử yêu cầu</h3>
                        <p class="text-sm text-gray-500">Các yêu cầu đã hoàn thành hoặc bị từ chối</p>
                    </div>
                    <div class="flex items-center gap-3">
                         <a href="{{ route('department.requests.history') }}"
                            class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if(isset($requestHistory) && $requestHistory->count() > 0)
                        <div class="space-y-4">
                            @foreach($requestHistory as $request)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-600 font-bold text-sm">#{{ substr($request->request_code, -3) }}</span>
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
                                         @if($request->status == 'COMPLETED' || $request->status == 'PAID')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-700">Hoàn thành</span>
                                        @elseif($request->status == 'REJECTED' || $request->status == 'CANCELLED')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Từ chối</span>
                                        @endif

                                        <a href="{{ route('department.requests.show', $request->id) }}" class="text-gray-400 hover:text-blue-600">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Chưa có lịch sử yêu cầu nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

            <!-- Sidebar Column -->
                <div class="space-y-6">
                    <!-- Phản hồi gần đây -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                 <i class="fas fa-comment-alt text-blue-600"></i> Phản hồi gần đây
                            </h3>
                            <button onclick="openMonthOrdersModal()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                        <div class="p-6">
                            @if(isset($recentFeedbacks) && $recentFeedbacks->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentFeedbacks as $feedback)
                                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition cursor-pointer group"
                                             onclick="openFeedbackModal({{ $feedback->id }}, '{{ $feedback->purchaseOrder->order_code }}', `{{ $feedback->feedback_content }}`, {{ $feedback->purchase_order_id }}, '{{ $feedback->status }}')">
                                            <div class="flex-shrink-0">
                                                @if($feedback->feedbackBy && $feedback->feedbackBy->avatar)
                                                    <img src="{{ $feedback->feedbackBy->avatar }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                                                        {{ substr($feedback->feedbackBy->full_name ?? 'U', 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start">
                                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-700">
                                                        {{ $feedback->feedbackBy->full_name ?? 'Unknown' }}
                                                    </p>
                                                    <span class="text-[10px] text-gray-500 bg-white px-1.5 py-0.5 rounded border border-gray-100 whitespace-nowrap">{{ $feedback->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-sm text-gray-600 line-clamp-2 mt-0.5 leading-snug">{{ $feedback->feedback_content }}</p>
                                                <div class="flex items-center gap-2 mt-1.5">
                                                     <p class="text-xs text-blue-500 font-medium flex items-center gap-1">
                                                        <i class="fas fa-hashtag text-[10px]"></i> {{ $feedback->purchaseOrder->order_code }}
                                                    </p>
                                                    @if($feedback->status == 'RESOLVED')
                                                        <span class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded border border-green-200">Đã giải quyết</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-comments text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm">Chưa có phản hồi nào gần đây</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="p-6 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl text-white shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                        <div class="absolute bottom-0 left-0 -ml-4 -mb-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>

                        <h4 class="font-bold mb-2 text-lg flex items-center gap-2">
                            <i class="fas fa-headset"></i> Cần hỗ trợ?
                        </h4>
                        <p class="text-sm text-blue-100 mb-5 leading-relaxed">Đội ngũ IT hỗ trợ luôn sẵn sàng giúp bạn giải đáp mọi thắc mắc.</p>
                        <button onclick="openSupportModal()"
                                class="w-full bg-white text-blue-700 font-bold py-2.5 rounded-lg hover:bg-blue-50 transition shadow-sm flex items-center justify-center gap-2 text-sm">
                            Liên hệ ngay <i class="fas fa-arrow-right text-xs"></i>
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
            let currentFeedbackOrderId = null;

            function openFeedbackModal(id, orderCode, content, orderId, status = '') {
                document.getElementById('feedbackOrderCode').textContent = orderCode;
                document.getElementById('feedbackContent').textContent = content;
                document.getElementById('viewDetailLink').href = `/department/orders/${orderId}`;
                currentFeedbackOrderId = orderId;

                const replyContent = document.getElementById('replyContent');
                const submitBtn = document.getElementById('modalSubmitBtn');
                const feedbackStatusBadge = document.getElementById('feedbackStatusBadge');

                // If Resolved, disable input
                if (status === 'RESOLVED') {
                    replyContent.disabled = true;
                    replyContent.placeholder = 'Đã giải quyết - Không thể trả lời';
                    if(submitBtn) submitBtn.disabled = true;
                    if(submitBtn) submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                     replyContent.disabled = false;
                     replyContent.placeholder = 'Nhập câu trả lời...';
                     if(submitBtn) submitBtn.disabled = false;
                     if(submitBtn) submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }

                document.getElementById('feedbackModal').classList.remove('hidden');
                if(status !== 'RESOLVED') document.getElementById('replyContent').focus();
            }

            function closeFeedbackModal() {
                document.getElementById('feedbackModal').classList.add('hidden');
                document.getElementById('replyContent').value = '';
                currentFeedbackOrderId = null;
            }

            async function submitDashboardReply(e) {
                if(e) e.preventDefault();
                if (!currentFeedbackOrderId) return;

                const content = document.getElementById('replyContent').value.trim();
                // If disabled, don't submit
                 if (document.getElementById('replyContent').disabled) return;
                 
                if (!content) {
                    alert('Vui lòng nhập nội dung trả lời');
                    return;
                }

                try {
                    const response = await fetch(`/department/orders/${currentFeedbackOrderId}/feedback`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ content })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Đã gửi phản hồi thành công!');
                        closeFeedbackModal();
                        // Optional: Reload page to update "Recent Feedback" list
                        window.location.reload();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể gửi'));
                    }
                } catch (error) {
                    console.error(error);
                    alert('Lỗi kết nối');
                }
            }
        </script>
    @endpush

    <!-- Feedback Reply Modal -->
    <div id="feedbackModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeFeedbackModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-comment-dots text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Phản hồi đơn hàng <span id="feedbackOrderCode" class="font-bold"></span>
                            </h3>
                            <div class="mt-2 bg-gray-50 p-3 rounded-lg border border-gray-100 mb-4">
                                <p class="text-sm text-gray-600 italic" id="feedbackContent"></p>
                            </div>

                            <form id="dashboardReplyForm" onsubmit="submitDashboardReply(event)">
                                <div class="mt-2">
                                    <label for="replyContent" class="block text-sm font-medium text-gray-700 mb-1">Trả lời nhanh</label>
                                    <textarea id="replyContent" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nhập câu trả lời..."></textarea>
                                </div>

                                <div class="mt-4 flex justify-between items-center">
                                     <a id="viewDetailLink" href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Xem chi tiết đơn hàng <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="modalSubmitBtn" onclick="submitDashboardReply(event)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Gửi trả lời
                    </button>
                    <button type="button" onclick="closeFeedbackModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Modal: Danh sách đơn hàng trong tháng -->
    <div id="monthOrdersModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[85vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Đơn hàng trong tháng {{ date('m/Y') }}</h3>
                </div>
                <button onclick="closeMonthOrdersModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(85vh-80px)] p-4 bg-gray-50" id="monthOrdersList">
                <!-- Loading State -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-2"></i>
                    <p class="text-gray-500">Đang tải dữ liệu...</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
        function openMonthOrdersModal() {
             document.getElementById('monthOrdersModal').classList.remove('hidden');
             document.body.style.overflow = 'hidden';
             fetchMonthOrders();
        }

        function closeMonthOrdersModal() {
             document.getElementById('monthOrdersModal').classList.add('hidden');
             document.body.style.overflow = 'auto';
        }

        async function fetchMonthOrders() {
            const listContainer = document.getElementById('monthOrdersList');
            try {
                const response = await fetch("{{ route('department.dashboard.month_orders') }}");
                const data = await response.json();

                if (data.success && data.orders.length > 0) {
                    let html = '<div class="space-y-3">';
                    data.orders.forEach(order => {
                        html += `
                            <div onclick="window.location.href='/department/orders/${order.id}'" 
                                 class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer group">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-gray-900 text-base group-hover:text-blue-600 transition">
                                        ${order.order_code}
                                    </h4>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold ${order.status_class}">
                                        ${order.status_label}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm text-gray-500 mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-gray-400"></i> ${order.created_at_formatted}
                                    </div>
                                    <div class="font-medium">
                                        ${order.items_count} sản phẩm
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    listContainer.innerHTML = html;
                } else {
                    listContainer.innerHTML = `
                        <div class="text-center py-12 text-gray-400">
                             <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                             <p>Không có đơn hàng nào trong tháng này</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
                listContainer.innerHTML = '<p class="text-center text-red-500 py-4">Lỗi tải dữ liệu. Vui lòng thử lại sau.</p>';
            }
        }
        
        // Close modal on outside click
        document.getElementById('monthOrdersModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeMonthOrdersModal();
            }
        });

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