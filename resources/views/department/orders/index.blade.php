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
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('department.dept_orders.index', ['status' => 'all']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status', 'all') == 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Tất cả
                </a>

                <a href="{{ route('department.dept_orders.index', ['status' => 'CREATED']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'CREATED' ? 'bg-yellow-600 text-white shadow-md' : 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' }}">
                    Chờ xác nhận
                </a>

                <a href="{{ route('department.dept_orders.index', ['status' => 'PAID']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'PAID' ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                    Đã thanh toán
                </a>

                <a href="{{ route('department.dept_orders.index', ['status' => 'COMPLETED']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'COMPLETED' ? 'bg-green-600 text-white shadow-md' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                    Hoàn thành
                </a>

                <a href="{{ route('department.dept_orders.index', ['status' => 'CANCELLED']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'CANCELLED' ? 'bg-red-600 text-white shadow-md' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                    Đã hủy
                </a>
            </div>

            <!-- History Icon Button -->
            <a href="{{ route('department.dept_orders.index', ['status' => 'history']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 {{ request('status') == 'history' ? 'bg-gray-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                title="Lịch sử">
                <i class="fas fa-history"></i>
                <span class="hidden sm:inline">Lịch sử</span>
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
                                    <a href="{{ route('department.dept_orders.show', $order->id) }}" 
                                        class="px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 font-medium transition">
                                        Chi tiết
                                    </a>
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
@endsection