@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Quản lý đơn hàng')
@section('header_title', 'Quản lý đơn hàng')
@section('page-subtitle', 'Xem và quản lý tất cả đơn hàng mua sắm')

@section('content')
    <div class="space-y-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tổng đơn hàng</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Chờ xử lý</p>
                        <h3 class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>



            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Hoàn thành</p>
                        <h3 class="text-2xl font-bold text-green-600">{{ number_format($stats['completed']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Đã hủy</p>
                        <h3 class="text-2xl font-bold text-red-600">{{ number_format($stats['cancelled']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <form method="GET" action="{{ route('admin.orders') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã đơn, nhà cung cấp..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Chờ xử lý</option>

                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Khoa/Phòng</label>
                    <select name="department_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i> Lọc
                    </button>
                </div>
            </form>
        </div>

        {{-- Orders Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã đơn hàng</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Khoa/Phòng</th>
                            <!-- <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nhà cung cấp</th> -->
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày đặt</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $order->order_code }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900">{{ $order->department->department_name ?? 'N/A' }}</p>
                                </td>
                                <!-- <td class="px-6 py-4">
                                    <p class="text-gray-900">{{ $order->supplier_name ?? 'N/A' }}</p>
                                </td> -->
                                <td class="px-6 py-4">
                                    <p class="text-gray-600">
                                        {{ $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ get_status_class($order->status) }}">
                                        {{ get_status_label($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="text-blue-600 hover:text-blue-700 font-medium">
                                        <i class="fas fa-eye mr-1"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                    <p>Không có đơn hàng nào</p>
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