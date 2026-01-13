@extends('layouts.department')

@section('title', 'Đề xuất sản phẩm')
@section('header_title', 'Đề xuất sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Danh sách đề xuất</h2>
            <a href="{{ route('department.proposals.create') }}"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                <i class="fas fa-plus mr-2"></i> Tạo đề xuất mới
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mô tả</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $proposal->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($proposal->primaryImage)
                                        <img src="{{ asset($proposal->primaryImage->file_path) }}"
                                            alt="{{ $proposal->product_name }}" class="w-12 h-12 rounded-lg object-cover border">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <span class="text-sm text-gray-900 font-medium">{{ $proposal->product_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($proposal->description, 50) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full {{ get_status_class($proposal->status) }}">
                                    {{ get_status_label($proposal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="showDetail({{ $proposal->id }})"
                                    class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-eye mr-1"></i> Chi tiết
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>Chưa có đề xuất nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($proposals->hasPages())
            <div class="mt-6">
                {{ $proposals->links() }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Chi tiết đề xuất</h3>
                <button onclick="closeDetail()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1" id="detailContent">
                <div class="flex justify-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(id) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailContent');

            modal.classList.remove('hidden');
            content.innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i></div>';

            fetch(`/department/proposals/${id}`)
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = `
                            <div class="space-y-4">
                                ${data.image ? `
                                <div class="flex justify-center mb-4">
                                    <img src="/images/products/${data.image}" alt="${data.product_name}" class="w-48 h-48 object-cover rounded-lg border">
                                </div>
                                ` : ''}
                                <div><strong>Tên sản phẩm:</strong> ${data.product_name}</div>
                                <div><strong>Mô tả:</strong> ${data.description || 'Chưa có'}</div>
                                <div><strong>Mã sản phẩm:</strong> ${data.product_code || 'Chưa có'}</div>
                                <div><strong>Danh mục:</strong> ${data.category ? data.category.category_name : 'Chưa có'}</div>
                                <div><strong>Đơn vị:</strong> ${data.unit || 'Chưa có'}</div>
                                <div><strong>Giá:</strong> ${data.unit_price ? new Intl.NumberFormat('vi-VN').format(data.unit_price) + ' đ' : 'Chưa có'}</div>
                                <div><strong>Nhà cung cấp:</strong> ${data.supplier ? data.supplier.supplier_name : 'Chưa có'}</div>
                                <div><strong>Trạng thái:</strong> <span class="px-3 py-1 text-xs font-semibold rounded-full ${data.status_class}">${data.status_label}</span></div>
                                ${data.rejection_reason ? `<div class="bg-red-50 border border-red-200 rounded-lg p-4"><strong class="text-red-700">Lý do từ chối:</strong><p class="text-red-600 mt-1">${data.rejection_reason}</p></div>` : ''}
                            </div>
                        `;
                })
                .catch(err => {
                    content.innerHTML = '<p class="text-red-500 text-center">Không thể tải chi tiết</p>';
                });
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
@endsection