<div class="space-y-6">
    <!-- Header Info -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
        <div>
            <p class="text-xs text-gray-500 font-medium uppercase">Mã yêu cầu</p>
            <p class="font-bold text-gray-900">{{ $request->request_code }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium uppercase">Khoa phòng</p>
            <p class="font-medium text-gray-900">{{ $request->department->department_name }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium uppercase">Người yêu cầu</p>
            <p class="font-medium text-gray-900">{{ $request->requester->full_name }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium uppercase">Trạng thái</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($request->status == 'SUBMITTED') bg-yellow-100 text-yellow-800
                @elseif($request->status == 'APPROVED') bg-green-100 text-green-800
                @elseif($request->status == 'REJECTED') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $request->status == 'SUBMITTED' ? 'Chờ duyệt' :
    ($request->status == 'APPROVED' ? 'Đã duyệt' :
        ($request->status == 'REJECTED' ? 'Đã từ chối' : $request->status)) }}
            </span>
        </div>
    </div>

    @if($request->note)
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <p class="text-sm text-blue-800"><span class="font-bold">Ghi chú:</span> {{ $request->note }}</p>
        </div>
    @endif

    <!-- Items Table -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">ĐVT</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">SL</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Đơn giá</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($request->items as $index => $item)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $item->product->product_name ?? 'N/A' }}
                            @if($item->reason)
                                <p class="text-xs text-gray-500 mt-0.5 italix">Lý do: {{ $item->reason }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $item->product->unit ?? 'Cái' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">
                            {{ number_format($item->expected_price, 0, ',', '.') }} ₫</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                            {{ number_format($item->quantity * $item->expected_price, 0, ',', '.') }} ₫</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-900">Tổng cộng:</td>
                    <td class="px-4 py-3 text-right font-bold text-blue-600 text-base">
                        {{ number_format($totalAmount, 0, ',', '.') }} ₫
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Actions (Optional: Quick Approve inside modal?) -->
    <!-- Leaving actions out for now as user just asked for "Show details", 
         but we could add Approve/Reject buttons here for convenience. -->
</div>