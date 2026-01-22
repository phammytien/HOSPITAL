@extends('layouts.department')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
    <div class="mb-6">
        <a href="{{ route('department.dept_orders.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Tracking Status -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-full">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-route text-blue-600 mr-2"></i> Theo dõi đơn hàng
                </h3>

                <div class="relative pl-6 border-l-2 border-gray-200 space-y-8">
                    @php
                        $steps = [
                            [
                                'status' => 'ORDERED',
                                'label' => 'Đã đặt hàng',
                                'desc' => 'Đơn hàng đã được duyệt và gửi tới nhà cung cấp',
                                'icon' => 'fa-file-invoice',
                                'time' => $order->created_at
                            ],
                            [
                                'status' => 'DELIVERING',
                                'label' => 'Đang vận chuyển',
                                'desc' => 'Hàng đang trên đường tới kho',
                                'icon' => 'fa-truck',
                                'time' => $order->shipping_at
                            ],
                            [
                                'status' => 'DELIVERED',
                                'label' => 'Đã giao hàng',
                                'desc' => 'Hàng đã về tới kho khoa/phòng',
                                'icon' => 'fa-box-open',
                                'time' => $order->delivered_at
                            ],
                            [
                                'status' => 'COMPLETED',
                                'label' => 'Hoàn thành',
                                'desc' => 'Đã xác nhận nhận hàng và kết thúc đơn',
                                'icon' => 'fa-check-circle',
                                'time' => $order->completed_at
                            ],
                        ];

                        $currentStatusIndex = -1;
                        if ($order->status == 'ORDERED')
                            $currentStatusIndex = 0;
                        elseif ($order->status == 'DELIVERING')
                            $currentStatusIndex = 1;
                        elseif ($order->status == 'DELIVERED')
                            $currentStatusIndex = 2;
                        elseif ($order->status == 'COMPLETED')
                            $currentStatusIndex = 3;
                    @endphp

                    @foreach($steps as $index => $step)
                        <div class="relative mb-8">
                            <span
                                class="absolute -left-[33px] flex items-center justify-center w-10 h-10 rounded-full border-4 border-white 
                                                                                                                                                                        {{ $index <= $currentStatusIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </span>
                            <div class="ml-4">
                                <h4
                                    class="text-base font-bold {{ $index <= $currentStatusIndex ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $step['label'] }}
                                </h4>
                                <p class="text-sm text-gray-500">{{ $step['desc'] }}</p>
                                @if($step['time'])
                                    <p class="text-xs text-gray-400 mt-1">
                                        <i class="far fa-clock mr-1"></i> {{ $step['time']->format('d/m/Y H:i') }}
                                    </p>
                                @endif

                                @if($index == $currentStatusIndex)
                                    <span
                                        class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold animate-pulse">
                                        Hiện tại
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Order Details & Actions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">#{{ $order->order_code ?? $order->id }}</h2>
                        <p class="text-gray-500 text-sm">Ngày tạo: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-500 text-sm">Yêu cầu gốc:
                            <span
                                class="font-medium text-gray-700">{{ $order->purchaseRequest->request_code ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ get_status_class($order->status) }}">
                            {{ get_status_label($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <h3 class="font-bold text-gray-800 mb-3">Danh sách sản phẩm</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-lg">Sản phẩm</th>
                                    <th class="px-4 py-3">Số lượng</th>
                                    <th class="px-4 py-3 rounded-r-lg">Đơn vị</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($order->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <p>{{ $item->product->product_name ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $item->product->product_code ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-900 font-semibold">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $item->product->unit ?? 'Cái' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions Section: Simple Confirmation -->
            @if($order->status == 'DELIVERED')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Xác nhận đơn hàng</h3>
                    <p class="text-gray-600 mb-6 font-sm">
                        Vui lòng kiểm tra kỹ số lượng và chất lượng hàng hóa trước khi xác nhận đã nhận hàng.
                    </p>

                    <form action="{{ route('department.dept_orders.confirm', $order->id) }}" method="POST">
                        @csrf
                        <div class="flex gap-4">
                            <button type="submit"
                                class="flex-1 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Xác nhận đã nhận hàng
                            </button>
                            <!-- Reject button triggers modal -->
                            <button type="button" onclick="openRejectModal()"
                                class="px-6 py-3 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition">
                                <i class="fas fa-times-circle mr-2"></i> Từ chối
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Chat / Feedback Section -->
            @php
                $isFeedbackResolved = $order->feedbacks->where('status', 'RESOLVED')->count() > 0;
                $isResolved = $order->status == 'RESOLVED' || $isFeedbackResolved;
            @endphp

            @if((in_array($order->status, ['DELIVERED', 'COMPLETED', 'CANCELLED', 'RESOLVED']) && $order->feedbacks->count() > 0) || in_array($order->status, ['COMPLETED', 'CANCELLED']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col h-[600px] mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-3">
                        <i class="fas fa-comments text-blue-600"></i> Phản hồi
                        @if($isResolved)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Đã giải quyết</span>
                        @endif
                    </h3>

                    <!-- Chat Messages Area -->
                    <div id="chatMessages"
                        class="flex-1 overflow-y-auto space-y-2 p-4 bg-gray-50 rounded-lg mb-4 border border-gray-100">
                        <!-- Loading state -->
                        <div class="text-center text-gray-400 py-12">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm">Đang tải tin nhắn...</p>
                        </div>
                    </div>

                    <!-- Chat Input Area -->
                    @if(!$isResolved)
                        <div class="flex-none pt-4 border-t border-gray-100">
                            <!-- Product Selection Toggles -->
                            <div class="mb-3">
                                <button type="button"
                                    onclick="document.getElementById('productReviewSelector').classList.toggle('hidden')"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 focus:outline-none">
                                    <i class="fas fa-tag"></i> Đánh giá sản phẩm cụ thể (Click để chọn)
                                </button>

                                <div id="productReviewSelector"
                                    class="hidden mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($order->items as $item)
                                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-white p-1 rounded transition">
                                            <input type="checkbox" name="review_products" value="{{ $item->product->product_name }}"
                                                class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <span class="text-sm text-gray-700 truncate" title="{{ $item->product->product_name }}">
                                                {{ $item->product->product_name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <h4 class="text-sm font-bold text-gray-700 mb-2">Trả lời phản hồi</h4>
                            <form id="chatForm" onsubmit="sendMessage(event, 'issue')" class="relative">
                                <!-- Restore Rating Stars -->
                                <div id="ratingSelector" class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Đánh giá chất lượng dịch vụ</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="hidden" name="rating" id="ratingInput" value="5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star star-rating cursor-pointer text-yellow-400 text-xl"
                                                data-rating="{{ $i }}" onclick="setRating({{ $i }})"></i>
                                        @endfor
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Ý kiến phản hồi (Tùy chọn)</label>
                                    <textarea id="chatInput" rows="3"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none shadow-sm"
                                        placeholder="Nhập nội dung trả lời..." required></textarea>
                                </div>
                                <div class="mt-4 flex gap-3 justify-end">
                                    <button type="button" onclick="confirmGoodProduct(event)" id="goodBtn"
                                        class="px-6 py-2.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition flex items-center gap-2 shadow-md">
                                        <i class="fas fa-check-circle"></i> <span>Sản phẩm tốt</span>
                                    </button>

                                    <button type="submit" id="sendBtn"
                                        class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition flex items-center gap-2 shadow-md">
                                        <i class="fas fa-exclamation-circle"></i> <span>Phản hồi vấn đề</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="flex-none pt-4">
                            <div class="bg-gray-100 p-4 rounded-xl text-center text-gray-500 text-sm border border-gray-200">
                                <i class="fas fa-lock mr-2"></i> Hội thoại đã được đánh dấu giải quyết
                            </div>
                        </div>
                    @endif
                </div>

                @push('scripts')
                    <script>
                        const orderId = {{ $order->id }};
                        const currentUserId = {{ Auth::id() }};
                        let isPolling = true;

                        function confirmGoodProduct(e) {
                            e.preventDefault();

                            const confirmed = confirm(
                                "Bạn xác nhận sản phẩm không có vấn đề.\n" +
                                "Sau khi gửi phản hồi này, bạn sẽ không thể khiếu nại cho đơn hàng này nữa.\n\n" +
                                "Bạn có chắc chắn muốn tiếp tục không?"
                            );

                            if (confirmed) {
                                sendMessage(e, 'good');
                            }
                        }


                        // Function to load messages
                        async function loadMessages() {
                            if (!isPolling) return;

                            try {
                                const response = await fetch(`{{ route('department.feedback.messages', $order->id) }}`);
                                const data = await response.json();

                                if (data.success) {
                                    renderMessages(data.messages, data.existing_rating);
                                }
                            } catch (error) {
                                console.error('Error loading messages:', error);
                            }
                        }

                        // Render messages to UI (Card Style)
                        function renderMessages(messages, existingRating) {
                            const container = document.getElementById('chatMessages');

                            if (messages.length === 0) {
                                container.innerHTML = `
                                                                                                                                                <div class="text-center text-gray-400 py-12">
                                                                                                                                                    <i class="fas fa-comments text-4xl mb-3 opacity-20"></i>
                                                                                                                                                    <p class="text-sm">Chưa có trao đổi nào về đơn hàng này.</p>
                                                                                                                                                </div>`;
                                return;
                            }

                            // Handle one-time rating visibility
                            if (existingRating) {
                                const rel = document.getElementById('ratingSelector');
                                const rin = document.getElementById('ratingInput');
                                if (rel) rel.classList.add('hidden');
                                if (rin) rin.value = existingRating;
                            }

                            const isScrolledToBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

                            const html = messages.map(msg => {
                                const isMe = msg.is_current_user;
                                const isAdmin = msg.type === 'admin';

                                const bgClass = isAdmin ? 'bg-blue-50 border-blue-100' : 'bg-white border-gray-200';
                                const userLabel = isAdmin ? 'Bộ phận Mua hàng' : (isMe ? 'Bạn (' + msg.user_name + ')' : msg.user_name);
                                const avatarColor = isAdmin ? 'bg-blue-600' : 'bg-gray-400';

                                let ratingStars = '';
                                if (msg.rating) {
                                    ratingStars = `<div class="flex text-yellow-400 text-[10px] ml-2">
                                                                                                                                                    ${Array.from({ length: 5 }, (_, i) => `<i class="fas fa-star ${i < msg.rating ? '' : 'text-gray-300'}"></i>`).join('')}
                                                                                                                                                </div>`;
                                }

                                return `
                                                                                                                                                <div class="mb-6 last:mb-2 group">
                                                                                                                                                    <div class="flex items-start gap-3">
                                                                                                                                                        <div class="w-10 h-10 rounded-full ${avatarColor} flex items-center justify-center text-white text-xs font-bold shadow-sm flex-shrink-0">
                                                                                                                                                            ${isAdmin ? 'B' : msg.user_name.charAt(0)}
                                                                                                                                                        </div>
                                                                                                                                                        <div class="flex-1">
                                                                                                                                                            <div class="flex items-center mb-1">
                                                                                                                                                                <span class="font-bold text-sm text-gray-900">${userLabel}</span>
                                                                                                                                                                ${ratingStars}
                                                                                                                                                            </div>
                                                                                                                                                            <div class="p-4 rounded-2xl border ${bgClass} shadow-sm relative">
                                                                                                                                                                <p class="text-gray-800 text-sm leading-relaxed whitespace-pre-wrap">${msg.content}</p>
                                                                                                                                                            </div>
                                                                                                                                                            <div class="mt-1 text-[10px] text-gray-400 flex items-center gap-1 px-1">
                                                                                                                                                                <i class="far fa-clock"></i> ${msg.time}
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            `;
                            }).join('');

                            if (container.dataset.lastHash !== JSON.stringify(messages.length + (messages[messages.length - 1]?.id || ''))) {
                                container.innerHTML = html;
                                container.dataset.lastHash = JSON.stringify(messages.length + (messages[messages.length - 1]?.id || ''));

                                if (isScrolledToBottom || !container.dataset.initialized) {
                                    scrollToBottom();
                                    container.dataset.initialized = 'true';
                                }
                            }
                        }

                        function scrollToBottom() {
                            const container = document.getElementById('chatMessages');
                            container.scrollTop = container.scrollHeight;
                        }

                        // Send message logic
                        async function sendMessage(e, action = 'issue') {
                            if (e) e.preventDefault();
                            const input = document.getElementById('chatInput');
                            const sendBtn = document.getElementById('sendBtn');
                            const goodBtn = document.getElementById('goodBtn');
                            let content = input.value.trim();

                            if (!content && action === 'issue') return;

                            // Check for selected products
                            const selectedProducts = Array.from(document.querySelectorAll('input[name="review_products"]:checked'))
                                .map(cb => cb.value);

                            if (selectedProducts.length > 0) {
                                const productString = selectedProducts.join(', ');
                                content = `[Phản hồi về SP: ${productString}]\n${content}`;
                            }

                            // Disable UI
                            input.disabled = true;
                            sendBtn.disabled = true;
                            goodBtn.disabled = true;

                            const originalBtnHtml = action === 'good' ? goodBtn.innerHTML : sendBtn.innerHTML;
                            if (action === 'good') goodBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                            else sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                            const ratingEl = document.getElementById('ratingInput');
                            const rating = ratingEl ? ratingEl.value : 5;

                            try {
                                const response = await fetch(`{{ route('department.feedback.store', $order->id) }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        content,
                                        rating: rating,
                                        action: action
                                    })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    input.value = '';
                                    // Uncheck products
                                    document.querySelectorAll('input[name="review_products"]:checked').forEach(cb => cb.checked = false);

                                    if (action === 'good') {
                                        location.reload(); // Hard reload for status update
                                    } else {
                                        await loadMessages(); // Reload immediately
                                        scrollToBottom();
                                    }
                                } else {
                                    alert('Không thể gửi tin nhắn: ' + (data.message || 'Lỗi không xác định'));
                                }
                            } catch (error) {
                                alert('Lỗi kết nối!');
                                console.error(error);
                            } finally {
                                input.disabled = false;
                                sendBtn.disabled = false;
                                goodBtn.disabled = false;
                                if (action === 'good') goodBtn.innerHTML = originalBtnHtml;
                                else sendBtn.innerHTML = originalBtnHtml;
                                input.focus();
                            }
                        }

                        // Start polling
                        loadMessages();
                        const pollInterval = setInterval(loadMessages, 3000); // Poll every 3s

                        window.addEventListener('beforeunload', () => {
                            clearInterval(pollInterval);
                        });
                    </script>
                @endpush
            @endif

            <!-- Reject Modal -->
            <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                        onclick="closeRejectModal()"></div>

                    <!-- Modal panel -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('department.dept_orders.reject', $order->id) }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-bold text-red-600 border-b border-gray-200 pb-2 mb-4 flex justify-between items-center"
                                            id="modal-title">
                                            Từ chối nhận hàng
                                            <button type="button" onclick="closeRejectModal()"
                                                class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </h3>

                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 mb-4">Vui lòng chọn lý do từ chối nhận hàng:</p>

                                            <!-- Option: Wrong Product -->
                                            <div class="mb-3">
                                                <div class="flex items-center">
                                                    <input id="reason_wrong" name="reason_option" type="radio"
                                                        value="wrong_product" checked
                                                        class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                                        onchange="toggleRejectReason()">
                                                    <label for="reason_wrong"
                                                        class="ml-3 block text-sm font-medium text-gray-700">
                                                        Sai sản phẩm
                                                    </label>
                                                </div>

                                                <!-- Product List (Hidden by default) -->
                                                <div id="productsList"
                                                    class="hidden mt-2 ml-7 space-y-2 border-l-2 border-gray-200 pl-3">
                                                    @foreach($order->items as $item)
                                                        <div class="flex items-start">
                                                            <div class="flex items-center h-5">
                                                                <input id="item_{{ $item->id }}" name="wrong_items[]"
                                                                    type="checkbox" value="{{ $item->id }}"
                                                                    class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                                                            </div>
                                                            <div class="ml-3 text-xs">
                                                                <label for="item_{{ $item->id }}"
                                                                    class="font-medium text-gray-700">{{ $item->product->product_name }}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Option: Other Reason -->
                                            <div class="mb-3">
                                                <div class="flex items-center">
                                                    <input id="reason_other" name="reason_option" type="radio" value="other"
                                                        class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                                                        onchange="toggleRejectReason()">
                                                    <label for="reason_other"
                                                        class="ml-3 block text-sm font-medium text-gray-700">
                                                        Lý do khác
                                                    </label>
                                                </div>

                                                <!-- Other Reason Input -->
                                                <div id="otherReasonInput" class="hidden mt-2 ml-7">
                                                    <textarea name="other_reason" rows="3"
                                                        class="shadow-sm focus:ring-red-500 focus:border-red-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                        placeholder="Nhập lý do..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Xác nhận Từ chối
                                </button>
                                <button type="button" onclick="closeRejectModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function setRating(rating) {
                const rin = document.getElementById('ratingInput');
                if (rin) rin.value = rating;
                const stars = document.querySelectorAll('.star-rating');
                stars.forEach(star => {
                    if (parseInt(star.dataset.rating) <= rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            }
            // Init default rating
            setRating(5);

            // Modal Logic
            function openRejectModal() {
                document.getElementById('rejectModal').classList.remove('hidden');
                toggleRejectReason(); // Ensure correct state when opening
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }

            function toggleRejectReason() {
                const isWrongProduct = document.getElementById('reason_wrong').checked;
                const productsList = document.getElementById('productsList');
                const otherReasonInput = document.getElementById('otherReasonInput');

                if (isWrongProduct) {
                    productsList.classList.remove('hidden');
                    otherReasonInput.classList.add('hidden');
                } else {
                    productsList.classList.add('hidden');
                    otherReasonInput.classList.remove('hidden');
                }
            }

            // Init toggle state on load
            document.addEventListener('DOMContentLoaded', function () {
                toggleRejectReason();
            });
        </script>
    @endpush
@endsection