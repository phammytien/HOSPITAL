@extends('layouts.admin')

@section('title', 'Duyệt đề xuất')
@section('header_title', 'Duyệt đề xuất sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Đề xuất chờ duyệt</h2>
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i> Tổng: <span class="font-bold">{{ $proposals->total() }}</span> đề
                xuất
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tên sản phẩm</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Khoa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Buyer xử lý</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Danh mục</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">NCC</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Giá</th>
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
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $proposal->product_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $proposal->product_code ?? 'Chưa có mã' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->department->department_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->buyer->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->category->category_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->supplier->supplier_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                {{ number_format($proposal->unit_price, 0, ',', '.') }} đ
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openApproveModal({{ $proposal->id }}, '{{ $proposal->product_name }}')"
                                        class="px-4 py-2 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-check mr-1"></i> Duyệt
                                    </button>
                                    <button onclick="openRejectModal({{ $proposal->id }}, '{{ $proposal->product_name }}')"
                                        class="px-4 py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition">
                                        <i class="fas fa-times mr-1"></i> Từ chối
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>Không có đề xuất nào chờ duyệt</p>
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

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeApproveModal()"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Xác nhận duyệt đề xuất</h3>
                <p class="text-gray-600 mb-6">
                    Bạn có chắc muốn duyệt đề xuất "<span id="approveProductName" class="font-bold"></span>"?
                    <br><br>
                    Sản phẩm sẽ được tạo và thêm vào danh sách vật tư.
                </p>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Hủy
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i> Duyệt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeRejectModal()"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Từ chối đề xuất</h3>
                <p class="text-gray-600 mb-4">
                    Đề xuất: "<span id="rejectProductName" class="font-bold"></span>"
                </p>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Lý do từ chối <span class="text-red-500">*</span>
                        </label>
                        <textarea name="rejection_reason" required rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Nhập lý do từ chối đề xuất này..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Hủy
                        </button>
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-times mr-2"></i> Từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openApproveModal(id, name) {
            document.getElementById('approveProductName').textContent = name;
            document.getElementById('approveForm').action = `/admin/proposals/${id}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal(id, name) {
            document.getElementById('rejectProductName').textContent = name;
            document.getElementById('rejectForm').action = `/admin/proposals/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection