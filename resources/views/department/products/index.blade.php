@extends('layouts.department')

@section('title', 'Sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Danh mục sản phẩm</h1>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <!-- Category Dropdown -->
                    <div class="w-full md:w-64">
                        <form action="{{ route('department.products.index') }}" method="GET" id="categoryForm">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="category_id" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition appearance-none bg-white cursor-pointer">
                                <option value="all" {{ !request('category_id') || request('category_id') == 'all' ? 'selected' : '' }}>Tất cả danh mục</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <!-- Search Bar -->
                    <form action="{{ route('department.products.index') }}" method="GET" class="relative w-full md:w-96">
                        @if(request('category_id'))
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            placeholder="Tìm kiếm tên sản phẩm, mã số...">
                        <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div onclick="openProductModal({{ $product->id }})"
                            class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group cursor-pointer h-full flex flex-col">
                            <!-- Product Image/Icon -->
                            <div class="relative h-40 bg-white flex items-center justify-center overflow-hidden">
                                @php
                                    $imageUrl = getProductImage($product->id);
                                @endphp

                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}"
                                        class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <!-- Fallback Letter Avatar -->
                                    <div
                                        class="relative z-10 w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">
                                            {{ strtoupper(substr($product->product_name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Hover Overlay hint -->
                                <div
                                    class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span
                                        class="bg-white/90 px-3 py-1 rounded-full text-xs font-medium text-blue-600 shadow-sm backdrop-blur-sm">
                                        Xem chi tiết
                                    </span>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-4 flex-1 flex flex-col">
                                <!-- Category Badge -->
                                <div class="mb-2">
                                    <span
                                        class="inline-block px-3 py-1 text-xs font-semibold bg-blue-50 text-blue-600 rounded-full">
                                        {{ $product->category->category_name ?? 'Khác' }}
                                    </span>
                                </div>

                                <!-- Product Name -->
                                <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 h-10 group-hover:text-blue-600 transition-colors"
                                    title="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                </h3>

                                <!-- Product Code -->
                                <p class="text-sm text-gray-500 mb-3 font-mono">{{ $product->product_code }}</p>

                                <div class="mt-auto">
                                    <!-- Product Details Grid -->
                                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100 text-center">
                                        <!-- Unit -->
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Đơn vị</p>
                                            <p class="text-sm font-semibold text-gray-700">{{ $product->unit }}</p>
                                        </div>

                                        <!-- Price -->
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Giá tham khảo</p>
                                            <p class="text-sm font-bold text-blue-600">
                                                {{ number_format($product->unit_price, 0, ',', '.') }} đ
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
                    <div
                        class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-5xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Không tìm thấy sản phẩm nào</h3>
                    <p class="text-gray-600">Thử tìm kiếm với từ khóa khác hoặc chọn danh mục khác</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="productModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeProductModal()"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Header -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">Chi tiết sản phẩm</p>
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modalTitle">Loading...</h3>
                        </div>
                        <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Left Column: Image -->
                        <div class="md:col-span-1">
                            <div
                                class="aspect-square bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden mb-4">
                                <img id="modalImage" src="" alt="Product Image"
                                    class="w-full h-full object-contain p-4 hidden">
                                <div id="modalImagePlaceholder" class="hidden">
                                    <span class="text-6xl font-black text-gray-200 select-none"
                                        id="modalImageLetter"></span>
                                </div>
                            </div>

                            <!-- Department Stock Status -->
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                <p class="text-xs font-semibold text-blue-800 uppercase tracking-wide mb-2">Kho Khoa/Phòng
                                </p>
                                <div class="flex items-end justify-between">
                                    <span class="text-sm text-gray-600">Tồn hiện tại:</span>
                                    <span class="text-2xl font-bold text-blue-700" id="modalDepartmentStock">--</span>
                                </div>
                                <p class="text-xs text-blue-600 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> Số lượng thực tế tại kho của bạn
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Details -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Basic Info -->
                            <div>
                                <h4
                                    class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3 border-b border-gray-100 pb-2">
                                    Thông tin cơ bản</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Mã sản
                                            phẩm</label>
                                        <p class="text-base font-semibold text-gray-900" id="modalCode">--</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Đơn vị
                                            tính</label>
                                        <p class="text-base font-semibold text-gray-900" id="modalUnit">--</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Danh
                                            mục</label>
                                        <p class="text-base font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded inline-block"
                                            id="modalCategory">--</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Giá nhập (Tham
                                            khảo)</label>
                                        <p class="text-base font-bold text-gray-900" id="modalPrice">--</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Mô tả sản phẩm</label>
                                <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 leading-relaxed min-h-[80px]"
                                    id="modalDescription">
                                    --
                                </div>
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">Nhà cung cấp</label>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-store text-gray-400 mr-2"></i>
                                    <span id="modalSupplier">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeProductModal()"
                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Đóng
                    </button>
                    <!-- Potential Request Button -->
                    <!-- 
                        <a href="{{ route('department.requests.create') }}" class="mt-3 w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-plus mr-2 mt-0.5"></i> Tạo yêu cầu
                        </a> 
                        -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openProductModal(id) {
                // Show modal with loading state
                document.getElementById('productModal').classList.remove('hidden');

                // Fetch data
                fetch(`/department/products/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        // Populate data
                        document.getElementById('modalTitle').textContent = data.product_name;
                        document.getElementById('modalCode').textContent = data.product_code;
                        document.getElementById('modalUnit').textContent = data.unit;
                        document.getElementById('modalCategory').textContent = data.category ? data.category.category_name : 'N/A';
                        document.getElementById('modalPrice').textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.unit_price);
                        document.getElementById('modalDescription').textContent = data.description || 'Chưa có mô tả';
                        document.getElementById('modalSupplier').textContent = data.supplier ? (data.supplier.supplier_name || data.supplier.supplier_code) : 'Chưa cập nhật';

                        // Department Stock
                        document.getElementById('modalDepartmentStock').textContent = data.department_stock;

                        // Image logic
                        const imgEl = document.getElementById('modalImage');
                        const placeholderEl = document.getElementById('modalImagePlaceholder');
                        const letterEl = document.getElementById('modalImageLetter');

                        if (data.image_url) {
                            imgEl.src = data.image_url;
                            imgEl.classList.remove('hidden');
                            placeholderEl.classList.add('hidden');
                        } else {
                            imgEl.classList.add('hidden');
                            placeholderEl.classList.remove('hidden');
                            placeholderEl.className = "w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg";
                            letterEl.textContent = data.product_name.charAt(0).toUpperCase();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Không thể tải thông tin sản phẩm');
                    });
            }

            function closeProductModal() {
                document.getElementById('productModal').classList.add('hidden');
            }
        </script>
    @endpush
@endsection