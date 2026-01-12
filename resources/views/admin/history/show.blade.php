@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu')
@section('page-title', 'Chi tiết yêu cầu #' . $request->request_code)
@section('header_title', $request->request_code)
@section('page-subtitle', 'Chi tiết lịch sử yêu cầu mua hàng')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.history') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại lịch sử
        </a>
        
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-print mr-2"></i> In
            </button>
        </div>
    </div>
    
    <!-- Status Banner -->
    <div class="bg-white rounded-xl p-6 border-l-4 
        @if($request->status == 'DRAFT') border-gray-400 bg-gray-50
        @elseif($request->status == 'SUBMITTED') border-blue-400 bg-blue-50
        @elseif($request->status == 'APPROVED') border-green-400 bg-green-50
        @elseif($request->status == 'REJECTED') border-red-400 bg-red-50
        @elseif($request->status == 'COMPLETED') border-teal-400 bg-teal-50
        @elseif($request->status == 'PAID') border-purple-400 bg-purple-50
        @elseif($request->status == 'DELIVERED') border-indigo-400 bg-indigo-50
        @elseif($request->status == 'CANCELLED') border-gray-600 bg-gray-100
        @endif">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold 
                    @if($request->status == 'DRAFT') text-gray-900
                    @elseif($request->status == 'SUBMITTED') text-blue-900
                    @elseif($request->status == 'APPROVED') text-green-900
                    @elseif($request->status == 'REJECTED') text-red-900
                    @else text-gray-900
                    @endif">
                    @if($request->status == 'APPROVED')
                        <i class="fas fa-check-circle mr-2"></i> Yêu cầu đã được phê duyệt
                    @elseif($request->status == 'REJECTED')
                        <i class="fas fa-times-circle mr-2"></i> Yêu cầu đã bị từ chối
                    @else
                        <i class="fas fa-info-circle mr-2"></i> Trạng thái: {{ $request->status }}
                    @endif
                </h3>
                <p class="text-sm mt-1 text-gray-600">
                    Mã yêu cầu: {{ $request->request_code }} • 
                    Ngày tạo: {{ $request->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <span class="px-6 py-2 rounded-lg text-lg font-bold
                @if($request->status == 'APPROVED') bg-green-100 text-green-800
                @elseif($request->status == 'REJECTED') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ get_request_status_label($request->status) == $request->status ? get_status_label($request->status) : get_request_status_label($request->status) }}
            </span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Chi tiết sản phẩm -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Chi tiết sản phẩm</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($request->items as $item)
                    <div class="p-6">
                        <div class="flex gap-4">
                            <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                <i class="fas fa-box text-3xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-1">{{ $item->product->product_name ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-500 mb-3">{{ $item->product->category->category_name ?? 'N/A' }}</p>
                                
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500 mb-1">Số lượng</p>
                                        <p class="font-semibold text-gray-900">{{ number_format($item->quantity, 2) }} {{ $item->product->unit ?? '' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 mb-1">Đơn giá</p>
                                        <p class="font-semibold text-gray-900">{{ number_format($item->expected_price, 0, ',', '.') }} đ</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 mb-1">Thành tiền</p>
                                        <p class="font-bold text-blue-600">{{ number_format($item->quantity * $item->expected_price, 0, ',', '.') }} đ</p>
                                    </div>
                                </div>
                                
                                @if($item->reason)
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-700"><strong>Lý do:</strong> {{ $item->reason }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Lịch sử & Workflow -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Lịch sử xử lý</h3>
                </div>
                
                <div class="p-6">
                    @if($request->workflows->count() > 0)
                    <div class="space-y-4">
                        @foreach($request->workflows as $workflow)
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-gray-900">{{ $workflow->actionBy->full_name ?? 'N/A' }}</h4>
                                    <span class="text-sm text-gray-500">{{ $workflow->action_time->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    Chuyển trạng thái từ <span class="font-semibold">{{ get_request_status_label($workflow->from_status) == $workflow->from_status ? get_status_label($workflow->from_status) : get_request_status_label($workflow->from_status) }}</span> 
                                    sang <span class="font-semibold">{{ get_request_status_label($workflow->to_status) == $workflow->to_status ? get_status_label($workflow->to_status) : get_request_status_label($workflow->to_status) }}</span>
                                </p>
                                @if($workflow->action_note)
                                <p class="text-sm text-gray-500 mt-2 p-3 bg-gray-50 rounded-lg">
                                    {{ $workflow->action_note == 'Approved by buyer' ? 'Đã được bộ phận mua hàng phê duyệt' : $workflow->action_note }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Chưa có lịch sử thay đổi</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Giải trình người yêu cầu -->
            @if($request->note)
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Ghi chú / Giải trình</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700">{{ $request->note }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ngân sách -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Chi phí</h3>
                
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-900 font-semibold">Tổng cộng</span>
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin khoa -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin yêu cầu</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Khoa/Phòng ban</p>
                        <p class="font-semibold text-gray-900">{{ $request->department->department_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Người yêu cầu</p>
                        <p class="font-semibold text-gray-900">{{ $request->requester->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Kỳ yêu cầu</p>
                        <p class="font-semibold text-gray-900">{{ $request->period }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
