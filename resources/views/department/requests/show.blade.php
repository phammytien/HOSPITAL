@extends('layouts.department')

@section('title', 'Chi tiết yêu cầu')
@section('header_title', $request->request_code)
@section('page-subtitle', 'Chi tiết yêu cầu mua hàng')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('department.requests.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
        
        <div class="flex items-center space-x-3">
            @if($request->status == 'DRAFT')
            <form action="{{ route('department.requests.submit', $request->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Bạn có chắc muốn gửi yêu cầu này để duyệt?')"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-paper-plane mr-2"></i> Gửi duyệt
                </button>
            </form>
            <a href="{{ route('department.requests.edit', $request->id) }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Chỉnh sửa
            </a>
            @elseif($request->status == 'SUBMITTED')
            <form action="{{ route('department.requests.withdraw', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn muốn rút yêu cầu này về nháp để chỉnh sửa?');">
                @csrf
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    <i class="fas fa-undo mr-2"></i> Rút yêu cầu
                </button>
            </form>
            @elseif($request->status == 'REJECTED')
            <a href="{{ route('department.requests.edit', $request->id) }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-redo mr-2"></i> Yêu cầu làm lại
            </a>
            @endif
            
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
        @endif">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold 
                    @if($request->status == 'DRAFT') text-gray-900
                    @elseif($request->status == 'SUBMITTED') text-blue-900
                    @elseif($request->status == 'APPROVED') text-green-900
                    @elseif($request->status == 'REJECTED') text-red-900
                    @endif">
                    @if($request->status == 'DRAFT')
                        <i class="fas fa-file-alt mr-2"></i> Yêu cầu đang ở trạng thái nháp
                    @elseif($request->status == 'SUBMITTED')
                        <i class="fas fa-clock mr-2"></i> Yêu cầu đang chờ phê duyệt
                    @elseif($request->status == 'APPROVED')
                        <i class="fas fa-check-circle mr-2"></i> Yêu cầu đã được phê duyệt
                    @elseif($request->status == 'REJECTED')
                        <i class="fas fa-times-circle mr-2"></i> Yêu cầu đã bị từ chối
                    @endif
                </h3>
                <p class="text-sm mt-1
                    @if($request->status == 'DRAFT') text-gray-600
                    @elseif($request->status == 'SUBMITTED') text-blue-600
                    @elseif($request->status == 'APPROVED') text-green-600
                    @elseif($request->status == 'REJECTED') text-red-600
                    @endif">
                    Mã yêu cầu: {{ $request->request_code }} • 
                    Ngày tạo: {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}
                </p>
            </div>
            @if($request->status == 'DRAFT')
            <span class="badge badge-draft text-lg px-6 py-2">Nháp</span>
            @elseif($request->status == 'SUBMITTED')
            <span class="badge badge-submitted text-lg px-6 py-2">Chờ duyệt</span>
            @elseif($request->status == 'APPROVED')
            <span class="badge badge-approved text-lg px-6 py-2">Đã duyệt</span>
            @elseif($request->status == 'REJECTED')
            <span class="badge badge-rejected text-lg px-6 py-2">Từ chối</span>
            @endif
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
                            @php
                                $imageUrl = getProductImage($item->product->id);
                            @endphp
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $item->product->product_name ?? 'N/A' }}" class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                    <i class="fas fa-box text-gray-400 text-2xl"></i>
                                </div>
                            @endif
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
                    <h3 class="text-lg font-bold text-gray-900">Lịch sử mua hàng & Sơ sánh</h3>
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
                                    Chuyển trạng thái từ <span class="font-semibold">{{ $workflow->from_status }}</span> 
                                    sang <span class="font-semibold">{{ $workflow->to_status }}</span>
                                </p>
                                @if($workflow->action_note)
                                <p class="text-sm text-gray-500 mt-2 p-3 bg-gray-50 rounded-lg">{{ $workflow->action_note }}</p>
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
                <h3 class="text-lg font-bold text-gray-900 mb-4">Giải trình người yêu cầu</h3>
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
                <h3 class="text-lg font-bold text-gray-900 mb-4">Ngân sách ({{ $request->period }})</h3>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Tổng chi phí</span>
                        <span class="font-bold text-gray-900">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-900 font-semibold">Tổng cộng</span>
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                    </div>
                    

                </div>
            </div>
            
            <!-- Thông tin khoa -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin khoa</h3>
                
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
                        <p class="text-gray-500 mb-1">Email</p>
                        <p class="font-semibold text-gray-900">{{ $request->requester->email ?? 'N/A' }}</p>
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
