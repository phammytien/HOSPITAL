@extends('layouts.department')

@section('title', 'Chi tiết yêu cầu')
@section('header_title', $request->request_code)
@section('page-subtitle', 'Chi tiết yêu cầu mua hàng')

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printable-section,
            #printable-section * {
                visibility: visible;
            }

            #printable-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 30px;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 1.5cm;
                size: A4;
            }

            * {
                box-shadow: none !important;
            }
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .print-header h1 {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .print-header-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 13px;
            color: #666;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .print-table thead th {
            background-color: #f9fafb;
            padding: 12px 10px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .print-table tbody td {
            padding: 15px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            vertical-align: top;
        }

        .print-summary {
            margin: 30px 0;
            padding: 20px;
            background-color: #f9fafb;
        }

        .print-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .print-summary-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            margin-top: 10px;
            border-top: 2px solid #d1d5db;
            font-size: 16px;
            font-weight: bold;
        }

        .print-summary-total .amount {
            color: #2563eb;
            font-size: 22px;
        }

        .print-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 30px;
        }

        .print-signature-box {
            text-align: center;
            flex: 1;
        }

        .print-signature-box .title {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .print-signature-box .subtitle {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-bottom: 60px;
        }

        .print-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('department.requests.history') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
            </a>

            <div class="flex items-center space-x-3">
                @if(!$request->is_submitted)
                    <form action="{{ route('department.requests.submit', $request->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn có chắc muốn gửi yêu cầu này để duyệt?')"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm hover:shadow-md">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi duyệt
                        </button>
                    </form>
                    <a href="{{ route('department.requests.edit', $request->id) }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm hover:shadow-md">
                        <i class="fas fa-edit mr-2"></i> Chỉnh sửa
                    </a>

                @elseif($request->is_submitted && !$request->status)
                    <form action="{{ route('department.requests.withdraw', $request->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Bạn muốn rút yêu cầu này về nháp để chỉnh sửa?');">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition shadow-sm hover:shadow-md">
                            <i class="fas fa-undo mr-2"></i> Rút yêu cầu
                        </button>
                    </form>

                @elseif($request->status == 'REJECTED')
                    <a href="{{ route('department.requests.edit', $request->id) }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm hover:shadow-md">
                        <i class="fas fa-redo mr-2"></i> Yêu cầu làm lại
                    </a>
                @endif

                <button onclick="window.print()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-print mr-2"></i> In
                </button>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="bg-white rounded-xl p-6 border-l-4 shadow-sm no-print
            @if(!$request->is_submitted) border-gray-400 bg-gray-50
            @elseif($request->is_submitted && !$request->status) border-blue-400 bg-blue-50
            @elseif($request->status == 'APPROVED') border-green-400 bg-green-50
            @elseif($request->status == 'REJECTED') border-red-400 bg-red-50
            @endif">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold 
                        @if(!$request->is_submitted) text-gray-900
                        @elseif($request->is_submitted && !$request->status) text-blue-900
                        @elseif($request->status == 'APPROVED') text-green-900
                        @elseif($request->status == 'REJECTED') text-red-900
                        @endif">
                        @if(!$request->is_submitted)
                            <i class="fas fa-file-alt mr-2"></i> Yêu cầu đang ở trạng thái nháp
                        @elseif($request->is_submitted && !$request->status)
                            <i class="fas fa-clock mr-2"></i> Yêu cầu đang chờ phê duyệt
                        @elseif($request->status == 'APPROVED')
                            <i class="fas fa-check-circle mr-2"></i> Yêu cầu đã được phê duyệt
                        @elseif($request->status == 'REJECTED')
                            <i class="fas fa-times-circle mr-2"></i> Yêu cầu đã bị từ chối
                        @endif
                    </h3>
                    <p class="text-sm mt-1
                        @if(!$request->is_submitted) text-gray-600
                        @elseif($request->is_submitted && !$request->status) text-blue-600
                        @elseif($request->status == 'APPROVED') text-green-600
                        @elseif($request->status == 'REJECTED') text-red-600
                        @endif">
                        Mã yêu cầu: {{ $request->request_code }} •
                        Ngày tạo: {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </p>
                </div>

                @if(!$request->is_submitted)
                    <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-bold">Nháp</span>
                @elseif($request->is_submitted && !$request->status)
                    <span class="px-4 py-2 rounded-lg bg-blue-200 text-blue-800 font-bold">Chờ duyệt</span>
                @elseif($request->status == 'APPROVED')
                    <span class="px-4 py-2 rounded-lg bg-green-200 text-green-800 font-bold">Đã duyệt</span>
                @elseif($request->status == 'REJECTED')
                    <span class="px-4 py-2 rounded-lg bg-red-200 text-red-800 font-bold">Từ chối</span>
                @endif
            </div>
        </div>

                        <!-- Timeline Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 no-print mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-route text-blue-600 mr-2"></i> Tiến độ xử lý
            </h3>
            
            <div class="relative">
                @php
                    // Define Milestones
                    $milestones = [
                        ['id' => 'SUBMITTED', 'label' => 'Đã gửi yêu cầu', 'icon' => 'fa-paper-plane'],
                        ['id' => 'APPROVED', 'label' => 'Đã duyệt', 'icon' => 'fa-clipboard-check'],
                        ['id' => 'ORDERED', 'label' => 'Đã đặt hàng', 'icon' => 'fa-shopping-cart'],
                        ['id' => 'DELIVERING', 'label' => 'Đang giao hàng', 'icon' => 'fa-truck'],
                        ['id' => 'DELIVERED', 'label' => 'Đã nhận hàng', 'icon' => 'fa-box-open'],
                        ['id' => 'COMPLETED', 'label' => 'Hoàn thành', 'icon' => 'fa-check-circle'],
                    ];

                    $activeMilestones = [];
                    $latestStatus = '';
                    $isRejected = ($request->status == 'REJECTED');
                    $rejectionTime = $isRejected ? $request->updated_at : null;

                    // 1. Submitted
                    if ($request->is_submitted) {
                         $activeMilestones['SUBMITTED'] = [
                            'time' => $request->created_at,
                            'done' => true
                         ];
                         $latestStatus = 'SUBMITTED';
                    }

                    // 2. Approved or Rejected at this stage
                   $approvedLog = $request->workflows->where('to_status', 'APPROVED')->first();
                   
                   // If Rejected, we might still want to show the "Approved" step as Red/Failed if it was rejected *instead* of approved? 
                   // Or if it was approved THEN rejected later?
                   // Logic: If Approved exists, show it. If Rejected and NO Approved, show rejection here? 
                   // The user wants "full time" load: likely meaning show WHEN the stop happened.
                   
                   if ($approvedLog || $request->status == 'APPROVED' || $request->status == 'COMPLETED' || $request->purchaseOrder) {
                        $activeMilestones['APPROVED'] = [
                            'time' => $approvedLog ? $approvedLog->action_time : ($request->purchaseOrder ? $request->purchaseOrder->created_at->subMinutes(5) : null), 
                            'done' => true
                        ];
                        $latestStatus = 'APPROVED';
                    } elseif ($isRejected) {
                         // If rejected and NOT approved, we can mark this step as visited but failed?
                         // Or just keep it simpler: Timeline shows success path.
                    }
                    
                    // Order Related
                    $po = $request->purchaseOrder; 
                    if ($po) {
                        // 3. Ordered
                        if ($po->ordered_at || in_array($po->status, ['ORDERED', 'DELIVERING', 'DELIVERED', 'COMPLETED', 'RESOLVED', 'CANCELLED'])) {
                             $activeMilestones['ORDERED'] = ['time' => $po->ordered_at ?? $po->created_at, 'done' => true];
                             $latestStatus = 'ORDERED';
                        }
                        // 4. Delivering
                        if ($po->shipping_at || in_array($po->status, ['DELIVERING', 'DELIVERED', 'COMPLETED', 'RESOLVED'])) {
                             $activeMilestones['DELIVERING'] = ['time' => $po->shipping_at, 'done' => true];
                             $latestStatus = 'DELIVERING';
                        }
                        // 5. Delivered
                        if ($po->delivered_at || in_array($po->status, ['DELIVERED', 'COMPLETED', 'RESOLVED'])) {
                             $activeMilestones['DELIVERED'] = ['time' => $po->delivered_at, 'done' => true];
                             $latestStatus = 'DELIVERED';
                        }
                        // 6. Completed
                         if ($po->completed_at || $request->status == 'COMPLETED') {
                             $activeMilestones['COMPLETED'] = ['time' => $po->completed_at ?? $request->updated_at, 'done' => true];
                             $latestStatus = 'COMPLETED';
                        }
                    } else {
                        // Manual Completion without PO
                        if ($request->status == 'COMPLETED') {
                             $activeMilestones['COMPLETED'] = ['time' => $request->updated_at, 'done' => true];
                             $latestStatus = 'COMPLETED';
                        }
                    }
                @endphp

                <div class="hidden sm:block">
                     <div class="flex items-center justify-between w-full">
                        @foreach($milestones as $index => $step)
                            @php
                                $isActive = isset($activeMilestones[$step['id']]);
                                $stepTime = $isActive ? $activeMilestones[$step['id']]['time'] : null;
                                
                                // Color Logic
                                $circleClass = 'bg-gray-200 text-gray-400';
                                $lineClass = 'bg-gray-200';
                                
                                if ($isActive) {
                                    $circleClass = 'bg-green-600 text-white';
                                    $lineClass = 'bg-green-600';
                                } else if ($isRejected && $index == count($activeMilestones)) {
                                     // If this is the *next* step after the last active one, and request is rejected
                                     $circleClass = 'bg-red-600 text-white';
                                     $lineClass = 'bg-red-200';
                                     // Use rejection time
                                     $stepTime = $rejectionTime;
                                     // Change icon to X?
                                     // For now keep standard icon but RED container
                                }
                            @endphp

                            <div class="relative flex flex-col items-center flex-1">
                                <!-- Line Connector -->
                                @if($index > 0)
                                    <div class="absolute top-5 right-[50%] w-full h-1 {{ $isActive ? 'bg-green-600' : ($isRejected && $index <= count($activeMilestones) ? 'bg-red-200' : 'bg-gray-200') }}"></div>
                                @endif

                                <!-- Icon Circle -->
                                <div class="z-10 flex items-center justify-center w-10 h-10 rounded-full border-2 border-white {{ $circleClass }}">
                                    @if($isRejected && !$isActive && $index == count($activeMilestones))
                                        <i class="fas fa-times"></i>
                                    @else
                                        <i class="fas {{ $step['icon'] }}"></i>
                                    @endif
                                </div>
                                
                                <!-- Label -->
                                <div class="mt-2 text-center">
                                    <p class="text-xs font-bold {{ $isActive ? 'text-gray-900' : ($isRejected && !$isActive && $index == count($activeMilestones) ? 'text-red-600' : 'text-gray-400') }}">
                                        {{ $isRejected && !$isActive && $index == count($activeMilestones) ? 'Đã hủy/Từ chối' : $step['label'] }}
                                    </p>
                                    @if($stepTime)
                                        <p class="text-[10px] text-gray-500">{{ $stepTime->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Mobile Vertical Timeline -->
                <div class="sm:hidden space-y-4">
                     @foreach($milestones as $index => $step)
                         @php 
                            $isActive = isset($activeMilestones[$step['id']]); 
                            $isNextRejected = ($isRejected && !$isActive && $index == count($activeMilestones));
                         @endphp
                        <div class="flex items-start">
                             <div class="flex flex-col items-center mr-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $isActive ? 'bg-green-600 text-white' : ($isNextRejected ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-400') }}">
                                    <i class="fas {{ $isNextRejected ? 'fa-times' : $step['icon'] }} text-xs"></i>
                                </div>
                                @if(!$loop->last)
                                    <div class="h-6 w-0.5 {{ isset($activeMilestones[$milestones[$index+1]['id']]) ? 'bg-green-600' : 'bg-gray-200' }} my-1"></div>
                                @endif
                             </div>
                             <div>
                                 <p class="text-sm font-bold {{ $isActive ? 'text-gray-900' : ($isNextRejected ? 'text-red-900' : 'text-gray-400') }}">
                                     {{ $isNextRejected ? 'Đã hủy/Từ chối' : $step['label'] }}
                                 </p>
                                 @if($isActive)
                                    <p class="text-xs text-gray-500">{{ $activeMilestones[$step['id']]['time']->format('d/m/Y H:i') }}</p>
                                 @elseif($isNextRejected)
                                     <p class="text-xs text-gray-500">{{ $rejectionTime ? $rejectionTime->format('d/m/Y H:i') : '' }}</p>
                                 @endif
                             </div>
                        </div>
                     @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- PRINTABLE SECTION -->
                <div id="printable-section" class="bg-white rounded-xl border border-gray-200 overflow-hidden p-6">

                    <!-- Product Table -->
                    <table class="print-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Hình ảnh</th>
                                <th>Chi tiết sản phẩm</th>
                                <th style="width: 100px; text-align: center;">Số lượng</th>
                                <th style="width: 120px; text-align: right;">Đơn giá</th>
                                <th style="width: 120px; text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->items as $item)
                                <tr>
                                    <td style="text-align: center;">
                                        @php
                                            $imageUrl = getProductImage($item->product->id);
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product->product_name ?? 'N/A' }}"
                                                style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #e5e7eb;">
                                        @else
                                            <div
                                                style="width: 60px; height: 60px; background-color: #f3f4f6; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb;">
                                                <i class="fas fa-box" style="color: #9ca3af; font-size: 20px;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #111827; margin-bottom: 4px;">
                                            {{ $item->product->product_name ?? 'N/A' }}</div>
                                        <div style="font-size: 12px; color: #6b7280;">Loại:
                                            {{ $item->product->category->category_name ?? 'N/A' }}</div>
                                        <div style="font-size: 11px; color: #9ca3af;">Mã thuốc:
                                            {{ $item->product->product_code ?? 'N/A' }}</div>
                                    </td>
                                    <td style="text-align: center; font-weight: 600;">
                                        {{ number_format($item->quantity, 2) }} {{ $item->product->unit ?? 'Hộp' }}
                                    </td>
                                    <td style="text-align: right; font-weight: 500;">
                                        {{ number_format($item->expected_price, 0, ',', '.') }} đ
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #2563eb;">
                                        {{ number_format($item->quantity * $item->expected_price, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Summary Section -->
                    <div class="print-summary">
                        <div class="print-summary-total">
                            <span>Tổng cộng:</span>
                            <span class="amount">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>
                </div>

                <!-- Workflow History & Comparison (No Print) -->
                <div class="bg-white rounded-xl border border-gray-200 no-print">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Lịch sử & So sánh</h3>
                    </div>

                    <div class="p-6">
                        <!-- Workflow List -->
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">Lịch sử xử lý</h4>
                        @if($request->workflows->count() > 0)
                            <div class="space-y-6 mb-8 relative pl-2">
                                <div class="absolute top-0 bottom-0 left-[19px] w-0.5 bg-gray-100"></div>
                                @php
                                    $statusMap = [
                                        'DRAFT' => 'Nháp',
                                        'SUBMITTED' => 'Đã gửi yêu cầu',
                                        'PENDING' => 'Chờ xử lý',
                                        'APPROVED' => 'Đã duyệt',
                                        'REJECTED' => 'Đã từ chối',
                                        'COMPLETED' => 'Hoàn thành',
                                        'CANCELLED' => 'Đã hủy',
                                        'ORDERED' => 'Đã đặt hàng',
                                        'DELIVERING' => 'Đang giao hàng',
                                        'DELIVERED' => 'Đã nhận hàng',
                                    ];
                                @endphp
                                @foreach($request->workflows as $workflow)
                                    <div class="relative flex items-start space-x-4">
                                        <div
                                            class="z-10 w-10 h-10 rounded-full border-4 border-white shadow-sm flex items-center justify-center flex-shrink-0 
                                            {{ $workflow->to_status == 'APPROVED' ? 'bg-green-100 text-green-600' : 'bg-blue-50 text-blue-600' }}">
                                            <i class="fas {{ $workflow->to_status == 'APPROVED' ? 'fa-check' : 'fa-user' }}"></i>
                                        </div>
                                        <div class="flex-1 bg-gray-50 rounded-lg p-3">
                                            <div class="flex items-start justify-between mb-1">
                                                <div>
                                                    <h4 class="font-bold text-gray-900 text-sm">
                                                        {{ $workflow->actionBy->full_name ?? 'N/A' }}
                                                        @if($workflow->to_status == 'APPROVED')
                                                            <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full border border-green-200">Người duyệt</span>
                                                        @endif
                                                    </h4>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <span class="">{{ $statusMap[$workflow->from_status] ?? $workflow->from_status }}</span>
                                                        <i class="fas fa-arrow-right text-xs text-gray-400 mx-1"></i>
                                                        <span class="font-semibold {{ $workflow->to_status == 'APPROVED' ? 'text-green-700' : '' }}">{{ $statusMap[$workflow->to_status] ?? $workflow->to_status }}</span>
                                                    </p>
                                                    @if($workflow->action_note)
                                                        <p class="text-xs text-gray-500 mt-2 italic">
                                                            "{{ $workflow->action_note }}"</p>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2">{{ $workflow->action_time->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 mb-8">
                                <p class="text-gray-500 text-sm">Chưa có lịch sử thay đổi</p>
                            </div>
                        @endif

                        <!-- Product Comparison -->
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">So sánh sản phẩm (Cùng kỳ tháng trước)</h4>
                        <div class="space-y-4">
                             @foreach($request->items as $item)
                                @php
                                    $prevOrderItems = \App\Models\PurchaseOrderItem::where('product_id', $item->product_id)
                                        ->whereHas('purchaseOrder', function($q) use ($request) {
                                            $q->where('department_id', $request->department_id)
                                              ->where('created_at', '<', $request->created_at)
                                              ->where('created_at', '>=', $request->created_at->subDays(30));
                                        })
                                        ->with('purchaseOrder')
                                        ->orderByDesc('id')
                                        ->limit(1)
                                        ->get();

                                    $prevItem = $prevOrderItems->first();
                                @endphp
                                <div class="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                             <div class="w-10 h-10 rounded bg-indigo-50 flex items-center justify-center mr-3 text-indigo-600">
                                                 <i class="fas fa-cube"></i>
                                             </div>
                                             <div>
                                                 <p class="font-bold text-gray-900">{{ $item->product->product_name }}</p>
                                                 <p class="text-xs text-gray-500">Giá hiện tại: <span class="font-semibold text-gray-800">{{ number_format($item->expected_price) }} đ</span></p>
                                             </div>
                                        </div>
                                        @if($prevItem)
                                            <div class="text-right">
                                                 <a href="{{ route('department.dept_orders.show', $prevItem->purchaseOrder->id) }}" class="text-xs text-blue-600 hover:underline flex items-center justify-end mb-1">
                                                     Xem đơn cũ <i class="fas fa-external-link-alt ml-1"></i>
                                                 </a>
                                                 <span class="text-xs text-gray-500">{{ $prevItem->purchaseOrder->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($prevItem)
                                        <div class="grid grid-cols-2 gap-4 text-xs mt-3 pt-3 border-t border-gray-100">
                                            <div>
                                                <span class="text-gray-500 block mb-1">So sánh giá</span>
                                                @php 
                                                    $diff = $item->expected_price - ($prevItem->unit_price ?? 0);
                                                    $percent = $prevItem->unit_price > 0 ? ($diff / $prevItem->unit_price) * 100 : 0;
                                                @endphp
                                                <div class="flex items-center">
                                                    <span class="font-semibold text-gray-700 mr-2">{{ number_format($prevItem->unit_price ?? 0) }} đ</span>
                                                    <span class="{{ $diff > 0 ? 'text-red-500' : ($diff < 0 ? 'text-green-500' : 'text-gray-400') }} font-bold">
                                                        {{ $diff > 0 ? '+' : '' }}{{ number_format($diff) }} đ ({{ number_format($percent, 1) }}%)
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 block mb-1">Tốc độ giao hàng (Đơn trước)</span>
                                                @if($prevItem->purchaseOrder->delivered_at && $prevItem->purchaseOrder->ordered_at)
                                                    @php
                                                        $duration = $prevItem->purchaseOrder->ordered_at->diff($prevItem->purchaseOrder->delivered_at);
                                                        $durationString = $duration->days > 0 ? $duration->days . ' ngày ' . $duration->h . ' giờ' : $duration->h . ' giờ ' . $duration->i . ' phút';
                                                    @endphp
                                                    <span class="font-semibold text-gray-800"><i class="fas fa-clock text-gray-400 mr-1"></i> {{ $durationString }}</span>
                                                @else
                                                    <span class="text-gray-400 italic">--</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                                            <span class="text-xs text-gray-400 italic">Không tìm thấy đơn hàng tương tự trong 30 ngày qua</span>
                                        </div>
                                    @endif
                                </div>
                             @endforeach
                        </div>

                    </div>
                </div>

                <!-- Note (No Print) -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 no-print">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Giải trình người yêu cầu</h3>
                    <div class="p-4 bg-gray-50 rounded-lg space-y-4">
                        @if($request->note)
                            <div>
                                <h4 class="font-semibold text-gray-800 text-sm mb-1">Lý do chung:</h4>
                                <p class="text-gray-700">{{ $request->note }}</p>
                            </div>
                        @endif

                        @php
                            $hasItemNotes = $request->items->contains(function($item) {
                                return !empty($item->note);
                            });
                        @endphp

                        @if($hasItemNotes)
                            <div class="pt-3 border-t border-gray-200">
                                <h4 class="font-semibold text-gray-800 text-sm mb-2">Chi tiết từng sản phẩm:</h4>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($request->items as $item)
                                        @if($item->note)
                                            <li class="text-gray-700 text-sm">
                                                <span class="font-medium">{{ $item->product->product_name ?? 'Sản phẩm' }}:</span> 
                                                <span class="italic text-gray-600">{{ $item->note }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if(!$request->note && !$hasItemNotes)
                             <p class="text-gray-500 italic">Không có nội dung giải trình.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar (No Print) -->
            <div class="space-y-6 no-print">
                <!-- Budget -->
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
                            <span class="text-2xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }}
                                VNĐ</span>
                        </div>
                    </div>
                </div>

                <!-- Department Info -->
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