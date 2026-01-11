@extends('layouts.buyer')

@section('title', 'Xác nhận Đơn đặt hàng')
@section('header_title', 'Xác nhận Đơn đặt hàng')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Tổng tiền</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-blue-600">
                        {{ $order->order_code }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">
                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ get_status_class($order->status) }}">
                            {{ get_status_label($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if($order->status == 'CREATED')
                        <form action="{{ route('department.orders.confirm', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Xác nhận đã nhận hàng/dịch vụ?');">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                Xác nhận
                            </button>
                        </form>
                        <span class="text-gray-300">|</span>
                        <form action="{{ route('department.orders.reject', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Từ chối đơn hàng này?');">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                Từ chối
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400 text-sm italic">Đã xử lý</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                        Không có đơn hàng nào cần xử lý.
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
