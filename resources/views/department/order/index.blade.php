@extends('layouts.department')

@section('title', 'Xác nhận đơn hàng')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Xác nhận đơn hàng</h1>
                <p class="text-gray-500 mt-1">Danh sách đơn hàng từ bộ phận mua sắm cần xác nhận</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap gap-2">
            <a href="{{ route('department.dept_orders.index', ['status' => 'all']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status', 'all') == 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tất cả
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'CREATED']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'CREATED' ? 'bg-orange-600 text-white shadow-md' : 'bg-orange-50 text-orange-600 hover:bg-orange-100' }}">
                Mới tạo
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'PENDING']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'PENDING' ? 'bg-yellow-600 text-white shadow-md' : 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' }}">
                Chờ xử lý
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'DELIVERING']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'DELIVERING' ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                Đang giao
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'DELIVERED']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'DELIVERED' ? 'bg-purple-600 text-white shadow-md' : 'bg-purple-50 text-purple-600 hover:bg-purple-100' }}">
                Đã giao hàng
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'COMPLETED']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'COMPLETED' ? 'bg-green-600 text-white shadow-md' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                Đã hoàn tất
            </a>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Mã đơn hàng</th>
                            <th class="px-6 py-3">Ngày đặt</th>
                            <th class="px-6 py-3">Ngày giao (Dự kiến)</th>
                            <th class="px-6 py-3">Tổng tiền</th>
                            <th class="px-6 py-3">Trạng thái</th>
                            <th class="px-6 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $order->order_code ?? '#' . $order->id }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-blue-600 font-medium">
                                    {{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') : '--' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ number_format($order->total_amount ?? 0, 0, ',', '.') }} đ
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusLabel = match ($order->status) {
                                            'CREATED' => 'Mới tạo',
                                            'PENDING' => 'Chờ xử lý',
                                            'DELIVERING' => 'Đang giao',
                                            'DELIVERED' => 'Đã giao hàng',
                                            'COMPLETED' => 'Đã hoàn tất',

                                            'CANCELLED' => 'Đã hủy',
                                            default => $order->status
                                        };
                                        $statusClass = match ($order->status) {
                                            'CREATED' => 'bg-gray-100 text-gray-700',
                                            'PENDING' => 'bg-yellow-100 text-yellow-700',
                                            'DELIVERING' => 'bg-blue-100 text-blue-700',
                                            'DELIVERED' => 'bg-purple-100 text-purple-700',
                                            'COMPLETED' => 'bg-green-100 text-green-700',

                                            'CANCELLED' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <!-- Allow confirmation if Delivering, Delivered or Paid -->
                                    <!-- Allow confirmation only if Delivered (at Warehouse) -->
                                    @if($order->status == 'DELIVERED')
                                        <form action="{{ route('department.dept_orders.confirm', $order->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Xác nhận đã nhận đủ hàng hóa từ kho? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-xs shadow-sm transition flex items-center gap-1"
                                                title="Xác nhận nhận bàn giao">
                                                <i class="fas fa-check-double"></i> Xác nhận đã nhận
                                            </button>
                                        </form>
                                    @elseif($order->status == 'COMPLETED')
                                        <span class="text-green-600 text-xs font-bold flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i> Đã nhận
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Chờ hàng về</span>
                                    @endif

                                    <!-- Reject Button (Only if Delivered) -->
                                    @if($order->status == 'DELIVERED')
                                                        <button onclick="openRejectModal('{{ $order->id }}', {{ $order->items->map(function ($i) {
                                        return ['id' => $i->id, 'name' => $i->product->product_name ?? 'SP']; }) }})"
                                                            class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 font-medium text-xs shadow-sm transition flex items-center gap-1"
                                                            title="Từ chối nhận hàng">
                                                            <i class="fas fa-times"></i> Từ chối
                                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                        <p>Không có đơn hàng nào</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-red-50">
                <h3 class="font-bold text-red-700 text-lg">Từ chối nhận hàng</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="rejectForm" action="" method="POST" class="p-6">
                @csrf
                <p class="text-sm text-gray-600 mb-4">Vui lòng chọn lý do từ chối nhận hàng:</p>

                <div class="space-y-4">
                    <!-- Option 1: Wrong Product -->
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reason_option" value="wrong_product"
                                class="text-red-600 focus:ring-red-500" onclick="toggleReason('wrong_product')">
                            <span class="font-medium text-gray-700">Sai sản phẩm</span>
                        </label>
                        <div id="wrongProductList"
                            class="mt-2 ml-6 hidden space-y-2 p-3 bg-gray-50 rounded border border-gray-200 max-h-40 overflow-y-auto">
                            <!-- Items will be populated by JS -->
                        </div>
                    </div>

                    <!-- Option 2: Other Reason -->
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="reason_option" value="other" class="text-red-600 focus:ring-red-500"
                                onclick="toggleReason('other')">
                            <span class="font-medium text-gray-700">Lý do khác</span>
                        </label>
                        <textarea id="otherReasonInput" name="other_reason" rows="3"
                            class="mt-2 ml-6 hidden w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                            placeholder="Nhập lý do từ chối..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium text-sm transition">
                        Hủy
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm transition shadow-sm">
                        Xác nhận Từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(orderId, items) {
            const form = document.getElementById('rejectForm');
            form.action = `/department/orders/${orderId}/reject`;

            const listContainer = document.getElementById('wrongProductList');
            listContainer.innerHTML = '';

            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                            <input type="checkbox" name="wrong_items[]" value="${item.id}" class="rounded text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">${item.name}</span>
                        `;
                listContainer.appendChild(div);
            });

            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function toggleReason(type) {
            const list = document.getElementById('wrongProductList');
            const text = document.getElementById('otherReasonInput');

            if (type === 'wrong_product') {
                list.classList.remove('hidden');
                text.classList.add('hidden');
                text.required = false;
            } else {
                list.classList.add('hidden');
                text.classList.remove('hidden');
                text.required = true;
            }
        }
    </script>
@endsection