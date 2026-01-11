@extends('layouts.buyer')

@section('title', 'Chi tiết Đơn hàng')

@section('content')
    <div class="space-y-6">
        <!-- Breadcrumb & Back -->
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('buyer.orders.index') }}" class="hover:text-blue-600 transition">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
            </a>
            <span>/</span>
            <span class="font-medium text-gray-900">Chi tiết đơn hàng #{{ $order->order_code ?? $order->id }}</span>
        </div>

        <!-- Order Header & Stepper -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Đơn hàng #{{ $order->order_code ?? $order->id }}</h1>
                    <div class="flex items-center gap-3 mt-2 text-sm text-gray-500">
                        <span><i class="far fa-calendar mr-1"></i> Ngày tạo: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                        <span>|</span>
                        <span><i class="far fa-building mr-1"></i> Khoa: <span class="font-medium text-gray-900">{{ $order->department->department_name }}</span></span>
                    </div>
                </div>
                <!-- Interactive Buttons Removed -->
                 <div class="flex gap-2">
                    <a href="{{ route('buyer.tracking.show', $order->id) }}" class="px-4 py-2 bg-gray-100 text-blue-600 rounded-lg hover:bg-blue-50 transition font-medium shadow-sm border border-blue-100">
                        <i class="fas fa-truck-loading mr-2"></i> Chuyển sang Theo dõi Giao hàng
                    </a>
                </div>
            </div>

            <!-- Visual Stepper (Keep as Visual Indicator) -->
            <div class="relative w-full py-4">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 rounded-full -z-10"></div>
                <div class="absolute top-1/2 left-0 h-1 bg-blue-600 -translate-y-1/2 rounded-full -z-10 transition-all duration-500" style="width: {{ $progress }}%"></div>
                
                <div class="flex justify-between w-full">
                    @foreach($steps as $key => $step)
                        @php
                            $isActive = $key == $order->status || ($progress >= 100 && $loop->last) || (array_search($order->status, array_keys($steps)) >= $loop->index);
                            $isCurrent = $key == $order->status;
                        @endphp
                        <div class="flex flex-col items-center gap-2 group cursor-default">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 {{ $isActive ? 'bg-blue-600 border-blue-600 text-white shadow-lg scale-110' : 'bg-white border-gray-300 text-gray-400' }} transition-all duration-300 z-10">
                                <i class="fas {{ $step['icon'] }} text-sm"></i>
                            </div>
                            <span class="text-xs font-medium {{ $isCurrent ? 'text-blue-700 font-bold' : ($isActive ? 'text-blue-600' : 'text-gray-400') }} uppercase tracking-wider bg-white px-2">
                                {{ $step['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @if($order->status == 'CANCELLED')
                <div class="mt-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-center">
                    <i class="fas fa-times-circle mr-2"></i> Đơn hàng này đã bị hủy.
                </div>
            @endif
        </div>

        <!-- Products List (Read Only) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-boxes text-blue-600"></i> Danh sách sản phẩm
                </h2>
                <div class="text-sm font-medium text-gray-600">
                    Tổng tiền: <span class="text-blue-600 text-lg ml-1">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Sản phẩm</th>
                            <th class="px-6 py-3 text-center">SL</th>
                            <th class="px-6 py-3 text-right">Đơn giá</th>
                            <th class="px-6 py-3 text-right">Thành tiền</th>
                            <th class="px-6 py-3 text-center">Trạng thái SP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item->product->product_name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $item->product->category->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">{{ number_format($item->amount ?? ($item->quantity * $item->unit_price), 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $itemStatusClass = match($item->status) {
                                        'PENDING' => 'bg-gray-100 text-gray-600',
                                        'ORDERED' => 'bg-blue-50 text-blue-600',
                                        'DELIVERING' => 'bg-yellow-50 text-yellow-600',
                                        'DELIVERED' => 'bg-green-50 text-green-600',
                                        default => 'bg-gray-100 text-gray-600'
                                    };
                                    $itemStatusLabel = match($item->status) {
                                        'PENDING' => 'Chờ xử lý',
                                        'ORDERED' => 'Đã đặt',
                                        'DELIVERING' => 'Đang giao',
                                        'DELIVERED' => 'Đã về kho',
                                        default => $item->status
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $itemStatusClass }} border border-opacity-20">
                                    {{ $itemStatusLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
