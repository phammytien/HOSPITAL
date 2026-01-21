@extends('layouts.buyer')

@section('title', 'Báo cáo')
@section('header_title', '')

@section('content')
<div class="space-y-6">
    <!-- Page Header with Logo -->
    <!-- Header & Tools Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Báo cáo & Thống kê</h2>
                <p class="text-sm text-gray-500 mt-1">Xem và xuất báo cáo mua hàng theo kỳ/quý</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('buyer.reports.export', array_filter(['period' => $selectedPeriod, 'department_id' => $departmentId, 'status' => $status])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg font-medium transition-colors border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Xuất Excel</span>
                </a>
                <a href="{{ route('buyer.reports.export-pdf', array_filter(['period' => $selectedPeriod, 'department_id' => $departmentId, 'status' => $status])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg font-medium transition-colors border border-rose-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Xuất PDF</span>
                </a>
            </div>
        </div>

        <div class="px-6 pb-6 bg-white">
            <form method="GET" action="{{ route('buyer.reports.index') }}" id="filterForm" class="flex flex-col md:flex-row md:items-center gap-3">
                <div class="text-sm font-bold text-gray-400 uppercase whitespace-nowrap mr-2">LỌC:</div>
                
                <!-- Department Filter -->
                <div class="w-full md:w-auto min-w-[200px]">
                    <select name="department_id" onchange="document.getElementById('filterForm').submit()" class="bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm border">
                        <option value="">-- Tất cả Khoa/Phòng --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-auto min-w-[200px]">
                    <select name="status" onchange="document.getElementById('filterForm').submit()" class="bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm border">
                        <option value="">-- Tất cả Trạng thái --</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>
                                {{ match($s) {
                                    'COMPLETED' => 'Hoàn thành',
                                    'CANCELLED' => 'Đã hủy',
                                    default => $s
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Period Filter -->
                <div class="w-full md:w-auto min-w-[200px]">
                    <select name="period" onchange="document.getElementById('filterForm').submit()" class="bg-white border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 shadow-sm border">
                        <option value="">-- Tất cả Kỳ/Quý --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p }}" {{ request('period') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filter Button -->
                <div>
                    <a href="{{ route('buyer.reports.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors whitespace-nowrap shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Tổng yêu cầu</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Hoàn thành</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cancelled -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Đã hủy</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Tỷ lệ hoàn thành</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['completion_rate'] }}%</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>


    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-16">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mã yêu cầu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Khoa/Phòng</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kỳ/Quý</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Số mặt hàng</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Tổng thành tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ngày đặt</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="reportTableBody">
                    @php 
                        $sttCounter = 1;
                        $groupedByRequest = collect();
                        foreach($requests as $order) {
                            if($order->purchaseRequest) {
                                $requestCode = $order->purchaseRequest->request_code ?? 'N/A';
                                if (!$groupedByRequest->has($requestCode)) {
                                    $groupedByRequest[$requestCode] = [
                                        'id' => $order->id,
                                        'request' => $order->purchaseRequest,
                                        'department' => $order->department,
                                        'order_date' => $order->order_date,
                                        'status' => $order->status,
                                        'items' => collect()
                                    ];
                                }
                                if ($order->purchaseRequest->items) {
                                    foreach($order->purchaseRequest->items as $item) {
                                        $groupedByRequest[$requestCode]['items']->push($item);
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    @forelse($groupedByRequest as $requestCode => $group)
                        @php
                            $totalAmount = 0;
                            $totalQuantity = 0;
                            foreach($group['items'] as $item) {
                                $totalAmount += ($item->product->unit_price ?? 0) * ($item->quantity ?? 0);
                                $totalQuantity += ($item->quantity ?? 0);
                            }
                            $itemCount = $group['items']->count();
                            $targetId = 'details-' . str_replace(['/', ' ', '.'], '-', $requestCode);
                        @endphp
                        
                        <!-- Master Row -->
                        <tr class="master-row cursor-pointer hover:bg-blue-50/50 transition-colors group" onclick="toggleAccordion('{{ $targetId }}', this)">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $sttCounter++ }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-blue-600">{{ $requestCode }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700">{{ $group['department']->department_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $group['request']->period ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold">{{ $itemCount }} mặt hàng</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="text-sm font-black text-gray-900">{{ number_format($totalAmount, 0, ',', '.') }} ₫</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $group['order_date'] ? $group['order_date']->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-widest uppercase {{ get_status_class($group['status']) }}">
                                    {{ get_status_label($group['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-transform duration-300 accordion-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </td>
                        </tr>

                        <!-- Details Row (Accordion Content) -->
                        <tr id="{{ $targetId }}" class="details-row hidden bg-gray-50/80 border-l-4 border-blue-500">
                            <td colspan="9" class="p-0">
                                <div class="overflow-hidden">
                                    <div class="px-6 py-4">
                                        <div class="bg-white rounded-xl border border-blue-100 shadow-sm overflow-hidden">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="bg-blue-50/50 border-b border-blue-100">
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-blue-800 uppercase tracking-tighter">Sản phẩm</th>
                                                        <th class="px-4 py-3 text-center text-xs font-bold text-blue-800 uppercase tracking-tighter w-24">Số lượng</th>
                                                        <th class="px-4 py-3 text-right text-xs font-bold text-blue-800 uppercase tracking-tighter w-32">Đơn giá</th>
                                                        <th class="px-4 py-3 text-right text-xs font-bold text-blue-800 uppercase tracking-tighter w-40">Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach($group['items'] as $item)
                                                        @php
                                                            $uPrice = $item->product->unit_price ?? 0;
                                                            $qty = $item->quantity ?? 0;
                                                            $sTotal = $uPrice * $qty;
                                                        @endphp
                                                        <tr class="hover:bg-blue-50/30 transition-colors">
                                                            <td class="px-4 py-3">
                                                                <div class="font-bold text-gray-800">{{ $item->product->product_name ?? 'N/A' }}</div>
                                                                <div class="text-[10px] text-gray-500 tracking-wider">{{ $item->product->product_code ?? '' }}</div>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <span class="font-bold text-gray-900">{{ number_format($qty, 0, '.', ',') }}</span>
                                                            </td>
                                                            <td class="px-4 py-3 text-right text-gray-600">
                                                                {{ number_format($uPrice, 0, '.', ',') }} ₫
                                                            </td>
                                                            <td class="px-4 py-3 text-right">
                                                                <span class="font-bold text-gray-900">{{ number_format($sTotal, 0, '.', ',') }} ₫</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-blue-50/20 border-t border-blue-100 italic">
                                                    <tr>
                                                        <td colspan="3" class="px-4 py-2 text-right text-xs font-bold text-blue-900 mr-2 uppercase">Cộng tổng:</td>
                                                        <td class="px-4 py-2 text-right text-sm font-black text-blue-600">
                                                            {{ number_format($totalAmount, 0, '.', ',') }} ₫
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Không tìm thấy báo cáo nào phù hợp</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-6xl shadow-2xl rounded-xl bg-white my-10">
            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Chi tiết đơn hàng</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="mt-6">
                <div class="flex justify-center items-center py-12">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAccordion(targetId, masterRow) {
    const detailsRow = document.getElementById(targetId);
    const icon = masterRow.querySelector('.accordion-icon');
    const isHidden = detailsRow.classList.contains('hidden');

    // Close all other rows
    document.querySelectorAll('.details-row').forEach(row => {
        if (row.id !== targetId) {
            row.classList.add('hidden');
        }
    });
    
    document.querySelectorAll('.accordion-icon').forEach(i => {
        if (i !== icon) {
            i.style.transform = 'rotate(0deg)';
        }
    });
    
    document.querySelectorAll('.master-row').forEach(row => {
        if (row !== masterRow) {
            row.classList.remove('bg-blue-50/80', 'border-l-4', 'border-blue-500');
        }
    });

    // Toggle target row
    if (isHidden) {
        detailsRow.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
        masterRow.classList.add('bg-blue-50/80', 'border-l-4', 'border-blue-500');
    } else {
        detailsRow.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
        masterRow.classList.remove('bg-blue-50/80', 'border-l-4', 'border-blue-500');
    }
}

function viewOrderDetails(orderId) {
    document.getElementById('orderModal').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = `
        <div class="flex justify-center items-center py-12">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    `;
    
    fetch(`/buyer/orders/${orderId}/details`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderOrderDetails(data.order);
            } else {
                document.getElementById('modalContent').innerHTML = '<p class="text-center text-red-600 py-4">Không thể tải thông tin đơn hàng</p>';
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('modalContent').innerHTML = '<p class="text-center text-red-600 py-4">Có lỗi xảy ra khi tải dữ liệu</p>';
        });
}

function renderOrderDetails(order) {
    const statusColors = {
        'PENDING': 'bg-yellow-100 text-yellow-800',
        'APPROVED': 'bg-blue-100 text-blue-800',
        'ORDERED': 'bg-purple-100 text-purple-800',
        'DELIVERING': 'bg-indigo-100 text-indigo-800',
        'DELIVERED': 'bg-teal-100 text-teal-800',
        'COMPLETED': 'bg-green-100 text-green-800',
        'CANCELLED': 'bg-red-100 text-red-800'
    };
    
    let itemsHtml = '';
    order.items.forEach((item, index) => {
        itemsHtml += `
            <div class="flex items-start gap-3 py-3 ${index > 0 ? 'border-t border-gray-100' : ''}">
                <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">${item.product_name}</p>
                    <p class="text-xs text-gray-500 mt-0.5">${item.category_name || 'N/A'}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">${item.quantity}</p>
                </div>
                <div class="text-right min-w-[120px]">
                    <p class="text-sm font-medium text-gray-900">${new Intl.NumberFormat('vi-VN').format(item.unit_price)} ₫</p>
                </div>
                <div class="text-right min-w-[120px]">
                    <p class="text-sm font-semibold text-gray-900">${new Intl.NumberFormat('vi-VN').format(item.total_price)} ₫</p>
                </div>
            </div>
        `;
    });
    
    // Calculate totals
    const subtotal = order.total_amount;
    const vatRate = 0.05; // 5% VAT
    const vatAmount = subtotal * vatRate;
    const total = subtotal;
    
    // Build workflows HTML
    let workflowsHtml = '';
    if (order.workflows && order.workflows.length > 0) {
        order.workflows.forEach(workflow => {
            const iconColor = workflow.action === 'APPROVED' ? 'green' : 
                            workflow.action === 'REJECTED' ? 'red' : 
                            workflow.action === 'COMPLETED' ? 'green' :
                            workflow.action === 'CANCELLED' ? 'red' :
                            workflow.action === 'PENDING' || workflow.action === 'DRAFT' ? 'blue' : 'gray';
            const icon = workflow.action === 'APPROVED' || workflow.action === 'COMPLETED' ? 'M5 13l4 4L19 7' : 
                        workflow.action === 'REJECTED' || workflow.action === 'CANCELLED' ? 'M6 18L18 6M6 6l12 12' : 
                        'M12 4v16m8-8H4';
            
            workflowsHtml += `
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-${iconColor}-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-${iconColor}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${workflow.action_text}</p>
                        <p class="text-xs text-gray-500 mt-0.5">${workflow.action_role}: ${workflow.action_by}</p>
                    </div>
                    <span class="text-xs text-gray-400">${workflow.created_at}</span>
                </div>
            `;
        });
    } else {
        workflowsHtml = '<p class="text-sm text-gray-500 text-center py-4">Chưa có lịch sử xử lý</p>';
    }
    
    const html = `
        <div class="grid grid-cols-12 gap-6">
            <!-- Left Column - Order Info -->
            <div class="col-span-4 space-y-4">
                <!-- General Info Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">THÔNG TIN CHUNG</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Mã đơn hàng</p>
                            <p class="text-sm font-semibold text-gray-900">${order.order_code}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Mã yêu cầu</p>
                            <p class="text-sm font-semibold text-gray-900">${order.request_code || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Trạng thái</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                ${order.status_text}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Khoa/Phòng</p>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900">${order.department_name}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Ngày đặt hàng</p>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-900">${order.order_date}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Summary Section -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <h4 class="text-xs font-semibold text-blue-900 uppercase mb-3">TỔNG THANH TOÁN</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tổng tiền hàng</span>
                            <span class="font-medium text-gray-900">${new Intl.NumberFormat('vi-VN').format(subtotal)} ₫</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Thuế VAT (0%)</span>
                            <span class="font-medium text-gray-900">${new Intl.NumberFormat('vi-VN').format(0)} ₫</span>
                        </div>
                        <div class="pt-2 border-t border-blue-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700">TỔNG THANH TOÁN</span>
                                <span class="text-xl font-bold text-blue-600">${new Intl.NumberFormat('vi-VN').format(total)} ₫</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Đã bao gồm VAT (0%)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Products & History -->
            <div class="col-span-8 space-y-4">
                <!-- Products List -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900">Danh sách sản phẩm</h4>
                            <span class="text-xs text-gray-500">${order.items.length} sản phẩm</span>
                        </div>
                    </div>
                    <div class="px-4">
                        <!-- Table Header -->
                        <div class="flex items-center gap-3 py-2 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase">
                            <div class="w-10"></div>
                            <div class="flex-1">Tên sản phẩm</div>
                            <div class="text-right w-16">Số lượng</div>
                            <div class="text-right min-w-[120px]">Đơn giá</div>
                            <div class="text-right min-w-[120px]">Thành tiền</div>
                        </div>
                        <!-- Items -->
                        ${itemsHtml}
                    </div>
                </div>
                
                <!-- Notes Section -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h5 class="text-sm font-semibold text-gray-900 mb-1">Ghi chú</h5>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                ${order.note || 'Không có ghi chú'}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Processing History -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-sm font-semibold text-gray-900">Lịch sử xử lý</h4>
                    </div>
                    <div class="p-4 space-y-3">
                        ${workflowsHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('modalContent').innerHTML = html;
}

function closeModal() {
    document.getElementById('orderModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('orderModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
