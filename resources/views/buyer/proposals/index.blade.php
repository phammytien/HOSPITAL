@extends('layouts.buyer')

@section('title', 'Quản lý đề xuất')
@section('header_title', 'Quản lý đề xuất sản phẩm')

@section('content')
    <div class="space-y-6">
        <!-- New Tab Style -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8" aria-label="Tabs">
                <a href="{{ route('buyer.proposals.index', ['status' => 'all']) }}"
                    class="py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-all {{ (!$status || $status === 'all') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-bars text-xs"></i>
                    Tất cả
                    <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] {{ (!$status || $status === 'all') ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $counts['all'] }}
                    </span>
                </a>

                <a href="{{ route('buyer.proposals.index', ['status' => 'PENDING']) }}"
                    class="py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-all {{ $status === 'PENDING' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="far fa-clock text-xs"></i>
                    Chờ xử lý
                    <div class="relative ml-1">
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'PENDING' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['PENDING'] }}
                        </span>
                        @if($counts['PENDING'] > 0)
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                            </span>
                        @endif
                    </div>
                </a>

                <a href="{{ route('buyer.proposals.index', ['status' => 'APPROVED']) }}"
                    class="py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-all {{ $status === 'APPROVED' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="far fa-check-circle text-xs"></i>
                    Đã duyệt
                    <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] {{ $status === 'APPROVED' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $counts['APPROVED'] }}
                    </span>
                </a>

                <a href="{{ route('buyer.proposals.index', ['status' => 'REJECTED']) }}"
                    class="py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 transition-all {{ $status === 'REJECTED' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="far fa-times-circle text-xs"></i>
                    Đã từ chối
                    <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] {{ $status === 'REJECTED' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $counts['REJECTED'] }}
                    </span>
                </a>
            </nav>
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
                                    <div class="flex justify-center">
                                        <a href="{{ route('buyer.proposals.edit', $proposal->id) }}"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-edit mr-2"></i> Tiếp nhận & Xử lý
                                        </a>
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
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-[480px] mx-4 overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Chi tiết đề xuất</h3>
                <button onclick="closeProposalDetail()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Status Row -->
                <div class="flex justify-between items-start">
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Mã đề xuất</label>
                        <p class="font-semibold text-gray-900 text-base" id="modal_code"></p>
                    </div>
                    <div class="text-right">
                        <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider block mb-1">Trạng thái</label>
                        <span id="modal_status_badge" class="px-3 py-1 text-xs font-bold rounded-full"></span>
                    </div>
                </div>

                <!-- Product Image -->
                <div id="modal_image_container"
                    class="hidden flex justify-center py-2 h-32 bg-gray-50/50 border border-gray-100 rounded-xl items-center overflow-hidden">
                    <img id="modal_product_image" src="" alt="Sản phẩm"
                        class="max-h-full max-w-full object-contain transform hover:scale-105 transition-transform duration-500">
                </div>

                <!-- Product Info Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Tên sản phẩm</label>
                        <p class="font-semibold text-gray-900 text-base mt-1" id="modal_product_name"></p>
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Khoa đề xuất</label>
                        <p class="text-gray-700 font-medium text-sm mt-1" id="modal_department"></p>
                    </div>
                </div>

                <!-- Category & Price Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Danh mục</label>
                        <p class="text-gray-700 font-medium text-sm mt-1" id="modal_category"></p>
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Giá dự kiến</label>
                        <p class="text-blue-600 font-bold text-base mt-1" id="modal_price"></p>
                    </div>
                </div>

                <!-- Dates Row -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="far fa-paper-plane text-[9px] text-gray-400"></i>
                            <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Ngày gửi</label>
                        </div>
                        <p class="text-gray-700 font-medium text-sm" id="modal_created_at"></p>
                    </div>
                    <div id="modal_approved_at_container">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="far fa-check-circle text-[9px] text-gray-400"></i>
                            <label class="text-[10px] text-gray-500 uppercase font-semibold tracking-wider">Ngày duyệt</label>
                        </div>
                        <p class="text-gray-700 font-medium text-sm" id="modal_updated_at"></p>
                    </div>
                </div>

                <!-- Rejection Reason Section -->
                <div id="modal_rejection_section" class="hidden p-3 bg-red-50/50 rounded-xl border border-red-100/50">
                    <label class="text-[10px] text-red-600 uppercase font-bold tracking-wider flex items-center gap-2 mb-1">
                        <i class="fas fa-exclamation-circle"></i> Lý do từ chối
                    </label>
                    <p class="text-red-700 text-sm font-bold leading-relaxed" id="modal_rejection_reason"></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-100 text-right">
                <button onclick="closeProposalDetail()"
                    class="px-6 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-bold text-[10px] uppercase tracking-widest transition-all shadow-sm">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function openProposalDetail(proposal) {
            // Populate basic info
            document.getElementById('modal_code').innerText = '#' + proposal.id;
            document.getElementById('modal_product_name').innerText = proposal.product_name;
            document.getElementById('modal_category').innerText = proposal.category ? proposal.category.category_name : '-';
            document.getElementById('modal_department').innerText = proposal.department ? proposal.department.department_name : '-';
            
            // Dates
            document.getElementById('modal_created_at').innerText = formatDate(proposal.created_at);
            
            const approvedContainer = document.getElementById('modal_approved_at_container');
            if (proposal.status === 'APPROVED' || proposal.status === 'REJECTED') {
                document.getElementById('modal_updated_at').innerText = formatDate(proposal.updated_at);
                approvedContainer.classList.remove('invisible');
                const label = approvedContainer.querySelector('label');
                label.innerText = proposal.status === 'APPROVED' ? 'Ngày duyệt đề xuất' : 'Ngày từ chối';
            } else {
                approvedContainer.classList.add('invisible');
            }

            // Image Logic
            const imageContainer = document.getElementById('modal_image_container');
            const productImage = document.getElementById('modal_product_image');

            if (proposal.primary_image && proposal.primary_image.file_path) {
                productImage.src = '/' + proposal.primary_image.file_path;
                productImage.classList.remove('hidden');
                
                // Remove placeholder if present (cleanup)
                const placeholder = imageContainer.querySelector('span');
                if (placeholder) placeholder.remove();

                imageContainer.classList.remove('hidden');
                imageContainer.classList.add('flex');
            } else {
                // HIDE CONTAINER if no image
                imageContainer.classList.add('hidden');
                imageContainer.classList.remove('flex');
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