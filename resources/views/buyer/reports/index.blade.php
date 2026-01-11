@extends('layouts.buyer')

@section('title', 'Báo cáo')
@section('header_title', 'Danh sách Yêu cầu Mua hàng')

@section('content')
<div class="space-y-6">
    <!-- Page Header with Logo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-end mb-6">
            <!-- Export Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('buyer.reports.export', array_filter(['year' => $year, 'quarter' => $quarter, 'department_id' => $departmentId, 'status' => $status])) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Xuất Excel
                </a>
                <a href="{{ route('buyer.reports.export-pdf', array_filter(['year' => $year, 'quarter' => $quarter, 'department_id' => $departmentId, 'status' => $status])) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Xuất PDF
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('buyer.reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tất cả Khoa/Phòng</label>
                <select name="department_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tất cả Trạng thái</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả</option>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tất cả Kỳ/Quý</label>
                <select name="period" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Tất cả Kỳ/Quý --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p }}" {{ request('period') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter (hidden but included) -->


            <!-- Action Buttons -->
            <div class="flex gap-2 items-end">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition whitespace-nowrap">
                    Lọc
                </button>
                <a href="{{ route('buyer.reports.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition text-center whitespace-nowrap">
                    Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Tổng yêu cầu</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Chờ duyệt</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Đã duyệt</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Từ chối</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rejected_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Tỷ lệ duyệt</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['approval_rate'] }}%</p>
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mã đơn hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mã yêu cầu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Khoa/Phòng</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kỳ/Quý</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider text-right">Tổng tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ngày đặt</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-blue-600">{{ $order->order_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $order->purchaseRequest->request_code ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $order->department->department_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700 font-medium">{{ $order->purchaseRequest->period ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">{{ $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ get_status_class($order->status) }}">
                                    {{ get_status_label($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <button onclick="viewOrderDetails({{ $order->id }})" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Xem chi tiết
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-600 text-lg font-medium">Không tìm thấy đơn hàng nào</p>
                                    <p class="text-gray-500 text-sm mt-1">Vui lòng thử điều chỉnh bộ lọc của bạn</p>
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
