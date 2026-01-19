@extends('layouts.buyer')

@section('title', 'Danh sách Đơn đặt hàng')
@section('header_title', 'Danh sách Đơn đặt hàng')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    
    <!-- Tabs & Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <!-- Status Tabs -->
        <div class="border-b border-gray-100 bg-gray-50/50">
            <nav class="flex -mb-px px-4 gap-4 overflow-x-auto" aria-label="Tabs">
                @php
                    $currentStatus = request('status');
                    $tabs = [
                        ['label' => 'Tất cả', 'value' => '', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
                        ['label' => 'Đã duyệt', 'value' => 'CREATED', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Hoàn thành', 'value' => 'COMPLETED', 'icon' => 'M5 13l4 4L19 7'],
                        ['label' => 'Đã từ chối', 'value' => 'CANCELLED', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];
                @endphp

                @foreach($tabs as $tab)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $tab['value'], 'page' => 1]) }}"
                       class="flex items-center gap-2 py-4 px-4 text-sm font-medium border-b-2 transition-all duration-200 whitespace-nowrap {{ ($currentStatus == $tab['value']) ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}" />
                        </svg>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Additional Filters -->
        <div class="p-4 flex flex-wrap gap-6 items-center">
            <form action="{{ route('buyer.orders.index') }}" method="GET" class="flex flex-wrap gap-4 w-full">
                <input type="hidden" name="status" value="{{ request('status') }}">
                
                <div class="flex items-center gap-4">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Lọc:</span>
                    <div class="flex items-center gap-2">
                        <select name="department_id" onchange="this.form.submit()"
                            class="border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 bg-white border shadow-sm py-2 pl-3 pr-10">
                            <option value="">-- Tất cả Khoa/Phòng --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
        
                        <select name="period" onchange="this.form.submit()"
                            class="border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 bg-white border shadow-sm py-2 pl-3 pr-10">
                            <option value="">-- Tất cả Kỳ/Quý --</option>
                            @foreach($periods as $p)
                                <option value="{{ $p }}" {{ request('period') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>

                        <a href="{{ route('buyer.orders.index') }}"
                            class="inline-flex items-center px-6 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 shadow-sm">
                            Xóa lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã yêu cầu</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khoa/Phòng</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Tổng tiền</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr onclick="openModal('order-modal-{{ $order->id }}')" class="hover:bg-blue-50 transition cursor-pointer group">
                    <td class="px-6 py-4 text-sm font-medium text-blue-600">
                        {{ $order->order_code }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $order->purchaseRequest->request_code ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                        {{ $order->department->department_name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">
                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            // Display status from purchase_request, not purchase_order
                            $requestStatus = $order->purchaseRequest->status ?? $order->status;
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ get_status_class($requestStatus) }}">
                            {{ get_status_label($requestStatus) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                        Không có đơn hàng nào đã thanh toán.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
</div>

<!-- Order Detail Modals -->
@foreach($orders as $order)
<div id="order-modal-{{ $order->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('order-modal-{{ $order->id }}')"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Chi tiết đơn hàng <span class="text-blue-600">#{{ $order->order_code }}</span>
                    </h3>
                    <button type="button" onclick="closeModal('order-modal-{{ $order->id }}')" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="bg-gray-50 px-4 py-5 sm:p-6">
                <!-- Feedback Alert (If Exists) -->
                @if($order->feedbacks->isNotEmpty())
                    @php 
                        $feedback = $order->feedbacks->sortByDesc('id')->first();
                    @endphp
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-star text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Đánh giá từ Khoa: <span class="font-bold text-lg">{{ $feedback->rating }}/5</span> <i class="fas fa-star text-yellow-500 text-xs"></i>
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>{{ $feedback->feedback_content }}</p>
                                    <p class="mt-1 text-xs opacity-75">- {{ $feedback->feedback_date }} -</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rejection Alert -->
                @if($order->status == 'CANCELLED')
                    @php
                        $rejectionLog = null;
                        
                        // 1. Try to get from PurchaseFeedbacks (New way)
                        if ($order->purchaseRequest && $order->purchaseRequest->feedbacks && $order->purchaseRequest->feedbacks->isNotEmpty()) {
                            $feedback = $order->purchaseRequest->feedbacks->sortByDesc('id')->first();
                            if ($feedback) {
                                $rejectionLog = (object)[
                                    'action_note' => $feedback->feedback_content,
                                    'actionBy' => $feedback->user,
                                    'action_time' => $feedback->feedback_date
                                ];
                            }
                        }

                        // 2. Fallback to Workflows (Old way)
                        if (!$rejectionLog && $order->purchaseRequest && $order->purchaseRequest->workflows) {
                            $rejectionLog = $order->purchaseRequest->workflows
                                ->where('to_status', 'CANCELLED')
                                ->sortByDesc('id')
                                ->first();
                        }
                    @endphp
                    @if($rejectionLog)
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">
                                    Đơn hàng đã bị từ chối
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="font-medium">Lý do: "{{ $rejectionLog->action_note }}"</p>
                                    <p class="mt-1 text-xs opacity-80">
                                        Bởi: {{ $rejectionLog->actionBy->full_name ?? 'N/A' }} 
                                       - Lúc: {{ \Carbon\Carbon::now()->format('H:i d/m/Y') }}

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                     <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                        <p class="text-sm text-red-700 font-medium">Đơn hàng đã bị hủy (Không tìm thấy lý do chi tiết).</p>
                    </div>
                    @endif
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Order Info -->
                    <div class="md:col-span-1 space-y-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <h4 class="font-semibold text-gray-700 border-b pb-2 mb-3">Thông tin chung</h4>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="text-gray-500 text-xs uppercase font-bold tracking-wider">Ngày đặt hàng</dt>
                                    <dd class="font-medium text-gray-900 mt-0.5">
                                        <i class="far fa-calendar-alt text-blue-500 mr-1"></i>
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 text-xs uppercase font-bold tracking-wider">Khoa/Phòng</dt>
                                    <dd class="font-medium text-gray-900 mt-0.5">
                                        <i class="fas fa-hospital text-blue-500 mr-1"></i>
                                        {{ $order->department->department_name ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 text-xs uppercase font-bold tracking-wider">Người duyệt</dt>
                                    <dd class="font-medium text-gray-900 mt-0.5">
                                        <i class="fas fa-user-check text-blue-500 mr-1"></i>
                                        @php
                                            $confirmerName = $order->approver->full_name ?? null;
                                            
                                            if (!$confirmerName && $order->purchaseRequest) {
                                                $workflow = $order->purchaseRequest->workflows
                                                    ->where('to_status', 'APPROVED')
                                                    ->sortByDesc('id')
                                                    ->first();
                                                if ($workflow && $workflow->actionBy) {
                                                    $confirmerName = $workflow->actionBy->full_name;
                                                }
                                            }
                                            if (!$confirmerName) $confirmerName = 'N/A';
                                        @endphp
                                        {{ $confirmerName }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 text-xs uppercase font-bold tracking-wider">Trạng thái</dt>
                                    <dd class="mt-1">
                                        <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full {{ get_status_class($order->status) }}">
                                            {{ get_status_label($order->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="md:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                <h4 class="font-semibold text-gray-700">Danh sách sản phẩm</h4>
                            </div>
                            <div class="overflow-x-auto max-h-96">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2">Sản phẩm</th>
                                            <th class="px-4 py-2 text-center">ĐVT</th>
                                            <th class="px-4 py-2 text-center">SL</th>
                                            <th class="px-4 py-2 text-right">Đơn giá</th>
                                            <th class="px-4 py-2 text-right">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($order->items as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 font-medium text-gray-900">
                                                {{ $item->product->product_name ?? 'N/A' }}
                                                <div class="text-xs text-gray-400 font-normal">{{ $item->product->product_code ?? '' }}</div>
                                            </td>
                                            <td class="px-4 py-2 text-center">{{ $item->unit ?? $item->product->unit ?? '' }}</td>
                                            <td class="px-4 py-2 text-center font-semibold">{{ $item->quantity }}</td>
                                            <td class="px-4 py-2 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-blue-600">{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 font-bold text-gray-900">
                                        <tr>
                                            <td colspan="4" class="px-4 py-2 text-right uppercase text-xs">Tổng cộng</td>
                                            <td class="px-4 py-2 text-right text-base text-blue-700">{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button type="button" onclick="closeModal('order-modal-{{ $order->id }}')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            document.querySelectorAll('[id^="order-modal-"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
</script>
@endsection
