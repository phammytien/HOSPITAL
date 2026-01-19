@extends('layouts.department')

@section('title', $pageTitle ?? 'Yêu cầu mua hàng')
@section('header_title', $pageTitle ?? 'Yêu cầu mua hàng')
@section('page-subtitle', 'Danh sách yêu cầu mua sắm của khoa bạn')

@section('content')
    <div class="space-y-6">
        <!-- Filter & Actions -->
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    @php
                        $baseRoute = isset($activeTab) && $activeTab == 'history' ? 'department.requests.history' : 'department.requests.index';
                    @endphp

                    <a href="{{ route($baseRoute) }}"
                        class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Tất cả
                    </a>

                    @if(isset($activeTab) && $activeTab == 'history')
                        {{-- Tabs for History --}}
                        <a href="{{ route($baseRoute, ['status' => 'COMPLETED']) }}"
                            class="px-4 py-2 rounded-lg {{ request('status') == 'COMPLETED' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Hoàn thành
                        </a>
                        <a href="{{ route($baseRoute, ['status' => 'CANCELLED']) }}"
                            class="px-4 py-2 rounded-lg {{ request('status') == 'CANCELLED' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Đã hủy
                        </a>
                        <a href="{{ route($baseRoute, ['status' => 'REJECTED']) }}"
                            class="px-4 py-2 rounded-lg {{ request('status') == 'REJECTED' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Đã từ chối
                        </a>
                    @else
                        {{-- Tabs for Active Requests --}}
                        <a href="{{ route($baseRoute, ['status' => 'DRAFT']) }}"
                            class="px-4 py-2 rounded-lg {{ request('status') == 'DRAFT' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Bản nháp
                        </a>
                        <a href="{{ route($baseRoute, ['status' => 'SUBMITTED']) }}"
                            class="px-4 py-2 rounded-lg {{ request('status') == 'SUBMITTED' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Chờ duyệt
                        </a>
                        <a href="{{ route($baseRoute, ['status' => 'APPROVED']) }}"
                            class="px-4 py-2 rounded-lg {{ request('status') == 'APPROVED' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Đã duyệt
                        </a>
                    @endif
                </div>

                <!-- Create Button (Only for Active View) -->
                @if(!isset($activeTab) || $activeTab != 'history')
                    <a href="{{ route('department.requests.create') }}"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition flex items-center justify-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tạo yêu cầu mới</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã yêu cầu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Khoa yêu cầu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Đơn vị tiền</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Thời gian</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($requests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <span
                                                class="text-blue-600 font-bold text-sm">#{{ substr($request->request_code, -3) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $request->request_code }}</p>
                                            <p class="text-xs text-gray-500">{{ $request->period }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-hospital text-blue-600"></i>
                                        <span class="text-gray-900">{{ $request->department->department_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        @php
                                            $firstProduct = $request->items->first();
                                        @endphp
                                        @if($firstProduct)
                                            <p class="font-medium text-gray-900">{{ $firstProduct->product->product_name ?? 'N/A' }}
                                            </p>
                                            @if($request->items->count() > 1)
                                                <p class="text-xs text-gray-500">+{{ $request->items->count() - 1 }} sản phẩm khác</p>
                                            @endif
                                        @else
                                            <p class="text-gray-500">Chưa có sản phẩm</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $total = $request->items->sum(function ($item) {
                                            return $item->quantity * $item->expected_price;
                                        });
                                    @endphp
                                    <p class="font-semibold text-gray-900">{{ number_format($total, 0, ',', '.') }} đ</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusLabel = $request->status ? get_request_status_label($request->status) : ($request->is_submitted ? 'Chờ duyệt' : 'Bản nháp');

                                        $statusClass = 'bg-gray-100 text-gray-800'; // Default draft
                                        if ($request->status) {
                                            $statusClass = get_request_status_class($request->status);
                                        } elseif ($request->is_submitted) {
                                            $statusClass = 'bg-yellow-100 text-yellow-800'; // 'Submitted' style
                                        }
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900">{{ $request->created_at?->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->created_at?->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <button onclick="viewRequestDetails({{ $request->id }})"
                                            class="text-gray-400 hover:text-blue-600 transition" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$request->is_submitted)
                                            <a href="{{ route('department.requests.edit', $request->id) }}"
                                                class="text-gray-400 hover:text-green-600" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('department.requests.destroy', $request->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @elseif($request->is_submitted && !$request->status)
                                            <form action="{{ route('department.requests.withdraw', $request->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Bạn muốn rút yêu cầu này về nháp để chỉnh sửa?');">
                                                @csrf
                                                <button type="submit" class="text-gray-400 hover:text-orange-600"
                                                    title="Rút yêu cầu">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 mb-4">Không tìm thấy yêu cầu nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal for Request Details -->
    <div id="requestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div
                class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700">
                <h3 class="text-xl font-bold text-white">Chi tiết yêu cầu</h3>
                <button onclick="closeModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="modalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <div class="flex items-center justify-center py-12">
                    <i class="fas fa-spinner fa-spin text-blue-600 text-4xl"></i>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
                <button onclick="closeModal()"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Đóng
                </button>
                <a id="viewFullPageLink" href="#"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Xem trang đầy đủ
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function viewRequestDetails(requestId) {
                const modal = document.getElementById('requestModal');
                const modalContent = document.getElementById('modalContent');
                const viewFullPageLink = document.getElementById('viewFullPageLink');

                // Show modal
                modal.classList.remove('hidden');

                // Update full page link
                viewFullPageLink.href = `/department/requests/${requestId}`;

                // Show loading
                modalContent.innerHTML = `
                        <div class="flex items-center justify-center py-12">
                            <i class="fas fa-spinner fa-spin text-blue-600 text-4xl"></i>
                        </div>
                    `;

                // Fetch request details
                fetch(`/department/requests/${requestId}`)
                    .then(response => response.text())
                    .then(html => {
                        // Parse the HTML and extract the content
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Extract the printable section
                        const content = doc.querySelector('#printable-section');

                        if (content) {
                            modalContent.innerHTML = content.outerHTML;
                        } else {
                            modalContent.innerHTML = `
                                    <div class="text-center py-12">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                                        <p class="text-gray-600">Không thể tải thông tin yêu cầu</p>
                                    </div>
                                `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        modalContent.innerHTML = `
                                <div class="text-center py-12">
                                    <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                                    <p class="text-gray-600">Đã xảy ra lỗi khi tải dữ liệu</p>
                                </div>
                            `;
                    });
            }

            function closeModal() {
                const modal = document.getElementById('requestModal');
                modal.classList.add('hidden');
            }

            // Close modal when clicking outside
            document.getElementById('requestModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        </script>
    @endpush
@endsection