@extends('layouts.department')

@section('title', 'Chỉnh sửa yêu cầu')
@section('header_title', 'Chỉnh sửa yêu cầu')
@section('page-subtitle', 'Cập nhật thông tin yêu cầu mua sắm')

@section('content')
    <form action="{{ route('department.requests.update', $purchaseRequest->id) }}" method="POST" id="editRequestForm"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
            <!-- Main Form (6 parts) -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Search & Add Product Section -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Soạn thảo yêu cầu</h3>
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
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $product->product_name }}"
                                            class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-400">
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

                <!-- Nguồn ngân sách -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Nguồn ngân sách đang chọn</h3>
                        @php
                            $totalCurrent = $budgetTotal > 0 ? $budgetTotal : 1;
                            $percentage = ($usedBudget / $totalCurrent) * 100;
                        @endphp
                        <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-semibold">
                            Đã dùng {{ number_format($percentage, 1) }}%
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
                        <input type="text" name="period" value="{{ old('period', $purchaseRequest->period) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="VD: 2026_Q1">
                        @error('period')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Lý do chung cho đơn hàng *</label>

                        <!-- Predefined Reasons Grid (4 rows x 3 cols) -->
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            @php
                                $commonReasons = [
                                    'Bổ sung trang thiết bị',
                                    'Thay thế thiết bị hỏng',
                                    'Nâng cấp cơ sở vật chất',
                                    'Phục vụ điều trị bệnh nhân',
                                    'Dự trữ vật tư tiêu hao',
                                    'Mở rộng quy mô hoạt động',
                                    'Đáp ứng yêu cầu kiểm định',
                                    'Chuẩn bị cho dự án mới',
                                    'Thay thế vật tư hết hạn',
                                    'Tăng cường năng lực khám'
                                ];
                            @endphp

                            @foreach($commonReasons as $index => $reason)
                                <label
                                    class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                    <input type="radio" name="common_reason_type" value="{{ $reason }}"
                                        {{ old('note', $purchaseRequest->note) == $reason ? 'checked' : '' }}
                                        onchange="applyCommonReason('{{ $reason }}')"
                                        class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">{{ $reason }}</span>
                                </label>
                            @endforeach

                            <!-- Custom Reason Option -->
                            <label
                                class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition">
                                <input type="radio" name="common_reason_type" value="custom"
                                    {{ !in_array(old('note', $purchaseRequest->note), $commonReasons) && old('note', $purchaseRequest->note) != '' ? 'checked' : '' }}
                                    onchange="toggleCustomReason(true)" class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 font-semibold">Khác...</span>
                            </label>
                        </div>

                        <!-- Custom Reason Input -->
                        <div id="customReasonContainer" class="{{ !in_array(old('note', $purchaseRequest->note), $commonReasons) && old('note', $purchaseRequest->note) != '' ? '' : 'hidden' }}">
                            <textarea id="customReasonInput" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nhập lý do khác..." onchange="applyCommonReason(this.value)">{{ !in_array(old('note', $purchaseRequest->note), $commonReasons) ? old('note', $purchaseRequest->note) : '' }}</textarea>
                        </div>

                        <input type="hidden" name="note" id="commonReasonValue" value="{{ old('note', $purchaseRequest->note) }}">
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar (4 parts) -->
            <div class="lg:col-span-4">
                <!-- Chi tiết chi phí -->
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6" id="cost-summary-section">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Chi tiết chi phí</h3>

                    <!-- Selected Products List (Scrollable, max 4 items visible) -->
                    <div class="mb-6">
                        <div id="sidebarProductList" class="space-y-3 max-h-[400px] overflow-y-auto pr-2 mb-4">
                            <!-- Products will be rendered here -->
                        </div>
                        <div id="sidebarEmptyState" class="py-8 text-center hidden">
                            <i class="fas fa-shopping-cart text-gray-300 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Chưa có sản phẩm</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6 pt-4 border-t border-gray-200">
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

                    <button type="submit" name="submit_action" value="submit" id="btnSubmit"
                        class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition mb-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-2"></i>
                        <span id="btnSubmitText">Xác nhận & Gửi duyệt</span>
                    </button>

                    <button type="button" onclick="saveDraft()" id="btnDraft"
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
            let currentCommonReason = "{{ old('note', $purchaseRequest->note) }}";

            // Initialize from existing data
            @if(isset($purchaseRequest) && $purchaseRequest->items)
                @foreach($purchaseRequest->items as $item)
                    selectedProducts.push({
                        id: {{ $item->product_id }},
                        name: "{{ $item->product->product_name ?? 'Unknown' }}",
                        price: {{ $item->expected_price }},
                        unit: "{{ $item->product->unit ?? 'pcs' }}",
                        quantity: {{ $item->quantity }},
                        image: "{{ getProductImage($item->product_id) }}",
                        reason: "{{ $item->reason ?? '' }}"
                    });
                @endforeach
            @endif

            function toggleProductList() {
                const dropdown = document.getElementById('productListDropdown');
                if (dropdown && dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    const items = document.querySelectorAll('.product-item');
                    items.forEach(item => item.style.display = 'block');
                } else if (dropdown) {
                    dropdown.classList.add('hidden');
                }
            }

            function addProductToCart(id, name, price, unit, image) {
                const existingProduct = selectedProducts.find(p => p.id === id);

                if (existingProduct) {
                    existingProduct.quantity += 1;
                    showToast('Đã tăng số lượng sản phẩm', 'info');
                } else {
                    selectedProducts.push({
                        id: id,
                        name: name,
                        price: parseFloat(price),
                        unit: unit,
                        quantity: 1,
                        image: image || '',
                        reason: currentCommonReason || ''
                    });
                    showToast('Đã thêm sản phẩm vào danh sách', 'success');
                }

                renderProducts();
                updateTotal();

                // Hide dropdown
                const dropdown = document.getElementById('productListDropdown');
                if (dropdown) dropdown.classList.add('hidden');
                document.getElementById('productSearch').value = '';

                markAsAdded(id);
            }

            function markAsAdded(id) {
                const productItem = document.querySelector(`.product-item[data-id="${id}"]`);
                if (productItem) {
                    productItem.classList.add('bg-green-50', 'opacity-50');
                    if (!productItem.querySelector('.text-green-600')) {
                        const contentDiv = productItem.querySelector('.flex-1');
                        if (contentDiv) {
                            contentDiv.innerHTML += '<span class="text-green-600 text-sm ml-2"><i class="fas fa-check"></i> Đã thêm</span>';
                        }
                    }
                }
            }

            function updateQuantity(index, quantity) {
                selectedProducts[index].quantity = parseInt(quantity) || 1;
                updateTotal();
                // We re-render to update the "Thành tiền" in the row
                renderProducts();
            }

            function removeProduct(index) {
                const product = selectedProducts[index];
                selectedProducts.splice(index, 1);
                renderProducts();
                updateTotal();

                // Reset UI in search dropdown
                const productItem = document.querySelector(`.product-item[data-id="${product.id}"]`);
                if (productItem) {
                    productItem.classList.remove('bg-green-50', 'opacity-50');
                    const checkMark = productItem.querySelector('.text-green-600');
                    if (checkMark) checkMark.remove();
                }
            }

            function renderProducts() {
                const sidebarContainer = document.getElementById('sidebarProductList');
                const sidebarEmptyState = document.getElementById('sidebarEmptyState');

                if (selectedProducts.length === 0) {
                    sidebarContainer.innerHTML = '';
                    sidebarEmptyState.classList.remove('hidden');
                    return;
                }

                sidebarEmptyState.classList.add('hidden');

                let html = '';
                selectedProducts.forEach((product, index) => {
                    let imageHtml = '';
                    if (product.image) {
                        imageHtml = `<img src="${product.image}" alt="${product.name}" class="w-12 h-12 rounded-lg object-cover border border-gray-200">`;
                    } else {
                        const initials = product.name.substring(0, 2).toUpperCase();
                        imageHtml = `
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                <span class="text-sm font-bold text-gray-400 select-none">${initials}</span>
                            </div>
                        `;
                    }

                    html += `
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex gap-3 mb-3">
                                ${imageHtml}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">${product.name}</h4>
                                    <p class="text-xs text-gray-500">${product.unit}</p>
                                </div>
                                <button type="button" 
                                    onclick="removeProduct(${index})"
                                    class="text-red-500 hover:text-red-700 h-6 w-6" title="Xóa">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs text-gray-500 block mb-1">Số lượng</label>
                                        <input type="number" 
                                               name="items[${index}][quantity]"
                                               value="${product.quantity}"
                                               min="1"
                                               step="1"
                                               onchange="updateQuantity(${index}, this.value)"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-center"
                                        required>
                                        <input type="hidden" name="items[${index}][product_id]" value="${product.id}">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 block mb-1">Đơn giá</label>
                                        <input type="text" 
                                               value="${formatMoney(product.price)} đ"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-gray-100 text-gray-600"
                                               readonly>
                                        <input type="hidden" name="items[${index}][expected_price]" value="${product.price}">
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-xs text-gray-500">Lý do</label>
                                        <label class="flex items-center text-xs text-blue-600 cursor-pointer">
                                            <input type="checkbox" 
                                                   id="customReasonCheck_${index}"
                                                   ${product.reason && product.reason !== currentCommonReason ? 'checked' : ''}
                                                   onchange="toggleProductCustomReason(${index})"
                                                   class="mr-1 text-blue-600 focus:ring-blue-500">
                                            <span>Lý do khác</span>
                                        </label>
                                    </div>
                                    <input type="text" 
                                           id="productReason_${index}"
                                           name="items[${index}][reason]"
                                           value="${product.reason || currentCommonReason}"
                                           ${product.reason && product.reason !== currentCommonReason ? '' : 'readonly'}
                                           placeholder="Sử dụng lý do chung..."
                                           onchange="updateProductReason(${index}, this.value)"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm ${product.reason && product.reason !== currentCommonReason ? 'bg-white' : 'bg-gray-50 text-gray-600'} product-reason-input">
                                </div>

                                <div class="pt-2 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Thành tiền:</span>
                                        <span class="font-bold text-blue-600 text-sm">${formatMoney(product.price * product.quantity)} đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                sidebarContainer.innerHTML = html;
                
                // Mark already added items in dropdown
                selectedProducts.forEach(p => markAsAdded(p.id));
            }

            function updateProductReason(index, value) {
                selectedProducts[index].reason = value;
            }

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
                
                const btnSubmit = document.getElementById('btnSubmit');
                const btnDraft = document.getElementById('btnDraft');
                const btnSubmitText = document.getElementById('btnSubmitText');

                if (total + BUDGET_USED > BUDGET_TOTAL) {
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

            function toggleCustomReason(show) {
                const container = document.getElementById('customReasonContainer');
                const input = document.getElementById('customReasonInput');

                if (show) {
                    container.classList.remove('hidden');
                    input.focus();
                } else {
                    container.classList.add('hidden');
                    input.value = '';
                }
            }

            function applyCommonReason(reason) {
                currentCommonReason = reason;
                document.getElementById('commonReasonValue').value = reason;

                // Apply to all products that don't have custom reason
                selectedProducts.forEach((product, index) => {
                    const checkbox = document.getElementById(`customReasonCheck_${index}`);
                    if (!checkbox || !checkbox.checked) {
                        product.reason = reason;
                    }
                });
                renderProducts();
            }

            function toggleProductCustomReason(index) {
                const checkbox = document.getElementById(`customReasonCheck_${index}`);
                const input = document.getElementById(`productReason_${index}`);

                if (checkbox.checked) {
                    input.removeAttribute('readonly');
                    input.classList.remove('bg-gray-50', 'text-gray-600');
                    input.classList.add('bg-white');
                    input.placeholder = 'Nhập lý do riêng...';
                    input.value = '';
                    input.focus();
                    selectedProducts[index].reason = '';
                } else {
                    input.setAttribute('readonly', true);
                    input.classList.add('bg-gray-50', 'text-gray-600');
                    input.classList.remove('bg-white');
                    input.placeholder = 'Sử dụng lý do chung...';
                    input.value = currentCommonReason;
                    selectedProducts[index].reason = currentCommonReason;
                }
            }

            function saveDraft() {
                const form = document.getElementById('editRequestForm');
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

                const dropdown = document.getElementById('productListDropdown');
                if (searchTerm.length > 0 && visibleCount > 0) {
                    dropdown.classList.remove('hidden');
                } else if (visibleCount === 0) {
                    dropdown.classList.add('hidden');
                }
            });

            document.addEventListener('click', function (e) {
                const dropdown = document.getElementById('productListDropdown');
                const searchBox = document.getElementById('productSearch');
                const addButton = e.target.closest('a[href*="products"]');

                if (dropdown && !dropdown.contains(e.target) && e.target !== searchBox && !addButton) {
                    dropdown.classList.add('hidden');
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                renderProducts();
                updateTotal();
            });
        </script>
    @endpush
@endsection