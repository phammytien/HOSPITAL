@extends('layouts.buyer')

@section('title', 'Quản lý đề xuất')
@section('header_title', 'Quản lý đề xuất sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- Header & Filter -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Danh sách đề xuất</h2>

            <!-- Status Filter -->
            <form method="GET" class="flex items-center space-x-3">
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="CREATED" {{ request('status') == 'CREATED' ? 'selected' : '' }}>Mới tạo</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tên sản phẩm</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Khoa đề xuất</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Danh mục</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Giá</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $proposal->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $proposal->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->department->department_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->category->category_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                {{ $proposal->unit_price ? number_format($proposal->unit_price, 0, ',', '.') . ' đ' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full {{ get_status_class($proposal->status) }}">
                                    {{ get_status_label($proposal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($proposal->status == 'PENDING')
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('buyer.proposals.edit', $proposal->id) }}"
                                            class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-edit mr-1"></i> Sửa
                                        </a>
                                        <form action="{{ route('buyer.proposals.submit', $proposal->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Bạn có chắc muốn gửi đề xuất này lên Admin để duyệt?')"
                                                class="px-4 py-2 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition">
                                                <i class="fas fa-paper-plane mr-1"></i> Gửi duyệt
                                            </button>
                                        </form>
                                    </div>
                                @elseif($proposal->status == 'CREATED')
                                    <span class="text-xs text-gray-500">Chờ Admin duyệt</span>
                                @elseif($proposal->status == 'APPROVED')
                                    <a href="javascript:void(0)" onclick='openProposalDetail(@json($proposal))'
                                        class="text-xs text-green-600 hover:underline">
                                        <i class="fas fa-check-circle mr-1"></i> Xem sản phẩm
                                    </a>
                                @elseif($proposal->status == 'REJECTED')
                                    <span class="text-xs text-red-600 cursor-pointer hover:underline"
                                        onclick='openProposalDetail(@json($proposal))' title="Xem lý do từ chối">
                                        <i class="fas fa-times-circle mr-1"></i> Đã từ chối
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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
    <!-- Proposal Detail Modal -->
    <div id="proposalDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Chi tiết đề xuất</h3>
                <button onclick="closeProposalDetail()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Mã đề xuất</label>
                        <p class="font-medium text-gray-900" id="modal_code"></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Trạng thái</label>
                        <span id="modal_status_badge" class="px-2 py-1 text-xs font-semibold rounded-full"></span>
                    </div>
                </div>

                <!-- Product Image -->
                <div id="modal_image_container" class="hidden flex justify-center py-2">
                    <img id="modal_product_image" src="" alt="Sản phẩm"
                        class="max-h-48 rounded-lg border border-gray-200 object-cover">
                </div>

                <!-- Product Image -->
                <div id="modal_image_container"
                    class="hidden flex justify-center py-2 h-48 bg-gray-50 rounded-lg mb-3 items-center">
                    <img id="modal_product_image" src="" alt="Sản phẩm"
                        class="max-h-full max-w-full object-contain rounded-lg">
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Tên sản phẩm</label>
                    <p class="font-medium text-gray-900 text-lg" id="modal_product_name"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Danh mục</label>
                        <p class="text-gray-700" id="modal_category"></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Giá dự kiến</label>
                        <p class="text-gray-700" id="modal_price"></p>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Khoa đề xuất</label>
                    <p class="text-gray-700" id="modal_department"></p>
                </div>

                <!-- Rejection Reason Section -->
                <div id="modal_rejection_section" class="hidden mt-4 p-3 bg-red-50 rounded-lg border border-red-100">
                    <label class="text-xs text-red-600 uppercase font-bold flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> Lý do từ chối
                    </label>
                    <p class="text-red-700 text-sm mt-1" id="modal_rejection_reason"></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-right">
                <button onclick="closeProposalDetail()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium text-sm transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
        function openProposalDetail(proposal) {
            // Populate basic info
            document.getElementById('modal_code').innerText = '#' + proposal.id;
            document.getElementById('modal_product_name').innerText = proposal.product_name;
            document.getElementById('modal_category').innerText = proposal.category ? proposal.category.category_name : '-';
            document.getElementById('modal_department').innerText = proposal.department ? proposal.department.department_name : '-';

            // Image Logic
            const imageContainer = document.getElementById('modal_image_container');
            const productImage = document.getElementById('modal_product_image');

            // Check if we have an image
            if (proposal.primary_image && proposal.primary_image.file_path) {
                // User stores in public/products, database path is 'products/filename'
                // Direct access from public root
                productImage.src = '/' + proposal.primary_image.file_path;
                productImage.classList.remove('hidden');

                // Remove any existing text placeholder if I added one previously?
                // Or cleaner: reset container content
                // But I have an img tag there. Let's just handle it via visibility/innerHTML if needed.
                // Simplest: 
                // Clear container innerHTML
                imageContainer.innerHTML = '';
                // Append IMG
                const img = document.createElement('img');
                img.src = '/' + proposal.primary_image.file_path;
                img.alt = 'Sản phẩm';
                img.className = 'max-h-full max-w-full object-contain rounded-lg';
                imageContainer.appendChild(img);

                imageContainer.classList.remove('hidden');
                imageContainer.classList.add('flex');
            } else {
                // No image -> Show text placeholder
                imageContainer.innerHTML = '<span class="text-gray-400 font-medium italic">Không có ảnh sản phẩm</span>';
                imageContainer.classList.remove('hidden'); // Always show container
                imageContainer.classList.add('flex');
            }

            // Format Price
            const price = proposal.unit_price ? new Intl.NumberFormat('vi-VN').format(proposal.unit_price) + ' đ' : '-';
            document.getElementById('modal_price').innerText = price;

            // Status Badge
            const statusBadge = document.getElementById('modal_status_badge');
            // Reset classes
            statusBadge.className = 'px-2 py-1 text-xs font-semibold rounded-full';

            let statusLabel = proposal.status;
            let statusClass = 'bg-gray-100 text-gray-700';

            // Simple map for status class/label (or pass from backend helper if complex)
            if (proposal.status === 'PENDING') { statusLabel = 'Chờ xử lý'; statusClass = 'bg-yellow-100 text-yellow-700'; }
            if (proposal.status === 'CREATED') { statusLabel = 'Mới tạo'; statusClass = 'bg-blue-100 text-blue-700'; }
            if (proposal.status === 'APPROVED') { statusLabel = 'Đã duyệt'; statusClass = 'bg-green-100 text-green-700'; }
            if (proposal.status === 'REJECTED') { statusLabel = 'Đã từ chối'; statusClass = 'bg-red-100 text-red-700'; }

            statusBadge.innerText = statusLabel;
            statusBadge.classList.add(...statusClass.split(' '));

            // Rejection Logic
            const rejectSection = document.getElementById('modal_rejection_section');
            if (proposal.status === 'REJECTED' && proposal.rejection_reason) {
                document.getElementById('modal_rejection_reason').innerText = proposal.rejection_reason;
                rejectSection.classList.remove('hidden');
            } else {
                rejectSection.classList.add('hidden');
            }

            // Show Modal
            document.getElementById('proposalDetailModal').classList.remove('hidden');
        }

        function closeProposalDetail() {
            document.getElementById('proposalDetailModal').classList.add('hidden');
        }

        // Close on click outside
        window.onclick = function (event) {
            const modal = document.getElementById('proposalDetailModal');
            if (event.target == modal) {
                closeProposalDetail();
            }
        }
    </script>
@endsection