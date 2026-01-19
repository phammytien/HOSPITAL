@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')
@section('page-title', 'Quản lý nhà cung cấp')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600 mt-1">Quản lý thông tin các nhà cung cấp vật tư y tế</p>
        </div>
        <button onclick="openModal('add')" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition shadow-md flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Thêm nhà cung cấp
        </button>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form id="filterForm" method="GET" action="{{ route('admin.suppliers') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm theo tên, mã SUP, người liên hệ..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Tìm kiếm
            </button>
        </form>
    </div>

    @if($suppliers->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Chưa có nhà cung cấp nào</h3>
            <p class="text-gray-500 mb-6">Bắt đầu bằng cách thêm nhà cung cấp đầu tiên của bạn</p>
            <button onclick="openModal('add')" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Thêm nhà cung cấp
            </button>
        </div>
    @else
        <!-- Suppliers Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã SUP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên nhà cung cấp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người liên hệ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số sản phẩm</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($suppliers as $supplier)
                    <!-- Main Row -->
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleProducts({{ $supplier->id }})">
                        <td class="px-6 py-4 text-center">
                            <i id="chevron-{{ $supplier->id }}" class="fas fa-chevron-right text-gray-400 transition-transform duration-200"></i>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-blue-600">{{ $supplier->supplier_code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $supplier->supplier_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $supplier->contact_person ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $supplier->phone_number ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $supplier->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $supplier->products_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('view', {{ $supplier->id }})" 
                                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="openModal('edit', {{ $supplier->id }})" 
                                        class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteSupplier({{ $supplier->id }})" 
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Expandable Products Row -->
                    <tr id="products-{{ $supplier->id }}" class="hidden bg-gray-50">
                        <td colspan="8" class="px-6 py-4">
                            <div class="flex items-center justify-center py-4" id="loading-{{ $supplier->id }}">
                                <i class="fas fa-spinner fa-spin text-blue-600 mr-2"></i>
                                <span class="text-sm text-gray-600">Đang tải sản phẩm...</span>
                            </div>
                            <div id="products-content-{{ $supplier->id }}" class="hidden">
                                <!-- Products will be loaded here -->
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Hiển thị <span class="font-medium">{{ $suppliers->firstItem() }}</span> 
                đến <span class="font-medium">{{ $suppliers->lastItem() }}</span> 
                trong tổng số <span class="font-medium">{{ $suppliers->total() }}</span> nhà cung cấp
            </div>
            <div class="flex gap-2">
                @if ($suppliers->onFirstPage())
                    <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                        <i class="fas fa-chevron-left mr-1"></i>Trước
                    </span>
                @else
                    <a href="{{ $suppliers->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chevron-left mr-1"></i>Trước
                    </a>
                @endif

                @if ($suppliers->hasMorePages())
                    <a href="{{ $suppliers->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Sau<i class="fas fa-chevron-right ml-1"></i>
                    </a>
                @else
                    <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                        Sau<i class="fas fa-chevron-right ml-1"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Auto-submit form on search input (with debounce)
let searchTimeout;
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});

// --- Modal Logic ---
function openModal(mode, id = null) {
    const modal = document.getElementById('supplierModal');
    const form = document.getElementById('supplierForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtnHeader');
    const lastUpdatedInfo = document.getElementById('lastUpdatedInfo');

    // Reset form and errors
    form.reset();
    document.querySelectorAll('.error-feedback').forEach(el => el.classList.add('hidden'));
    
    // Remove readonly/disabled from all inputs
    form.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = false;
        el.classList.remove('bg-gray-100');
    });

    // Make supplier_code readonly again
    document.getElementById('supplier_code').setAttribute('readonly', true);
    document.getElementById('supplier_code').classList.add('bg-gray-50');

    if (mode === 'add') {
        modalTitle.textContent = 'Thêm nhà cung cấp mới';
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Lưu nhà cung cấp';
        submitBtn.classList.remove('hidden');
        form.dataset.id = '';
        
        if(lastUpdatedInfo) lastUpdatedInfo.classList.add('hidden');
        
        // Auto-generate code
        generateSupplierCode();
    } else {
        // Fetch data for Edit or View
        fetch(`/admin/suppliers/${id}`)
            .then(res => res.json())
            .then(data => {
                // Fill form
                document.getElementById('supplier_code').value = data.supplier_code;
                document.getElementById('supplier_name').value = data.supplier_name;
                document.getElementById('contact_person').value = data.contact_person || '';
                document.getElementById('phone_number').value = data.phone_number || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('address').value = data.address || '';
                document.getElementById('note').value = data.note || '';

                form.dataset.id = id;

                if (mode === 'edit') {
                    modalTitle.textContent = 'Chỉnh sửa nhà cung cấp';
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Lưu thay đổi';
                    submitBtn.classList.remove('hidden');
                    
                    if(lastUpdatedInfo) {
                        lastUpdatedInfo.classList.remove('hidden');
                        lastUpdatedInfo.textContent = `Lần cập nhật cuối: ${new Date(data.updated_at).toLocaleString('vi-VN')} bởi Admin`;
                    }

                } else if (mode === 'view') {
                    modalTitle.textContent = 'Chi tiết nhà cung cấp';
                    submitBtn.classList.add('hidden');
                    
                    if(lastUpdatedInfo) {
                        lastUpdatedInfo.classList.remove('hidden');
                        lastUpdatedInfo.textContent = `Lần cập nhật cuối: ${new Date(data.updated_at).toLocaleString('vi-VN')} bởi Admin`;
                    }
                    
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
    document.getElementById('supplierModal').classList.add('hidden');
}

function generateSupplierCode() {
    fetch('/admin/suppliers/generate-code')
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('supplier_code').value = data.code;
            }
        });
}

