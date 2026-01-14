@extends('layouts.department')

@section('title', 'Kho Khoa Phòng')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">QUẢN LÝ KHO HÀNG KHOA PHÒNG</h2>
                <p class="text-gray-500 mt-1">Theo dõi và quản lý tồn kho vật tư y tế tại
                    {{ $warehouse->department->department_name ?? 'Khoa' }}
                </p>
                @if(!$warehouse)
                    <p class="text-red-500 mt-2"><i class="fas fa-exclamation-triangle"></i> Khoa của bạn chưa được cấp kho
                        hàng.</p>
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('department.inventory.export') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 flex items-center shadow-sm">
                    <i class="fas fa-download mr-2"></i> Xuất báo cáo
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('department.inventory.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                        placeholder="Tìm kiếm theo mã sản phẩm hoặc tên vật tư">
                </div>

                <div class="w-full md:w-48">
                    <select name="cat" onchange="this.form.submit()"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                        <option value="">Tất cả loại vật tư</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('cat') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-48">
                    <select name="stock_status" onchange="this.form.submit()"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                        <option value="">Trạng thái tồn</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Còn hàng
                        </option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Sắp hết
                        </option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Hết
                            hàng</option>
                    </select>
                </div>

                <button type="submit"
                    class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-black transition flex items-center justify-center font-medium">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mã sản
                                phẩm</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tên vật
                                tư</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Loại
                                vật tư</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Đơn vị
                                tính</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Số
                                lượng tồn</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Hành
                                động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $item)
                            @php
                                $qty = $item->stock_quantity_dept ?? 0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-700">{{ $item->product_code }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900">{{ $item->product_name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full uppercase">
                                        {{ $item->category->category_name ?? 'Vật tư' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">{{ $item->unit }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-lg font-bold {{ $qty == 0 ? 'text-red-500' : ($qty <= 10 ? 'text-orange-500' : 'text-gray-900') }}">
                                            {{ number_format($qty) }}
                                        </span>
                                        @if($qty == 0)
                                            <span class="text-[10px] uppercase font-bold text-red-500">Hết hàng</span>
                                        @elseif($qty <= 10)
                                            <span class="text-[10px] uppercase font-bold text-orange-500">Sắp hết</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <!-- Quick Take (Lấy) Buttons -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-semibold text-green-700 w-8">Lấy:</span>
                                            <button onclick="quickAction({{ $item->id }}, 'take', 1)"
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition font-medium">1</button>
                                            <button onclick="quickAction({{ $item->id }}, 'take', 2)"
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition font-medium">2</button>
                                            <button onclick="quickAction({{ $item->id }}, 'take', 3)"
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition font-medium">3</button>
                                            <button onclick="quickAction({{ $item->id }}, 'take', 5)"
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition font-medium">5</button>
                                            <button onclick="quickAction({{ $item->id }}, 'take', 10)"
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition font-medium">10</button>
                                            <button onclick="customAction({{ $item->id }}, 'take')"
                                                class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition font-medium"
                                                title="Số tùy chỉnh">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                        <!-- Quick Return (Trả) Buttons -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-semibold text-blue-700 w-8">Trả:</span>
                                            <button onclick="quickAction({{ $item->id }}, 'return', 1)"
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-medium">1</button>
                                            <button onclick="quickAction({{ $item->id }}, 'return', 2)"
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-medium">2</button>
                                            <button onclick="quickAction({{ $item->id }}, 'return', 3)"
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-medium">3</button>
                                            <button onclick="quickAction({{ $item->id }}, 'return', 5)"
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-medium">5</button>
                                            <button onclick="quickAction({{ $item->id }}, 'return', 10)"
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-medium">10</button>
                                            <button onclick="customAction({{ $item->id }}, 'return')"
                                                class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium"
                                                title="Số tùy chỉnh">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                        <p>Không tìm thấy vật tư nào trong kho</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center bg-gray-50">
                    <span class="text-sm text-gray-500">Hiển thị {{ $products->firstItem() }}-{{ $products->lastItem() }}
                        trên tổng số {{ $products->total() }} mặt hàng</span>
                    <div>
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function quickAction(productId, action, quantity) {
            const actionText = action === 'take' ? 'lấy' : 'trả';
            const confirmMsg = `Bạn có chắc muốn ${actionText} ${quantity} sản phẩm này?`;

            if (confirm(confirmMsg)) {
                // Show loading state
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch('{{ route("department.inventory.quick-action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        action: action,
                        quantity: quantity
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Auto reload without showing alert
                            location.reload();
                        } else {
                            alert(data.message || 'Có lỗi xảy ra!');
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi thực hiện thao tác!');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            }
        }

        function customAction(productId, action) {
            const actionText = action === 'take' ? 'lấy' : 'trả';
            const quantity = prompt(`Nhập số lượng muốn ${actionText}:`, '1');

            if (quantity && !isNaN(quantity) && parseInt(quantity) > 0) {
                quickAction(productId, action, parseInt(quantity));
            } else if (quantity !== null) {
                alert('Vui lòng nhập số lượng hợp lệ!');
            }
        }
    </script>
@endsection