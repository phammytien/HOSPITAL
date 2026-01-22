<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Tồn Kho Chi Tiết</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Print Button (hidden on print) -->
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <i class="fas fa-print"></i>
            In Báo Cáo
        </button>
    </div>

    <!-- Report Container -->
    <div class="max-w-5xl mx-auto my-8 bg-white shadow-xl rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="p-8 border-b-2 border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">HỆ THỐNG Y TẾ QUỐC TẾ</h1>
                    <p class="text-sm text-gray-600">123 Đường Sức Khỏe, Quận 1, TP. Hồ Chí Minh</p>
                    <p class="text-sm text-gray-600">Điện thoại: (028) 3822 0000 | Email: contact@hospital.com</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold text-blue-600 mb-2">BÁO CÁO TỒN KHO CHI TIẾT</h2>
                    <p class="text-xs text-gray-600">Ngày xuất báo cáo: {{ now()->format('d/m/Y - H:i') }} </p>
                    <p class="text-xs text-gray-600">Mã báo cáo: INV-{{ now()->format('Y') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="p-8 bg-gray-50">
            <div class="grid grid-cols-4 gap-4">
                <!-- Total Warehouses -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-warehouse text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Tổng kho</p>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ str_pad($stats['total_warehouses'], 2, '0', STR_PAD_LEFT) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Đang hoạt động</p>
                </div>

                <!-- Total Products -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-green-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Sản phẩm</p>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_products']) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Mặt hàng tồn kho</p>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-orange-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Cảnh báo tồn</p>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-orange-600">{{ str_pad($stats['low_stock_count'], 2, '0', STR_PAD_LEFT) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Sản phẩm sắp hết</p>
                </div>

                <!-- Total Value -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Giá trị tồn</p>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ number_format($stats['total_value']/1000, 0) }}B</h3>
                    <p class="text-xs text-gray-500 mt-1">Đơn vị: VNĐ</p>
                </div>
            </div>
        </div>

        <!-- Warehouse Details -->
        <div class="p-8">
            @foreach($warehouses as $index => $warehouse)
                @php
                    $colorIndex = $index % count($warehouseColors);
                    $color = $warehouseColors[$colorIndex];
                @endphp
                
                <!-- Warehouse Header -->
                <div class="mb-6 {{ $index > 0 ? 'mt-8' : '' }}">
                    <div class="{{ $color['bg'] }} {{ $color['text'] }} px-4 py-3 rounded-t-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-warehouse"></i>
                            <span class="font-bold">{{ strtoupper($warehouse->warehouse_name) }} ({{ $warehouse->warehouse_code }})</span>
                        </div>
                        <span class="text-sm">Cập nhật cuối: {{ $warehouse->last_updated ? $warehouse->last_updated->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>

                    <!-- Products Table -->
                    <div class="border-x border-b border-gray-200 rounded-b-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Mã SP</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tên sản phẩm</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Danh mục</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Số lượng</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($warehouse->products as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-mono text-blue-600 font-semibold">{{ $item->product->product_code }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-gray-900">{{ $item->product->product_name }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
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
                                                ● SẮP HẾT
                                            </span>
                                        @elseif($item->quantity < 50)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                ● CÒN ÍT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                ● ĐỦ HÀNG
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Footer / Signatures -->
        <div class="p-8 border-t-2 border-gray-200 bg-gray-50">
            <div class="grid grid-cols-3 gap-8 mb-8">
                <div class="text-center">
                    <h4 class="font-bold text-gray-800 mb-12">THỦ KHO</h4>
                    <div class="border-t border-gray-400 pt-2 mt-12">
                        <p class="text-sm text-gray-600">Ký và ghi rõ họ tên</p>
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="font-bold text-gray-800 mb-12">KẾ TOÁN KHO</h4>
                    <div class="border-t border-gray-400 pt-2 mt-12">
                        <p class="text-sm text-gray-600">Ký và ghi rõ họ tên</p>
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="font-bold text-gray-800 mb-12">GIÁM ĐỐC BỆNH VIỆN</h4>
                    <div class="border-t border-gray-400 pt-2 mt-12">
                        <p class="text-sm text-gray-600">Ký tên và đóng dấu</p>
                    </div>
                </div>
            </div>

            <div class="text-center text-xs text-gray-500 border-t border-gray-300 pt-4">
                <p>Tài liệu này được tạo ra từ Hệ thống Quản lý Bệnh viện. Bản quyền thuộc về © 2024 Medsoft Solutions.</p>
                <p class="mt-1">Trang 1/1 • Mã tài liệu: INV_DETAIL_{{ now()->format('Ymd') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional - comment out if not needed)
        // window.onload = function() {
        //     setTimeout(() => window.print(), 500);
        // };
    </script>
</body>
</html>
