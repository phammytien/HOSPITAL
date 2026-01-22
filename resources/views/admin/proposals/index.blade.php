@extends('layouts.admin')

@section('title', 'Duyệt đề xuất')
@section('header_title', 'Duyệt đề xuất sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <!-- <h2 class="text-2xl font-bold text-gray-900">Đề xuất sản phẩm</h2> -->
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
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                        <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="openDetailModal({{ $proposal->id }})">
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
                                <div class="flex justify-center">
                                    @if($proposal->status === 'CREATED')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> Chờ duyệt
                                        </span>
                                    @elseif($proposal->status === 'APPROVED')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Đã duyệt
                                        </span>
                                    @elseif($proposal->status === 'REJECTED')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Đã từ chối
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>Không có đề xuất nào</p>
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
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeDetailModal()"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-3xl w-full p-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Chi tiết đề xuất</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Loading State -->
                <div id="detailLoading" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-3"></i>
                    <p class="text-gray-600">Đang tải...</p>
                </div>

                <!-- Content -->
                <div id="detailContent" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Product Image -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hình ảnh sản phẩm</label>
                                <div id="detailImage" class="w-full h-64 rounded-lg bg-gray-100 flex items-center justify-center border">
                                    <i class="fas fa-box text-4xl text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div id="detailStatusBadge"></div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Mã đề xuất</label>
                                <p id="detailId" class="text-sm font-medium text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Tên sản phẩm</label>
                                <p id="detailName" class="text-sm font-medium text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Mã sản phẩm</label>
                                <p id="detailCode" class="text-sm text-gray-900"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Khoa</label>
                                    <p id="detailDepartment" class="text-sm text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Buyer xử lý</label>
                                    <p id="detailBuyer" class="text-sm text-gray-900"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Danh mục</label>
                                    <p id="detailCategory" class="text-sm text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Nhà cung cấp</label>
                                    <p id="detailSupplier" class="text-sm text-gray-900"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Đơn vị</label>
                                    <p id="detailUnit" class="text-sm text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Giá</label>
                                    <p id="detailPrice" class="text-sm font-bold text-gray-900"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Mô tả</label>
                                <p id="detailDescription" class="text-sm text-gray-700"></p>
                            </div>
                            
                            <!-- Rejection Reason (if rejected) -->
                            <div id="detailRejectionReason" class="hidden">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Lý do từ chối</label>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <p id="detailRejectionText" class="text-sm text-red-800"></p>
                                </div>
                            </div>

                            <!-- Approver Info (if processed) -->
                            <div id="detailApprover" class="hidden">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Người xử lý</label>
                                <p id="detailApproverName" class="text-sm text-gray-900"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons (only for CREATED status) -->
                    <div id="detailActions" class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                        <button onclick="closeDetailModal()"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Đóng
                        </button>
                        <button id="btnReject" onclick="openRejectModalFromDetail()"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i> Từ chối
                        </button>
                        <button id="btnApprove" onclick="openApproveModalFromDetail()"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i> Duyệt
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
        let currentProposal = null;

        // Open detail modal
        function openDetailModal(id) {
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailLoading').classList.remove('hidden');
            document.getElementById('detailContent').classList.add('hidden');

            // Fetch proposal details
            fetch(`/admin/proposals/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentProposal = data.proposal;
                        displayProposalDetails(data.proposal);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tải thông tin đề xuất.');
                    closeDetailModal();
                });
        }

        // Display proposal details in modal
        function displayProposalDetails(proposal) {
            // Hide loading, show content
            document.getElementById('detailLoading').classList.add('hidden');
            document.getElementById('detailContent').classList.remove('hidden');

            // Fill in the data
            document.getElementById('detailId').textContent = '#' + proposal.id;
            document.getElementById('detailName').textContent = proposal.product_name;
            document.getElementById('detailCode').textContent = proposal.product_code || 'Chưa có mã';
            document.getElementById('detailDepartment').textContent = proposal.department?.department_name || 'N/A';
            document.getElementById('detailBuyer').textContent = proposal.buyer?.full_name || 'N/A';
            document.getElementById('detailCategory').textContent = proposal.category?.category_name || 'N/A';
            document.getElementById('detailSupplier').textContent = proposal.supplier?.supplier_name || 'N/A';
            document.getElementById('detailUnit').textContent = proposal.unit || 'N/A';
            document.getElementById('detailPrice').textContent = new Intl.NumberFormat('vi-VN').format(proposal.unit_price) + ' đ';
            document.getElementById('detailDescription').textContent = proposal.description || 'Không có mô tả';

            // Image
            const imageContainer = document.getElementById('detailImage');
            if (proposal.primary_image && proposal.primary_image.file_path) {
                imageContainer.innerHTML = `<img src="/${proposal.primary_image.file_path}" alt="${proposal.product_name}" class="w-full h-full object-cover rounded-lg">`;
            } else {
                imageContainer.innerHTML = '<i class="fas fa-box text-4xl text-gray-400"></i>';
            }

            // Status badge
            const statusBadge = document.getElementById('detailStatusBadge');
            if (proposal.status === 'CREATED') {
                statusBadge.innerHTML = `
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-2xl text-yellow-600 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-yellow-900">Chờ duyệt</h4>
                                <p class="text-sm text-yellow-700">Đề xuất đang chờ được xử lý</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (proposal.status === 'APPROVED') {
                statusBadge.innerHTML = `
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl text-green-600 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-green-900">Đã duyệt</h4>
                                <p class="text-sm text-green-700">Sản phẩm đã được tạo thành công</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (proposal.status === 'REJECTED') {
                statusBadge.innerHTML = `
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-2xl text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-red-900">Đã từ chối</h4>
                                <p class="text-sm text-red-700">Đề xuất không được chấp nhận</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Show/hide rejection reason
            if (proposal.status === 'REJECTED' && proposal.rejection_reason) {
                document.getElementById('detailRejectionReason').classList.remove('hidden');
                document.getElementById('detailRejectionText').textContent = proposal.rejection_reason;
            } else {
                document.getElementById('detailRejectionReason').classList.add('hidden');
            }

            // Show/hide approver
            if ((proposal.status === 'APPROVED' || proposal.status === 'REJECTED') && proposal.approver) {
                document.getElementById('detailApprover').classList.remove('hidden');
                document.getElementById('detailApproverName').textContent = proposal.approver.full_name;
            } else {
                document.getElementById('detailApprover').classList.add('hidden');
            }

            // Show/hide action buttons
            const actionsDiv = document.getElementById('detailActions');
            if (proposal.status === 'CREATED') {
                document.getElementById('btnApprove').classList.remove('hidden');
                document.getElementById('btnReject').classList.remove('hidden');
            } else {
                document.getElementById('btnApprove').classList.add('hidden');
                document.getElementById('btnReject').classList.add('hidden');
            }
        }

        // Close detail modal
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentProposal = null;
        }

        // Open approve modal from detail
        function openApproveModalFromDetail() {
            if (!currentProposal) return;
            
            document.getElementById('approveProductName').textContent = currentProposal.product_name;
            document.getElementById('approveForm').action = `/admin/proposals/${currentProposal.id}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        // Close approve modal
        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        // Open reject modal from detail
        function openRejectModalFromDetail() {
            if (!currentProposal) return;
            
            document.getElementById('rejectProductName').textContent = currentProposal.product_name;
            document.getElementById('rejectForm').action = `/admin/proposals/${currentProposal.id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        // Close reject modal
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Handle approve form submission
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                closeApproveModal();
                
                // Reload the proposal details
                if (currentProposal) {
                    openDetailModal(currentProposal.id);
                }
                
                // Show success message
                alert('Đã duyệt đề xuất và tạo sản phẩm mới thành công!');
                
                // Reload page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi duyệt đề xuất.');
            });
        });

        // Handle reject form submission
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                closeRejectModal();
                
                // Reload the proposal details
                if (currentProposal) {
                    openDetailModal(currentProposal.id);
                }
                
                // Show success message
                alert('Đã từ chối đề xuất!');
                
                // Reload page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi từ chối đề xuất.');
            });
        });
    </script>
@endsection