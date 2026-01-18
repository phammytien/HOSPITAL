@extends('layouts.department')

@section('title', 'Tạo yêu cầu mới')
@section('header_title', 'Tạo yêu cầu mới')
@section('page-subtitle', 'Quản lý và đề xuất mua sắm trang thiết bị vật tư')

@section('content')
    <form action="{{ route('department.requests.store') }}" method="POST" id="createRequestForm"
        enctype="multipart/form-data">
        @csrf
        <!-- Hardcoded Budget Source ID for now, should be dynamic if needed -->
        <input type="hidden" name="budget_source_id" value="1">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Nguồn ngân sách -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Nguồn ngân sách đang chọn</h3>
                        <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-semibold">
                            Đã dùng {{ number_format(($usedBudget / $budgetTotal) * 100, 1) }}%
                        </span>
                    </div>

                    <div class="p-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl text-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm opacity-90">Nguồn Ngân Sách Đang Chọn</span>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 class="text-2xl font-bold mb-1">
                            {{ Auth::user()->department->department_code ?? 'N/A' }}_{{ date('Y') }}
                        </h4>
                        <p class="text-sm opacity-90">Ngân sách {{ Auth::user()->department->department_name ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kỳ yêu cầu *</label>

                        <input type="text" name="period" value="{{ date('Y') . '_Q' . ceil(date('n') / 3) }}" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                            placeholder="VD: 2026_Q1">
                        @error('period')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                        <textarea name="note" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="Nhập lý do mua sắm...">{{ old('note') }}</textarea>
                    </div>
                </div>

                <!-- Soạn thảo yêu cầu -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Soạn thảo yêu cầu</h3>
                    </div>

                    <!-- Search & Add Product -->
                    <div class="mb-6">
                        <div class="flex gap-3">
                            <div class="flex-1 relative">
                                <div class="relative">
                                    <input type="text" id="productSearch"
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Tìm kiếm sản phẩm theo tên hoặc mã..." onfocus="toggleProductList()">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>
                            <a href="{{ route('department.products.index') }}"
                                class="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2 whitespace-nowrap transition shadow-sm hover:shadow-md">
                                <i class="fas fa-th mr-2"></i>
                                <span>Chọn từ danh mục</span>
                            </a>
                        </div>

                        <!-- Product List Dropdown -->
                        <div id="productListDropdown"
                            class="hidden mt-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg bg-white shadow-lg">
                            @foreach($products as $product)
                                @php $img = getProductImage($product->id); @endphp
                                <div class="p-3 hover:bg-gray-50 cursor-pointer product-item" data-id="{{ $product->id }}"
                                    data-name="{{ $product->product_name }}" data-price="{{ $product->unit_price }}"
                                    data-unit="{{ $product->unit }}"
                                    onclick="addProductToCart({{ $product->id }}, '{{ $product->product_name }}', {{ $product->unit_price }}, '{{ $product->unit }}', '{{ $img }}')">
                                    <div class="flex items-center space-x-3">
                                        <!-- Image Placeholder -->
                                        <!-- Image Display -->
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $product->product_name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-400">
                                                {{ substr($product->product_name, 0, 1) }}
                                            </div>
                                        @endif

                                        <div class="flex-1 flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $product->product_name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $product->category->category_name ?? 'N/A' }} •
                                                    {{ $product->product_code }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-900">
                                                    {{ number_format($product->unit_price, 0, ',', '.') }} đ
                                                </p>
                                                <p class="text-sm text-gray-500">{{ $product->unit }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Selected Products Table -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900">Danh sách hàng hóa</h4>
                            <div class="flex gap-4 text-sm">
                                <button type="button" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-list mr-1"></i> Danh sách
                                </button>
                                <button type="button" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-th mr-1"></i> Lưới
                                </button>
                            </div>
                        </div>

                        <div id="selectedProducts" class="divide-y divide-gray-200">
                            <!-- Products will be added here dynamically -->
                        </div>

                        <div id="emptyState" class="p-12 text-center">
                            <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Chưa có sản phẩm nào được chọn</p>
                            <p class="text-sm text-gray-400 mt-2">Sử dụng ô tìm kiếm phía trên để thêm sản phẩm</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="space-y-6">
                <!-- Chi tiết chi phí -->
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6" id="cost-summary-section">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Chi tiết chi phí</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tạm tính (<span id="itemCount">0</span> mục)</span>
                            <span class="font-semibold" id="subtotal">0 đ</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-900 font-semibold">Tổng thanh toán</span>
                            <span class="text-2xl font-bold text-blue-600" id="total">0 đ</span>
                        </div>
                    </div>

                    <button type="submit" name="submit_action" value="submit" id="btnSubmit" disabled
                        class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition mb-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-2"></i>
                        <span id="btnSubmitText">Xác nhận & Gửi duyệt</span>
                    </button>

                    <button type="button" onclick="saveDraft()" id="btnDraft" disabled
                        class="w-full py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Lưu nháp
                    </button>


                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            let selectedProducts = [];
            let productCounter = 0;

            // Initialize from Session (Preselected)
            @if(isset($preselectedProducts) && count($preselectedProducts) > 0)
                selectedProducts = @json($preselectedProducts);
            @endif

            // Update period field with current Quarter
            // Update period field with current Quarter
            function updatePeriodTime() {
                const now = new Date();
                const month = now.getMonth() + 1;
                const year = now.getFullYear();
                const quarter = Math.ceil(month / 3);
                const timeString = `${year}_Q${quarter}`;

                const periodInput = document.querySelector('input[name="period"]');
                if (periodInput) {
                    periodInput.value = timeString;
                }
            }
            updatePeriodTime();

            // LocalStorage Cart Management
            const CART_KEY = 'department_request_cart';

            function initCart() {
                // Check if we came from catalog (append mode) or started fresh (clear mode)
                const urlParams = new URLSearchParams(window.location.search);
                const isFromCatalog = urlParams.get('from_catalog');

                if (!isFromCatalog) {
                    // New Request -> Clear old data
                    localStorage.removeItem(CART_KEY);
                    selectedProducts = [];
                } else {
                    // Load from LS
                    const stored = localStorage.getItem(CART_KEY);
                    if (stored) {
                        try {
                            const cartItems = JSON.parse(stored);
                            // Merge with any prefilled items (though usually empty on create)
                            // Map simple cart items to full structure if needed, or just push
                            // Assuming cart saves: {id, name, price, unit, quantity}
                            
                            // We need to merge duplicates if any (though LS shouldn't have them if managed right)
                            cartItems.forEach(item => {
                                const exists = selectedProducts.find(p => p.id === item.id);
                                if (!exists) {
                                    selectedProducts.push({
                                        id: item.id,
                                        name: item.name,
                                        price: item.price,
                                        unit: item.unit,
                                        quantity: item.quantity,
                                        image: item.image || '', // Load image
                                        reason: ''
                                    });
                                } else {
                                    exists.quantity = item.quantity;
                                    if (!exists.image && item.image) exists.image = item.image; // Update image if available
                                }
                            });
                        } catch (e) {
                            console.error('Error parsing cart:', e);
                        }
                    }
                }
                
                renderProducts();
                updateTotal();
            }

            // Sync to LS whenever we change products
            function syncToStorage() {
                const simpleCart = selectedProducts.map(p => ({
                    id: p.id,
                    name: p.name,
                    price: p.price,
                    unit: p.unit,
                    quantity: p.quantity
                }));
                localStorage.setItem(CART_KEY, JSON.stringify(simpleCart));
            }

            function toggleProductList() {
                const dropdown = document.getElementById('productListDropdown');
                // ... (Show/Hide logic)
                 if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    const items = document.querySelectorAll('.product-item');
                    items.forEach(item => item.style.display = 'block');
                } else {
                    dropdown.classList.add('hidden');
                }
            }

            function addProductToCart(id, name, price, unit, image) {
                // Check if product already exists
                const existingProduct = selectedProducts.find(p => p.id === id);

                if (existingProduct) {
                    existingProduct.quantity += 1;
                    // Update image if missing
                    if (!existingProduct.image && image) existingProduct.image = image;
                    showToast('Đã tăng số lượng sản phẩm', 'info');
                } else {
                    selectedProducts.push({
                        id: id,
                        name: name,
                        price: parseFloat(price),
                        unit: unit,
                        quantity: 1,
                        image: image || '',
                        reason: ''
                    });
                    showToast('Đã thêm sản phẩm vào danh sách', 'success');
                }
                
                syncToStorage();
                renderProducts();
                updateTotal();
                
                // Hide dropdown
                document.getElementById('productListDropdown').classList.add('hidden');
                document.getElementById('productSearch').value = '';

                const productItem = document.querySelector(`.product-item[data-id="${id}"]`);
                if (productItem) {
                    productItem.classList.add('bg-green-50', 'opacity-50');
                    productItem.innerHTML += '<span class="text-green-600 text-sm ml-2"><i class="fas fa-check"></i> Đã thêm</span>';
                }
            }
            
            // Override update/remove to sync
            const originalUpdateQuantity = updateQuantity; // Not defined globally yet?
            // Re-defining to include sync:
            
            function updateQuantity(index, quantity) {
                selectedProducts[index].quantity = parseInt(quantity);
                syncToStorage();
                updateTotal();
            }
            
            function removeProduct(index) {
                const product = selectedProducts[index];
                selectedProducts.splice(index, 1);
                syncToStorage();
                renderProducts();
                updateTotal();
                
                 // Reset UI
                const productItem = document.querySelector(`.product-item[data-id="${product.id}"]`);
                if (productItem) {
                    productItem.classList.remove('bg-green-50', 'opacity-50');
                    const checkMark = productItem.querySelector('.text-green-600');
                    if (checkMark) checkMark.remove();
                }
            }
            
            // On Submit Success -> Clear LS
            document.getElementById('createRequestForm').addEventListener('submit', function() {
                // We clear it assuming submit works. If validation fails, user stays on page.
                // Ideally clear only on actual success, but standard form submit refreshes page.
                // If we come back with errors, 'from_catalog' wont be there, so it clears?
                // Wait. If validation fails, Laravel redirects back with input.
                // We should probably NOT clear immediately, or recover from old('items').
                // But this is a SPA-like cart.
                // Let's clear on Submit for now. If failure, Laravel repopulates fields usually? 
                // Actually JS selectedProducts won't be repopulated from Laravel `old()` automatically unless we code it.
                // But let's stick to the happy path first.
                // localStorage.removeItem(CART_KEY); // User said "if create new then lost".
            });

            // Initialize
            document.addEventListener('DOMContentLoaded', function () {
                initCart();
            });

            function renderProducts() {
                const container = document.getElementById('selectedProducts');
                const emptyState = document.getElementById('emptyState');
                const costSummary = document.getElementById('cost-summary-section');

                // Toggle visibility
                // if (selectedProducts.length > 0) {
                //     costSummary.classList.remove('hidden');
                // } else {
                //     costSummary.classList.add('hidden');
                // }

                if (selectedProducts.length === 0) {
                    container.innerHTML = '';
                    emptyState.classList.remove('hidden');
                    return;
                }

                emptyState.classList.add('hidden');

                let html = '';
                selectedProducts.forEach((product, index) => {
                    // Handle image logic
                    let imageHtml = '';
                    if (product.image) {
                        imageHtml = `<img src="${product.image}" alt="${product.name}" class="w-20 h-20 rounded-lg object-cover border border-gray-200">`;
                    } else {
                        // Placeholder with Initials
                        const initials = product.name.substring(0, 2).toUpperCase();
                        imageHtml = `
                            <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                <span class="text-xl font-bold text-gray-400 select-none">${initials}</span>
                            </div>
                        `;
                    }

                    html += `
                                                                                                                            <div class="p-4">
                                                                                                                                <div class="flex gap-4">
                                                                                                                                    ${imageHtml}
                                                                                                                                    <div class="flex-1">
                                                                                                                                        <h4 class="font-semibold text-gray-900 mb-1">${product.name}</h4>
                                                                                                                                        <div class="grid grid-cols-2 gap-3 mt-3">
                                                                                                                                            <div>
                                                                                                                                                <label class="text-xs text-gray-500 block mb-1">Số lượng</label>
                                                                                                                                                <input type="number" 
                                                                                                                                                       name="items[${index}][quantity]"
                                                                                                                                                       value="${product.quantity}"
                                                                                                                                                       min="1"
                                                                                                                                                       step="1"
                                                                                                                                                       onchange="updateQuantity(${index}, this.value)"
                                                                                                                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-center"
                                                                                                                                                       required>
                                                                                                                                                <input type="hidden" name="items[${index}][product_id]" value="${product.id}">
                                                                                                                                            </div>
                                                                                                                                            <div>
                                                                                                                                                <label class="text-xs text-gray-500 block mb-1">Đơn giá (${product.unit})</label>
                                                                                                                                                 <input type="text" 
                                                                                                                                                        value="${formatMoney(product.price)} đ"
                                                                                                                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                                                                                                                                                        readonly
                                                                                                                                                        required>
                                                                                                                                                 <input type="hidden" name="items[${index}][expected_price]" value="${product.price}">
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <div class="mt-3">
                                                                                                                                            <label class="text-xs text-gray-500 block mb-1">Lý do</label>
                                                                                                                                            <input type="text" 
                                                                                                                                                   name="items[${index}][reason]"
                                                                                                                                                   value="${product.reason}"
                                                                                                                                                   placeholder="Nhập lý do mua..."
                                                                                                                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                    <div class="text-right">
                                                                                                                                        <p class="font-bold text-blue-600 text-lg mb-2">${formatMoney(product.price * product.quantity)} đ</p>
                                                                                                                                        <button type="button" 
                                                                                                                                                onclick="removeProduct(${index})"
                                                                                                                                                class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors" title="Xóa">
                                                                                                                                            <i class="fas fa-trash-alt"></i>
                                                                                                                                        </button>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        `;
                });

                container.innerHTML = html;
            }

            // Previously defined in init scope, but ensuring global scope access for HTML events
            // (The previous tool call redefined these inside init block/comments, clearing up duplication)
            // Wait, I should make sure these function definitions match the ones I promised in the previous tool.
            // The previous tool completely replaced the block containing these.
            // Let me CHECK if I need to do this.
            // The previous tool replaced lines 196-261.
            // But updateQuantity was at line 339!
            // So I DO need to update lines 339+ to use syncToStorage.

            function updateQuantity(index, quantity) {
                selectedProducts[index].quantity = parseInt(quantity);
                syncToStorage();
                updateTotal();
            }

            function updatePrice(index, price) {
                selectedProducts[index].price = parseFloat(price);
                syncToStorage();
                updateTotal();
            }

            function removeProduct(index) {
                const product = selectedProducts[index];
                selectedProducts.splice(index, 1);
                syncToStorage();
                renderProducts();
                updateTotal();

                // Reset product item in dropdown
                const productItem = document.querySelector(`.product-item[data-id="${product.id}"]`);
                if (productItem) {
                    productItem.classList.remove('bg-green-50', 'opacity-50');
                    const checkMark = productItem.querySelector('.text-green-600');
                    if (checkMark) checkMark.remove();
                }
            }

            // Toast notification function
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-opacity ${type === 'success' ? 'bg-green-500' :
                    type === 'warning' ? 'bg-orange-500' :
                        'bg-blue-500'
                    }`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            }

            function updateTotal() {
                const subtotal = selectedProducts.reduce((sum, p) => sum + (p.price * p.quantity), 0);
                const total = subtotal;

                document.getElementById('itemCount').textContent = selectedProducts.length;
                document.getElementById('subtotal').textContent = formatMoney(subtotal) + ' đ';
                document.getElementById('total').textContent = formatMoney(total) + ' đ';

                // Check Budget
                const BUDGET_TOTAL = {{ $budgetTotal }};
                const BUDGET_USED = {{ $usedBudget + $pendingBudget }};
                if (total + BUDGET_USED > BUDGET_TOTAL) {
                    showToast('Cảnh báo: Tổng tiền vượt quá ngân sách cho phép!', 'warning');
                    document.getElementById('total').classList.add('text-red-600');
                } else {
                    document.getElementById('total').classList.remove('text-red-600');
                }

                // Update Buttons State
                // Update Buttons State
                const btnSubmit = document.getElementById('btnSubmit');
                const btnDraft = document.getElementById('btnDraft');
                const btnSubmitText = document.getElementById('btnSubmitText');

                if (total + BUDGET_USED > BUDGET_TOTAL) {
                    showToast('Cảnh báo: Tổng tiền vượt quá ngân sách cho phép!', 'warning');
                    document.getElementById('total').classList.add('text-red-600');

                    btnSubmit.disabled = true;
                    btnDraft.disabled = true;
                    btnSubmitText.textContent = 'Vượt ngân sách';
                    btnSubmit.classList.add('bg-red-500');
                } else {
                    document.getElementById('total').classList.remove('text-red-600');
                    btnSubmit.classList.remove('bg-red-500');

                    if (selectedProducts.length === 0) {
                        btnSubmit.disabled = true;
                        btnDraft.disabled = true;
                        btnSubmitText.textContent = 'Xác nhận & Gửi duyệt';
                    } else {
                        btnSubmit.disabled = false;
                        btnDraft.disabled = false;
                        btnSubmitText.textContent = 'Xác nhận & Gửi duyệt';
                    }
                }
            }

            function formatMoney(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount);
            }

            function saveDraft() {
                // Change form action to save as draft
                const form = document.getElementById('createRequestForm');

                // Add hidden input for submit_action
                let input = document.querySelector('input[name="submit_action"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'submit_action';
                    form.appendChild(input);
                }
                input.value = 'draft';

                form.submit();
            }

            // Search functionality
            document.getElementById('productSearch').addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();
                const items = document.querySelectorAll('.product-item');
                let visibleCount = 0;

                items.forEach(item => {
                    const name = item.dataset.name.toLowerCase();
                    if (name.includes(searchTerm)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Auto show dropdown when typing
                const dropdown = document.getElementById('productListDropdown');
                if (searchTerm.length > 0 && visibleCount > 0) {
                    dropdown.classList.remove('hidden');
                } else if (visibleCount === 0) {
                    dropdown.classList.add('hidden');
                }
            });

            // Focus search box when clicking "Thêm vào danh sách" button
            // Focus search box when clicking "Thêm vào danh sách" button
            const toggleBtn = document.querySelector('button[onclick="toggleProductList()"]');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.getElementById('productSearch').focus();
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                const dropdown = document.getElementById('productListDropdown');
                const searchBox = document.getElementById('productSearch');
                const addButton = e.target.closest('button[onclick="toggleProductList()"]');

                if (!dropdown.contains(e.target) && e.target !== searchBox && !addButton) {
                    dropdown.classList.add('hidden');
                }
            });

            // Auto-load products from Session (handled above) or specific initialization
            document.addEventListener('DOMContentLoaded', function () {
                renderProducts();
                updateTotal();
            });
        </script>
    @endpush
@endsection