// Handle Form Submit
document.getElementById('supplierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = this.dataset.id;
    const url = id ? `/admin/suppliers/${id}` : '/admin/suppliers';
    const method = id ? 'PUT' : 'POST';
    
    const formData = new FormData(this);
    if (method === 'PUT') {
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
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
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi hệ thống',
            text: 'Không thể kết nối đến máy chủ.',
        });
    });
});

// Delete Supplier
function deleteSupplier(id) {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Nhà cung cấp sẽ bị xóa khỏi hệ thống!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa ngay',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/suppliers/${id}`, {
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
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

// Toggle Products Display
const expandedSuppliers = new Set();

function toggleProducts(supplierId) {
    const productsRow = document.getElementById(`products-${supplierId}`);
    const chevron = document.getElementById(`chevron-${supplierId}`);
    const loadingDiv = document.getElementById(`loading-${supplierId}`);
    const contentDiv = document.getElementById(`products-content-${supplierId}`);
    
    if (expandedSuppliers.has(supplierId)) {
        // Collapse
        productsRow.classList.add('hidden');
        chevron.classList.remove('rotate-90');
        expandedSuppliers.delete(supplierId);
    } else {
        // Expand
        productsRow.classList.remove('hidden');
        chevron.classList.add('rotate-90');
        expandedSuppliers.add(supplierId);
        
        // Load products if not already loaded
        if (!contentDiv.dataset.loaded) {
            loadingDiv.classList.remove('hidden');
            contentDiv.classList.add('hidden');
            
            fetch(`/admin/suppliers/${supplierId}/products`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderProducts(supplierId, data.products);
                        contentDiv.dataset.loaded = 'true';
                    }
                    loadingDiv.classList.add('hidden');
                    contentDiv.classList.remove('hidden');
                })
                .catch(err => {
                    console.error(err);
                    contentDiv.innerHTML = '<p class="text-center text-red-600 py-4">Lỗi khi tải sản phẩm</p>';
                    loadingDiv.classList.add('hidden');
                    contentDiv.classList.remove('hidden');
                });
        }
    }
}

function renderProducts(supplierId, products) {
    const contentDiv = document.getElementById(`products-content-${supplierId}`);
    
    if (products.length === 0) {
        contentDiv.innerHTML = '<p class="text-center text-gray-500 py-4">Nhà cung cấp chưa có sản phẩm nào</p>';
        return;
    }
    
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
    
    products.forEach(product => {
        const stockBadge = product.stock_quantity <= 0 ? 
            '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Hết hàng</span>' :
            product.stock_quantity <= 10 ?
            '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Sắp hết</span>' :
            '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Sẵn hàng</span>';
        
        html += `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-start gap-3">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        ${product.image_url ? 
                            `<img src="${product.image_url}" alt="${product.product_name}" class="w-full h-full object-cover rounded-lg">` :
                            '<i class="fas fa-box text-2xl text-gray-400"></i>'
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">${product.product_name}</h4>
                            ${stockBadge}
                        </div>
                        <p class="text-xs text-gray-500 mb-2">${product.category_name} • ${product.product_code}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600">Tồn kho: <strong>${product.stock_quantity}</strong> ${product.unit}</span>
                            <span class="text-sm font-semibold text-blue-600">${new Intl.NumberFormat('vi-VN').format(product.unit_price)} VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    contentDiv.innerHTML = html;
}
</script>
@endpush

<!-- Modal HTML -->
<div id="supplierModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
            onclick="closeModal()"></div>
        <!-- Modal panel -->
        <div
            class="relative bg-white rounded-xl shadow-2xl transform transition-all max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div
                class="sticky top-0 bg-white z-10 flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                        <span>Quản lý nhà cung cấp</span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                        <span>Chi tiết nhà cung cấp</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Thêm nhà cung cấp</h3>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="submitBtnHeader" 
                            onclick="document.getElementById('supplierForm').dispatchEvent(new Event('submit'))" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 text-sm transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                    <button onclick="closeModal()" class="ml-2 text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <form id="supplierForm" class="p-6 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Thông tin cơ bản
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Mã nhà cung cấp</label>
                            <input type="text" name="supplier_code" id="supplier_code" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 font-medium"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Tên nhà cung cấp</label>
                            <input type="text" name="supplier_name" id="supplier_name" placeholder="Nhập tên nhà cung cấp..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-address-book text-blue-600"></i>
                        Thông tin liên hệ
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Người liên hệ</label>
                            <input type="text" name="contact_person" id="contact_person" placeholder="Nguyễn Văn A"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Số điện thoại</label>
                            <input type="text" name="phone_number" id="phone_number" placeholder="0123456789"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Email</label>
                            <input type="email" name="email" id="email" placeholder="example@company.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Địa chỉ</label>
                            <textarea name="address" id="address" rows="2" placeholder="Nhập địa chỉ đầy đủ..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-blue-600"></i>
                        Ghi chú
                    </h4>
                    <textarea name="note" id="note" rows="3" placeholder="Thêm ghi chú về nhà cung cấp..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Footer Info -->
                <div id="lastUpdatedInfo" class="hidden text-xs text-gray-500 pt-4 border-t border-gray-200">
                    Lần cập nhật cuối: 10/01/2026 bởi Admin
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
