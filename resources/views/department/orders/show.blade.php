@extends('layouts.department')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
    <div class="mb-6">
        <a href="{{ route('department.dept_orders.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Tracking Status -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-full">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-route text-blue-600 mr-2"></i> Theo dõi đơn hàng
                </h3>

                <div class="relative pl-6 border-l-2 border-gray-200 space-y-8">
                    @php
                        $steps = [
                            ['status' => 'ORDERED', 'label' => 'Đã đặt hàng', 'desc' => 'Đơn hàng đã được duyệt và gửi tới nhà cung cấp', 'icon' => 'fa-file-invoice'],
                            ['status' => 'DELIVERING', 'label' => 'Đang vận chuyển', 'desc' => 'Hàng đang trên đường tới kho', 'icon' => 'fa-truck'],
                            ['status' => 'DELIVERED', 'label' => 'Đã giao hàng', 'desc' => 'Hàng đã về tới kho khoa/phòng', 'icon' => 'fa-box-open'],
                            ['status' => 'COMPLETED', 'label' => 'Hoàn thành', 'desc' => 'Đã xác nhận nhận hàng và kết thúc đơn', 'icon' => 'fa-check-circle'],
                        ];

                        $currentStatusIndex = -1;
                        if ($order->status == 'ORDERED')
                            $currentStatusIndex = 0;
                        elseif ($order->status == 'DELIVERING')
                            $currentStatusIndex = 1;
                        elseif ($order->status == 'DELIVERED')
                            $currentStatusIndex = 2;
                        elseif ($order->status == 'COMPLETED')
                            $currentStatusIndex = 3;
                    @endphp

                    @foreach($steps as $index => $step)
                        <div class="relative mb-8">
                            <span
                                class="absolute -left-[33px] flex items-center justify-center w-10 h-10 rounded-full border-4 border-white 
                                        {{ $index <= $currentStatusIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </span>
                            <div class="ml-4">
                                <h4
                                    class="text-base font-bold {{ $index <= $currentStatusIndex ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $step['label'] }}
                                </h4>
                                <p class="text-sm text-gray-500">{{ $step['desc'] }}</p>
                                @if($index == $currentStatusIndex)
                                    <span
                                        class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold animate-pulse">
                                        Hiện tại
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Order Details & Actions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">#{{ $order->order_code ?? $order->id }}</h2>
                        <p class="text-gray-500 text-sm">Ngày tạo: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-500 text-sm">Yêu cầu gốc:
                            <span
                                class="font-medium text-gray-700">{{ $order->purchaseRequest->request_code ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ get_status_class($order->status) }}">
                            {{ get_status_label($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <h3 class="font-bold text-gray-800 mb-3">Danh sách sản phẩm</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-lg">Sản phẩm</th>
                                    <th class="px-4 py-3">Số lượng</th>
                                    <th class="px-4 py-3">Đơn vị</th>
                                    <th class="px-4 py-3 rounded-r-lg">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($order->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <p>{{ $item->product->product_name ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $item->product->product_code ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-900 font-semibold">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $item->product->unit ?? 'Cái' }}</td>
                                        <td class="px-4 py-3 text-gray-400 italic">{{ $item->note ?? '--' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions Section (Only for DELIVERED status) -->
            @if($order->status == 'DELIVERED')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Xác nhận đơn hàng</h3>
                    <p class="text-gray-600 mb-6 font-sm">
                        Vui lòng kiểm tra kỹ số lượng và chất lượng hàng hóa trước khi xác nhận.
                    </p>

                    <form action="{{ route('department.dept_orders.confirm', $order->id) }}" method="POST">
                        @csrf
                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá chất lượng dịch vụ</label>
                            <div class="flex items-center space-x-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})"
                                        class="star-rating text-3xl text-gray-300 focus:outline-none transition transform hover:scale-110"
                                        data-rating="{{ $i }}">★</button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="5">
                        </div>

                        <!-- Feedback -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ý kiến phản hồi (Tùy chọn)</label>
                            <textarea name="feedback_content" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                placeholder="Nhập ý kiến của bạn..."></textarea>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="flex-1 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Xác nhận đã nhận hàng
                            </button>
                            <!-- Reject button could be a modal trigger or separate form -->
                            <button type="button" onclick="document.getElementById('rejectSection').classList.toggle('hidden')"
                                class="px-6 py-3 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition">
                                <i class="fas fa-times-circle mr-2"></i> Từ chối
                            </button>
                        </div>
                    </form>

                    <!-- Hidden Reject Form -->
                    <div id="rejectSection" class="hidden mt-6 pt-6 border-t border-gray-200">
                        <form action="{{ route('department.dept_orders.reject', $order->id) }}" method="POST">
                            @csrf
                            <label class="block text-sm font-medium text-red-700 mb-2">Lý do từ chối *</label>
                            <textarea name="reason" rows="3" required
                                class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none mb-4"
                                placeholder="Nhập lý do từ chối..."></textarea>
                            <button type="submit"
                                class="w-full py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">
                                Xác nhận từ chối
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function setRating(rating) {
                document.getElementById('ratingInput').value = rating;
                const stars = document.querySelectorAll('.star-rating');
                stars.forEach(star => {
                    if (parseInt(star.dataset.rating) <= rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            }
            // Init default rating
            setRating(5);
        </script>
    @endpush
@endsection