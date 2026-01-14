@extends('layouts.buyer')

@section('title', 'Cập nhật đề xuất')
@section('header_title', 'Cập nhật đề xuất sản phẩm')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('buyer.proposals.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Cập nhật đề xuất #{{ $proposal->id }}</h2>
        </div>

        <!-- Proposal Info (Read-only) -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="font-bold text-blue-900 mb-3">Thông tin đề xuất từ Khoa</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-blue-700 font-medium">Khoa đề xuất:</span>
                    <span class="text-blue-900 ml-2">{{ $proposal->department->department_name }}</span>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">Người tạo:</span>
                    <span class="text-blue-900 ml-2">{{ $proposal->createdBy->full_name }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-blue-700 font-medium">Tên sản phẩm:</span>
                    <span class="text-blue-900 ml-2 font-bold">{{ $proposal->product_name }}</span>
                </div>
                @if($proposal->description)
                    <div class="col-span-2">
                        <span class="text-blue-700 font-medium">Mô tả:</span>
                        <p class="text-blue-900 mt-1">{{ $proposal->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h3 class="font-bold text-white text-xl flex items-center">
                <i class="fas fa-edit mr-3"></i> Bổ sung thông tin chi tiết
            </h3>
            <p class="text-blue-100 text-sm mt-1">Vui lòng điền đầy đủ thông tin để gửi Admin duyệt</p>
        </div>
        
        <form action="{{ route('buyer.proposals.update', $proposal->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Thông tin cơ bản -->
            <div class="mb-8">
                <h4 class="font-bold text-gray-900 mb-4 pb-2 border-b-2 border-blue-500 inline-block text-lg">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i> Thông tin cơ bản
                </h4>
                
                <div class="grid grid-cols-2 gap-6 mt-6">
                    <!-- Category (triggers auto product code) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Danh mục <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required onchange="generateProductCode()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    data-code="{{ $category->category_code }}"
                                    {{ old('category_id', $proposal->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Auto-generated Product Code -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mã sản phẩm <span class="text-gray-400 text-xs">(Tự động)</span>
                        </label>
                        <input type="text" name="product_code" id="product_code" readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-mono"
                               value="{{ old('product_code', $proposal->product_code) }}"
                               placeholder="Chọn danh mục để tạo mã">
                        @error('product_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Đơn vị tính <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="hidden" name="unit" id="unitInput" value="{{ old('unit', $proposal->unit) }}">
                            
                            <button type="button" id="unitButton"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    onclick="toggleUnitDropdown()">
                                <span id="unitButtonText" class="{{ old('unit', $proposal->unit) ? 'text-gray-900' : 'text-gray-500' }}">
                                    {{ old('unit', $proposal->unit) ?: '-- Chọn đơn vị --' }}
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="unitChevron"></i>
                            </button>
                        
                            <div id="unitDropdown" class="hidden absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                @php
                                    $units = ['Hộp', 'Viên', 'Vỉ', 'Lọ', 'Chai', 'Ống', 'Gói', 'Cái', 'Chiếc', 'Kg', 'Gam', 'Lít', 'Ml', 'Cuộn', 'Bộ', 'Đôi', 'Thùng', 'Ram'];
                                @endphp
                                @foreach($units as $u)
                                <div class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-gray-700 border-b border-gray-50 last:border-0 transition-colors duration-150"
                                     onclick="selectUnit('{{ $u }}')">
                                    {{ $u }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Giá dự kiến <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="unit_price" required step="0.01" min="0"
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Nhập giá sản phẩm"
                                   value="{{ old('unit_price', $proposal->unit_price) }}">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">đ</span>
                        </div>
                        @error('unit_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Nhà cung cấp -->
            <div class="mb-8">
                <h4 class="font-bold text-gray-900 mb-4 pb-2 border-b-2 border-green-500 inline-block text-lg">
                    <i class="fas fa-truck text-green-600 mr-2"></i> Nhà cung cấp
                </h4>
                
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Chọn nhà cung cấp <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">-- Chọn nhà cung cấp --</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $proposal->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 3: Hình ảnh sản phẩm -->
            <div class="mb-8">
                <h4 class="font-bold text-gray-900 mb-4 pb-2 border-b-2 border-purple-500 inline-block text-lg">
                    <i class="fas fa-image text-purple-600 mr-2"></i> Hình ảnh sản phẩm
                </h4>
                
                <div class="mt-6">
                    <div class="flex items-start space-x-6">
                        <!-- Image Preview -->
                        <div class="flex-shrink-0">
                            <div id="imagePreviewContainer" class="w-48 h-48 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                                @if($proposal->primaryImage)
                                <img id="imagePreview" src="{{ asset($proposal->primaryImage->file_path) }}" alt="Preview" class="w-full h-full object-cover">
                                @else
                                <div id="imagePlaceholder" class="text-center p-4">
                                    <i class="fas fa-image text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-xs text-gray-400">Chưa có ảnh</p>
                                </div>
                                @endif
                            </div>
                            @if($proposal->primaryImage)
                            <p class="text-xs text-gray-500 mt-2 text-center">Ảnh hiện tại</p>
                            @endif
                        </div>

                        <!-- Upload Input -->
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tải ảnh lên hoặc dán URL
                            </label>
                            <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/jpg"
                                   onchange="previewImage(event)"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Chấp nhận: JPG, PNG. Tối đa 2MB. Kích thước đề xuất: 800x800px
                            </p>
                            @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <a href="{{ route('buyer.proposals.index') }}"
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Quay lại
                    </a>
                    <button type="button" onclick="openRejectModal()"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition shadow-md hover:shadow-lg flex items-center">
                        <i class="fas fa-times-circle mr-2"></i> Từ chối
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button type="submit"
                            class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-md hover:shadow-lg flex items-center">
                        <i class="fas fa-save mr-2"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
            <h3 class="font-bold text-red-900 text-lg">Từ chối đề xuất</h3>
        </div>
        <form action="{{ route('buyer.proposals.reject', $proposal->id) }}" method="POST" class="p-6">
            @csrf
            <p class="text-gray-700 mb-4">
                Bạn có chắc muốn từ chối đề xuất "<strong>{{ $proposal->product_name }}</strong>"?
            </p>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Lý do từ chối <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" required rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="VD: Không có nhà cung cấp, Giá quá cao, Không cần thiết..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-times-circle mr-2"></i> Từ chối
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-generate product code based on category
function generateProductCode() {
    const categorySelect = document.getElementById('category_id');
    const productCodeInput = document.getElementById('product_code');
    const categoryId = categorySelect.value;
    const proposalId = '{{ $proposal->id }}';
    
    if (categoryId) {
        // Show loading state
        productCodeInput.setAttribute('placeholder', 'Đang tạo mã...');
        
        fetch(`{{ route('buyer.proposals.generate-code') }}?category_id=${categoryId}&proposal_id=${proposalId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    productCodeInput.value = data.code;
                } else {
                    console.error(data.message);
                    productCodeInput.value = '';
                    productCodeInput.setAttribute('placeholder', 'Lỗi tạo mã');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                productCodeInput.value = '';
                productCodeInput.setAttribute('placeholder', 'Lỗi kết nối');
            });
    } else {
        productCodeInput.value = '';
        productCodeInput.setAttribute('placeholder', 'Chọn danh mục để tạo mã');
    }
}

// Preview image when file is selected
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const container = document.getElementById('imagePreviewContainer');
            
            if (preview) {
                preview.src = e.target.result;
            } else {
                if (placeholder) placeholder.remove();
                const img = document.createElement('img');
                img.id = 'imagePreview';
                img.src = e.target.result;
                img.className = 'w-full h-full object-cover';
                container.appendChild(img);
            }
        }
        reader.readAsDataURL(file);
    }
}

// Generate code on page load if category is already selected
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const productCodeInput = document.getElementById('product_code');
    
    // Only generate if category is selected but code is empty
    if (categorySelect.value && !productCodeInput.value) {
        generateProductCode();
    }
});

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Custom Dropdown Logic
function toggleUnitDropdown() {
    const dropdown = document.getElementById('unitDropdown');
    const chevron = document.getElementById('unitChevron');
    dropdown.classList.toggle('hidden');
    
    if (!dropdown.classList.contains('hidden')) {
        chevron.style.transform = 'rotate(180deg)';
    } else {
        chevron.style.transform = 'rotate(0deg)';
    }
}

function selectUnit(value) {
    document.getElementById('unitInput').value = value;
    const btnText = document.getElementById('unitButtonText');
    btnText.innerText = value;
    btnText.className = 'text-gray-900';
    
    // Close dropdown
    toggleUnitDropdown();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('unitDropdown');
    const button = document.getElementById('unitButton');
    
    if (dropdown && button && !button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
        document.getElementById('unitChevron').style.transform = 'rotate(0deg)';
    }
});
    </script>
@endsection