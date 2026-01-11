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
            <a href="{{ route('department.dept_orders.index', ['status' => 'PROCESSING']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'PROCESSING' ? 'bg-orange-600 text-white shadow-md' : 'bg-orange-50 text-orange-600 hover:bg-orange-100' }}">
                Đã duyệt
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'DELIVERED']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'DELIVERED' ? 'bg-yellow-600 text-white shadow-md' : 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' }}">
                Chờ xác nhận
            </a>

            <a href="{{ route('department.dept_orders.index', ['status' => 'COMPLETED']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'COMPLETED' ? 'bg-green-600 text-white shadow-md' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                Hoàn thành
            </a>
            <a href="{{ route('department.dept_orders.index', ['status' => 'REJECTED']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'REJECTED' ? 'bg-red-600 text-white shadow-md' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                Đã từ chối
            </a>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Mã đơn hàng</th>
                            <th class="px-6 py-3">Ngày tạo</th>
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
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ number_format($order->total_amount ?? 0, 0, ',', '.') }} đ
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ get_status_class($order->status) }}">
                                        {{ get_status_label($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if($order->status == 'DELIVERED')
                                        <button type="button" onclick="openConfirmModal({{ $order->id }})"
                                            class="text-green-600 hover:text-green-800 font-medium inline-block"
                                            title="Xác nhận nhận bàn giao">
                                            <i class="fas fa-check-circle"></i> Xác nhận
                                        </button>
                                        <button onclick="openRejectModal('{{ $order->id }}', {{ $order->items->map(function ($i) { return ['id' => $i->id, 'name' => $i->product->product_name ?? 'SP']; }) }})"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                            title="Từ chối nhận hàng">
                                            <i class="fas fa-times-circle"></i> Từ chối
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeConfirmModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="confirmForm" action="" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Xác nhận hoàn tất đơn hàng
                                </h3>
                                <div class="mt-2 text-left">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Vui lòng đánh giá chất lượng dịch vụ trước khi xác nhận.
                                    </p>

                                    <!-- Rating -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Đánh giá chung</label>
                                        <div class="flex items-center space-x-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" onclick="setRating({{ $i }})"
                                                    class="star-rating text-2xl text-gray-300 focus:outline-none transition transform hover:scale-110"
                                                    data-rating="{{ $i }}">★</button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput" value="5">
                                    </div>

                                    <!-- Feedback -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ý kiến phản hồi (Tùy
                                            chọn)</label>
                                        <textarea name="feedback_content" rows="3"
                                            class="w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 block sm:text-sm border-gray-300 rounded-md"
                                            placeholder="Nhập ý kiến của bạn..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Xác nhận & Gửi đánh giá
                        </button>
                        <button type="button" onclick="closeConfirmModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openConfirmModal(id) {
            const form = document.getElementById('confirmForm');
            form.action = `/department/orders/${id}/confirm`;
            document.getElementById('confirmModal').classList.remove('hidden');
            setRating(5); // Default 5 stars
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

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

        // Reject Modal Functions
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
@endsection