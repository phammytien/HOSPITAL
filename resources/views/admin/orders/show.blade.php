@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')
@section('page-title', 'Chi tiết đơn hàng #' . $order->order_code)
@section('header_title', $order->order_code)
@section('page-subtitle', 'Thông tin chi tiết đơn hàng')

@section('content')

    {{-- Toast Notification --}}
    @if(session('success'))
        <div id="toast"
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[100] bg-white rounded-xl shadow-2xl border border-green-200 p-6 min-w-[400px]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 mb-1">Thành công!</h4>
                    <p class="text-gray-600 text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="toast"
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[100] bg-white rounded-xl shadow-2xl border border-red-200 p-6 min-w-[400px]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 mb-1">Lỗi!</h4>
                    <p class="text-gray-600 text-sm">{{ session('error') }}</p>
                </div>
                <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Back Button --}}
        <a href="{{ route('admin.orders') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>

        {{-- Status Update Form - Hidden for Admin --}}
        {{-- <div class="bg-white rounded-xl p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Cập nhật trạng thái</h3>
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái mới</label>
                    <select name="status" id="statusSelect" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="this.form.submit()">
                        <option value="CREATED" {{ $order->status == 'CREATED' ? 'selected' : '' }}>Mới tạo</option>
                        <option value="PENDING" {{ $order->status == 'PENDING' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="ORDERED" {{ $order->status == 'ORDERED' ? 'selected' : '' }}>Đã đặt hàng</option>
                        <option value="DELIVERING" {{ $order->status == 'DELIVERING' ? 'selected' : '' }}>Đang giao</option>
                        <option value="DELIVERED" {{ $order->status == 'DELIVERED' ? 'selected' : '' }}>Đã nhận hàng</option>
                        <option value="COMPLETED" {{ $order->status == 'COMPLETED' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="CANCELLED" {{ $order->status == 'CANCELLED' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="REJECTED" {{ $order->status == 'REJECTED' ? 'selected' : '' }}>Đã từ chối</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <input type="text" name="note" placeholder="Ghi chú (tùy chọn)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div> --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Order Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Order Info --}}
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin đơn hàng</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Mã đơn hàng</p>
                            <p class="font-semibold text-gray-900">{{ $order->order_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Ngày đặt hàng</p>
                            <p class="font-semibold text-gray-900">
                                {{ $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <!-- <div>
                            <p class="text-sm text-gray-500 mb-1">Nhà cung cấp</p>
                            <p class="font-semibold text-gray-900">{{ $order->supplier_name ?? 'N/A' }}</p>
                        </div> -->
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Khoa/Phòng</p>
                            <p class="font-semibold text-gray-900">{{ $order->department->department_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Chi tiết sản phẩm</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <div class="p-6">
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 mb-1">{{ $item->product->product_name ?? 'N/A' }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mb-3">
                                            {{ $item->product->category->category_name ?? 'N/A' }}</p>

                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-500 mb-1">Số lượng</p>
                                                <p class="font-semibold text-gray-900">{{ number_format($item->quantity, 2) }}
                                                    {{ $item->product->unit ?? '' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Đơn giá</p>
                                                <p class="font-semibold text-gray-900">
                                                    {{ number_format($item->unit_price, 0, ',', '.') }} đ</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Thành tiền</p>
                                                <p class="font-bold text-blue-600">
                                                    {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }} đ</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Summary --}}
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tổng quan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tổng tiền</span>
                            <span class="font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <p class="text-sm text-gray-500 mb-1">Trạng thái hiện tại</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ get_status_class($order->status) }}">
                                {{ get_status_label($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Approver Info --}}
                @if($order->approver)
                    <div class="bg-white rounded-xl p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Người phê duyệt</h3>
                        <div class="space-y-2 text-sm">
                            <p class="text-gray-900 font-semibold">{{ $order->approver->full_name }}</p>
                            <p class="text-gray-600">{{ $order->approver->email }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function closeToast() {
                const toast = document.getElementById('toast');
                if (toast) {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }
            }

            // Auto-hide toast after 3 seconds
            if (document.getElementById('toast')) {
                setTimeout(() => {
                    closeToast();
                }, 3000);
            }
        </script>
    @endpush
@endsection