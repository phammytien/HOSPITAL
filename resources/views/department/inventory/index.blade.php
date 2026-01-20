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
                <button onclick="openHistoryModal()"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 flex items-center shadow-sm mr-2">
                    <i class="fas fa-history mr-2"></i> Xem lịch sử
                </button>
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

    {{-- INVENTORY HISTORY MODAL --}}
    <div id="historyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeHistoryModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Lịch sử Xuất/Nhập Kho
                                </h3>
                                <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- Filters --}}
                            <div class="mt-4 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Loại xem</label>
                                    <select id="historyType" onchange="toggleHistoryFilters(); loadHistoryData();"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="daily">Chi tiết theo ngày (Tháng)</option>
                                        <option value="quarterly">Tổng hợp theo Quý</option>
                                    </select>
                                </div>

                                {{-- Month Filter (Daily) --}}
                                <div id="monthFilterGroup">
                                    <label class="block text-sm font-medium text-gray-700">Tháng</label>
                                    <select id="historyMonth" onchange="loadHistoryData()"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>Tháng {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Quarter Filter (Quarterly) --}}
                                <div id="quarterFilterGroup" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700">Quý</label>
                                    <select id="historyQuarter" onchange="loadHistoryData()"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="1" {{ ceil(date('n') / 3) == 1 ? 'selected' : '' }}>Quý 1 (T1-T3)
                                        </option>
                                        <option value="2" {{ ceil(date('n') / 3) == 2 ? 'selected' : '' }}>Quý 2 (T4-T6)
                                        </option>
                                        <option value="3" {{ ceil(date('n') / 3) == 3 ? 'selected' : '' }}>Quý 3 (T7-T9)
                                        </option>
                                        <option value="4" {{ ceil(date('n') / 3) == 4 ? 'selected' : '' }}>Quý 4 (T10-T12)
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Năm</label>
                                    <input type="number" id="historyYear" value="{{ date('Y') }}" min="2020" max="2099"
                                        onchange="loadHistoryData()"
                                        class="mt-1 block w-32 pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                </div>

                                <div>
                                    <button onclick="resetHistoryFilters()"
                                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                        Xóa lọc
                                    </button>
                                </div>
                            </div>

                            {{-- Data Table --}}
                            <div class="mt-4 overflow-x-auto min-h-[300px]">
                                <table class="min-w-full divide-y divide-gray-200" id="historyTable">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            {{-- Headers injected via JS --}}
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="historyTableBody">
                                        {{-- Data injected via JS --}}
                                    </tbody>
                                </table>
                                <div id="historyLoading" class="hidden text-center py-10">
                                    <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                                    <p class="text-gray-500 mt-2">Đang tải dữ liệu...</p>
                                </div>
                                <div id="historyEmpty" class="hidden text-center py-10 text-gray-500">
                                    Không tìm thấy dữ liệu nào.
                                </div>

                                {{-- Pagination --}}
                                <div id="historyPagination" class="hidden mt-4 flex justify-between items-center">
                                    <div class="text-sm text-gray-500">
                                        Hiển thị <span id="paginationStart">1</span>-<span id="paginationEnd">10</span> /
                                        <span id="paginationTotal">0</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="changePage(-1)" id="prevPageBtn"
                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Trước
                                        </button>
                                        <button onclick="changePage(1)" id="nextPageBtn"
                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Sau
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- History Modal Logic ---
        let historyFullData = [];
        let historyCurrentPage = 1;
        const historyItemsPerPage = 10;

        function openHistoryModal() {
            document.getElementById('historyModal').classList.remove('hidden');
            loadHistoryData(); // Load default
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }

        function toggleHistoryFilters() {
            const type = document.getElementById('historyType').value;
            if (type === 'daily') {
                document.getElementById('monthFilterGroup').classList.remove('hidden');
                document.getElementById('quarterFilterGroup').classList.add('hidden');
            } else {
                document.getElementById('monthFilterGroup').classList.add('hidden');
                document.getElementById('quarterFilterGroup').classList.remove('hidden');
            }
        }

        function resetHistoryFilters() {
            document.getElementById('historyType').value = 'daily';
            document.getElementById('historyMonth').value = new Date().getMonth() + 1;
            document.getElementById('historyQuarter').value = Math.ceil((new Date().getMonth() + 1) / 3);
            document.getElementById('historyYear').value = new Date().getFullYear();
            toggleHistoryFilters();
            loadHistoryData();
        }

        function loadHistoryData() {
            historyCurrentPage = 1; // Reset to first page
            const type = document.getElementById('historyType').value;
            const month = document.getElementById('historyMonth').value;
            const quarter = document.getElementById('historyQuarter').value;
            const year = document.getElementById('historyYear').value;

            // UI Loading
            document.getElementById('historyLoading').classList.remove('hidden');
            document.getElementById('historyEmpty').classList.add('hidden');
            document.getElementById('historyPagination').classList.add('hidden');
            document.getElementById('historyTableBody').innerHTML = '';

            // Set Headers
            const thead = document.querySelector('#historyTable thead tr');
            if (type === 'daily') {
                thead.innerHTML = `
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày giờ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người thực hiện</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ghi chú</th>
                        `;
            } else {
                thead.innerHTML = `
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn vị</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng Nhập / Trả lại</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng Lấy / Xuất</th>
                        `;
            }

            // Fetch Data
            const params = new URLSearchParams({
                type: type,
                month: month,
                year: year,
                quarter: quarter
            });

            fetch(`{{ route('department.inventory.history_data') }}?${params.toString()}`)
                .then(response => response.json())
                .then(res => {
                    historyFullData = res.data || [];
                    document.getElementById('historyLoading').classList.add('hidden');

                    if (historyFullData.length === 0) {
                        document.getElementById('historyEmpty').classList.remove('hidden');
                        return;
                    }

                    renderHistoryPage();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('historyLoading').classList.add('hidden');
                    alert('Lỗi khi tải dữ liệu. Vui lòng thử lại.');
                });
        }

        function renderHistoryPage() {
            const type = document.getElementById('historyType').value;
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';

            const totalItems = historyFullData.length;
            const totalPages = Math.ceil(totalItems / historyItemsPerPage);
            const startIdx = (historyCurrentPage - 1) * historyItemsPerPage;
            const endIdx = Math.min(startIdx + historyItemsPerPage, totalItems);
            const pageData = historyFullData.slice(startIdx, endIdx);

            if (type === 'daily') {
                pageData.forEach(row => {
                    let badgeClass = '';
                    let badgeText = '';

                    switch (row.action_type) {
                        case 'import_order':
                            badgeClass = 'bg-green-100 text-green-800';
                            badgeText = 'Nhập kho (Đơn hàng)';
                            break;
                        case 'return':
                            badgeClass = 'bg-yellow-100 text-yellow-800';
                            badgeText = 'Trả lại';
                            break;
                        case 'take':
                            badgeClass = 'bg-blue-100 text-blue-800';
                            badgeText = 'Lấy dùng';
                            break;
                        default:
                            badgeClass = 'bg-gray-100 text-gray-800';
                            badgeText = 'Khác';
                    }

                    tbody.innerHTML += `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${row.date}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${row.product_name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeClass}">${badgeText}</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">${row.quantity}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${row.performed_by}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="${row.note || ''}">${row.note || '-'}</td>
                                </tr>
                            `;
                });
            } else {
                pageData.forEach(row => {
                    tbody.innerHTML += `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${row.product_name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${row.product_unit}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold">+${row.total_import}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">-${row.total_export}</td>
                                </tr>
                            `;
                });
            }

            // Update pagination UI
            document.getElementById('paginationStart').textContent = startIdx + 1;
            document.getElementById('paginationEnd').textContent = endIdx;
            document.getElementById('paginationTotal').textContent = totalItems;
            document.getElementById('historyPagination').classList.remove('hidden');

            // Enable/disable buttons
            document.getElementById('prevPageBtn').disabled = historyCurrentPage === 1;
            document.getElementById('nextPageBtn').disabled = historyCurrentPage === totalPages;
        }

        function changePage(direction) {
            const totalPages = Math.ceil(historyFullData.length / historyItemsPerPage);
            historyCurrentPage += direction;
            if (historyCurrentPage < 1) historyCurrentPage = 1;
            if (historyCurrentPage > totalPages) historyCurrentPage = totalPages;
            renderHistoryPage();
        }
    </script>
@endsection