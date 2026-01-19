@extends('layouts.buyer')

@section('title', 'Quản lý Yêu cầu Mua hàng')
@section('header_title', 'Danh sách Yêu cầu Mua hàng')

@section('content')
    <div class="space-y-6">

        <!-- Tabs & Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Status Tabs -->
            <div class="border-b border-gray-100 bg-gray-50/50">
                <nav class="flex -mb-px px-4 gap-4 overflow-x-auto" aria-label="Tabs">
                    @php
                        $currentStatus = request('status');
                        $tabs = [
                            ['label' => 'Tất cả', 'value' => '', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
                            ['label' => 'Chờ xử lý', 'value' => 'PENDING', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Đã duyệt', 'value' => 'APPROVED', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Hoàn thành', 'value' => 'COMPLETED', 'icon' => 'M5 13l4 4L19 7'],
                            ['label' => 'Đã từ chối', 'value' => 'REJECTED', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                    @endphp

                    @foreach($tabs as $tab)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $tab['value'], 'page' => 1]) }}"
                           class="flex items-center gap-2 py-4 px-4 text-sm font-medium border-b-2 transition-all duration-200 whitespace-nowrap {{ ($currentStatus == $tab['value']) ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}" />
                            </svg>
                            {{ $tab['label'] }}
                            @if($tab['value'] === 'PENDING' && $pendingCount > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Additional Filters -->
            <div class="p-4 flex flex-wrap gap-6 items-center">
                <form method="GET" action="{{ route('buyer.requests.index') }}" id="filterForm" class="flex flex-wrap gap-4 w-full md:w-auto">
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

                            <a href="{{ route('buyer.requests.index') }}"
                                class="inline-flex items-center px-6 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 shadow-sm">
                                Xóa lọc
                            </a>
                        </div>
                    </div>
                </form>

                @if(request('status') == 'PENDING')
                <div class="ml-auto flex items-center gap-3">
                    <button type="button" id="bulkApproveBtn" disabled
                        onclick="handleBulkApprove()"
                        class="hidden md:flex items-center gap-2 px-6 py-2.5 bg-[#cdeed7] text-white rounded-2xl text-sm font-bold transition disabled:cursor-not-allowed shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Duyệt toàn bộ ( <span id="selectedCount">0</span> )
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            @if(request('status') == 'PENDING')
                            <th class="px-6 py-4 w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            @endif
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã yêu cầu
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khoa/Phòng
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Người tạo
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kỳ/Quý</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Trạng thái</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                                Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($requests as $req)
                            <tr class="hover:bg-gray-50 transition group">
                                @if(request('status') == 'PENDING')
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="request_ids[]" value="{{ $req->id }}" class="request-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                @endif
                                <td class="px-6 py-4 text-sm font-medium text-blue-600">
                                    {{ $req->request_code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $req->department->department_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $req->requester->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $req->period }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $req->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $status = $req->status ?: ($req->is_submitted ? 'PENDING' : 'DRAFT');
                                        $statusClass = get_request_status_class($status);
                                        $statusLabel = get_request_status_label($status);
                                        
                                        if ($status === 'DRAFT') {
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusLabel = 'Bản nháp';
                                        }
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">

                                    <button onclick="openCompareModal({{ $req->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium" title="So sánh">
                                        So kết quả
                                    </button>

                                    @if($req->is_submitted && (!$req->status || $req->status == 'PENDING'))
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('buyer.requests.approve', $req->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn duyệt yêu cầu này?');">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                                Duyệt
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $req->id }})"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Từ chối
                                        </button>

                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (request('status') == 'PENDING') ? 8 : 7 }}" class="px-6 py-12 text-center text-gray-500 italic">
                                    Không tìm thấy yêu cầu nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $requests->withQueryString()->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const bulkBtn = document.getElementById('bulkApproveBtn');
            const countSpan = document.getElementById('selectedCount');

            if (!selectAll) return; // Only run if selectAll exists (PENDING tab)

            function updateBulkBtn() {
                const checkboxes = document.querySelectorAll('.request-checkbox');
                const checkedCount = Array.from(checkboxes).filter(c => c.checked).length;
                if (bulkBtn) {
                    bulkBtn.disabled = checkedCount === 0;
                    countSpan.textContent = checkedCount;
                    
                    if (checkedCount > 0) {
                        bulkBtn.classList.remove('bg-[#cdeed7]');
                        bulkBtn.classList.add('bg-[#8bd3a1]');
                        bulkBtn.classList.add('hover:bg-[#7bc291]');
                    } else {
                        bulkBtn.classList.add('bg-[#cdeed7]');
                        bulkBtn.classList.remove('bg-[#8bd3a1]');
                        bulkBtn.classList.remove('hover:bg-[#7bc291]');
                    }
                }
            }

            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.request-checkbox');
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkBtn();
            });

            // Using event delegation for checkboxes in case of future dynamic changes
            document.querySelector('table').addEventListener('change', function(e) {
                if (e.target.classList.contains('request-checkbox')) {
                    const checkboxes = document.querySelectorAll('.request-checkbox');
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    const noneChecked = Array.from(checkboxes).every(c => !c.checked);
                    
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = !allChecked && !noneChecked;
                    
                    updateBulkBtn();
                }
            });
        });

        function handleBulkApprove() {
            const selectedIds = Array.from(document.querySelectorAll('.request-checkbox:checked')).map(cb => cb.value);
            
            if (selectedIds.length === 0) return;

            if (confirm(`Bạn có chắc chắn muốn phê duyệt ${selectedIds.length} yêu cầu đã chọn?`)) {
                
                fetch("{{ route('buyer.requests.bulk-approve') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Đã có lỗi xảy ra.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã có lỗi xảy ra hệ thống.');
                });
            }
        }
    </script>
    @endpush



    <!-- Reject Modal (Keep Existing) -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeRejectModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <!-- Heroicon name: outline/exclamation -->
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Từ chối Yêu cầu
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-2">
                                        Vui lòng nhập lý do từ chối yêu cầu này. Lý do sẽ được gửi thông báo về khoa/phòng.
                                    </p>

                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <button type="button" onclick="fillRejectReason('Sai đơn giá dự kiến')"
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition">Sai
                                            đơn giá</button>
                                        <button type="button" onclick="fillRejectReason('Vượt ngân sách quý')"
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition">Vượt
                                            ngân sách</button>
                                        <button type="button" onclick="fillRejectReason('Thiếu thông tin chi tiết')"
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition">Thiếu
                                            thông tin</button>
                                        <button type="button" onclick="fillRejectReason('Sai quy cách sản phẩm')"
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition">Sai
                                            quy cách</button>
                                        <button type="button" onclick="fillRejectReason('Số lượng không hợp lý')"
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition">Sai
                                            số lượng</button>
                                    </div>

                                    <textarea name="reason" id="rejectReason" rows="4"
                                        class="w-full shadow-sm focus:ring-red-500 focus:border-red-500 block sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Nhập lý do từ chối..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Xác nhận Từ chối
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Compare Modal -->
    <div id="compareModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeCompareModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="compare-modal-title">
                                So sánh Yêu cầu
                            </h3>

                            <div id="compareContent" class="space-y-4">
                                <!-- Content loaded via AJAX -->
                                <div class="animate-pulse flex space-x-4">
                                    <div class="flex-1 space-y-4 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 bg-gray-200 rounded"></div>
                                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeCompareModal()"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            const form = document.getElementById('rejectForm');
            form.action = `/buyer/requests/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function openCompareModal(id) {
            document.getElementById('compareModal').classList.remove('hidden');
            const content = document.getElementById('compareContent');
            content.innerHTML = '<p class="text-gray-500 text-center">Đang tải dữ liệu...</p>';

            fetch(`/buyer/requests/${id}/compare`)
                .then(response => response.json())
                .then(data => {
                    const currency = new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    });

                    // --- Item Details Table ---
                    let itemsHtml = '<table class="min-w-full divide-y divide-gray-200 mt-2 border border-gray-200 rounded-lg overflow-hidden">';
                    itemsHtml += '<thead class="bg-gray-50"><tr><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase">Sản phẩm</th><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">SL</th><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Giá dự kiến</th><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Thành tiền</th></tr></thead><tbody class="bg-white divide-y divide-gray-200">';
                    data.items.forEach(item => {
                        itemsHtml += `<tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${item.product ? item.product.product_name : 'N/A'}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">${item.quantity}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">${currency.format(item.expected_price)}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 font-medium text-right">${currency.format(item.quantity * item.expected_price)}</td>
                            </tr>`;
                    });
                    itemsHtml += '</tbody></table>';

                    // --- Category Comparison Table ---
                    let compHtml = '<table class="min-w-full divide-y divide-gray-200 mt-2 border border-gray-200 rounded-lg overflow-hidden">';
                    compHtml += `<thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase">Nhóm sản phẩm</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Kỳ này (${data.current_period})</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Kỳ trước (${data.previous_period || 'N/A'})</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Chênh lệch</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">`;
                    data.comparison.forEach(row => {
                        const diffClass = row.diff > 0 ? 'text-red-600' : (row.diff < 0 ? 'text-green-600' : 'text-gray-500');
                        const diffPrefix = row.diff > 0 ? '+' : '';
                        compHtml += `<tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">${row.category}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">${currency.format(row.current_amount)}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">${currency.format(row.previous_amount)}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-bold ${diffClass} text-right">${diffPrefix}${currency.format(row.diff)}</td>
                            </tr>`;
                    });
                    compHtml += '</tbody></table>';

                    // --- Previous Items Details Table ---
                    let prevItemsHtml = '';
                    if (data.previous_items && data.previous_items.length > 0) {
                        prevItemsHtml = '<div class="mt-6"><div class="flex justify-between items-center bg-gray-100 p-3 rounded-t-lg"><h4 class="font-bold text-gray-800 text-sm">Chi tiết hàng hóa kỳ trước (' + (data.previous_period || 'N/A') + ')</h4></div>';
                        prevItemsHtml += '<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-b-lg overflow-hidden">';
                        prevItemsHtml += '<thead class="bg-gray-50"><tr><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase">Sản phẩm</th><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">SL</th><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Đơn giá</th><th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase text-right">Thành tiền</th></tr></thead><tbody class="bg-white divide-y divide-gray-200">';
                        data.previous_items.forEach(item => {
                            prevItemsHtml += `<tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                        ${item.product_name} 
                                        <span class="text-xs text-gray-400 block">${item.request_code}</span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">${item.quantity}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">${currency.format(item.unit_price)}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 font-medium text-right">${currency.format(item.total)}</td>
                                </tr>`;
                        });
                        prevItemsHtml += '</tbody></table></div>';
                    } else {
                        prevItemsHtml = '<p class="text-sm text-gray-500 italic mt-4">Không có dữ liệu mua hàng kỳ trước.</p>';
                    }

                    // --- Budget Analysis Display ---
                    let budgetHtml = '';
                    if (data.budget_limit > 0) {
                        const percent = Math.min(data.budget_usage_percent, 100).toFixed(1);
                        const isOver = data.is_over_budget;
                        const barColor = isOver ? 'bg-red-500' : (percent > 80 ? 'bg-orange-500' : 'bg-green-500');
                        const textColor = isOver ? 'text-red-700' : 'text-gray-700';
                        const icon = isOver ? '<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>' : '';

                        budgetHtml = `
                                <div class="bg-white p-4 rounded-lg border ${isOver ? 'border-red-300 ring-2 ring-red-100' : 'border-gray-200'} mb-6">
                                    <h4 class="text-sm font-bold text-gray-800 uppercase mb-3 flex items-center justify-between">
                                        <span>${icon}Thông tin Ngân sách (${data.current_period})</span>
                                        <span class="${isOver ? 'text-red-600' : 'text-gray-600'} text-xs font-semibold">
                                            ${isOver ? 'ĐÃ VƯỢT NGÂN SÁCH' : 'Trong hạn mức'}
                                        </span>
                                    </h4>

                                    <div class="flex justify-between items-end mb-2">
                                        <div>
                                            <p class="text-xs text-gray-500">Đã dùng (gồm yêu cầu này)</p>
                                            <p class="text-xl font-bold ${textColor}">${currency.format(data.accumulated_total)}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Ngân sách Quý</p>
                                            <p class="text-xl font-bold text-gray-900">${currency.format(data.budget_limit)}</p>
                                        </div>
                                    </div>

                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                        <div class="${barColor} h-2.5 rounded-full" style="width: ${percent}%"></div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-medium ${textColor}">${percent}%</span>
                                    </div>
                                </div>
                            `;
                    }

                    // --- Rejection Reason Display ---
                    let feedbackHtml = '';
                    if (data.status === 'REJECTED' && data.rejection_reason) {
                        feedbackHtml = `
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm leading-5 font-medium text-red-800">
                                                Yêu cầu này đã bị từ chối
                                            </h3>
                                            <div class="mt-1 text-sm leading-5 text-red-700">
                                                <p class="font-bold">Lý do: ${data.rejection_reason}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                    }

                    const html = `
                            ${budgetHtml}
                            ${feedbackHtml}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <h4 class="text-sm font-bold text-blue-800 uppercase mb-2">Tổng yêu cầu hiện tại</h4>
                                    <p class="text-2xl font-bold text-blue-600">${currency.format(data.current_total)}</p>
                                    <p class="text-xs text-blue-500 mt-1">Khoa: ${data.department}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase mb-2">Tổng kỳ trước (${data.previous_period || 'Không có'})</h4>
                                    <p class="text-2xl font-bold text-gray-800">${currency.format(data.previous_total)}</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm mb-2 border-l-4 border-blue-500 pl-2">So sánh theo Nhóm sản phẩm</h4>
                                    ${compHtml}
                                </div>

                                <div>
                                    <div class="flex justify-between items-center bg-gray-100 p-3 rounded-t-lg">
                                        <h4 class="font-bold text-gray-800 text-sm">Chi tiết hàng hóa yêu cầu (${data.current_period})</h4>
                                    </div>
                                    ${itemsHtml}
                                </div>

                                ${prevItemsHtml}
                            </div>
                        `;
                    content.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    content.innerHTML = '<p class="text-red-500 text-center">Có lỗi xảy ra khi tải dữ liệu.</p>';
                });
        }

        function closeCompareModal() {
            document.getElementById('compareModal').classList.add('hidden');
        }

        function fillRejectReason(text) {
            document.getElementById('rejectReason').value = text;
        }
    </script>
@endsection