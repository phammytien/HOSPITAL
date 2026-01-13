@extends('layouts.buyer')

@section('title', 'Theo dõi đơn hàng #' . ($order->order_code ?? $order->id))

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Breadcrumb & Action -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('buyer.tracking.index') }}" class="hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
                <span class="text-gray-300">/</span>
                <span class="font-bold text-gray-800">Theo dõi #{{ $order->order_code ?? $order->id }}</span>
            </div>

            <!-- Top Action Button -->
            <form action="{{ route('buyer.tracking.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                @if($order->status == 'CREATED')
                    <div class="flex items-center gap-2">
                        <input type="date" name="expected_delivery_date" required min="{{ date('Y-m-d') }}"
                            class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" name="status" value="ORDERED"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center">
                            <i class="fas fa-clock mr-2"></i> Xác nhận / Chờ xử lý
                        </button>
                    </div>
                @elseif($order->status == 'PENDING')
                    <button type="submit" name="status" value="ORDERED"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i> Xác nhận Đặt hàng
                    </button>
                @elseif($order->status == 'ORDERED')
                    <button type="submit" name="status" value="DELIVERING"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition shadow-sm flex items-center">
                        <i class="fas fa-truck mr-2"></i> Bắt đầu Giao hàng
                    </button>
                @elseif($order->status == 'DELIVERING')
                    <button type="submit" name="status" value="DELIVERED" onclick="return confirm('Xác nhận hàng đã về kho?')"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition shadow-sm flex items-center">
                        <i class="fas fa-warehouse mr-2"></i> Đã về kho
                    </button>
                @elseif($order->status == 'DELIVERED')
                    <span
                        class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg font-medium border border-yellow-200 flex items-center">
                        <i class="fas fa-clock mr-2"></i> Chờ xác nhận
                    </span>
                @elseif($order->status == 'COMPLETED')
                    <span
                        class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium border border-green-200 flex items-center">
                        <i class="fas fa-check mr-2"></i> Hoàn tất
                    </span>
                @elseif($order->status == 'CANCELLED')
                    <span
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium border border-gray-200 flex items-center">
                        <i class="fas fa-times mr-2"></i> Đã hủy
                    </span>
                @elseif($order->status == 'REJECTED')
                    <span
                        class="px-4 py-2 bg-red-100 text-white rounded-lg font-medium border border-red-200 flex items-center">
                        <i class="fas fa-ban mr-2"></i> Đã từ chối
                    </span>
                @endif
            </form>
        </div>

        <!-- Stepper Card -->
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start relative">

                @foreach($steps as $key => $step)
                    @php
                        // Helper logic
                        $statusKeys = array_keys($steps);
                        $currentIndex = array_search($order->status, $statusKeys);
                        $stepIndex = array_search($key, $statusKeys);

                        $isActive = $stepIndex <= $currentIndex;
                        $isCurrent = $stepIndex === $currentIndex;

                        // Colors
                        $iconBg = $isActive ? ($isCurrent ? 'bg-blue-600' : 'bg-blue-600') : 'bg-gray-100';
                        $iconColor = $isActive ? 'text-white' : 'text-gray-400';
                        $textColor = $isActive ? 'text-blue-900' : 'text-gray-400';

                        // Date
                        $date = null;
                        if ($key == 'CREATED')
                            $date = $order->created_at;
                        elseif ($key == 'PENDING')
                             $date = $order->updated_at; // Or updated_at since pending doesn't have a specific timestamp column yet
                        elseif ($key == 'ORDERED')
                            $date = $order->ordered_at;
                        elseif ($key == 'DELIVERING')
                            $date = $order->shipping_at;
                        elseif ($key == 'DELIVERED')
                            $date = $order->delivered_at;
                        elseif ($key == 'COMPLETED')
                            $date = $order->completed_at;
                    @endphp

                    <div class="flex flex-col items-center flex-1 z-0 relative">
                        <!-- Connection Line Color Overlay -->
                        @if(!$loop->first)
                            @php
                                $lineColor = $stepIndex <= $currentIndex ? 'bg-blue-600' : 'bg-gray-100';
                            @endphp
                            <div class="absolute top-6 right-[50%] w-full h-[4px] -z-10 {{ $lineColor }}"></div>
                        @endif

                        <div
                            class="w-12 h-12 rounded-full {{ $iconBg }} {{ $iconColor }} flex items-center justify-center text-lg mb-4 shadow-sm z-10">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </div>
                        <h3 class="font-bold uppercase text-xs tracking-wider {{ $textColor }} mb-1">{{ $step['label'] }}</h3>
                        @if($date)
                            <p class="text-[11px] text-gray-500 font-medium">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($date)->format('H:i:s') }}</p>
                        @elseif($isCurrent)
                            <p class="text-[11px] text-blue-500 italic font-medium mt-1">Đang xử lý...</p>
                        @else
                            <p class="text-[11px] text-gray-300 mt-1">--/--/----</p>
                            <p class="text-[10px] text-gray-300">--:--</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Info Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- 1. Department -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4 hover:shadow-md transition">
                <div
                    class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="far fa-building"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium mb-0.5">Khoa / Phòng</p>
                    <p class="font-bold text-gray-900 leading-tight">{{ $order->department->department_name ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- 2. Created Date -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4 hover:shadow-md transition">
                <div
                    class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium mb-0.5">Ngày tạo</p>
                    <p class="font-bold text-gray-900 leading-tight">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- 3. Expected Date -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4 hover:shadow-md transition">
                <div
                    class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="far fa-clock"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium mb-0.5">Dự kiến giao</p>
                    <p class="font-bold text-gray-900 leading-tight text-orange-600">
                        {{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') : '--/--/----' }}
                    </p>
                </div>
            </div>

            <!-- 4. Total Amount -->
            <div
                class="bg-blue-600 p-6 rounded-2xl shadow-md flex items-center gap-4 text-white hover:bg-blue-700 transition">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <p class="text-xs text-blue-100 uppercase font-medium mb-0.5">Tổng giá trị</p>
                    <p class="font-bold text-xl leading-tight">{{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                </div>
            </div>
        </div>

        <!-- Product List -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                    <i class="fas fa-list text-blue-600"></i> Danh sách sản phẩm & Tiến độ
                </h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold shadow-sm">
                    Tổng: {{ $order->items->count() }} sản phẩm
                </span>
            </div>

            <div class="p-6">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="py-3 font-bold uppercase text-xs tracking-wider">Sản phẩm</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-center">SL</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-right">Đơn giá</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-right">Thành tiền</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-center">Trạng thái SP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                            <tr class="group hover:bg-gray-50 transition">
                                <td class="py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:shadow-sm transition">
                                            <i class="fas fa-box text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-base">
                                                {{ $item->product->product_name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->product->category->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center font-bold text-gray-700">
                                    {{ (float) $item->quantity == (int) $item->quantity ? (int) $item->quantity : $item->quantity }}
                                </td>
                                <td class="py-4 text-right text-gray-500 font-medium">
                                    {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-4 text-right font-bold text-gray-900 text-base">
                                    {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
                                <td class="py-4 text-center">
                                    @php
                                        $statusClass = get_status_class($item->status);
                                        $statusLabel = get_status_label($item->status);
                                        $dotColor = match ($item->status) {
                                            'ORDERED' => 'text-blue-500',
                                            'DELIVERED' => 'text-emerald-500',
                                            'PAID', 'COMPLETED' => 'text-green-500',
                                            'PENDING' => 'text-gray-400',
                                            'CANCELLED', 'REJECTED' => 'text-red-500',
                                            default => 'text-gray-400'
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                        <i class="fas fa-circle text-[8px] mr-1.5 {{ $dotColor }}"></i> {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Footer Summaries -->
                <div class="mt-8 pt-4 border-t border-gray-100 flex flex-col items-end gap-1">
                    <div class="flex justify-between w-72 text-gray-500 text-sm">
                        <span>Tạm tính:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}
                            đ</span>
                    </div>
                    <div class="flex justify-between w-72 text-gray-500 text-sm">
                        <span>VAT (0%):</span>
                        <span class="font-semibold text-gray-900">0 đ</span>
                    </div>
                    <div
                        class="flex justify-between w-72 text-blue-600 text-xl font-bold mt-3 pt-3 border-t border-gray-100">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Copyright -->
        <div class="text-center text-xs text-gray-400 mt-8 pb-4">
            &copy; 2026 Hệ thống Quản lý Vật tư Bệnh viện - Professional Healthcare Supply Chain
        </div>
    </div>
@endsection