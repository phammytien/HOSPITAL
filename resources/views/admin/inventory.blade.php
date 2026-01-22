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
            <button onclick="window.print()" class="border border-blue-600 bg-white hover:bg-blue-50 text-blue-700 px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-print mr-2"></i>In Báo Cáo
            </button>
            <a href="{{ route('admin.inventory.export', request()->all()) }}" class="border border-green-600 bg-white hover:bg-green-50 text-green-700 px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-file-excel mr-2"></i>Xuất Excel
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
   

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kho</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng ban đầu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng tồn kho</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày cập nhật</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($warehouses as $warehouse)
                    <!-- Warehouse Row -->
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleWarehouse({{ $warehouse->id }})">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <button class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 hover:bg-blue-200 transition">
                                    <i class="fas fa-chevron-right text-blue-500 text-sm toggle-icon" id="icon-{{ $warehouse->id }}"></i>
                                </button>
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-warehouse text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $warehouse->warehouse_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $warehouse->warehouse_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($warehouse->initial_quantity) }}</span>
                            <span class="text-xs text-gray-500 ml-1">items</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-lg font-bold {{ $warehouse->total_quantity < 10 ? 'text-red-600' : ($warehouse->total_quantity < 50 ? 'text-orange-600' : 'text-green-600') }}">
                                {{ number_format($warehouse->total_quantity) }}
                            </span>
                            <span class="text-xs text-gray-500 ml-1">items</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">
                                {{ $warehouse->last_updated ? $warehouse->last_updated->format('d/m/Y H:i') : 'N/A' }}
                            </span>
                        </td>
                    </tr>

                    <!-- Products Detail Row (Hidden by default) -->
                    <tr class="product-details hidden" id="products-{{ $warehouse->id }}">
                        <td colspan="4" class="px-0 py-0">
                            <div class="bg-gray-50 border-t border-gray-200">
                                <div class="px-8 py-4">
                                    <!-- Product Details Header -->
                                    <div class="mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700 uppercase">
                                            <i class="fas fa-box mr-2"></i>Danh sách sản phẩm ({{ $warehouse->product_count }})
                                        </h4>
                                    </div>

                                    <!-- Products Table -->
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <table class="w-full">
                                            <thead class="bg-gray-100 border-b border-gray-200">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Mã sản phẩm</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Tên sản phẩm</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Danh mục</th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase">Số lượng</th>
                                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-600 uppercase">Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($warehouse->products as $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3">
                                                        <span class="text-xs font-mono text-blue-600">{{ $item->product->product_code }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                                @if($item->product->image_url)
                                                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->product_name }}" class="w-full h-full object-cover">
                                                                @else
                                                                    <i class="fas fa-box text-gray-400 text-sm"></i>
                                                                @endif
                                                            </div>
                                                            <span class="text-sm text-gray-900">{{ $item->product->product_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                                            {{ $item->product->category->category_name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <span class="text-sm font-bold {{ $item->quantity < 10 ? 'text-red-600' : ($item->quantity < 50 ? 'text-orange-600' : 'text-green-600') }}">
                                                            {{ number_format($item->quantity) }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 ml-1">{{ $item->product->unit }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($item->quantity < 10)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Sắp hết
                                                            </span>
                                                        @elseif($item->quantity < 50)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1.5"></span>Còn ít
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Đủ hàng
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-6xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">Không có dữ liệu tồn kho</p>
                            <p class="text-sm mt-2">Thử thay đổi bộ lọc hoặc thêm sản phẩm vào kho</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleWarehouse(warehouseId) {
    const detailsRow = document.getElementById('products-' + warehouseId);
    const icon = document.getElementById('icon-' + warehouseId);
    
    if (detailsRow.classList.contains('hidden')) {
        // Expand
        detailsRow.classList.remove('hidden');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        // Collapse
        detailsRow.classList.add('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}
</script>

<style>
@media print {
    /* Print-specific adjustments */
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
        background-color: white !important;
        font-size: 11px; /* Reduce base font size */
    }

    @page {
        margin: 0.5cm; /* Minimize margins */
        size: A4;
    }

    /* Hide non-print elements */
    nav, aside, header, .no-print,
    button:not(.force-print),
    .sidebar, .navbar,
    [class*="sidebar"], [class*="navbar"],
    .flex.space-x-3 {
        display: none !important;
    }

    /* Show page in full width for print */
    .space-y-6 {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Expand all warehouses for print */
    .product-details {
        display: table-row !important;
    }

    /* Hide toggle icons */
    .toggle-icon {
        display: none !important;
    }

    /* Add report header */
    .space-y-6:before {
        content: '';
        display: block;
        margin-bottom: 10px;
    }

    /* FIX: Style ONLY the main page header, not stats cards */
    .space-y-6 > .flex.items-center.justify-between:first-child {
        display: block !important;
        text-align: center;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .space-y-6 > .flex.items-center.justify-between:first-child > div {
        display: none;
    }

    .space-y-6 > .flex.items-center.justify-between:first-child:before {
        content: 'HỆ THỐNG Y TẾ QUỐC TẾ';
        display: block;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .space-y-6 > .flex.items-center.justify-between:first-child:after {
        content: 'BÁO CÁO TỒN KHO CHI TIẾT';
        display: block;
        font-size: 16px;
        font-weight: bold;
        color: #2563eb;
        margin-top: 5px;
    }

    /* Compact Stats Cards */
    .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 0.5rem !important; /* Reduce gap */
        page-break-inside: avoid;
        margin-bottom: 15px;
    }

    .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 > div {
        padding: 0.5rem !important; /* Reduce padding */
        border: 1px solid #e5e7eb !important;
    }
    
    .grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 h3 {
        font-size: 1.25rem !important; /* Smaller numbers */
    }

    /* Ensure color preservation for warehouse headers */
    .hover\:bg-gray-50:not(.product-details) {
        background-color: var(--warehouse-color, #2563eb) !important;
        color: white !important;
    }

    /* Assign colors to warehouses */
    tbody tr:nth-child(4n+1):not(.product-details) { --warehouse-color: #2563eb; }
    tbody tr:nth-child(4n+2):not(.product-details) { --warehouse-color: #7c3aed; }
    tbody tr:nth-child(4n+3):not(.product-details) { --warehouse-color: #0891b2; }
    tbody tr:nth-child(4n+4):not(.product-details) { --warehouse-color: #059669; }

    /* Compact warehouse rows */
    tbody tr.cursor-pointer td {
        color: white !important;
        font-weight: 600 !important;
        padding: 6px 10px !important; /* Compact padding */
    }

    tbody tr.cursor-pointer i { color: white !important; }
    tbody tr.cursor-pointer .bg-blue-100 { background-color: rgba(255, 255, 255, 0.2) !important; }
    
    tbody tr.cursor-pointer .text-blue-500,
    tbody tr.cursor-pointer .text-gray-900,
    tbody tr.cursor-pointer .text-gray-500,
    tbody tr.cursor-pointer .text-sm,
    tbody tr.cursor-pointer .font-semibold,
    tbody tr.cursor-pointer .font-bold {
        color: white !important;
    }

    /* Compact Product Table */
    .product-details td {
        background-color: #f9fafb !important;
        padding: 4px 8px !important; /* Compact padding */
    }
    
    .product-details .p-8, .product-details .px-8 {
        padding: 0.5rem !important; /* Remove large padding */
    }

    /* Reduce image sizes */
    .w-10.h-10 {
        width: 24px !important;
        height: 24px !important;
    }
    
    .text-3xl { font-size: 1.25rem !important; }
    .text-2xl { font-size: 1rem !important; }

    /* Page Breaks */
    .bg-white.rounded-lg.shadow-sm.border { page-break-inside: avoid; }
    tbody tr.cursor-pointer { page-break-after: avoid; }
    .product-details { page-break-before: avoid; page-break-inside: auto; }

    /* Signature Section */
    .space-y-6:after {
        content: '';
        display: block;
        margin-top: 30px;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
    }

    body:after {
        content: 'Ký tên và xác nhận: ________________________        ________________________        ________________________';
        display: block;
        position: fixed;
        bottom: 0.5cm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 10px;
        white-space: pre;
    }

    /* Hide elements to save space */
    .w-8.h-8.bg-blue-100 { display: none !important; }
    
    /* Global reset for print margins */
    * { margin-top: 0; margin-bottom: 0; }
    .p-6 { padding: 0.5rem !important; }
    .mb-6 { margin-bottom: 0.5rem !important; }
    .gap-6 { gap: 0.5rem !important; }
    
    /* Table font size */
    table { font-size: 10px !important; }
}
</style>

@endsection