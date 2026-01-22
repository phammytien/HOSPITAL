@extends('layouts.buyer')

@section('title', 'Theo dõi Giao hàng')
@section('header_title', 'Theo dõi Giao hàng')

@section('content')
    <div class="mb-6 overflow-x-auto">
        <div class="flex border-b border-gray-200 min-w-max">
            @php
                $tabs = [
                    'CREATED' => ['label' => 'Mới tạo', 'icon' => 'fa-plus-circle'],
                    'ORDERED' => ['label' => 'Đã đặt hàng', 'icon' => 'fa-shopping-cart'],
                    'DELIVERING' => ['label' => 'Đang giao', 'icon' => 'fa-truck'],
                    'DELIVERED' => ['label' => 'Đã nhận hàng', 'icon' => 'fa-box-open'],
                    'COMPLETED' => ['label' => 'Hoàn tất', 'icon' => 'fa-check-circle'],
                    'CANCELLED' => ['label' => 'Đã hủy', 'icon' => 'fa-times-circle'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <a href="{{ route('buyer.tracking.index', array_merge(request()->query(), ['status' => $key])) }}"
                    class="flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ $status === $key ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas {{ $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                    @if(isset($counts[$key]) && $counts[$key] > 0)
                        <span
                            class="ml-2 px-2 py-0.5 rounded-full text-xs {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                            {{ $counts[$key] }}
                        </span>
                    @endif
                </a>
            @endforeach

            <a href="{{ route('buyer.tracking.index', ['status' => 'FEEDBACK']) }}"
                class="flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ $status === 'FEEDBACK' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-comment-dots"></i>
                <span>Phản hồi</span>
                @php
                    $pendingFeedbackCount = \App\Models\PurchaseFeedback::where('is_delete', false)
                        ->whereNotNull('rating')
                        ->whereNotNull('purchase_order_id')
                        ->where('status', 'PENDING')
                        ->distinct('purchase_order_id')
                        ->count('purchase_order_id');
                @endphp
                @if($pendingFeedbackCount > 0)
                    <span
                        class="ml-2 px-2 py-0.5 rounded-full text-xs {{ $status === 'FEEDBACK' ? 'bg-blue-600 text-white' : 'bg-red-500 text-white' }}">
                        {{ $pendingFeedbackCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
        <!-- Filters Section -->
        <div class="px-8 py-5 border-b border-gray-50 bg-white items-center">
            <form action="{{ route('buyer.tracking.index') }}" method="GET" id="filter-form"
                class="flex flex-wrap items-center gap-5">
                <input type="hidden" name="status" value="{{ $status }}">

                <div class="text-[12px] font-bold text-gray-400 uppercase tracking-widest">LỌC:</div>

                <div class="flex-grow max-w-[280px]">
                    <select name="department_id" onchange="this.form.submit()"
                        class="w-full border-gray-100 shadow-sm bg-white rounded-xl text-sm font-semibold text-gray-700 focus:border-blue-400 focus:ring-blue-100 hover:border-gray-200 transition-all cursor-pointer py-2.5 px-4">
                        <option value="">-- Tất cả Khoa/Phòng --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($status !== 'FEEDBACK')
                <div class="flex-grow max-w-[220px]">
                    <select name="period" onchange="this.form.submit()"
                        class="w-full border-gray-100 shadow-sm bg-white rounded-xl text-sm font-semibold text-gray-700 focus:border-blue-400 focus:ring-blue-100 hover:border-gray-200 transition-all cursor-pointer py-2.5 px-4">
                        <option value="">-- Tất cả Kỳ/Quý --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p }}" {{ request('period') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(request('department_id') || request('period'))
                    <a href="{{ route('buyer.tracking.index', ['status' => $status]) }}"
                        class="px-8 py-2.5 bg-white border border-gray-100 rounded-xl text-sm font-extrabold text-gray-700 hover:bg-gray-50 hover:border-gray-200 transition-all shadow-sm">
                        Xóa lọc
                    </a>
                @endif
            </form>
        </div>

        @if(!in_array($status, ['DELIVERED', 'COMPLETED', 'CANCELLED']))
            <form id="bulk-action-form" action="{{ route('buyer.tracking.bulk-update') }}" method="POST">
                @csrf
        @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    @if($status === 'FEEDBACK')
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Người gửi</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nội dung</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Đánh giá</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày gửi</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $feedback)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $feedback->feedbackBy->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $feedback->feedbackBy->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-700 line-clamp-1 italic">"{{ Str::limit($feedback->feedback_content, 80) }}"</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= ($feedback->rating ?? 0) ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm border-gray-600">
                                        {{ $feedback->created_at ? $feedback->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($feedback->status == 'PENDING')
                                        <span class="px-2.5 py-1 bg-orange-50 text-orange-700 rounded-full text-[10px] font-bold border border-orange-100">Chờ xử lý</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-100">Đã giải quyết</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('buyer.tracking.show', $feedback->purchase_order_id) }}"
                                        class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-bold text-sm transition">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-comment-slash text-4xl mb-3 text-gray-100"></i>
                                        <p>Không tìm thấy phản hồi nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @else
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            @if(!in_array($status, ['DELIVERED', 'COMPLETED', 'CANCELLED']))
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                            @endif
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã đơn hàng
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Khoa/Phòng
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Trạng
                                thái</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                                Tiến
                                độ</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                                Hành
                                động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition border-row">
                                @if(!in_array($status, ['DELIVERED', 'COMPLETED', 'CANCELLED']))
                                    <td class="px-6 py-4 w-10">
                                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                            class="order-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm font-medium">
                                    <a href="{{ route('buyer.tracking.show', $order->id) }}"
                                        class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $order->order_code ?? '#' . $order->id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $order->department->department_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($order->status === 'DELIVERED')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 flex items-center justify-center gap-1 mx-auto w-fit">
                                            <i class="fas fa-clock text-[10px]"></i>
                                            Chờ xác nhận
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ get_status_class($order->status) }}">
                                            {{ get_status_label($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-600">
                                    @php
                                        $totalItems = $order->items_count ?? $order->items->count();
                                        if (in_array($order->status, ['COMPLETED', 'DELIVERED'])) {
                                            $deliveredItems = $totalItems;
                                        } else {
                                            $deliveredItems = $order->items->whereIn('status', ['DELIVERED', 'COMPLETED'])->count();
                                        }
                                    @endphp
                                    {{ $deliveredItems }}/{{ $totalItems }} <span class="hidden sm:inline">sản phẩm về
                                        kho</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('buyer.tracking.show', $order->id) }}"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center justify-end gap-1">
                                        @if(in_array($order->status, ['DELIVERED', 'COMPLETED', 'CANCELLED']))
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        @else
                                            <i class="fas fa-truck-loading"></i> Cập nhật
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ !in_array($status, ['DELIVERED', 'COMPLETED', 'CANCELLED']) ? '7' : '6' }}"
                                    class="px-6 py-12 text-center text-gray-500 italic">
                                    Không có đơn hàng nào để theo dõi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @endif
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>

            @if(!in_array($status, ['DELIVERED', 'COMPLETED', 'CANCELLED']))
                    <!-- Spacer to prevent bar from covering content -->
                    <div id="bulk-spacer" class="h-0 transition-all duration-300"></div>

                    <!-- Bulk Action Bar (Floating) -->
                    <div id="bulk-action-bar"
                        class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-sm shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-100 rounded-2xl px-6 py-3 items-center gap-5 z-50 transition-all duration-300 transform animate-in fade-in slide-in-from-bottom-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 font-bold text-sm">
                                <span id="selected-count">0</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">Đơn hàng đã chọn</span>
                        </div>

                        <div class="h-8 w-px bg-gray-100"></div>

                        <div class="flex items-center gap-4">
                            @php
                                $targetStatus = '';
                                $targetLabel = '';
                                if ($status === 'CREATED' || $status === 'PENDING') {
                                    $targetStatus = 'ORDERED';
                                    $targetLabel = 'Đã đặt hàng';
                                } elseif ($status === 'ORDERED') {
                                    $targetStatus = 'DELIVERING';
                                    $targetLabel = 'Đang giao';
                                } elseif ($status === 'DELIVERING') {
                                    $targetStatus = 'DELIVERED';
                                    $targetLabel = 'Đã nhận hàng';
                                } elseif ($status === 'DELIVERED') {
                                    $targetStatus = 'COMPLETED';
                                    $targetLabel = 'Hoàn tất';
                                } else {
                                    $targetStatus = 'ORDERED'; // Fallback
                                    $targetLabel = 'Đã đặt hàng';
                                }
                            @endphp
                            <input type="hidden" name="status" id="bulk-status-input" value="{{ $targetStatus }}">
                            <div
                                class="px-5 py-2.5 bg-blue-50/50 text-blue-700 rounded-xl font-bold text-sm border border-blue-100 flex items-center gap-2 whitespace-nowrap">
                                <i class="fas fa-arrow-right text-[10px]"></i>
                                <span>{{ $targetLabel }}</span>
                            </div>

                            <div id="bulk-date-container"
                                class="{{ ($status === 'ORDERED') ? 'flex' : 'hidden' }} items-center gap-3 bg-blue-50/50 border border-blue-100 px-4 py-2 rounded-xl transition-all duration-300 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <label
                                        class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-0.5 whitespace-nowrap">Ngày
                                        dự kiến giao</label>
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-calendar-alt text-blue-500"></i>
                                        <input type="date" name="expected_delivery_date" id="bulk-date-input"
                                            min="{{ date('Y-m-d') }}"
                                            class="bg-transparent border-none p-0 text-sm font-bold text-blue-900 focus:ring-0 min-w-[120px]"
                                            {{ ($status === 'ORDERED') ? 'required' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="submit-bulk-btn"
                                class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-blue-700 hover:shadow-lg transition-all duration-300 flex items-center gap-2 group whitespace-nowrap shadow-sm">
                                <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </form>
            @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('.order-checkbox');
                const actionBar = document.getElementById('bulk-action-bar');
                const selectedCount = document.getElementById('selected-count');
                const bulkForm = document.getElementById('bulk-action-form');
                const submitBtn = document.getElementById('submit-bulk-btn');
                const statusInput = document.getElementById('bulk-status-input');
                const dateContainer = document.getElementById('bulk-date-container');
                const dateInput = document.getElementById('bulk-date-input');

                const bulkSpacer = document.getElementById('bulk-spacer');

                function updateActionBar() {
                    const checked = document.querySelectorAll('.order-checkbox:checked');
                    const count = checked.length;

                    selectedCount.textContent = count;

                    if (count > 0) {
                        actionBar.classList.remove('hidden');
                        actionBar.classList.add('flex');
                        if (bulkSpacer) bulkSpacer.classList.replace('h-0', 'h-24');
                    } else {
                        actionBar.classList.add('hidden');
                        actionBar.classList.remove('flex');
                        if (bulkSpacer) bulkSpacer.classList.replace('h-24', 'h-0');
                    }
                }

                // The status is now determined by PHP and set in a hidden input, not user-selectable via JS.
                // The date container visibility is also determined by PHP.
                // Therefore, the dynamic logic for statusSelect change event is no longer needed.

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => {
                            cb.checked = selectAll.checked;
                            const row = cb.closest('tr');
                            if (selectAll.checked) {
                                row.classList.add('bg-blue-50');
                            } else {
                                row.classList.remove('bg-blue-50');
                            }
                        });
                        updateActionBar();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function () {
                        const row = cb.closest('tr');
                        if (cb.checked) {
                            row.classList.add('bg-blue-50');
                        } else {
                            row.classList.remove('bg-blue-50');
                        }

                        // Update Select All state
                        if (selectAll) {
                            const allChecked = Array.from(checkboxes).every(c => c.checked);
                            selectAll.checked = allChecked;
                        }
                        updateActionBar();
                    });
                });

                if (submitBtn) {
                    submitBtn.addEventListener('click', function () {
                        const status = statusInput.value;
                        const statusLabel = status === 'ORDERED' ? 'Đã đặt hàng' :
                            status === 'DELIVERING' ? 'Đang giao' :
                                status === 'DELIVERED' ? 'Đã nhận hàng' : 'Hoàn tất';

                        if (dateInput.required && !dateInput.value) {
                            alert('Vui lòng chọn Ngày dự kiến giao hàng.');
                            return;
                        }

                        if (confirm(`Bạn có chắc chắn muốn chuyển trạng thái cho các đơn hàng đã chọn sang "${statusLabel}"?`)) {
                            bulkForm.submit();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection