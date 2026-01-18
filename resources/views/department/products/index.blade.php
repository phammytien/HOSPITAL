@extends('layouts.department')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Danh mục sản phẩm</h1>
                <p class="text-gray-600">Tìm kiếm và thêm sản phẩm vào phiếu yêu cầu mua hàng</p>
            </div>

            <!-- Cart / Current Draft Status -->
            <div class="flex items-center gap-3">
                @if(isset($cartCount) && $cartCount > 0)
                    <a href="{{ route('department.requests.index', ['status' => 'DRAFT']) }}"
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="font-medium">Phiếu nháp hiện tại</span>
                        <span class="bg-white text-blue-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                    </a>
                @endif
            </div>
        </div>

    <!-- Filter Bar & Grid -->
        <div class="flex flex-col gap-6 pb-20"> <!-- Added padding-bottom for sticky footer -->
            <!-- Filter Bar (Horizontal) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <form action="{{ route('department.products.index') }}" method="GET" id="filterForm">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1 relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Tìm kiếm tên sản phẩm, mã số...">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                        </div>

                        <!-- Category Filter (Dropdown) -->
                        <div class="w-full md:w-64 relative">
                            <select name="category_id" onchange="this.form.submit()"
                                class="w-full appearance-none pl-4 pr-10 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none cursor-pointer bg-white hover:border-gray-300 transition">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->category_name }} ({{ $cat->products_count }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-3.5 text-xs text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            <div>
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div id="card-{{ $product->id }}" 
                                 data-name="{{ $product->product_name }}"
                                 data-price="{{ $product->unit_price }}"
                                 data-unit="{{ $product->unit }}"
                                 data-image="{{ getProductImage($product->id) }}"
                                 class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col h-full group overflow-hidden relative">
                                
                                <!-- Checkbox (Top Right) -->
                                <div class="absolute top-2 right-2 z-10 w-6 h-6">
                                    <label class="custom-checkbox cursor-pointer relative w-full h-full block">
                                        <input type="checkbox" 
                                               id="check-{{ $product->id }}"
                                               class="product-checkbox opacity-0 absolute w-0 h-0" 
                                               data-id="{{ $product->id }}" 
                                               onchange="toggleSelection({{ $product->id }})">
                                        <div class="w-6 h-6 border-2 border-gray-300 rounded bg-white transition-colors hover:border-blue-400 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white hidden pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    </label>
                                </div>

                                <!-- Image Section -->
                                <div class="relative aspect-square bg-gray-50 flex items-center justify-center overflow-hidden cursor-pointer"
                                    onclick="openProductModal({{ $product->id }})">
                                    @php
                                        $imageUrl = getProductImage($product->id);
                                    @endphp

                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-50 group-hover:bg-gray-100 transition-colors">
                                            <span class="text-4xl font-black text-gray-200 select-none">
                                                {{ strtoupper(substr($product->product_name, 0, 2)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content Section -->
                                <div class="p-4 flex-1 flex flex-col">
                                    <!-- Category & Code -->
                                    <div class="flex justify-between items-start mb-2 pr-6"> <!-- Added padding-right to avoid overlap with checkbox if needed, though checkbox is absolute top -->
                                        <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded uppercase tracking-wide">
                                            {{ $product->category->category_name ?? 'Khác' }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-mono">#{{ $product->product_code }}</span>
                                    </div>

                                    <!-- Name -->
                                    <h3 class="text-sm font-bold text-gray-900 mb-2 line-clamp-2 h-[40px] leading-tight cursor-pointer hover:text-blue-600 transition-colors"
                                        onclick="openProductModal({{ $product->id }})" title="{{ $product->product_name }}">
                                        {{ $product->product_name }}
                                    </h3>

                                    <!-- Price & Unit -->
                                    <div class="flex items-end justify-between pt-3 mt-auto border-t border-gray-50">
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-0.5">Giá tham khảo</p>
                                            <p class="text-sm font-bold text-blue-600">
                                                {{ number_format($product->unit_price, 0, ',', '.') }}<span class="text-xs">đ</span>
                                            </p>
                                        </div>
                                        
                                        <!-- Quantity Input for Selection Mode -->
                                        <div class="flex items-center gap-1">
                                             <input type="number" 
                                                    id="qty-{{ $product->id }}"
                                                    value="1" min="1" step="1"
                                                    disabled
                                                    class="w-16 px-2 py-1 text-center text-sm border border-gray-200 rounded focus:ring-2 focus:ring-blue-500 outline-none disabled:bg-gray-50 disabled:text-gray-400 font-bold"
                                                    onchange="updateQuantity({{ $product->id }}, this.value)"
                                                    onclick="this.select()">
                                             <span class="text-xs text-gray-500 font-medium">{{ $product->unit }}</span>
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
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Không tìm thấy sản phẩm nào</h3>
                        <p class="text-gray-500 mt-1">Thử chọn danh mục khác hoặc tìm kiếm với từ khóa khác</p>
                        <a href="{{ route('department.products.index') }}" class="inline-block mt-4 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Xóa bộ lọc
                        </a>
                    </div>
                @endif
            </div>
        </div>

    <!-- Sticky Bottom Bar -->
    <div id="stickyFooter" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] p-4 z-40 hidden-footer translate-y-full transform transition-transform duration-300">
        <div class="container mx-auto max-w-7xl flex items-center justify-between">
            <div class="flex items-center gap-4">
                 <button onclick="toggleSelectAll()" class="text-gray-600 hover:text-blue-600 font-medium text-sm flex items-center gap-2">
                    <i class="fas fa-check-double"></i> Chọn tất cả
                </button>
                <div class="h-6 w-px bg-gray-300"></div>
                <span class="text-gray-800 font-semibold text-sm">
                    <span id="selectedCount" class="text-blue-600 text-lg font-bold">0</span> sản phẩm đã chọn
                </span>
            </div>
            
            <div class="flex items-center gap-3">
                 <button onclick="selectedItems.clear(); document.querySelectorAll('.product-checkbox').forEach(cb => { cb.checked = false; toggleSelection(cb.dataset.id); });" 
                         class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-sm">
                    Bỏ chọn tất cả
                </button>
                <button id="createRequestBtn" onclick="createRequest()" 
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-lg hover:bg-blue-700 hover:shadow-xl transition-all transform active:scale-95 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span id="btnText">{{ $cartCount > 0 ? 'Thêm vào đơn nháp hiện tại' : 'Tạo đơn với sản phẩm đã chọn' }}</span>
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Product Detail Modal (Preserved & Updated) -->
    <div id="productModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeProductModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-blue-600 mb-1" id="modalCategory">--</p>
                        <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Loading...</h3>
                    </div>
                    <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="bg-white px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Left: Image & Stock -->
                        <div class="md:col-span-1 space-y-4">
                            <div
                                class="aspect-square bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden relative">
                                <img id="modalImage" src="" alt="Product" class="w-full h-full object-contain p-4 hidden">
                                <div id="modalImagePlaceholder"
                                    class="hidden text-6xl font-black text-gray-200 select-none"></div>
                            </div>

                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <p class="text-xs font-bold text-blue-800 uppercase mb-2">Thông tin kho</p>
                                <div class="flex justify-between items-end">
                                    <span class="text-sm text-gray-600">Tồn kho của bạn:</span>
                                    <span class="text-2xl font-bold text-blue-700" id="modalDepartmentStock">--</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Info -->
                        <div class="md:col-span-2 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Mã sản phẩm</label>
                                    <p class="text-gray-900 font-mono font-medium" id="modalCode">--</p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Đơn vị tính</label>
                                    <p class="text-gray-900 font-medium" id="modalUnit">--</p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Giá tham
                                        khảo</label>
                                    <p class="text-lg font-bold text-blue-600" id="modalPrice">--</p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Nhà cung cấp</label>
                                    <p class="text-gray-900 text-sm truncate" id="modalSupplier">--</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Mô tả</label>
                                <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 leading-relaxed max-h-40 overflow-y-auto"
                                    id="modalDescription">--</div>
                            </div>

                            <!-- Modal Add Action -->
                            <div class="pt-4 border-t border-gray-100">
                                <form onsubmit="addToDraftModal(event)" class="flex gap-4 items-end">
                                    <input type="hidden" id="modalProductId">
                                    <input type="hidden" id="modalRawPrice">
                                    <div class="w-32">
                                        <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Số lượng
                                            mua</label>
                                        <input type="number" id="modalQuantity" value="1" min="1" step="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                                    </div>
                                    <button type="submit"
                                        class="flex-1 bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition shadow-sm">
                                        <i class="fas fa-cart-plus mr-2"></i> Thêm vào yêu cầu
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
    /* Custom Checkbox Style */
    .custom-checkbox input:checked + div {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    .custom-checkbox input:checked + div svg {
        display: block;
    }
    
    /* Sticky Footer Animation */
    #stickyFooter {
        transition: transform 0.3s ease-in-out;
    }
    #stickyFooter.hidden-footer {
        transform: translateY(100%);
    }
</style>
@endpush

@push('scripts')
<script>
    // State
    let selectedItems = new Map(); // productId -> quantity

    // Toggle Selection
    function toggleSelection(productId) {
        const checkbox = document.getElementById(`check-${productId}`);
        const card = document.getElementById(`card-${productId}`);
        const quantityInput = document.getElementById(`qty-${productId}`);
        
        if (checkbox.checked) {
            // Add to selection
            const qty = parseInt(quantityInput.value) || 1;
            selectedItems.set(productId, qty);
            
            // UI Updates
            card.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50/30');
            quantityInput.disabled = false;
        } else {
            // Remove from selection
            selectedItems.delete(productId);
            
            // UI Updates
            card.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50/30');
            quantityInput.disabled = true;
        }
        
        updateStickyFooter();
    }

    // Update Quantity in Selection Map
    function updateQuantity(productId, value) {
         if (selectedItems.has(productId)) {
             selectedItems.set(productId, parseInt(value) || 1);
         }
    }

    // Update Footer UI
    function updateStickyFooter() {
        const footer = document.getElementById('stickyFooter');
        const countSpan = document.getElementById('selectedCount');
        const count = selectedItems.size;

        if (count > 0) {
            footer.classList.remove('hidden-footer', 'translate-y-full');
            countSpan.textContent = count;
        } else {
            footer.classList.add('hidden-footer', 'translate-y-full');
        }
    }

    // Select All
    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const isAllChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => {
            if (cb.checked === isAllChecked) {
                cb.checked = !isAllChecked;
                // Trigger change event logic manually since JS setting doesn't fire it
                toggleSelection(parseInt(cb.dataset.id));
            }
        });
    }

    // Modal Logic (View Details)
    function openProductModal(id) {
        document.getElementById('productModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Đang tải...';

        fetch(`/department/products/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalProductId').value = data.id;
                document.getElementById('modalRawPrice').value = data.unit_price; // Store raw price
                document.getElementById('modalTitle').textContent = data.product_name;
                document.getElementById('modalCode').textContent = data.product_code;
                document.getElementById('modalUnit').textContent = data.unit;
                document.getElementById('modalCategory').textContent = data.category ? data.category.category_name : 'N/A';
                document.getElementById('modalPrice').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.unit_price);
                document.getElementById('modalDescription').textContent = data.description || 'Chưa có mô tả';
                document.getElementById('modalSupplier').textContent = data.supplier ? (data.supplier.supplier_name || data.supplier.supplier_code) : 'Chưa cập nhật';
                document.getElementById('modalDepartmentStock').textContent = data.department_stock;

                const imgEl = document.getElementById('modalImage');
                const phEl = document.getElementById('modalImagePlaceholder');
                if (data.image_url) {
                    imgEl.src = data.image_url;
                    imgEl.classList.remove('hidden');
                    phEl.classList.add('hidden');
                } else {
                    imgEl.classList.add('hidden');
                    phEl.classList.remove('hidden');
                    phEl.textContent = data.product_name.charAt(0).toUpperCase();
                }
            })
            .catch(err => console.error(err));
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }

    // Batch Action: Add to Cart (LocalStorage)
    function createRequest() {
        if (selectedItems.size === 0) return;

        const btn = document.getElementById('createRequestBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';

        // Get existing cart from LS (to merge)
        const CART_KEY = 'department_request_cart';
        let existingCart = [];
        try {
            const stored = localStorage.getItem(CART_KEY);
            if (stored) existingCart = JSON.parse(stored);
        } catch(e) {}

        // Merge items
        selectedItems.forEach((quantity, productId) => {
            const card = document.getElementById(`card-${productId}`);
            
            // Use reliable data attributes
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);
            const unit = card.dataset.unit;
            const image = card.dataset.image;
            
            const exists = existingCart.find(p => p.id === productId);
            if (exists) {
                exists.quantity += quantity;
                // Update image if missing?
                if (!exists.image) exists.image = image;
            } else {
                existingCart.push({
                    id: productId,
                    name: name,
                    price: price, // Now guaranteed to be correct number
                    unit: unit,
                    quantity: quantity,
                    image: image
                });
            }
        });

        localStorage.setItem(CART_KEY, JSON.stringify(existingCart));

        // Redirect with flag
        window.location.href = "{{ route('department.requests.create') }}?from_catalog=1";
    }

    // Modal Add Action (LocalStorage)
    function addToDraftModal(e) {
        e.preventDefault();
        
        const productId = parseInt(document.getElementById('modalProductId').value);
        const quantity = parseInt(document.getElementById('modalQuantity').value);
        const name = document.getElementById('modalTitle').textContent;
        const price = parseFloat(document.getElementById('modalRawPrice').value);
        const unit = document.getElementById('modalUnit').textContent;
        
        // Image source is tricky in modal -> we can grab from img src or store it too.
        // Let's grab it from the modalImage src if visible, or placeholder?
        // Actually, let's just make openProductModal store it in a hidden input too.
        // Or simpler: grab from `modalImage.src`
        const imgEl = document.getElementById('modalImage');
        let image = '';
        if (!imgEl.classList.contains('hidden')) {
            image = imgEl.src;
        }

        // Get existing cart
        const CART_KEY = 'department_request_cart';
        let existingCart = [];
        try {
            const stored = localStorage.getItem(CART_KEY);
            if (stored) existingCart = JSON.parse(stored);
        } catch(e) {}

        const exists = existingCart.find(p => p.id === productId);
        if (exists) {
            exists.quantity += quantity;
            if (!exists.image) exists.image = image;
        } else {
            existingCart.push({
                id: productId,
                name: name,
                price: price,
                unit: unit,
                quantity: quantity,
                image: image
            });
        }

        localStorage.setItem(CART_KEY, JSON.stringify(existingCart));

        // Redirect
        window.location.href = "{{ route('department.requests.create') }}?from_catalog=1";
    }
</script>
@endpush
@endsection