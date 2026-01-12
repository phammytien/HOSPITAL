@extends('layouts.buyer')

@section('title', 'Dashboard - Buyer')
@section('header_title', ' Hệ thống Mua hàng')

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <!-- Card 1: Pending -->
        <div
            class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 mb-1">Chờ duyệt</h3>
                    <span class="text-2xl font-bold text-gray-800">{{ $pendingCount }}</span>
                </div>
                <div class="p-2 bg-orange-50 rounded-lg">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Processing -->
        <div
            class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 mb-1">Đang xử lý</h3>
                    <span
                        class="text-2xl font-bold text-gray-800">{{ str_pad($processingCount, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Approved -->
        <div
            class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 mb-1">Đã duyệt (Tháng)</h3>
                    <span class="text-2xl font-bold text-gray-800">{{ $approvedMonthCount }}</span>
                </div>
                <div class="p-2 bg-green-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Paid (New) -->
        <div
            class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 mb-1">Đã bàn giao</h3>
                    <span class="text-2xl font-bold text-gray-800">{{ str_pad($paidCount, 2, '0', STR_PAD_LEFT) }}</span>
                    <p class="text-[0.65rem] text-gray-400 mt-1">Đã thanh toán</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 5: Rejected -->
        <div
            class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 mb-1">Đã từ chối</h3>
                    <span
                        class="text-2xl font-bold text-gray-800">{{ str_pad($rejectedCount, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="p-2 bg-red-50 rounded-lg">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Budget/Spending Chart -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">Biểu đồ Ngân sách</h3>
                <div class="flex gap-2">
                    <select id="spending_year_filter"
                        class="text-xs border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500 text-gray-600 bg-gray-50">
                        @foreach($availableYears as $optYear)
                            <option value="{{ $optYear }}" {{ $spendingYear == $optYear ? 'selected' : '' }}>Năm {{ $optYear }}
                            </option>
                        @endforeach
                    </select>
                    <select id="spending_filter"
                        class="text-xs border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500 text-gray-600 bg-gray-50">
                        <option value="">Tất cả khoa phòng</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $spendingDeptId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="spendingChart"></div>
        </div>

        <!-- Quantity Chart -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">Số lượng thiết bị</h3>
                <div class="flex gap-2">
                    <select id="quantity_year_filter"
                        class="text-xs border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500 text-gray-600 bg-gray-50">
                        @foreach($availableYears as $optYear)
                            <option value="{{ $optYear }}" {{ $quantityYear == $optYear ? 'selected' : '' }}>Năm {{ $optYear }}
                            </option>
                        @endforeach
                    </select>
                    <select id="quantity_filter"
                        class="text-xs border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500 text-gray-600 bg-gray-50">
                        <option value="">Tất cả khoa phòng</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $quantityDeptId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="quantityChart"></div>
        </div>
    </div>

    <!-- Bottom Section: Full Width Sections -->
    <div class="space-y-6">
        <!-- Recent Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Yêu cầu gần đây</h3>
                <a href="{{ route('buyer.requests.index') }}" class="text-sm text-blue-600 font-medium hover:underline">Xem
                    tất cả</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">Mã
                                đơn</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                                Khoa/Phòng</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                                Ngày tạo</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 text-center">
                                Trạng thái</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentRequests as $request)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $request->request_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $request->department->department_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $request->created_at?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match ($request->status) {
                                            'SUBMITTED' => 'bg-yellow-100 text-yellow-700',
                                            'APPROVED' => 'bg-green-100 text-green-700',
                                            'REJECTED' => 'bg-red-100 text-red-700',
                                            'PROCESSING' => 'bg-blue-100 text-blue-700',
                                            'PAID' => 'bg-indigo-100 text-indigo-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        $statusLabel = match ($request->status) {
                                            'SUBMITTED' => 'Chờ duyệt',
                                            'APPROVED' => 'Đã duyệt',
                                            'REJECTED' => 'Từ chối',
                                            'PROCESSING' => 'Đang xử lý',
                                            'PAID' => 'Đã bàn giao',
                                            default => $request->status
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openCompareModal({{ $request->id }})"
                                        class="text-gray-400 hover:text-blue-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Chưa có yêu cầu nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 text-center">
                <a href="{{ route('buyer.requests.index') }}"
                    class="text-sm text-gray-500 hover:text-blue-600 font-medium">Xem toàn bộ yêu cầu mua hàng <span
                        class="ml-1">&rarr;</span></a>
            </div>
        </div>

        <!-- Latest Updates (Notifications) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-2">
                    <div class="space-y-3">
                        @forelse($notifications as $notification)
                            <div class="flex items-start gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-50 border border-transparent hover:border-blue-100 transition duration-150 group"
                                 onclick='showQuickViewDetail(@json($notification), "notification")'>
                                <div class="flex-shrink-0 mt-1">
                                    @if($notification->type == 'success')
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center group-hover:bg-green-200">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @elseif($notification->type == 'error')
                                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center group-hover:bg-red-200">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                    @elseif($notification->type == 'warning')
                                         <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center group-hover:bg-yellow-200">
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800 transition group-hover:text-blue-700">{{ $notification->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $notification->message }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="self-center opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic text-center py-6">Chưa có thông báo mới.</p>
                        @endforelse
                    </div>
                </div>
        </div>

        <!-- Recent Feedback -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6">Phản hồi mới nhất</h3>
            <div class="space-y-4">
                @forelse($recentFeedbacks as $feedback)
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-white hover:shadow-lg border border-transparent hover:border-blue-100 transition duration-200 group"
                         onclick='showQuickViewDetail(@json($feedback), "feedback")'>
                        @php
                            $deptName = $feedback->purchaseRequest->department->department_name 
                                       ?? $feedback->purchaseOrder->purchaseRequest->department->department_name 
                                       ?? 'Khoa/Phòng';
                        @endphp
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex-shrink-0 overflow-hidden ring-4 ring-white group-hover:ring-blue-100 transition shadow-sm">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($deptName) }}&background=0D8ABC&color=fff"
                                alt="Department">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1.5">
                                <p class="text-sm">
                                    <span class="font-bold text-gray-900 group-hover:text-blue-900">{{ $deptName }}</span>
                                    <span class="text-gray-300 mx-1.5">•</span>
                                    <span class="text-xs font-bold text-blue-600 tracking-wide">
                                        {{ $feedback->purchaseRequest->request_code 
                                           ?? $feedback->purchaseOrder->purchaseRequest->request_code 
                                           ?? ('REQ-' . ($feedback->purchase_request_id ?? $feedback->purchaseOrder->purchase_request_id ?? '')) }}
                                    </span>
                                </p>
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-tighter">{{ $feedback->feedback_date->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">"{{ $feedback->feedback_content }}"</p>
                        </div>
                        <div class="self-center opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition duration-200">
                             <div class="p-1.5 bg-blue-50 rounded-full">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                             </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 italic text-center py-6">Chưa có phản hồi mới.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" aria-hidden="true"
                onclick="closeQuickViewModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-white px-8 pt-8 pb-6 border-b border-gray-50">
                    <div class="flex items-center justify-between mb-4">
                        <div id="quickViewBadge" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm"></div>
                        <button type="button" onclick="closeQuickViewModal()" class="text-gray-300 hover:text-gray-500 hover:rotate-90 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <h3 class="text-2xl leading-8 font-black text-gray-900 tracking-tight" id="quickViewTitle"></h3>
                    <p class="text-xs font-medium text-gray-400 mt-2 flex items-center gap-1" id="quickViewDate"></p>
                </div>
                <div class="px-8 py-8" id="quickViewBody">
                    <!-- Dynamic Content -->
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100">
                    <div id="quickViewActions" class="flex-1 flex gap-3"></div>
                     <button type="button" onclick="closeQuickViewModal()"
                        class="w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-6 py-2.5 bg-white text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none transition-all duration-200 sm:w-auto">
                        Đóng
                    </button>
                </div>
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
                                Chi tiết Yêu cầu (So sánh)
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
                    <!-- <a id="viewFullBtn" href="#" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Xem trang chi tiết
                        </a> -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function showQuickViewDetail(data, type) {
            const modal = document.getElementById('quickViewModal');
            const title = document.getElementById('quickViewTitle');
            const body = document.getElementById('quickViewBody');
            const date = document.getElementById('quickViewDate');
            const badge = document.getElementById('quickViewBadge');
            const actions = document.getElementById('quickViewActions');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
            actions.innerHTML = '';

            if (type === 'notification') {
                title.innerText = data.title;
                body.innerHTML = `<div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6"><p class="text-gray-700 leading-relaxed text-base font-medium">${data.message}</p></div>`;
                date.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${data.created_at_human || 'Vừa xong'}`;
                
                badge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm ';
                switch(data.type) {
                    case 'success': badge.innerText = 'Thành công'; badge.classList.add('bg-green-50', 'text-green-600', 'border', 'border-green-100'); break;
                    case 'error': badge.innerText = 'Lỗi hệ thống'; badge.classList.add('bg-red-50', 'text-red-600', 'border', 'border-red-100'); break;
                    case 'warning': badge.innerText = 'Cảnh báo'; badge.classList.add('bg-yellow-50', 'text-yellow-600', 'border', 'border-yellow-100'); break;
                    default: badge.innerText = 'Thông tin'; badge.classList.add('bg-blue-50', 'text-blue-600', 'border', 'border-blue-100'); break;
                }

                const reqMatch = data.message.match(/#?(REQ[-_]\d{4}[-_]\S+)/i);
                if (reqMatch) {
                    const reqCode = reqMatch[1];
                    actions.innerHTML = `<button onclick="window.location.href='/buyer/requests?search=${reqCode}'" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-lg px-6 py-2.5 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 hover:shadow-blue-200 transition-all duration-300 sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Truy cập Yêu cầu
                    </button>`;
                }
            } else if (type === 'feedback') {
                const reqCode = data.purchase_request?.request_code 
                             || data.purchase_order?.purchase_request?.request_code 
                             || ('REQ-' + (data.purchase_request_id || data.purchase_order?.purchase_request_id || 'N/A'));
                
                title.innerText = 'Phản hồi chi tiết';
                body.innerHTML = `
                    <div class="flex items-center gap-3 mb-6 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                        <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-400 font-bold uppercase tracking-wider">Liên quan đến</p>
                            <p class="text-sm font-black text-indigo-900">Yêu cầu ${reqCode}</p>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-8 italic text-gray-700 text-lg leading-relaxed shadow-inner font-medium">
                            ${data.feedback_content}
                        </div>
                        <div class="mt-4 flex items-center justify-end gap-2">
                            <span class="text-xs font-bold text-gray-400">— ${data.user ? data.user.full_name : 'Người dùng'}</span>
                        </div>
                    </div>
                `;
                date.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${data.feedback_date_human || 'Gần đây'}`;
                const deptName = data.purchase_request?.department?.department_name 
                              || data.purchase_order?.purchase_request?.department?.department_name 
                              || 'Khoa/Phòng';

                badge.innerText = 'Phản hồi từ ' + deptName;
                badge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm bg-indigo-50 text-indigo-600 border border-indigo-100';
                
                actions.innerHTML = `<button onclick="window.location.href='/buyer/requests?search=${reqCode}'" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-lg px-6 py-2.5 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 hover:shadow-blue-200 transition-all duration-300 sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    Xem chi tiết Yêu cầu
                </button>`;
            }
        }

        function closeQuickViewModal() {
            document.getElementById('quickViewModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openCompareModal(id) {
            document.getElementById('compareModal').classList.remove('hidden');
            const content = document.getElementById('compareContent');
            // const viewFullBtn = document.getElementById('viewFullBtn');
            // viewFullBtn.href = `/buyer/requests/${id}/detail`; // Placeholder if needed

            content.innerHTML = '<p class="text-gray-500 text-center">Đang tải dữ liệu...</p>';

            fetch(`/buyer/requests/${id}/compare`)
                .then(response => response.json())
                .then(data => {
                    const currency = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

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

        document.addEventListener('DOMContentLoaded', function () {
            // Filter Handling
            const updateFilters = () => {
                const spendingVal = document.getElementById('spending_filter').value;
                const quantityVal = document.getElementById('quantity_filter').value;
                const spendingYearVal = document.getElementById('spending_year_filter').value;
                const quantityYearVal = document.getElementById('quantity_year_filter').value;

                const url = new URL(window.location.href);

                if (spendingVal) url.searchParams.set('spending_dept_id', spendingVal);
                else url.searchParams.delete('spending_dept_id');

                if (quantityVal) url.searchParams.set('quantity_dept_id', quantityVal);
                else url.searchParams.delete('quantity_dept_id');

                if (spendingYearVal) url.searchParams.set('spending_year', spendingYearVal);
                else url.searchParams.delete('spending_year');

                if (quantityYearVal) url.searchParams.set('quantity_year', quantityYearVal);
                else url.searchParams.delete('quantity_year');

                window.location.href = url.toString();
            };

            document.getElementById('spending_filter').addEventListener('change', updateFilters);
            document.getElementById('quantity_filter').addEventListener('change', updateFilters);
            document.getElementById('spending_year_filter').addEventListener('change', updateFilters);
            document.getElementById('quantity_year_filter').addEventListener('change', updateFilters);


            // Chart 1: Spending Trend (Area Chart)
            var spendingOptions = {
                series: [{
                    name: 'Giá trị mua hàng',
                    data: @json($spendingChartData)
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'],
                    labels: { style: { colors: '#64748b', fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' },
                        formatter: function (val) {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                            return val;
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                },
                tooltip: {
                    y: { formatter: function (val) { return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val) } }
                },
                colors: ['#3b82f6'],
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                }
            };

            var spendingChart = new ApexCharts(document.querySelector("#spendingChart"), spendingOptions);
            spendingChart.render();

            // Chart 2: Quantity per Quarter (Bar Chart)
            var quantityOptions = {
                series: [{
                    name: 'Số lượng thiết bị',
                    data: @json($quantityChartData)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '50%',
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'],
                    labels: { style: { colors: '#64748b', fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                },
                colors: ['#10b981']
            };

            var quantityChart = new ApexCharts(document.querySelector("#quantityChart"), quantityOptions);
            quantityChart.render();
        });
    </script>
@endsection