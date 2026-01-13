@extends('layouts.department')

@section('title', 'Danh mục sản phẩm')

@section('content')
    <div class="h-full flex flex-col">
        <!-- Header & Filter -->
        <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Danh mục sản phẩm</h1>

                <!-- Search -->
                <form action="{{ route('department.catalog.index') }}" method="GET" class="relative w-full md:w-96">
                    @if(request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="Tìm kiếm tên sản phẩm, mã số...">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </form>

                <button onclick="openSuggestModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-plus"></i> Đề xuất sản phẩm
                </button>
            </div>

            <!-- Categories -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('department.catalog.index', ['search' => request('search')]) }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ !request('category_id') || request('category_id') == 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Tất cả
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('department.catalog.index', ['category_id' => $cat->id, 'search' => request('search')]) }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ request('category_id') == $cat->id ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $cat->category_name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto pb-24">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition cursor-pointer relative overflow-hidden product-card"
                            onclick="toggleProductSelection(this)" data-id="{{ $product->id }}"
                            data-code="{{ $product->product_code }}" data-name="{{ $product->product_name }}"
                            data-unit="{{ $product->unit }}" data-price="{{ $product->unit_price }}">
                            <!-- Selection Overlay -->


                            <!-- Checkbox (Visual) -->
                            <div class="absolute top-3 right-3 z-10">
                                <div
                                    class="w-6 h-6 rounded border-2 border-gray-300 bg-white flex items-center justify-center transition group-hover:border-blue-500 selection-checkbox text-blue-600">
                                    <i class="fas fa-check hidden text-xs"></i>
                                </div>
                            </div>

                            <!-- Image Placeholder -->
                            <div
                                class="aspect-w-4 aspect-h-3 bg-gray-100 flex items-center justify-center text-4xl font-bold text-gray-300 uppercase select-none">
                                {{ substr($product->product_name, 0, 1) }}
                            </div>

                            <div class="p-4">
                                <div class="mb-2">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-600">
                                        {{ $product->category->category_name ?? 'Khác' }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-1 line-clamp-2 h-12" title="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-2">{{ $product->product_code }}</p>

                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-sm text-gray-500">{{ $product->unit }}</span>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" min="1" value="1"
                                            class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm focus:ring-blue-500 focus:border-blue-500 quantity-input"
                                            onclick="event.stopPropagation()" onchange="updateQuantity(this, '{{ $product->id }}')">
                                        <span
                                            class="text-lg font-bold text-blue-600">{{ number_format($product->unit_price, 0, ',', '.') }}
                                            đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                    <i class="fas fa-box-open text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium">Không tìm thấy sản phẩm nào</p>
                </div>
            @endif
        </div>

        <!-- Floating Action Bar -->
        <div id="floatingBar"
            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] p-4 transform translate-y-full transition-transform duration-300 z-50 md:pl-64">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-bold">
                        <span id="selectedCount">0</span> sản phẩm đã chọn
                    </div>
                    <button onclick="clearSelection()" class="text-gray-500 hover:text-red-500 text-sm font-medium">
                        Bỏ chọn tất cả
                    </button>
                </div>
                <button onclick="proceedToCreateRequest()"
                    class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl transition flex items-center justify-center space-x-2">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tạo yêu cầu với sản phẩm đã chọn</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // State management
        let selectedProducts = []; // Array of objects {id, code, name, unit, price}

        // Load from localStorage if exists (optional, to keep state across paginations)
        // For simplicity, we might just use session storage or standard localStorage

        function toggleProductSelection(card) {
            const id = card.dataset.id;
            const index = selectedProducts.findIndex(p => p.id === id);
            const checkbox = card.querySelector('.selection-checkbox');
            const checkIcon = checkbox.querySelector('i');

            if (index === -1) {
                // Add
                selectedProducts.push({
                    id: id,
                    code: card.dataset.code,
                    name: card.dataset.name,
                    unit: card.dataset.unit,
                    price: parseFloat(card.dataset.price),
                    quantity: parseInt(card.querySelector('.quantity-input').value) || 1
                });
                card.classList.add('ring-2', 'ring-blue-600');
                checkbox.classList.add('bg-blue-600', 'border-blue-600');
                checkbox.classList.remove('bg-white', 'border-gray-300');
                checkIcon.classList.remove('hidden');
                checkIcon.classList.add('text-white');
                checkIcon.classList.remove('text-blue-600');
            } else {
                // Remove
                selectedProducts.splice(index, 1);
                card.classList.remove('ring-2', 'ring-blue-600');
                checkbox.classList.remove('bg-blue-600', 'border-blue-600');
                checkbox.classList.add('bg-white', 'border-gray-300');
                checkIcon.classList.add('hidden');
                checkIcon.classList.remove('text-white');
            }

            updateFloatingBar();
        }

        function updateQuantity(input, id) {
            const qty = parseInt(input.value) || 1;
            const index = selectedProducts.findIndex(p => p.id === id);
            if (index !== -1) {
                selectedProducts[index].quantity = qty;
            }
        }

        function updateFloatingBar() {
            const bar = document.getElementById('floatingBar');
            const countSpan = document.getElementById('selectedCount');

            countSpan.innerText = selectedProducts.length;

            if (selectedProducts.length > 0) {
                bar.classList.remove('translate-y-full');
            } else {
                bar.classList.add('translate-y-full');
            }
        }

        function clearSelection() {
            selectedProducts = [];
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-600');
                const cb = card.querySelector('.selection-checkbox');
                cb.classList.remove('bg-blue-600', 'border-blue-600');
                cb.classList.add('bg-white', 'border-gray-300');
                cb.querySelector('i').classList.add('hidden');
            });
            updateFloatingBar();
        }

        function proceedToCreateRequest() {
            if (selectedProducts.length === 0) return;

            // Save to localStorage
            localStorage.setItem('pendingRequestCart', JSON.stringify(selectedProducts));

            // Redirect
            window.location.href = "{{ route('department.requests.create') }}";
        }

        // Init: Check existing selection (if we want to persist across pages, we would need to check IDs against localStorage)
        document.addEventListener('DOMContentLoaded', () => {
            // Optional: restore selection from localStorage if implementing persistence across/pages
        });
    </script>

    <!-- Suggestion Modal -->
    <div id="suggestModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800 text-lg">Đề xuất sản phẩm mới</h3>
                <button onclick="closeSuggestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200">
                <button onclick="switchTab('manual')" id="tab-manual"
                    class="flex-1 px-4 py-3 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 focus:outline-none">
                    Nhập thủ công
                </button>
                <button onclick="switchTab('import')" id="tab-import"
                    class="flex-1 px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                    Nhập từ File (CSV)
                </button>
            </div>

            <div class="p-6">
                <!-- Manual Form -->
                <form id="manualForm" action="{{ route('department.catalog.suggest') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
                            <input type="text" name="product_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                                placeholder="VD: Bông băng y tế...">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính <span class="text-red-500">*</span></label>
                                <input type="text" name="unit" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                                    placeholder="VD: Cuộn, Hộp...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá dự kiến (VNĐ)</label>
                                <input type="number" name="unit_price"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                                    placeholder="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả / Ghi chú</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                                placeholder="Thông số kỹ thuật, lý do đề xuất..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="closeSuggestModal()"
                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium">Hủy</button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Gửi đề xuất</button>
                        </div>
                    </div>
                </form>

                <!-- Import Form -->
                <form id="importForm" action="{{ route('department.catalog.import_suggest') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <div class="space-y-4 text-center">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 transition cursor-pointer relative">
                            <input type="file" name="file" accept=".csv,.txt" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="updateFileName(this)">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 font-medium" id="fileName">Kéo thả hoặc chọn file CSV để tải lên</p>
                            <p class="text-xs text-gray-400 mt-1">Hỗ trợ: .csv, .txt</p>
                        </div>
                        <div class="text-left bg-blue-50 p-3 rounded-lg border border-blue-100">
                            <p class="text-xs text-blue-700 font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i> Định dạng file CSV:</p>
                            <p class="text-xs text-blue-600">Cột 1: Tên món (Bắt buộc)</p>
                            <p class="text-xs text-blue-600">Cột 2: Đơn vị tính</p>
                            <p class="text-xs text-blue-600">Cột 3: Giá dự kiến (Số)</p>
                            <p class="text-xs text-blue-600">Cột 4: Mô tả</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="closeSuggestModal()"
                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium">Hủy</button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Upload & Gửi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Proposal Modal Functions
        function openSuggestModal() {
            document.getElementById('suggestModal').classList.remove('hidden');
        }

        function closeSuggestModal() {
            document.getElementById('suggestModal').classList.add('hidden');
        }

        function switchTab(tab) {
            const manualForm = document.getElementById('manualForm');
            const importForm = document.getElementById('importForm');
            const btnManual = document.getElementById('tab-manual');
            const btnImport = document.getElementById('tab-import');

            if (tab === 'manual') {
                manualForm.classList.remove('hidden');
                importForm.classList.add('hidden');
                btnManual.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                btnManual.classList.remove('text-gray-500');
                btnImport.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                btnImport.classList.add('text-gray-500');
            } else {
                manualForm.classList.add('hidden');
                importForm.classList.remove('hidden');
                btnImport.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                btnImport.classList.remove('text-gray-500');
                btnManual.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                btnManual.classList.add('text-gray-500');
            }
        }

        function updateFileName(input) {
            const fileName = input.files[0]?.name || 'Kéo thả hoặc chọn file CSV để tải lên';
            document.getElementById('fileName').innerText = fileName;
        }
    </script>
@endsection