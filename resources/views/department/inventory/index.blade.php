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
                                        <!-- Take (Lấy) Section -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-semibold text-green-700 w-8">Lấy:</span>
                                            <input type="number" id="qty-take-{{ $item->id }}" value="0" min="0" class="w-12 h-7 text-xs border border-green-200 rounded text-center focus:ring-1 focus:ring-green-500 outline-none" onkeydown="if(event.key === 'Enter') confirmAction({{ $item->id }}, 'take')">
                                            
                                            <button onclick="addToQty({{ $item->id }}, 'take', 1)" class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition border border-green-100 font-medium">+1</button>
                                            <button onclick="addToQty({{ $item->id }}, 'take', 2)" class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition border border-green-100 font-medium">+2</button>
                                            <button onclick="addToQty({{ $item->id }}, 'take', 5)" class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition border border-green-100 font-medium">+5</button>
                                            
                                            <button onclick="confirmAction({{ $item->id }}, 'take')" class="ml-1 px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition font-bold shadow-sm">
                                                Lấy
                                            </button>
                                        </div>

                                        <!-- Return (Trả) Section -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-semibold text-blue-700 w-8">Trả:</span>
                                            <input type="number" id="qty-return-{{ $item->id }}" value="0" min="0" class="w-12 h-7 text-xs border border-blue-200 rounded text-center focus:ring-1 focus:ring-blue-500 outline-none" onkeydown="if(event.key === 'Enter') confirmAction({{ $item->id }}, 'return')">
                                            
                                            <button onclick="addToQty({{ $item->id }}, 'return', 1)" class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition border border-blue-100 font-medium">+1</button>
                                            <button onclick="addToQty({{ $item->id }}, 'return', 2)" class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition border border-blue-100 font-medium">+2</button>
                                            <button onclick="addToQty({{ $item->id }}, 'return', 5)" class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition border border-blue-100 font-medium">+5</button>
                                            
                                            <button onclick="confirmAction({{ $item->id }}, 'return')" class="ml-1 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition font-bold shadow-sm">
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