@extends('layouts.admin')

@section('title', 'Lịch sử Mua hàng')
@section('page-title', 'Lịch sử Mua hàng')
@section('header_title', 'Lịch sử Mua hàng')
@section('page-subtitle', 'Quản lý và theo dõi lịch sử đơn hàng đã duyệt của các mua sắm')

@section('content')
<div class="space-y-6">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Total Spent --}}
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng chi tiêu</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalSpent, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">đ</span></h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Requests --}}
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng yêu cầu</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalRequests) }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800">Tùy chọn tìm kiếm</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.history.export', ['search' => request('search'), 'department_id' => request('department_id'), 'status' => request('status'), 'month_from' => request('month_from'), 'month_to' => request('month_to')]) }}" 
                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2" title="Xuất Excel">
                    <i class="fas fa-download"></i>
                    <span class="text-sm font-medium">Xuất Excel</span>
                </a>
                <button type="button" onclick="clearFilters()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2" title="Xóa lọc">
                    <i class="fas fa-times"></i>
                    <span class="text-sm font-medium">Xóa lọc</span>
                </button>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.history') }}" id="filterForm" class="p-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            {{-- Search --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Từ khóa tìm kiếm</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                           placeholder="Nhập mã yêu cầu, ghi chú..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Department --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Khoa / Phòng</label>
                <select name="department_id" id="departmentFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả phòng ban</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status" id="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="SUBMITTED" {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>Đã gửi</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Từ chối</option>
                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>Đã thanh toán</option>
                </select>
            </div>

            {{-- Month From --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian (Từ tháng)</label>
                <input type="month" name="month_from" id="monthFromFilter" value="{{ request('month_from') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Month To --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">(Đến tháng)</label>
                <input type="month" name="month_to" id="monthToFilter" value="{{ request('month_to') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </form>
    </div>

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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tổng tiền</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($history as $index => $request)
                    @php
                        $total = $request->items->sum(function($item) {
                            return $item->quantity * $item->expected_price;
                        });
                        $colors = ['blue', 'purple', 'orange', 'pink'];
                        $color = $colors[$index % 4];
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.history.show', $request->id) }}" class="flex items-center gap-3 group">
                                <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $color }}-200 transition">
                                    <i class="fas fa-file-alt text-{{ $color }}-600"></i>
                                </div>
                                <span class="font-semibold text-blue-600 hover:text-blue-700">{{ $request->request_code }}</span>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $request->department->department_name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900">{{ $request->requester->full_name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-600">{{ optional($request->created_at)->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400">{{ optional($request->created_at)->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ number_format($total, 0, ',', '.') }} đ</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 {{ get_request_status_class($request->status) }} rounded-full text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i>
                                    {{ get_request_status_label($request->status) == $request->status ? get_status_label($request->status) : get_request_status_label($request->status) }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
                Hiển thị <span class="font-semibold">{{ $history->firstItem() }}</span> đến <span class="font-semibold">{{ $history->lastItem() }}</span> trong <span class="font-semibold">{{ $history->total() }}</span>
            </p>
            {{ $history->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Clear all filters
function clearFilters() {
    window.location.href = '{{ route('admin.history') }}';
}

// Auto-submit form on filter change
document.getElementById('departmentFilter')?.addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

document.getElementById('statusFilter')?.addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

document.getElementById('monthFromFilter')?.addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

document.getElementById('monthToFilter')?.addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

// Search with debounce - only submit if there's content
let searchTimeout;
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const searchValue = this.value.trim();
    
    searchTimeout = setTimeout(() => {
        // Only submit if search has content OR if we're clearing a previous search
        if (searchValue.length > 0 || '{{ request('search') }}' !== '') {
            document.getElementById('filterForm').submit();
        }
    }, 500);
});
</script>
@endpush
@endsection
