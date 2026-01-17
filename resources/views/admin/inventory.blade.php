@extends('layouts.admin')

@section('title', 'Quản lý kho')
@section('page-title', 'Quản lý kho')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mt-1">Theo dõi tồn kho và quản lý hàng hóa trong các kho của bệnh viện</p>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('admin.inventory.export', request()->all()) }}"
                    class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium flex items-center">
                    <i class="fas fa-file-export mr-2"></i>Xuất báo cáo
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Tổng số kho</span>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-warehouse text-blue-500"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_warehouses']) }}</h3>
                <p class="text-xs text-gray-500 mt-2">Kho đang hoạt động</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Sản phẩm trong kho</span>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-green-500"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_products']) }}</h3>
                <p class="text-xs text-gray-500 mt-2">Tổng số mặt hàng</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Cảnh báo tồn kho</span>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-500"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-orange-600">{{ number_format($stats['low_stock_count']) }}</h3>
                <p class="text-xs text-gray-500 mt-2">Sản phẩm sắp hết</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Giá trị tồn kho</span>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-purple-500"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_value']) }}</h3>
                <p class="text-xs text-gray-500 mt-2">VNĐ</p>
            </div>
        </div>

        <!-- Filters and Search -->
        <form method="GET" action="{{ route('admin.inventory') }}" id="filterForm"
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="space-y-4">
                <!-- Search - Full Width -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 uppercase text-xs">Tìm kiếm sản phẩm</label>
                    <div class="relative">
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Tên sản phẩm, mã SKU..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                        <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 uppercase text-xs">Phòng ban</label>
                        <select name="department_id" id="departmentFilter"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                            <option value="">Tất cả phòng ban</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 uppercase text-xs">Kho lưu trữ</label>
                        <select name="warehouse_id" id="warehouseFilter"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                            <option value="">Tất cả kho</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <a href="{{ route('admin.inventory') }}"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium flex items-center justify-center gap-2 transition shadow-sm">
                            <i class="fas fa-times"></i>
                            Xóa lọc
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Inventory Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kho</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thông tin sản phẩm
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phòng ban</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Danh mục</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tồn kho</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inventory as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-warehouse text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $item->warehouse->warehouse_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $item->warehouse->warehouse_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if($item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->product_name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-box text-gray-400 text-lg"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $item->product->product_name }}</p>
                                            <p class="text-xs text-blue-600 font-mono">{{ $item->product->product_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm text-gray-700">{{ $item->warehouse->department->department_name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                        {{ $item->product->category->category_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <span
                                            class="text-lg font-bold {{ $item->quantity < 10 ? 'text-red-600' : ($item->quantity < 50 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ number_format($item->quantity, 0) }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-1">{{ $item->product->unit }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->quantity < 10)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Sắp hết hàng
                                        </span>
                                    @elseif($item->quantity < 50)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1.5"></span>Còn ít hàng
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Đủ hàng
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-box-open text-6xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Không có dữ liệu tồn kho</p>
                                    <p class="text-sm mt-2">Thử thay đổi bộ lọc hoặc thêm sản phẩm vào kho</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($inventory->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Hiển thị <span class="font-semibold text-gray-900">{{ $inventory->firstItem() ?? 0 }}</span>
                            đến <span class="font-semibold text-gray-900">{{ $inventory->lastItem() ?? 0 }}</span>
                            trong tổng số <span class="font-semibold text-gray-900">{{ $inventory->total() }}</span> sản phẩm
                        </p>
                        <div class="flex space-x-2">
                            {{-- Previous Button --}}
                            @if ($inventory->onFirstPage())
                                <span
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left mr-1"></i>Trước
                                </span>
                            @else
                                <a href="{{ $inventory->previousPageUrl() }}"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-chevron-left mr-1"></i>Trước
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($inventory->getUrlRange(1, $inventory->lastPage()) as $page => $url)
                                @if ($page == $inventory->currentPage())
                                    <span class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Next Button --}}
                            @if ($inventory->hasMorePages())
                                <a href="{{ $inventory->nextPageUrl() }}"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Sau<i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            @else
                                <span
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                                    Sau<i class="fas fa-chevron-right ml-1"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-submit form on filter change
            document.getElementById('departmentFilter')?.addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('warehouseFilter')?.addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('categoryFilter')?.addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });

            // Search with debounce
            let searchTimeout;
            document.getElementById('searchInput')?.addEventListener('input', function (e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        </script>
    @endpush
@endsection