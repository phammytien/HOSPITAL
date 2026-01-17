@extends('layouts.admin')

@section('title', 'Lịch sử Mua hàng')
@section('page-title', 'Lịch sử Mua hàng')
@section('header_title', 'Lịch sử Mua hàng')
@section('page-subtitle', 'Quản lý và theo dõi lịch sử đơn hàng đã duyệt của các mua sắm')

@section('content')
    <div class="space-y-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Spent --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        @php
                            $year = request('month_from') ? date('Y', strtotime(request('month_from'))) : date('Y');
                            $periodLabel = (request('month_from') || request('month_to')) ? 'theo bộ lọc' : 'Năm ' . $year;
                        @endphp
                        <p class="text-sm text-gray-600 mb-1">Tổng chi tiêu ({{ $periodLabel }})</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalSpent, 0, ',', '.') }} <span
                                class="text-sm font-normal text-gray-500">đ</span></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-blue-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-sm text-green-600 flex items-center">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span>Thống kê theo dữ liệu đã lọc</span>
                </p>
            </div>

            {{-- Total Approved Requests --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Yêu cầu đã duyệt</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalRequests) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-600 flex items-center">
                    <i class="fas fa-check mr-1"></i>
                    <span>98% tỷ lệ duyệt</span>
                </p>
            </div>

            {{-- Pending Requests --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Đang chờ xử lý</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ number_format($pendingCount) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-sm text-orange-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <span>Yêu cầu đang chờ duyệt</span>
                </p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <form method="GET" action="{{ route('admin.history') }}" id="filterForm"
                class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                {{-- Search --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Từ khóa tìm kiếm</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nhập mã yêu cầu, ghi chú..."
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Department --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Khoa / Phòng</label>
                    <select name="department_id" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả phòng ban</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Từ tháng/năm</label>
                    <div class="relative">
                        <input type="text" name="month_from" id="monthFrom" value="{{ request('month_from') }}"
                            placeholder="tháng/năm"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white cursor-pointer">
                        <i class="fas fa-calendar-alt absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Date To --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đến tháng/năm</label>
                    <div class="relative">
                        <input type="text" name="month_to" id="monthTo" value="{{ request('month_to') }}"
                            placeholder="tháng/năm"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white cursor-pointer">
                        <i class="fas fa-calendar-alt absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="md:col-span-3 flex justify-end gap-3">
                    <a href="{{ route('admin.history') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2 font-medium">
                        <i class="fas fa-times"></i>
                        Xóa lọc
                    </a>
                    <a href="{{ route('admin.history.export', request()->all()) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2 font-medium">
                        <i class="fas fa-file-excel"></i>
                        Xuất Excel
                    </a>
                </div>
            </form>
        </div>

        @push('scripts')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const filterForm = document.getElementById('filterForm');

                    const fpConfig = {
                        locale: "vn",
                        plugins: [
                            new monthSelectPlugin({
                                shorthand: true,
                                dateFormat: "Y-m",
                                altFormat: "m/Y",
                                theme: "light"
                            })
                        ],
                        disableMobile: "true",
                        onChange: function (selectedDates, dateStr, instance) {
                            filterForm.submit();
                        }
                    };

                    flatpickr("#monthFrom", fpConfig);
                    flatpickr("#monthTo", fpConfig);

                    const inputs = filterForm.querySelectorAll('input:not(#monthFrom):not(#monthTo), select');

                    let searchTimeout;
                    inputs.forEach(input => {
                        input.addEventListener('change', function () {
                            if (this.name !== 'search') {
                                filterForm.submit();
                            }
                        });

                        if (input.name === 'search') {
                            input.addEventListener('input', function () {
                                clearTimeout(searchTimeout);
                                searchTimeout = setTimeout(() => {
                                    filterForm.submit();
                                }, 500);
                            });
                        }
                    });
                });
            </script>
        @endpush

        {{-- History Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã yêu cầu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Khoa/Phòng</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Người yêu cầu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày hoàn thành</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tổng tiền</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($history as $index => $request)
                            @php
                                $total = $request->items->sum(function ($item) {
                                    return $item->quantity * $item->expected_price;
                                });
                                $colors = ['blue', 'purple', 'orange', 'pink'];
                                $color = $colors[$index % 4];
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.history.show', $request->id) }}"
                                        class="flex items-center gap-3 group">
                                        <div
                                            class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $color }}-200 transition">
                                            <i class="fas fa-file-alt text-{{ $color }}-600"></i>
                                        </div>
                                        <span
                                            class="font-semibold text-blue-600 hover:text-blue-700">{{ $request->request_code }}</span>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $request->department->department_name ?? 'N/A' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900">{{ $request->requester->full_name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-600">{{ optional($request->created_at)->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ optional($request->created_at)->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($request->status == 'COMPLETED' || $request->status == 'PAID')
                                        <p class="text-green-600 font-medium">{{ optional($request->updated_at)->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ optional($request->updated_at)->format('H:i') }}</p>
                                    @else
                                        <span class="text-gray-400 italic text-sm">Chưa hoàn thành</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ number_format($total, 0, ',', '.') }} đ</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-3 py-1 {{ get_request_status_class($request->status) }} rounded-full text-xs font-semibold flex items-center gap-1">
                                            <i class="fas fa-circle text-[8px]"></i>
                                            {{ get_request_status_label($request->status) == $request->status ? get_status_label($request->status) : get_request_status_label($request->status) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                                    <p>Chưa có lịch sử mua hàng</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($history->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Hiển thị <span class="font-semibold">{{ $history->firstItem() }}</span> đến <span
                            class="font-semibold">{{ $history->lastItem() }}</span> trong <span
                            class="font-semibold">{{ $history->total() }}</span>
                    </p>
                    {{ $history->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection