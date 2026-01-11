@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page-title', 'Quản lý danh mục sản phẩm')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quản lý danh mục sản phẩm</h2>
            <p class="text-gray-600 mt-1">Quản lý danh sách thuốc, vật tư y tế và các loại hàng hóa khác trong bệnh viện.</p>
        </div>
        <div class="flex space-x-3">
            <input type="file" id="importExcelInput" class="hidden" accept=".xlsx, .xls">
            <button onclick="document.getElementById('importExcelInput').click()" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-file-excel mr-2"></i>Nhập Excel
            </button>
            <button onclick="openAddModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-plus mr-2"></i>Thêm danh mục
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">Tổng sản phẩm</span>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-500"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ number_format($allProducts->count()) }}</h3>
            <p class="text-xs text-gray-500 mt-2">
                Tổng số sản phẩm trong kho
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">Danh mục chính</span>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-500"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $categories->count() }}</h3>
            <p class="text-xs text-gray-500 mt-2">{{ $allProducts->unique('category_id')->count() }} nhóm danh mục con</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">Sản phẩm mới</span>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus-circle text-blue-500"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $allProducts->where('created_at', '>=', now()->subDays(30))->count() }}</h3>
            <p class="text-xs text-gray-500 mt-2">Trong 30 ngày qua</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">Cảnh báo tồn kho</span>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-orange-500"></i>
                </div>
            </div> -->
            <h3 class="text-3xl font-bold text-gray-800">{{ $allProducts->where('stock_quantity', '<', 10)->count() }}</h3>
            <p class="text-xs text-red-600 mt-2">Cần nhập hàng ngay</p>
        </div>
    </div>

    <!-- Main Content with Sidebar -->
    <div class="flex gap-6">
        <!-- Sidebar Categories -->
        <div class="w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Danh mục</h3>
                    <button onclick="openAddModal()" class="text-blue-600 text-sm cursor-pointer hover:underline uppercase font-bold">Thêm mới</button>
                </div>
                
                <div class="space-y-1">
                    <a href="{{ route('admin.categories') }}" class="flex items-center justify-between px-3 py-2 rounded-lg {{ !request('category_id') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center">
                            <i class="fas fa-box w-5"></i>
                            <span class="ml-2">Quản lý chung</span>
                        </div>
                    </a>

                    <div class="pt-2 pb-1">
                        <p class="text-xs font-semibold text-gray-500 uppercase px-3 mb-2">DANH SÁCH DANH MỤC</p>
                    </div>

                    @foreach($allCategories as $cat)
                    <a href="{{ route('admin.categories', ['category_id' => $cat->id]) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg group transition-colors {{ request('category_id') == $cat->id ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center">
                            <i class="fas fa-folder w-5 {{ request('category_id') == $cat->id ? 'text-blue-500' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
                            <span class="ml-2 truncate max-w-[120px]" title="{{ $cat->category_name }}">{{ $cat->category_name }}</span>
                        </div>
                        <span class="text-xs {{ request('category_id') == $cat->id ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} px-2 py-0.5 rounded-full">{{ $cat->products_count ?? 0 }}</span>
                    </a>
                    @endforeach

                    @if($allCategories->isEmpty())
                        <div class="px-3 py-4 text-center text-sm text-gray-500">
                            Chưa có danh mục nào
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Table Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Tìm kiếm tên thuốc, mã SKU..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 ml-4">
                        <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Trạng thái</option>
                            <option>Còn hàng</option>
                            <option>Hết hàng</option>
                            <option>Sắp hết</option>
                        </select>
                        <button class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-filter text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên danh mục</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã danh mục</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng SP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-layer-group text-blue-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">{{ $category->category_name }}</p>
                                        <p class="text-xs text-gray-500">Tạo: {{ $category->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $category->category_code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $category->description }}">
                                {{ $category->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                    {{ $category->products_count }} sản phẩm
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->category_name) }}', '{{ addslashes($category->description ?? '') }}')" 
                                    class="text-blue-600 hover:text-blue-800" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $category->id }})" 
                                    class="text-red-600 hover:text-red-800" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-folder-open text-6xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Chưa có danh mục nào</p>
                                <p class="text-sm mt-2">Thêm danh mục đầu tiên để bắt đầu quản lý</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Filtered Products Section -->
    @if(isset($filteredProducts) && $filteredProducts->isNotEmpty())
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-box-open mr-2 text-blue-600"></i>
            Sản phẩm thuộc danh mục: <span class="text-blue-600 ml-1">{{ $categories->first()->category_name ?? '' }}</span>
            <span class="ml-3 px-2 py-0.5 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">{{ $filteredProducts->count() }} sản phẩm</span>
        </h3>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Mã SP</th>
                        <th class="px-6 py-3">Tên sản phẩm</th>
                        <th class="px-6 py-3">Đơn vị</th>
                        <th class="px-6 py-3">Đơn giá</th>
                        <th class="px-6 py-3">Tình trạng kho</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($filteredProducts as $prod)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-blue-600 font-medium">{{ $prod->product_code }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $prod->product_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $prod->unit }}</td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ number_format($prod->unit_price) }}đ</td>
                        <td class="px-6 py-4">
                            @if($prod->stock_quantity <= 10)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Sắp hết ({{ $prod->stock_quantity }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Còn hàng ({{ $prod->stock_quantity }})
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Thêm danh mục mới</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addCategoryForm">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tên danh mục *</label>
                <input type="text" name="category_name" id="add_category_name" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mã danh mục</label>
                <input type="text" name="category_code" id="add_category_code" placeholder="Tự động tạo từ tên danh mục"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline uppercase">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mô tả</label>
                <textarea name="description" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeAddModal()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Thêm danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Sửa danh mục</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editCategoryForm">
            @csrf
            <input type="hidden" id="edit_category_id">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tên danh mục *</label>
                <input type="text" id="edit_category_name" name="category_name" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mã danh mục</label>
                <input type="text" id="edit_category_code" name="category_code"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline uppercase">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mô tả</label>
                <textarea id="edit_description" name="description" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Hủy
                </button>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Xác nhận xóa</h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-gray-600 mb-4">Bạn có chắc chắn muốn xóa danh mục này không?</p>
        <input type="hidden" id="delete_category_id">
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="closeDeleteModal()"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Hủy
            </button>
            <button onclick="confirmDelete()"
                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                Xóa
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate Category Code
document.getElementById('add_category_name')?.addEventListener('input', function(e) {
    const name = e.target.value;
    // Get initials: "Thiết bị y tế" -> "TBYT"
    const code = name.normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Remove accents
                    .split(/\s+/) // Split by whitespace
                    .map(word => word.charAt(0)) // Get first char of each word
                    .join('')
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, ''); // Keep only alphanumeric
    
    document.getElementById('add_category_code').value = code;
});

// Modal functions
function openAddModal() {
    document.getElementById('addCategoryModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addCategoryModal').classList.add('hidden');
    document.getElementById('addCategoryForm').reset();
}

function openEditModal(id, name, description, code) {
    document.getElementById('edit_category_id').value = id;
    document.getElementById('edit_category_name').value = name;
    document.getElementById('edit_category_code').value = code || '';
    document.getElementById('edit_description').value = description || '';
    document.getElementById('editCategoryModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCategoryModal').classList.add('hidden');
    document.getElementById('editCategoryForm').reset();
}

function openDeleteModal(id) {
    document.getElementById('delete_category_id').value = id;
    document.getElementById('deleteCategoryModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteCategoryModal').classList.add('hidden');
}

// Add Category
document.getElementById('addCategoryForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("admin.categories.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', data.message);
            closeAddModal();
            location.reload(); // Reload to update table
        } else {
            showNotification('error', data.message);
        }
    } catch (error) {
        showNotification('error', 'Có lỗi xảy ra khi thêm danh mục');
    }
});

// Edit Category
document.getElementById('editCategoryForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('edit_category_id').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/admin/categories/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', data.message);
            closeEditModal();
            location.reload();
        } else {
            showNotification('error', data.message);
        }
    } catch (error) {
        showNotification('error', 'Có lỗi xảy ra khi cập nhật danh mục');
    }
});

// Delete Category
async function confirmDelete() {
    const id = document.getElementById('delete_category_id').value;
    
    try {
        const response = await fetch(`/admin/categories/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'DELETE'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', data.message);
            closeDeleteModal();
            location.reload();
        } else {
            showNotification('error', data.message);
        }
    } catch (error) {
        showNotification('error', 'Có lỗi xảy ra khi xóa danh mục');
    }
}

// Notification function
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Search functionality
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});


// Import Excel Functionality
document.getElementById('importExcelInput')?.addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    try {
        // Show loading state
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 bg-blue-500 text-white';
        notification.textContent = 'Đang nhập dữ liệu...';
        document.body.appendChild(notification);

        const response = await fetch('{{ route("admin.categories.import") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();
        
        // Remove loading notification
        notification.remove();

        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            if (data.errors) {
                // Show detailed errors
                showNotification('error', 'Lỗi: ' + data.errors.join('\n'));
            } else {
                showNotification('error', data.message);
            }
        }
    } catch (error) {
        showNotification('error', 'Có lỗi xảy ra khi nhập file Excel');
    }
    
    // Reset input
    this.value = '';
});
</script>
@endpush
@endsection
