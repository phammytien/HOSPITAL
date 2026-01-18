@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu')
@section('page-title', 'Chi tiết yêu cầu #' . $request->request_code)
@section('header_title', $request->request_code)
@section('page-subtitle', 'Chi tiết lịch sử yêu cầu mua hàng')

@push('styles')
<style>
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }
        
        /* Reset page structure */
        html, body {
            background: white !important;
            height: auto !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 10pt; /* Smaller font for print */
            color: black !important;
        }

        /* Hide admin layout elements */
        header, aside {
            display: none !important;
        }
        
        /* Re-enable overflow for content */
        main {
            height: auto !important;
            overflow: visible !important;
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
        }
        
        /* Layout adjustments - Flex system for print */
        .grid {
            display: flex !important;
            flex-wrap: nowrap !important; /* Prevent wrapping */
            gap: 15px !important;
            align-items: flex-start;
        }
        
        /* Force specific widths for print */
        .print\:w-\[32\%\] {
            width: 30% !important;
            flex-shrink: 0;
        }
        
        .print\:w-\[66\%\] {
            width: 68% !important;
        }
        
        /* Compact spacing for print */
        .p-6, .p-4 {
            padding: 10px !important;
        }
        
        .space-y-6 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 10px !important;
        }
        
        .gap-6 {
            gap: 15px !important;
        }
        
        /* Remove shadows/borders/bg for cleaner print */
        .shadow-sm {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .bg-gray-50, .bg-blue-50, .bg-gray-100 {
            background-color: transparent !important;
        }
        
        /* Ensure tables/lists are compact */
        .divide-y > :not([hidden]) ~ :not([hidden]) {
            border-top-width: 1px;
            border-color: #eee;
        }
        
        /* Hide back button and print buttons again just in case */
        .no-print, button, a[href*="history"] {
            display: none !important;
        }
        
        /* Avoid page breaks inside cards */
        .rounded-xl {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $request->request_code }}</h1>
            <a href="{{ route('admin.history') }}" class="text-sm text-gray-500 hover:text-gray-900 flex items-center mt-1">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại lịch sử
            </a>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- <button class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-file-pdf mr-2"></i> Xuất PDF
            </button> -->
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i> In phiếu
            </button>
        </div>
    </div>
    
    <!-- Status Banner -->
    <div class="rounded-xl p-4 border
        @if($request->status == 'APPROVED') bg-[#effbf5] border-[#bbf3d6]
        @elseif($request->status == 'REJECTED') bg-[#fef2f2] border-[#fecaca] 
        @else bg-gray-50 border-gray-200 @endif flex items-center justify-between">
        
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center
                @if($request->status == 'APPROVED') bg-[#34d399] text-white
                @elseif($request->status == 'REJECTED') bg-red-500 text-white
                @else bg-gray-400 text-white @endif">
                <i class="fas @if($request->status == 'APPROVED') fa-check @elseif($request->status == 'REJECTED') fa-times @else fa-info @endif text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $request->status == 'APPROVED' ? 'Yêu cầu đã được phê duyệt' : ($request->status == 'REJECTED' ? 'Yêu cầu đã bị từ chối' : 'Yêu cầu đang chờ xử lý') }}
                </h3>
                <div class="text-sm text-gray-600 flex items-center gap-2 mt-0.5">
                    <span>Cập nhật lần cuối: {{ $request->updated_at->format('d/m/Y H:i') }}</span>
                    @if($request->status == 'APPROVED') 
                        <span>•</span>
                        <span>Người duyệt: Trưởng khoa</span>
                    @endif
                </div>
            </div>
        </div>

        <span class="px-4 py-1.5 rounded-full text-sm font-semibold
            @if($request->status == 'APPROVED') bg-[#d1fae5] text-[#047857]
            @elseif($request->status == 'REJECTED') bg-red-100 text-red-800
            @else bg-gray-200 text-gray-800 @endif">
            Trạng thái: {{ get_request_status_label($request->status) == $request->status ? get_status_label($request->status) : get_request_status_label($request->status) }}
        </span>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar (Left Column) -->
        <div class="space-y-6 print:w-[32%]">
            <!-- Thông tin chung -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                    <h3 class="text-lg font-bold text-gray-900">Thông tin chung</h3>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">ĐƠN VỊ YÊU CẦU</p>
                        <p class="font-medium text-gray-900 text-base">{{ $request->department->department_name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2">NGƯỜI TẠO</p>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($request->requester->full_name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $request->requester->full_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">TỔNG GIÁ TRỊ DỰ KIẾN</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }} <span class="text-lg align-top">đ</span></p>
                    </div>
                </div>
            </div>

            <!-- Lịch sử xử lý -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Lịch sử xử lý</h3>
                
                <div class="relative pl-2 border-l-2 border-gray-100 space-y-8">
                    @foreach($request->workflows as $workflow)
                    <div class="relative pl-6">
                        <!-- Dot -->
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white 
                            @if($workflow->to_status == 'APPROVED') bg-green-500
                            @elseif($workflow->to_status == 'REJECTED') bg-red-500
                            @elseif($workflow->to_status == 'SUBMITTED') bg-blue-500
                            @else bg-gray-400 @endif">
                        </div>
                        
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $workflow->to_status == 'APPROVED' ? 'Đã phê duyệt' : 
                                   ($workflow->to_status == 'REJECTED' ? 'Đã từ chối' : 
                                   ($workflow->to_status == 'SUBMITTED' ? 'Gửi yêu cầu' : 'Cập nhật trạng thái')) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $workflow->action_time->format('d/m/Y H:i') }}</p>
                            @if($workflow->to_status == 'SUBMITTED')
                                <p class="text-sm text-gray-600 mt-1">Người gửi: {{ $workflow->actionBy->full_name ?? 'N/A' }}</p>
                            @elseif($workflow->to_status == 'APPROVED')
                                <p class="text-sm text-gray-600 mt-1">Duyệt bởi: {{ $workflow->actionBy->full_name ?? 'Trưởng khoa' }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Created -->
                    <div class="relative pl-6">
                         <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white bg-blue-500"></div>
                         <div>
                            <p class="font-semibold text-gray-900">Tạo yêu cầu</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content (Right Column) -->
        <div class="lg:col-span-2 space-y-6 print:w-[66%]">
            <!-- Chi tiết sản phẩm -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Chi tiết sản phẩm</h3>
                    <span class="text-sm text-gray-500">{{ $request->items->count() }} mặt hàng</span>
                </div>
                
                {{-- Table Header for list --}}
                <div class="grid grid-cols-12 px-6 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                    <div class="col-span-5">Sản phẩm</div>
                    <div class="col-span-2 text-center">Số lượng</div>
                    <div class="col-span-2 text-right">Đơn giá</div>
                    <div class="col-span-3 text-right">Thành tiền</div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($request->items as $item)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="grid grid-cols-12 items-start">
                            <div class="col-span-5 flex gap-4 pr-4">
                                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 text-blue-600">
                                    <i class="fas fa-first-aid"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">{{ $item->product->product_name ?? 'N/A' }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->product->category->category_name ?? 'N/A' }}</p>
                                    
                                    @if($item->reason)
                                    <div class="mt-2 inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-600 text-[10px]">
                                        <i class="fas fa-align-left mr-1.5 text-gray-400"></i>
                                        Lý do: {{ $item->reason }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-span-2 text-center">
                                <p class="font-bold text-gray-900">{{ number_format($item->quantity, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $item->product->unit ?? 'Đơn vị' }}</p>
                            </div>
                            
                            <div class="col-span-2 text-right">
                                <p class="text-sm font-medium text-gray-600">{{ number_format($item->expected_price, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400">đ</p>
                            </div>
                            
                            <div class="col-span-3 text-right">
                                <p class="font-bold text-blue-600">{{ number_format($item->quantity * $item->expected_price, 0, ',', '.') }} <span class="text-xs underline">đ</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                {{-- Footer Total --}}
                <div class="px-6 py-6 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex justify-end items-center gap-6">
                        <span class="font-bold text-gray-900">Tổng cộng:</span>
                        <div class="text-right">
                             <p class="text-2xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }} <span class="text-lg">VNĐ</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú nội bộ -->
             @if($request->note)
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                 <div class="flex items-center gap-2 mb-4 text-gray-500">
                    <i class="fas fa-comment-alt"></i>
                    <h3 class="text-sm font-bold uppercase">Ghi chú nội bộ</h3>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 italic text-gray-600 border-l-4 border-gray-300">
                    "{{ $request->note }}"
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
