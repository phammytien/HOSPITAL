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
                            {{ Auth::user()->department->department_code ?? 'N/A' }}-{{ date('Y') }}
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
                                <div class="p-3 hover:bg-gray-50 cursor-pointer product-item" data-id="{{ $product->id }}"
                                    data-name="{{ $product->product_name }}" data-price="{{ $product->unit_price }}"
                                    data-unit="{{ $product->unit }}"
                                    onclick="addProductToCart({{ $product->id }}, '{{ $product->product_name }}', {{ $product->unit_price }}, '{{ $product->unit }}')">
                                    <div class="flex items-center space-x-3">
                                        <!-- Image Placeholder -->
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-400">
                                            {{ substr($product->product_name, 0, 1) }}
                                        </div>

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

            // Update period field with current Quarter
            function updatePeriodTime() {
                const now = new Date();
                const month = now.getMonth() + 1;
                const year = now.getFullYear();
                const quarter = Math.ceil(month / 3);

                // Format: YYYY_Qx
                const timeString = `${year}_Q${quarter}`;

                const periodInput = document.querySelector('input[name="period"]');
                if (periodInput) {
                    periodInput.value = timeString;
                }
            }
            // Update once
            updatePeriodTime();

            function toggleProductList() {
                const dropdown = document.getElementById('productListDropdown');

                // Show if hidden
                if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    // Reset items display (show all)
                    const items = document.querySelectorAll('.product-item');
                    items.forEach(item => item.style.display = 'block');
                } else {
                    dropdown.classList.add('hidden');
                }
            }

            function addProductToCart(id, name, price, unit) {
                // Check if product already exists
                const exists = selectedProducts.find(p => p.id === id);
                if (exists) {
                    // Show toast notification instead of alert
                    showToast('Sản phẩm đã có trong danh sách!', 'warning');
                    return;
                }

                const product = {
                    id: id,
                    name: name,
                    price: price,
                    unit: unit,
                    quantity: 1,
                    reason: ''
                };

                selectedProducts.push(product);
                renderProducts();
                updateTotal();

                // Show success feedback
                showToast(`Đã thêm "${name}" vào danh sách`, 'success');

                // Mark product as added in dropdown
                const productItem = document.querySelector(`.product-item[data-id="${id}"]`);
                if (productItem) {
                    productItem.classList.add('bg-green-50', 'opacity-50');
                    productItem.innerHTML += '<span class="text-green-600 text-sm ml-2"><i class="fas fa-check"></i> Đã thêm</span>';
                }

                // DON'T close dropdown - allow multiple selections
                // toggleProductList();
            }

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
                    html += `
                                                                                                                    <div class="p-4">
                                                                                                                        <div class="flex gap-4">
                                                                                                                            <img src="https://via.placeholder.com/80" alt="${product.name}" class="w-20 h-20 rounded-lg object-cover">
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
                                                                                                                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                                                                                                                               required>
                                                                                                                                        <input type="hidden" name="items[${index}][product_id]" value="${product.id}">
                                                                                                                                    </div>
                                                                                                                                    <div>
                                                                                                                                        <label class="text-xs text-gray-500 block mb-1">Đơn giá (${product.unit})</label>
                                                                                                                                        <input type="number" 
                                                                                                                                               name="items[${index}][expected_price]"
                                                                                                                                               value="${product.price}"
                                                                                                                                               min="0"
                                                                                                                                               step="1000"
                                                                                                                                               onchange="updatePrice(${index}, this.value)"
                                                                                                                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                                                                                                                               required>
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
                                                                                                                                <p class="font-bold text-gray-900 mb-2">${formatMoney(product.price * product.quantity)} đ</p>
                                                                                                                                <button type="button" 
                                                                                                                                        onclick="removeProduct(${index})"
                                                                                                                                        class="text-red-600 hover:text-red-700 text-sm">
                                                                                                                                    <i class="fas fa-trash mr-1"></i> Xóa
                                                                                                                                </button>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                `;
                });

                container.innerHTML = html;
            }

            function updateQuantity(index, quantity) {
                selectedProducts[index].quantity = parseInt(quantity);
                updateTotal();
            }

            function updatePrice(index, price) {
                selectedProducts[index].price = parseFloat(price);
                updateTotal();
            }

            function removeProduct(index) {
                const product = selectedProducts[index];
                selectedProducts.splice(index, 1);
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

            // Auto-load products from Catalog (localStorage)
            document.addEventListener('DOMContentLoaded', function () {
                const pendingCart = localStorage.getItem('pendingRequestCart');
                if (pendingCart) {
                    try {
                        const products = JSON.parse(pendingCart);
                        let addedCount = 0;

                        products.forEach(p => {
                            const exists = selectedProducts.find(sp => sp.id == p.id);
                            if (!exists) {
                                selectedProducts.push({
                                    id: p.id,
                                    name: p.name,
                                    price: parseFloat(p.price),
                                    unit: p.unit,
                                    quantity: p.quantity || 1,
                                    reason: ''
                                });
                                addedCount++;
                            }
                        });

                        if (addedCount > 0) {
                            renderProducts();
                            updateTotal();
                            showToast(`Đã thêm ${addedCount} sản phẩm từ danh mục`, 'success');
                        }

                        // Clear storage
                        localStorage.removeItem('pendingRequestCart');
                    } catch (e) {
                        console.error('Error loading cart from catalog', e);
                    }
                }
            });
        </script>
    @endpush
@endsection