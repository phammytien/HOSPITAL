@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')


@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mt-1">Danh sách tất cả sản phẩm trong hệ thống</p>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('admin.products.export', ['category_id' => request('category_id'), 'search' => request('search'), 'supplier_id' => request('supplier_id')]) }}" 
                   class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium inline-flex items-center">
                    <i class="fas fa-file-export mr-2"></i>Xuất Excel
                </a>
                <button onclick="openModal('add')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                </button>
            </div>
        </div>

        <!-- Filters and Search -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="GET" action="{{ route('admin.products.index') }}" id="filterForm">
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative group">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                   placeholder="Tìm kiếm theo tên, mã SKU, hoặc nhà cung cấp" 
                                   class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm group-hover:bg-white group-hover:border-gray-300">
                            <i class="fas fa-search absolute left-4 top-3 text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                    </div>

                    <!-- Filters Group -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Category Filter -->
                        <div class="relative min-w-[180px]">
                            <select name="category_id" id="categoryFilter" 
                                    class="w-full appearance-none pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer hover:bg-white hover:border-gray-300 transition-all">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-3 text-xs text-gray-400 pointer-events-none"></i>
                        </div>

                        <!-- Supplier Filter -->
                        <div class="relative min-w-[200px]">
                            <select name="supplier_id" id="supplierFilter" onchange="this.form.submit()"
                                    class="w-full appearance-none pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer hover:bg-white hover:border-gray-300 transition-all">
                                <option value="">Tất cả nhà cung cấp</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-3 text-xs text-gray-400 pointer-events-none"></i>
                        </div>

                        <!-- Stock Status Filter -->
                        <div class="relative min-w-[180px]">
                            <select name="stock_status" id="stockStatusFilter" onchange="this.form.submit()"
                                    class="w-full appearance-none pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer hover:bg-white hover:border-gray-300 transition-all">
                                <option value="">Trạng thái tồn kho</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Sẵn hàng (>10)</option>
                                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Sắp hết (≤10)</option>
                                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng (0)</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-3 text-xs text-gray-400 pointer-events-none"></i>
                        </div>

                        @if(request('search') || request('category_id') || request('supplier_id') || request('stock_status'))
                            <a href="{{ route('admin.products.index') }}" 
                               class="px-4 py-2.5 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors flex items-center justify-center"
                               title="Xóa bộ lọc">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products ?? [] as $product)
                <div class="group bg-white rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden hover:shadow-[0_8px_30px_-10px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300">
                    <!-- Product Image Section -->
                    <div class="relative aspect-square bg-gray-50 overflow-hidden">
                        <!-- Status Badge -->


                        <div class="absolute top-3 right-3 z-10 flex flex-col gap-2 items-end">
                            <!-- Proposal Badge (if no supplier) -->
                            @if(!$product->supplier_id)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border bg-orange-50 text-orange-600 border-orange-200 shadow-sm">
                                    <i class="fas fa-lightbulb"></i>
                                    ĐỀ XUẤT
                                </span>
                            @endif


                        </div>

                        @php $imageUrl = getProductImage($product->id); @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-50 group-hover:bg-gray-100 transition-colors">
                                <span class="text-4xl font-black text-gray-200 select-none">
                                    {{ strtoupper(substr($product->product_name, 0, 2)) }}
                                </span>
                            </div>
                        @endif

                        <!-- Hover Overlay actions (Desktop) -->
                        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                            <button onclick="openModal('view', {{ $product->id }})" class="w-10 h-10 rounded-full bg-white text-gray-600 shadow-lg hover:text-blue-600 hover:scale-110 transition-all flex items-center justify-center" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>

                            @if(!$product->supplier_id)
                                <!-- Approve button for proposed products -->
                                <button onclick="approveProduct({{ $product->id }})" class="w-10 h-10 rounded-full bg-white text-gray-600 shadow-lg hover:text-green-600 hover:scale-110 transition-all flex items-center justify-center" title="Duyệt sản phẩm">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                            <button onclick="openModal('edit', {{ $product->id }})" class="w-10 h-10 rounded-full bg-white text-gray-600 shadow-lg hover:text-orange-500 hover:scale-110 transition-all flex items-center justify-center" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct({{ $product->id }})" class="w-10 h-10 rounded-full bg-white text-gray-600 shadow-lg hover:text-red-500 hover:scale-110 transition-all flex items-center justify-center" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Tags: Category & SKU -->
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wide">
                                {{ $product->category->category_name ?? 'N/A' }}
                            </span>
                            <span class="text-[10px] font-medium text-gray-400">#{{ $product->product_code }}</span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-sm font-bold text-gray-900 mb-2 line-clamp-2 h-[40px] leading-tight group-hover:text-blue-600 transition-colors" title="{{ $product->product_name }}">
                            {{ $product->product_name }}
                        </h3>

                        <!-- Supplier -->
                        <div class="flex items-center gap-1.5 mb-4">
                            <i class="fas fa-store text-gray-300 text-xs"></i>
                            <p class="text-xs text-gray-500 truncate font-medium">
                                Nhà cung cấp: {{ $product->supplier->supplier_name ?? $product->supplier->supplier_code ?? 'Chưa cập nhật' }}
                            </p>
                        </div>

                        <!-- Footer: Stock & Price -->
                        <div class="flex items-end justify-between pt-3 border-t border-gray-50">
                            <!-- <div>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-0.5">Tồn kho</p>
                                <p class="text-sm font-bold {{ $product->stock_quantity > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $product->stock_quantity }} <span class="text-[10px] text-gray-500 font-normal">{{ $product->unit }}</span>
                                </p>
                            </div> -->
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-0.5">Giá nhập</p>
                                <p class="text-sm font-bold text-blue-600">
                                    {{ number_format($product->unit_price) }}<span class="text-xs">đ</span>
                                </p>
                            </div>
                        </div>

                        <!-- Mobile Actions (Visible on small screens only) -->
                        <div class="lg:hidden mt-3 pt-3 border-t border-gray-100 flex justify-between gap-2">
                             <button onclick="openModal('view', {{ $product->id }})" class="flex-1 py-1.5 rounded bg-gray-50 text-gray-600 text-xs font-medium hover:bg-gray-100">
                                <i class="fas fa-eye mr-1"></i> Xem
                            </button>
                            @if(!$product->supplier_id)
                                <button onclick="approveProduct({{ $product->id }})" class="flex-1 py-1.5 rounded bg-green-50 text-green-600 text-xs font-medium hover:bg-green-100">
                                    <i class="fas fa-check mr-1"></i> Duyệt
                                </button>
                            @endif
                            <button onclick="openModal('edit', {{ $product->id }})" class="flex-1 py-1.5 rounded bg-gray-50 text-gray-600 text-xs font-medium hover:bg-gray-100">
                                <i class="fas fa-edit mr-1"></i> Sửa
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4">
                        <i class="fas fa-box-open text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Không tìm thấy sản phẩm</h3>
                    <p class="text-gray-500 text-sm">Thử thay đổi bộ lọc hoặc tìm kiếm từ khóa khác</p>
                    @if(request('search') || request('category_id'))
                        <a href="{{ route('admin.products.index') }}" class="inline-block mt-4 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Xóa bộ lọc
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-600">
                        Hiển thị <span class="font-semibold text-gray-900">{{ $products->firstItem() ?? 0 }}</span> 
                        đến <span class="font-semibold text-gray-900">{{ $products->lastItem() ?? 0 }}</span> 
                        trong tổng số <span class="font-semibold text-gray-900">{{ $products->total() }}</span> sản phẩm
                    </p>
                    <div class="flex space-x-2">
                        {{-- Previous Button --}}
                        @if ($products->onFirstPage())
                            <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i>Trước
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-left mr-1"></i>Trước
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if ($page == $products->currentPage())
                                <span class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Sau<i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                                Sau<i class="fas fa-chevron-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        // Store original full lists for filtering
        let originalCategories = [];
        let originalSuppliers = [];
        
        // Auto-submit form on search input (with debounce)
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        });
        // Preview image before upload
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                    document.getElementById('delete_image').value = '0';
                }
                reader.readAsDataURL(file);
            }
        }

        // Remove image
        function removeImage() {
            document.getElementById('image').value = '';
            document.getElementById('imagePreview').src = '';
            document.getElementById('imagePreviewContainer').classList.add('hidden');
            document.getElementById('uploadPlaceholder').classList.remove('hidden');
            document.getElementById('delete_image').value = '1';
        }

        // Load existing image when editing
        function loadExistingImage(productId) {
            fetch(`/admin/products/${productId}/image`)
                .then(res => res.json())
                .then(data => {
                    if (data.image_url) {
                        document.getElementById('imagePreview').src = data.image_url;
                        document.getElementById('imagePreviewContainer').classList.remove('hidden');
                        document.getElementById('uploadPlaceholder').classList.add('hidden');
                    } else {
                        document.getElementById('imagePreviewContainer').classList.add('hidden');
                        document.getElementById('uploadPlaceholder').classList.remove('hidden');
                    }
                    document.getElementById('delete_image').value = '0';
                })
                .catch(err => console.error('Error loading image:', err));
        }
        // Auto-submit form on category change
        document.getElementById('categoryFilter')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // --- Modal Logic ---
        // --- Modal Logic ---
        function openModal(mode, id = null) {
            const modal = document.getElementById('productModal');
            const form = document.getElementById('productForm');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const submitBtn = document.getElementById('submitBtn');
            const changeImageBtn = document.getElementById('changeImageBtn');
            const deleteImageBtn = document.getElementById('deleteImageBtn');
            const lastUpdatedInfo = document.getElementById('lastUpdatedInfo');

            // Sections
            const inventoryStatus = document.getElementById('inventoryStatus');
            const supplierSection = document.getElementById('supplierSection');

            // Reset form and errors
            form.reset();
            removeImage(); 
            document.querySelectorAll('.error-feedback').forEach(el => el.classList.add('hidden'));

            // Remove readonly/disabled from all inputs
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
                el.classList.remove('bg-gray-100');
            });

            // Reset dropdowns to show all options
            resetCategoryDropdown();
            resetSupplierDropdown();
            
            // Reset Warehouse selection
            const warehouseSelect = document.getElementById('warehouse_ids');
            if(warehouseSelect) {
                Array.from(warehouseSelect.options).forEach(opt => opt.selected = false);
            }

            // Make product_code readonly for add mode (will be changed for edit mode below)
            document.getElementById('product_code').setAttribute('readonly', true);
            document.getElementById('product_code').classList.add('bg-gray-50');

            if (mode === 'add') {
                modalTitle.textContent = 'Thêm sản phẩm mới';
                if(modalSubtitle) modalSubtitle.textContent = 'Cung cấp chi tiết sản phẩm để cập nhật vào hệ thống mua sắm nội bộ.';
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Lưu sản phẩm';
                submitBtn.classList.remove('hidden');
                form.dataset.id = '';

                // Visibility
                if(changeImageBtn) changeImageBtn.classList.add('hidden');
                if(deleteImageBtn) deleteImageBtn.classList.remove('hidden'); // Show delete button if image selected
                if(lastUpdatedInfo) lastUpdatedInfo.classList.add('hidden');

                // Hide only sections that require existing data
                if(inventoryStatus) inventoryStatus.classList.add('hidden');

                // Show input sections
                if(supplierSection) supplierSection.classList.remove('hidden');

                // Auto-generate code if category has value
                const categorySelect = document.getElementById('category_id');
                if(categorySelect && categorySelect.value) {
                    generateProductCode(categorySelect.value);
                }
            } else {
                // Fetch data for Edit or View
                fetch(`/admin/products/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        // Fill form
                        document.getElementById('product_code').value = data.product_code;
                        document.getElementById('product_name').value = data.product_name;
                        document.getElementById('category_id').value = data.category_id;
                        document.getElementById('unit').value = data.unit;
                        document.getElementById('stock_quantity').value = data.stock_quantity; // Update stock
                        updateInventoryUI(data.stock_quantity); // Sync UI
                        document.getElementById('unit_price').value = Math.round(data.unit_price);
                        document.getElementById('description').value = data.description || '';

                        if(data.supplier_id) {
                            document.getElementById('supplier_id').value = data.supplier_id;
                        }

                        // Fill Warehouse selection
                        if(data.warehouses) {
                            const warehouseSelect = document.getElementById('warehouse_ids');
                            if(warehouseSelect) {
                                const selectedIds = data.warehouses.map(w => w.id);
                                Array.from(warehouseSelect.options).forEach(opt => {
                                    opt.selected = selectedIds.includes(parseInt(opt.value));
                                });
                            }
                        }

                        form.dataset.id = id;
                        loadExistingImage(id);

                        if (mode === 'edit') {
                            modalTitle.textContent = 'Chỉnh sửa Sản phẩm';
                            if(modalSubtitle) modalSubtitle.textContent = 'Chỉnh sửa thông tin chi tiết của vật tư y tế trong hệ thống.';
                            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Lưu thay đổi';
                            submitBtn.classList.remove('hidden');

                            if(changeImageBtn) changeImageBtn.classList.remove('hidden');
                            if(deleteImageBtn) deleteImageBtn.classList.remove('hidden');
                            if(lastUpdatedInfo) lastUpdatedInfo.classList.remove('hidden');

                            if(inventoryStatus) inventoryStatus.classList.remove('hidden');
                            if(supplierSection) supplierSection.classList.remove('hidden');

                            // Allow editing product_code in edit mode
                            document.getElementById('product_code').removeAttribute('readonly');
                            document.getElementById('product_code').classList.remove('bg-gray-50');

                        } else if (mode === 'view') {
                            modalTitle.textContent = 'Chi tiết sản phẩm';
                             if(modalSubtitle) modalSubtitle.textContent = 'Xem thông tin chi tiết của sản phẩm.';
                            submitBtn.classList.add('hidden');

                            if(changeImageBtn) changeImageBtn.classList.add('hidden');
                            if(deleteImageBtn) deleteImageBtn.classList.add('hidden'); // Hide delete button in view mode
                            if(lastUpdatedInfo) lastUpdatedInfo.classList.remove('hidden');

                            if(inventoryStatus) inventoryStatus.classList.remove('hidden');
                            if(supplierSection) supplierSection.classList.remove('hidden');

                            // Disable inputs
                            form.querySelectorAll('input, select, textarea').forEach(el => {
                                el.disabled = true;
                                el.classList.add('bg-gray-100');
                            });
                        }
                    });
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        // Auto-generate Code Logic - works in both Add and Edit mode
        document.getElementById('category_id').addEventListener('change', function() {
            const productId = document.getElementById('productForm').dataset.id;
            const categoryId = this.value;
            
            // Generate product code
            generateProductCode(categoryId, productId);
            
            // Filter suppliers by category
            if (categoryId) {
                filterSuppliersByCategory(categoryId);
            } else {
                // Reset to all suppliers if no category selected
                resetSupplierDropdown();
            }
        });
        
        // Listen for supplier changes
        document.getElementById('supplier_id').addEventListener('change', function() {
            const supplierId = this.value;
            
            // Filter categories by supplier
            if (supplierId) {
                filterCategoriesBySupplier(supplierId);
            } else {
                // Reset to all categories if no supplier selected
                resetCategoryDropdown();
            }
        });

        function generateProductCode(categoryId, productId = null) {
            if(!categoryId) return;

            let url = `/admin/products/generate-code?category_id=${categoryId}`;
            if(productId) {
                url += `&product_id=${productId}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('product_code').value = data.code;
                    }
                });
        }

        // Handle Form Submit
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const url = id ? `/admin/products/${id}` : '/admin/products';
            const method = id ? 'PUT' : 'POST';

            const formData = new FormData(this);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    if (res.status === 422) {
                        // Clear previous errors
                        document.querySelectorAll('.error-feedback').forEach(el => el.classList.add('hidden'));
                        document.querySelectorAll('input, select, textarea').forEach(el => el.classList.remove('border-red-500'));
                        
                        // Show new errors
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.querySelector(`.error-feedback[data-field="${field}"]`);
                            const inputEl = document.querySelector(`[name="${field}"]`);
                            
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0];
                                errorEl.classList.remove('hidden');
                            }
                            if (inputEl) {
                                inputEl.classList.add('border-red-500');
                            }
                        });
                        
                        throw new Error('Validation failed');
                    }
                    throw new Error(data.message || 'Có lỗi xảy ra');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message || 'Có lỗi xảy ra, vui lòng kiểm tra lại.',
                    });
                    // Reset button if not success but no 422 (logic logic failed)
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(err => {
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Lưu thay đổi'; // Restore icon structure if needed, or originalText but originalText lacks HTML

                if (err.message === 'Validation failed') return;

                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi hệ thống',
                    text: 'Không thể kết nối đến máy chủ.',
                });
            });
        });

        // Delete Product
        function deleteProduct(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Sản phẩm sẽ bị xóa khỏi hệ thống!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/products/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Đã xóa!',
                                data.message,
                                'success'
                            ).then(() => location.reload());
                        } else {
                            Swal.fire('Lỗi', 'Không thể xóa sản phẩm.', 'error');
                        }
                    });
                }
            });
        }

        // Approve Product
        function approveProduct(id) {
            Swal.fire({
                title: 'Duyệt sản phẩm đề xuất?',
                text: "Sản phẩm sẽ được thêm vào danh mục chính thức!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Duyệt ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/products/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Đã duyệt!',
                                data.message,
                                'success'
                            ).then(() => location.reload());
                        } else {
                            Swal.fire('Lỗi', data.message || 'Không thể duyệt sản phẩm.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ.', 'error');
                    });
                }
            });
        }

        // Update Inventory UI based on quantity
        function updateInventoryUI(quantity) {
            const qty = parseInt(quantity) || 0;
            const stockQtyEl = document.getElementById('stockQuantity');
            const stockStatusEl = document.getElementById('stockStatus');
            const progressBar = document.getElementById('stockProgressBar');

            if(!stockQtyEl || !stockStatusEl || !progressBar) return;

            stockQtyEl.textContent = qty;

            // Logic: Warning level 50
            let percentage = (qty / 50) * 100;
            if (percentage > 100) percentage = 100;
            progressBar.style.width = `${percentage}%`;

            // Status classes
            stockStatusEl.className = 'px-2 py-1 text-xs font-medium rounded';
            progressBar.className = 'h-1.5 rounded-full transition-all duration-300';

            if (qty <= 0) {
                stockStatusEl.textContent = 'HẾT HÀNG';
                stockStatusEl.classList.add('bg-gray-100', 'text-gray-800');
                progressBar.classList.add('bg-gray-500');
            } else if (qty <= 10) {
                stockStatusEl.textContent = 'SẮP HẾT HÀNG';
                stockStatusEl.classList.add('bg-yellow-100', 'text-yellow-800');
                progressBar.classList.add('bg-yellow-500');
            } else {
                stockStatusEl.textContent = 'SẴN HÀNG';
                stockStatusEl.classList.add('bg-green-100', 'text-green-800');
                progressBar.classList.add('bg-green-500');
            }
        }

        // Listen for stock input changes
        document.getElementById('stock_quantity')?.addEventListener('input', function() {
            updateInventoryUI(this.value);
        });
        
        // === Dynamic Category-Supplier Filtering ===
        
        // Store original dropdown options on page load
        document.addEventListener('DOMContentLoaded', function() {
            storeOriginalDropdownOptions();
        });
        
        function storeOriginalDropdownOptions() {
            const categorySelect = document.getElementById('category_id');
            const supplierSelect = document.getElementById('supplier_id');
            
            // Store categories
            originalCategories = Array.from(categorySelect.options).map(opt => ({
                value: opt.value,
                text: opt.text
            }));
            
            // Store suppliers
            originalSuppliers = Array.from(supplierSelect.options).map(opt => ({
                value: opt.value,
                text: opt.text
            }));
        }
        
        function filterSuppliersByCategory(categoryId) {
            fetch(`/admin/products/suppliers-by-category?category_id=${categoryId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const supplierSelect = document.getElementById('supplier_id');
                        const currentValue = supplierSelect.value;
                        
                        // Clear current options except the first (placeholder)
                        supplierSelect.innerHTML = '<option value="">-- Chọn nhà cung cấp --</option>';
                        
                        if (data.suppliers.length === 0) {
                            // No suppliers for this category
                            const option = document.createElement('option');
                            option.value = '';
                            option.text = 'Chưa có nhà cung cấp cho danh mục này';
                            option.disabled = true;
                            supplierSelect.appendChild(option);
                        } else {
                            // Add filtered suppliers
                            data.suppliers.forEach(supplier => {
                                const option = document.createElement('option');
                                option.value = supplier.id;
                                option.text = supplier.supplier_name;
                                supplierSelect.appendChild(option);
                            });
                            
                            // Restore previous selection if it's in the filtered list
                            if (currentValue && data.suppliers.some(s => s.id == currentValue)) {
                                supplierSelect.value = currentValue;
                            }
                        }
                    }
                })
                .catch(err => console.error('Error filtering suppliers:', err));
        }
        
        function filterCategoriesBySupplier(supplierId) {
            fetch(`/admin/products/categories-by-supplier?supplier_id=${supplierId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const categorySelect = document.getElementById('category_id');
                        const currentValue = categorySelect.value;
                        
                        // Clear current options except the first (placeholder)
                        categorySelect.innerHTML = '<option value="" disabled selected>-- Chọn danh mục --</option>';
                        
                        if (data.categories.length === 0) {
                            // No categories for this supplier
                            const option = document.createElement('option');
                            option.value = '';
                            option.text = 'Nhà cung cấp chưa đăng ký danh mục nào';
                            option.disabled = true;
                            categorySelect.appendChild(option);
                        } else {
                            // Add filtered categories
                            data.categories.forEach(category => {
                                const option = document.createElement('option');
                                option.value = category.id;
                                option.text = category.category_name;
                                categorySelect.appendChild(option);
                            });
                            
                            // Restore previous selection if it's in the filtered list
                            if (currentValue && data.categories.some(c => c.id == currentValue)) {
                                categorySelect.value = currentValue;
                            }
                        }
                    }
                })
                .catch(err => console.error('Error filtering categories:', err));
        }
        
        function resetSupplierDropdown() {
            const supplierSelect = document.getElementById('supplier_id');
            const currentValue = supplierSelect.value;
            
            supplierSelect.innerHTML = '';
            originalSuppliers.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                supplierSelect.appendChild(option);
            });
            
            // Restore selection if possible
            if (currentValue) {
                supplierSelect.value = currentValue;
            }
        }
        
        function resetCategoryDropdown() {
            const categorySelect = document.getElementById('category_id');
            const currentValue = categorySelect.value;
            
            categorySelect.innerHTML = '';
            originalCategories.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                if (opt.value === '' || opt.text.includes('Chọn danh mục')) {
                    option.disabled = true;
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });
            
            // Restore selection if possible
            if (currentValue) {
                categorySelect.value = currentValue;
            }
        }
        </script>
    @endpush

    <!-- Modal HTML -->
    <div id="productModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <!-- Modal panel -->
            <div
                class="relative bg-white rounded-xl shadow-2xl transform transition-all max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div
                    class="sticky top-0 bg-white z-10 flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <!-- <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                            <span>Quản lý sản phẩm</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                            <span>Chi tiết sản phẩm</span>
                        </div> -->
                        <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Chỉnh sửa Sản phẩm</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="closeModal()" class="ml-2 text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <form id="productForm" class="p-6 space-y-8">
                    <!-- Section 1: Product Info -->
                    <div class="grid grid-cols-12 gap-6">
                        <!-- Left Column - Image Upload (3 columns) -->
                        <div class="col-span-3">
                            <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Ảnh sản phẩm</h4>
                            <!-- Image Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                                <div id="imagePreviewContainer" class="hidden">
                                    <img id="imagePreview" src="" alt="Preview"
                                        class="w-full h-40 object-cover rounded-lg mb-2">
                                    <button type="button" id="deleteImageBtn" onclick="removeImage()"
                                        class="text-sm text-red-600 hover:text-red-800 font-medium w-full">
                                        <i class="fas fa-trash mr-1"></i>Xóa ảnh
                                    </button>
                                </div>
                                <div id="uploadPlaceholder">
                                    <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center mb-2 cursor-pointer"
                                        onclick="document.getElementById('image').click()">
                                        <i class="fas fa-image text-gray-300 text-3xl"></i>
                                    </div>
                                </div>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)"
                                class="hidden">
                            <input type="hidden" name="delete_image" id="delete_image" value="0">
                            <!-- Change Image Button -->
                            <button type="button" id="changeImageBtn"
                                class="mt-3 w-full px-3 py-2 border border-gray-300 bg-white rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition flex items-center justify-center gap-2"
                                onclick="document.getElementById('image').click()">
                                <i class="fas fa-sync-alt"></i>
                                Thay đổi ảnh
                            </button>
                            <!-- Info Box -->
                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5 text-sm"></i>
                                    <div>
                                        <p class="text-xs font-semibold text-blue-900 mb-1">Mẹo tải ảnh</p>
                                        <p class="text-xs text-blue-700">Sử dụng ảnh có nền trắng và độ phân giải tối thiểu
                                            800×800px để hiển thị tốt nhất trên danh mục.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Inventory Status (edit mode only) -->
                            <div id="inventoryStatus" class="hidden mt-6">
                                <h4 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Trạng thái kho
                                </h4>
                                <div class="bg-white border border-gray-200 rounded-lg p-3 space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Tình trạng:</span>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded"
                                            id="stockStatus">SẮP HẾT HÀNG</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Tồn kho hiện tại:</span>
                                        <span class="font-bold text-gray-900" id="stockQuantity">42</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div id="stockProgressBar" class="bg-gray-500 h-1.5 rounded-full" style="width: 0%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 pt-2 border-t border-gray-200">Mức cảnh báo tồn kho là
                                        50 đơn vị. Vui lòng xem xét đặt hàng để tránh tình trạng hết hàng trong kho.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column - Form Fields (9 columns) -->
                        <div class="col-span-9 space-y-8">

                            <!-- Product Info Section -->
                            <div>
                                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    <h3 class="text-base font-bold text-gray-900">Thông tin cơ bản</h3>
                                </div>

                                <div class="space-y-4">
                                    <!-- Row 1: SKU, Unit, Quantity -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Mã
                                                sản phẩm</label>
                                            <input type="text" name="product_code" id="product_code" placeholder="TPCN001"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                                                readonly required>
                                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="product_code"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Đơn
                                                vị tính</label>
                                            <input type="text" name="unit" id="unit" list="unit_list" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Chọn hoặc nhập..." required>
                                            <datalist id="unit_list">
                                                @foreach($units ?? [] as $u)
                                                    <option value="{{ $u }}">
                                                @endforeach
                                                <option value="Hộp">
                                                <option value="Cái">
                                                <option value="Bộ">
                                                <option value="Lọ">
                                                <option value="Chai">
                                                <option value="Túi">
                                                <option value="Viên">
                                                <option value="Vỉ">
                                                <option value="Vỉ">
                                            </datalist>
                                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="unit"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Số lượng nhập vào</label>
                                            <input type="number" name="stock_quantity" id="stock_quantity" placeholder="0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-medium"
                                                required>
                                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="stock_quantity"></p>
                                        </div>
                                    </div>
                                    <!-- Row 2: Product Name (full width) -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Tên sản
                                            phẩm</label>
                                        <input type="text" name="product_name" id="product_name"
                                            placeholder="Nhập tên sản phẩm..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required>
                                    </div>
                                    <!-- Row 3: Category & Price -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Danh
                                                mục</label>
                                            <select name="category_id" id="category_id"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required>
                                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="product_name"></p>
                                                <option value="" disabled selected>-- Chọn danh mục --</option>
                                                @foreach($categories ?? [] as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="category_id"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Đơn
                                                giá nhập (VNĐ)</label>
                                            <div class="relative">
                                                <input type="number" name="unit_price" id="unit_price" placeholder="12000"
                                                    class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                                                    required>
                                                <span class="absolute right-3 top-2.5 text-sm text-gray-400">VNĐ</span>
                                            </div>
                                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="unit_price"></p>
                                        </div>
                                    </div>
                                    <!-- Row 4: Retail Price Removed -->
                                    <!-- Row 5: Description -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Mô tả
                                            sản phẩm</label>
                                        <textarea name="description" id="description" rows="3"
                                            placeholder="Sản phẩm hỗ trợ tăng cường hệ miễn dịch..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Supplier & Warehouse (edit mode only) -->
                            <div id="supplierSection" class="hidden">
                                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-2">
                                    <i class="fas fa-truck text-blue-600"></i>
                                    <h3 class="text-base font-bold text-gray-900">Nhà cung cấp & Kho vận</h3>
                                </div>
                                <div class="space-y-4">
                                    <!-- Supplier Info -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Nhà
                                                cung cấp</label>
                                            <select name="supplier_id" id="supplier_id"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">-- Chọn nhà cung cấp --</option>
                                                @foreach($suppliers ?? [] as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Warehouse Info -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Ngày
                                                nhập kho gần nhất</label>
                                            <input type="date" name="last_import_date" id="last_import_date" value="2023-11-15"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Hạn
                                                sử dụng tối đa</label>
                                            <div class="flex gap-2">
                                                <input type="number" name="expiry_value" id="expiry_value" value="24"
                                                    placeholder="24"
                                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <select name="expiry_unit" id="expiry_unit"
                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option>tháng</option>
                                                    <option>ngày</option>
                                                    <option>năm</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Info Box -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-info-circle text-blue-600"></i>
                                            </div>
                                            <div>
                                                <h5 class="text-sm font-semibold text-blue-900 mb-1">Chế độ xem trước</h5>
                                                <p class="text-xs text-blue-700">Bạn đang ở chế độ chỉnh sửa chi tiết.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Last updated info (edit mode only) -->
                    <div id="lastUpdatedInfo"
                        class="hidden text-xs text-gray-400 flex items-center gap-1 pt-4 border-t border-gray-200">
                        <i class="fas fa-clock"></i>
                        <span id="lastUpdatedText">Lần cuối cập nhật: 10/01/2026 bởi Admin</span>
                    </div>
                </form>
                <!-- Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 flex items-center justify-end gap-3 px-6 py-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 text-sm transition">
                        Hủy
                    </button>
                    <button type="button" id="submitBtn" 
                            onclick="document.getElementById('productForm').dispatchEvent(new Event('submit'))" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 text-sm transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .tab-button.active {
        border-bottom-color: #3b82f6;
        color: #3b82f6;
    }
    </style>

    <script>
    // Tab switching

    </script>
@endsection
