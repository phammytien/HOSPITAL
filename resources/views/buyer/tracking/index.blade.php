@extends('layouts.buyer')

@section('title', 'Theo dõi Giao hàng')
@section('header_title', 'Theo dõi Giao hàng')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Filters -->
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-4 items-center justify-between">
            <form action="{{ route('buyer.tracking.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-center w-full sm:w-auto">

                <select name="department_id"
                    class="border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Tất cả Khoa/Phòng --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>

                <select name="period" class="border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Tất cả Kỳ/Quý --</option>
                    @foreach($periods as $p)
                        <option value="{{ $p }}" {{ request('period') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                    Lọc
                </button>

                @if(request('department_id') || request('period'))
                    <a href="{{ route('buyer.tracking.index') }}" class="text-gray-500 text-sm hover:text-gray-700 underline">
                        Xóa lọc
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khoa/Phòng</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Trạng
                            thái</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Tiến
                            độ</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Hành
                            động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('buyer.tracking.show', $order->id) }}"
                                    class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $order->order_code ?? '#' . $order->id }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                {{ $order->department->department_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ get_status_class($order->status) }}">
                                    {{ get_status_label($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                <!-- Simple progress indicator or just count of items delivered? -->
                                @php
                                    $totalItems = $order->items_count ?? $order->items->count();
                                    // If order is COMPLETED, all items are considered delivered
                                    if (in_array($order->status, ['COMPLETED', 'DELIVERED'])) {
                                        $deliveredItems = $totalItems;
                                    } else {
                                        $deliveredItems = $order->items->whereIn('status', ['DELIVERED', 'COMPLETED'])->count();
                                    }
                                @endphp
                                {{ $deliveredItems }}/{{ $totalItems }} sản phẩm về kho
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('buyer.tracking.show', $order->id) }}"
                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center justify-end gap-1">
                                    @if(in_array($order->status, ['COMPLETED', 'CANCELLED']))
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    @else
                                        <i class="fas fa-truck-loading"></i> Cập nhật
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                Không có đơn hàng nào để theo dõi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
@endsection