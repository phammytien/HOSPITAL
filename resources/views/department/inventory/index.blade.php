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
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-20">
                                Hình ảnh</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tên vật
                                tư</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Loại
                                vật tư</th>
                            <th class="px-2 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">
                                ĐVT</th>
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
                                    @php
                                        $productImage = getProductImage($item->id);
                                    @endphp
                                    <div
                                        class="w-16 h-16 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center shadow-inner group-hover:shadow transition duration-200">
                                        @if($productImage)
                                            <img src="{{ $productImage }}" alt="{{ $item->product_name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="flex flex-col items-center opacity-30">
                                                <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                                <span class="text-[8px] text-gray-500 mt-1 uppercase font-black">No Image</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-base font-bold text-gray-900 block mb-1">{{ $item->product_name }}</span>
                                    <span
                                        class="text-[10px] text-gray-400 font-mono tracking-wider">{{ $item->product_code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full uppercase">
                                        {{ $item->category->category_name ?? 'Vật tư' }}
                                    </span>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-gray-700">{{ $item->unit }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-xl font-black {{ $qty == 0 ? 'text-red-500' : ($qty <= 10 ? 'text-orange-500' : 'text-gray-900') }}">
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
                                    <div class="flex flex-col gap-3">
                                        <!-- Take Section -->
                                        <div class="flex items-center gap-2">
                                            <input type="number" id="qty-take-{{ $item->id }}" value="0" min="0"
                                                class="w-14 h-11 text-lg border-2 border-green-200 rounded-lg text-center font-bold focus:ring-2 focus:ring-green-500 outline-none transition-all focus:border-green-400"
                                                onkeydown="if(event.key === 'Enter') confirmAction({{ $item->id }}, 'take')">

                                            <button onclick="addToQty({{ $item->id }}, 'take', 1)"
                                                class="px-5 py-2 text-base bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition border border-green-100 font-bold">+1</button>
                                            <button onclick="addToQty({{ $item->id }}, 'take', 2)"
                                                class="px-5 py-2 text-base bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition border border-green-100 font-bold">+2</button>
                                            <button onclick="addToQty({{ $item->id }}, 'take', 5)"
                                                class="px-5 py-2 text-base bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition border border-green-100 font-bold">+5</button>

                                            <button onclick="confirmAction({{ $item->id }}, 'take')"
                                                class="ml-auto px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-black shadow-md text-sm uppercase tracking-wider">
                                                Lấy
                                            </button>
                                        </div>

                                        <!-- Return Section -->
                                        <div class="flex items-center gap-2">
                                            <input type="number" id="qty-return-{{ $item->id }}" value="0" min="0"
                                                class="w-14 h-11 text-lg border-2 border-blue-200 rounded-lg text-center font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all focus:border-blue-400"
                                                onkeydown="if(event.key === 'Enter') confirmAction({{ $item->id }}, 'return')">

                                            <button onclick="addToQty({{ $item->id }}, 'return', 1)"
                                                class="px-5 py-2 text-base bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition border border-blue-100 font-bold">+1</button>
                                            <button onclick="addToQty({{ $item->id }}, 'return', 2)"
                                                class="px-5 py-2 text-base bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition border border-blue-100 font-bold">+2</button>
                                            <button onclick="addToQty({{ $item->id }}, 'return', 5)"
                                                class="px-5 py-2 text-base bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition border border-blue-100 font-bold">+5</button>

                                            <button onclick="confirmAction({{ $item->id }}, 'return')"
                                                class="ml-auto px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-black shadow-md text-sm uppercase tracking-wider">
                                                Trả
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
        function addToQty(productId, action, amount) {
            const inputId = `qty-${action}-${productId}`;
            const input = document.getElementById(inputId);
            if (input) {
                let currentVal = parseInt(input.value) || 0;
                input.value = currentVal + amount;
            }
        }

        function confirmAction(productId, action) {
            const inputId = `qty-${action}-${productId}`;
            const input = document.getElementById(inputId);

            if (!input) return;

            const quantity = parseInt(input.value);

            if (!quantity || quantity <= 0) {
                alert('Vui lòng nhập số lượng lớn hơn 0!');
                return;
            }

            // Reuse existing quickAction logic but pass true for skipConfirm if needed, 
            // or just call it directly. Ideally we prompt once.
            quickAction(productId, action, quantity);
        }

        function quickAction(productId, action, quantity) {
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
                        // Auto reload
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thực hiện thao tác!');
                });
        }
    </script>
@endsection