@extends('layouts.buyer')

@section('title', 'Theo dõi đơn hàng #' . ($order->order_code ?? $order->id))
@section('header_title', 'Theo dõi chi tiết đơn hàng')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Breadcrumb & Action -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('buyer.tracking.index') }}" class="hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
                <span class="text-gray-300">/</span>
                <span class="font-bold text-gray-800">Theo dõi #{{ $order->order_code ?? $order->id }}</span>
            </div>

            <!-- Top Action Button -->
            <form action="{{ route('buyer.tracking.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                @if($order->status == 'CREATED')
                    <button type="submit" name="status" value="ORDERED"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center">
                        <i class="fas fa-clock mr-2"></i> Xác nhận
                    </button>
                @elseif($order->status == 'PENDING')
                    <button type="submit" name="status" value="ORDERED"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i> Xác nhận Đặt hàng
                    </button>
                @elseif($order->status == 'ORDERED')
                    <div class="flex items-center gap-2">
                        <input type="date" name="expected_delivery_date" required min="{{ date('Y-m-d') }}"
                            class="text-sm border-gray-100 shadow-sm rounded-xl focus:ring-blue-100 focus:border-blue-400 py-2 px-4 font-semibold text-gray-700 transition-all">
                        <button type="submit" name="status" value="DELIVERING"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition shadow-sm flex items-center">
                            <i class="fas fa-truck mr-2"></i> Bắt đầu Giao hàng
                        </button>
                    </div>
                @elseif($order->status == 'DELIVERING')
                    <button type="submit" name="status" value="DELIVERED" onclick="return confirm('Xác nhận hàng đã về kho?')"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition shadow-sm flex items-center">
                        <i class="fas fa-warehouse mr-2"></i> Đã về kho
                    </button>
                @elseif($order->status == 'DELIVERED')
                    <span
                        class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg font-medium border border-yellow-200 flex items-center">
                        <i class="fas fa-clock mr-2"></i> Chờ xác nhận
                    </span>
                @elseif($order->status == 'COMPLETED')
                    <span
                        class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium border border-green-200 flex items-center">
                        <i class="fas fa-check mr-2"></i> Hoàn tất
                    </span>
                @elseif($order->status == 'CANCELLED')
                    <span
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium border border-gray-200 flex items-center">
                        <i class="fas fa-times mr-2"></i> Đã hủy
                    </span>
                @elseif($order->status == 'REJECTED')
                    <span
                        class="px-4 py-2 bg-red-100 text-white rounded-lg font-medium border border-red-200 flex items-center">
                        <i class="fas fa-ban mr-2"></i> Đã từ chối
                    </span>
                @endif
            </form>
        </div>

        <!-- Stepper Card -->
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start relative">

                @foreach($steps as $key => $step)
                    @php
                        // Helper logic
                        $statusKeys = array_keys($steps);
                        $currentIndex = array_search($order->status, $statusKeys);
                        $stepIndex = array_search($key, $statusKeys);

                        $isCancelled = in_array($order->status, ['CANCELLED', 'REJECTED']); // Simplify

                        // If cancelled:
                        // 1. Previous steps are Active.
                        // 2. The Step where it got cancelled should turn Red? 
                        // Or just show all steps up to X as active, but the last one red?
                        // Let's copy the Department Request logic: Only steps actually reached are green. 
                        // If rejected, the *next* logical step or the current step serves as the "Stop".

                        // BUT here we have a fixed set of steps in the loop.
                        // Logic:
                        // Mark steps as "Done" if index < currentIndex.
                        // If Current Index matches this step: Blue (Active).
                        // If Cancelled: The "Current" active status is CANCELLED, which is NOT in the $steps array keys usually?
                        // Wait, $steps keys are usually standard flow. 

                        // Check if CANCELLED is in keys? Typically no.
                        // Let's look at controller or standard keys. Usually: CREATED, ORDERED, DELIVERING, DELIVERED, COMPLETED.

                        // Implementation:
                        // If Cancelled, we want to know *when* (date).
                        // Is this step "past"?

                        $isPast = false;
                        $isCurrent = false;

                        // Map status to level
                        $levels = ['CREATED' => 0, 'PENDING' => 1, 'ORDERED' => 2, 'DELIVERING' => 3, 'DELIVERED' => 4, 'COMPLETED' => 5];
                        $currentLevel = $levels[$order->status] ?? -1;

                        if ($isCancelled) {
                            // If cancelled, show steps up to "where it was" ??
                            // Actually, if cancelled, the timeline usually stops. 
                            // The user wants a Red X at the end.
                            // We should probably just iterate the standard steps, and if we run out of standard steps or if we encounter the "cancellation point", we modify the last one or append?

                            // Let's assume strict levels. 
                            // If cancelled, we don't know "at what stage" easily unless we check history.
                            // Simple approach: 
                            // Just show checking existing timestamps.
                        }

                        $date = null;
                        if ($key == 'CREATED')
                            $date = $order->created_at;
                        elseif ($key == 'PENDING')
                            $date = $order->updated_at;
                        elseif ($key == 'ORDERED')
                            $date = $order->ordered_at;
                        elseif ($key == 'DELIVERING')
                            $date = $order->shipping_at;
                        elseif ($key == 'DELIVERED')
                            $date = $order->delivered_at;
                        elseif ($key == 'COMPLETED')
                            $date = $order->completed_at;

                        $isActive = ($date != null);

                        // Handling Cancelled Visuals
                        // If this step has a date, it's green.
                        // If this step is the *next* one after the last active one AND order is cancelled, make it Red X?

                        // Find last active index based on dates
                        $lastActiveIndex = -1;
                        foreach ($steps as $k => $s) {
                            $d = null;
                            if ($k == 'CREATED')
                                $d = $order->created_at;
                            elseif ($k == 'PENDING')
                                $d = $order->updated_at;
                            elseif ($k == 'ORDERED')
                                $d = $order->ordered_at;
                            elseif ($k == 'DELIVERING')
                                $d = $order->shipping_at;
                            elseif ($k == 'DELIVERED')
                                $d = $order->delivered_at;
                            elseif ($k == 'COMPLETED')
                                $d = $order->completed_at;
                            if ($d)
                                $lastActiveIndex++;
                        }

                        // Override for Cancelled
                        $showRedX = false;
                        if ($isCancelled && $stepIndex == $lastActiveIndex + 1) {
                            $showRedX = true;
                        }

                        // Colors
                        $iconBg = 'bg-gray-100';
                        $iconColor = 'text-gray-400';
                        $textColor = 'text-gray-400';
                        $lineColor = 'bg-gray-100';

                        if ($isActive) {
                            $iconBg = 'bg-blue-600';
                            $iconColor = 'text-white';
                            $textColor = 'text-blue-900';
                            $lineColor = 'bg-blue-600';
                        }

                        if ($showRedX) {
                            $iconBg = 'bg-red-600 shadow-md ring-4 ring-red-50';
                            $iconColor = 'text-white';
                            $textColor = 'text-red-700 font-bold';
                            $lineColor = 'bg-red-200'; // Line leading TO this is red? No, line FROM previous.
                        }

                    @endphp

                    <div class="flex flex-col items-center flex-1 z-0 relative">
                        <!-- Connection Line Color Overlay -->
                        @if(!$loop->first)
                            @php
                                // Line color logic: If Previous was Active, and This is Active (or Red X), line is colored.
                                // Actually, if This is Active/Red X, the line leading to it should be colored.
                                $prevKey = array_keys($steps)[$stepIndex - 1];
                                // We need to check if previous has date.
                                // Simplification: If $isActive or $showRedX, line is blue (or red).
                                $thisLineColor = 'bg-gray-100';
                                if ($isActive)
                                    $thisLineColor = 'bg-blue-600';
                                if ($showRedX)
                                    $thisLineColor = 'bg-red-200'; // Faint red line to the X
                            @endphp
                            <div class="absolute top-6 right-[50%] w-full h-[4px] -z-10 {{ $thisLineColor }}"></div>
                        @endif

                        <div
                            class="w-12 h-12 rounded-full {{ $iconBg }} {{ $iconColor }} flex items-center justify-center text-lg mb-4 shadow-sm z-10 transition-all duration-300">
                            @if($showRedX)
                                <i class="fas fa-times"></i>
                            @else
                                <i class="fas {{ $step['icon'] }}"></i>
                            @endif
                        </div>

                        <h3 class="font-bold uppercase text-xs tracking-wider {{ $textColor }} mb-1">
                            {{ $showRedX ? 'Đã hủy/Từ chối' : $step['label'] }}
                        </h3>

                        @if($date)
                            <p class="text-[11px] text-gray-500 font-medium">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($date)->format('H:i') }}</p>
                        @elseif($showRedX)
                            @php
                                $rejectDate = $order->updated_at ?? $order->created_at ?? now();
                             @endphp
                            <p class="text-[11px] text-red-500 font-medium">
                                {{ \Carbon\Carbon::parse($rejectDate)->format('d/m/Y') }}
                            </p>
                            <p class="text-[10px] text-red-400">{{ \Carbon\Carbon::parse($rejectDate)->format('H:i') }}</p>
                        @elseif($isActive) <!-- Shouldn't happen if date set, but just in case -->
                            <p class="text-[11px] text-blue-500 italic font-medium mt-1">Đang xử lý...</p>
                        @else
                            <p class="text-[11px] text-gray-300 mt-1">--/--/----</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Info Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- 1. Department -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4 hover:shadow-md transition">
                <div
                    class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="far fa-building"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium mb-0.5">Khoa / Phòng</p>
                    <p class="font-bold text-gray-900 leading-tight">{{ $order->department->department_name ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- 2. Created Date -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4 hover:shadow-md transition">
                <div
                    class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium mb-0.5">Ngày tạo</p>
                    <p class="font-bold text-gray-900 leading-tight">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- 3. Expected Date -->
            <div
                class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4 hover:shadow-md transition">
                <div
                    class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="far fa-clock"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium mb-0.5">Dự kiến giao</p>
                    <p class="font-bold text-gray-900 leading-tight text-orange-600">
                        {{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') : '--/--/----' }}
                    </p>
                </div>
            </div>

            <!-- 4. Total Amount -->
            <div
                class="bg-blue-600 p-6 rounded-2xl shadow-md flex items-center gap-4 text-white hover:bg-blue-700 transition">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <p class="text-xs text-blue-100 uppercase font-medium mb-0.5">Tổng giá trị</p>
                    <p class="font-bold text-xl leading-tight">{{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                </div>
            </div>
        </div>

        <!-- Product List -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                    <i class="fas fa-list text-blue-600"></i> Danh sách sản phẩm & Tiến độ
                </h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold shadow-sm">
                    Tổng: {{ $order->items->count() }} sản phẩm
                </span>
            </div>

            <div class="p-6">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="py-3 font-bold uppercase text-xs tracking-wider">Sản phẩm</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-center">SL</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-right">Đơn giá</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-right">Thành tiền</th>
                            <th class="py-3 font-bold uppercase text-xs tracking-wider text-center">Trạng thái SP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                            <tr class="group hover:bg-gray-50 transition">
                                <td class="py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:shadow-sm transition">
                                            <i class="fas fa-box text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-base">
                                                {{ $item->product->product_name ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->product->category->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center font-bold text-gray-700">
                                    {{ (float) $item->quantity == (int) $item->quantity ? (int) $item->quantity : $item->quantity }}
                                </td>
                                <td class="py-4 text-right text-gray-500 font-medium">
                                    {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 text-right font-bold text-gray-900 text-base">
                                    {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}
                                </td>
                                <td class="py-4 text-center">
                                    @php
                                        $statusClass = get_status_class($item->status);
                                        $statusLabel = get_status_label($item->status);
                                        $dotColor = match ($item->status) {
                                            'ORDERED' => 'text-blue-500',
                                            'DELIVERED' => 'text-emerald-500',
                                            'PAID', 'COMPLETED' => 'text-green-500',
                                            'PENDING' => 'text-gray-400',
                                            'CANCELLED', 'REJECTED' => 'text-red-500',
                                            default => 'text-gray-400'
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                        <i class="fas fa-circle text-[8px] mr-1.5 {{ $dotColor }}"></i> {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Footer Summaries -->
                <div class="mt-8 pt-4 border-t border-gray-100 flex flex-col items-end gap-1">
                    <div class="flex justify-between w-72 text-gray-500 text-sm">
                        <span>Tạm tính:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}
                            đ</span>
                    </div>

                    <div
                        class="flex justify-between w-72 text-blue-600 text-xl font-bold mt-3 pt-3 border-t border-gray-100">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Section (Chat) -->
        @if(in_array($order->status, ['COMPLETED', 'CANCELLED', 'REJECTED']))
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col h-[600px] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-comments text-blue-600"></i> Trao đổi với Khoa phòng
                        @php
                            // Check if conversation is resolved: search for ANY 'RESOLVED' feedback
                            $isResolved = $order->feedbacks()->where('status', 'RESOLVED')->exists();
                        @endphp
                        @if($isResolved)
                            <span
                                class="ml-2 px-2 py-0.5 bg-green-50 text-green-600 rounded text-[10px] font-bold border border-green-100 uppercase">Đã
                                giải quyết</span>
                        @endif
                    </h3>
                </div>

                <!-- Chat Area -->
                <div id="chatMessages" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50/50">
                    <div class="flex justify-center items-center h-full text-gray-400 italic">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm">Đang tải cuộc hội thoại...</p>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                @if(!$isResolved)
                    <div class="p-6 border-t border-gray-100 bg-white">
                        <form id="chatForm" onsubmit="sendMessage(event)" class="space-y-4">
                            <div class="relative">
                                <textarea id="chatInput" rows="3"
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition-all resize-none text-sm font-medium text-gray-700 shadow-inner"
                                    placeholder="Nhập nội dung phản hồi cho Khoa phòng..."></textarea>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <div id="waitMessage"
                                    class="hidden items-center gap-2 text-amber-600 bg-amber-50 px-4 py-2 rounded-xl border border-amber-100 animate-pulse">
                                    <i class="fas fa-clock text-xs"></i>
                                    <span class="text-xs font-bold">Đang chờ Khoa phòng phản hồi...</span>
                                </div>

                                <div class="flex items-center gap-3 ml-auto">
                                    <button type="button" id="resolveBtn" onclick="sendMessage(event, true)"
                                        class="px-6 py-3 bg-green-50 text-green-700 font-bold rounded-xl hover:bg-green-100 transition-all flex items-center gap-2 border border-green-200">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        <span>Đã giải quyết</span>
                                    </button>

                                    <button type="submit" id="sendBtn"
                                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-lg transition-all flex items-center gap-2 group">
                                        <span>Gửi phản hồi</span>
                                        <i
                                            class="fas fa-paper-plane text-xs group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                        <div class="py-4 bg-white border border-gray-100 rounded-2xl text-center shadow-sm">
                            <p class="text-xs font-bold text-gray-400">
                                <i class="fas fa-lock mr-2 text-gray-300"></i> Cuộc hội thoại đã kết thúc và được đánh dấu giải
                                quyết
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            @push('scripts')
                <script>
                    const orderId = {{ $order->id }};
                    let isPolling = true;

                    async function loadMessages() {
                        if (!isPolling) return;
                        try {
                            const response = await fetch("{{ route('buyer.feedback.messages', $order->id) }}");
                            const data = await response.json();
                            if (data.success) {
                                renderMessages(data.messages);
                                updateInputState(data.can_reply, data.is_resolved);
                            }
                        } catch (error) {
                            console.error('Error loading messages:', error);
                        }
                    }

                    function renderMessages(messages) {
                        const container = document.getElementById('chatMessages');
                        if (messages.length === 0) {
                            container.innerHTML = `
                                                                                                <div class="flex flex-col items-center justify-center h-full text-gray-300 italic py-10">
                                                                                                    <i class="fas fa-comments text-5xl mb-4 opacity-20"></i>
                                                                                                    <p class="text-sm">Chưa có phản hồi nào cho đơn hàng này.</p>
                                                                                                </div>`;
                            return;
                        }

                        const isScrolledToBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

                        const html = messages.map(msg => {
                            const isAdmin = msg.type === 'admin';
                            const isMe = msg.is_current_user;

                            const containerClass = isAdmin ? 'flex-row-reverse' : 'flex-row';
                            const bubbleClass = isAdmin ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border border-gray-100 shadow-sm';
                            const nameClass = isAdmin ? 'text-right' : 'text-left';
                            const avatarColor = isAdmin ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500';

                            let ratingHtml = '';
                            if (msg.rating) {
                                ratingHtml = `<div class="flex items-center gap-0.5 mt-1">
                                                                                                    ${Array.from({ length: 5 }, (_, i) => `<i class="fas fa-star text-[8px] ${i < msg.rating ? 'text-yellow-400' : 'text-gray-200'}"></i>`).join('')}
                                                                                                </div>`;
                            }

                            return `
                                                                                                <div class="flex ${containerClass} items-start gap-3 mb-4">
                                                                                                    <div class="w-8 h-8 rounded-full ${avatarColor} flex items-center justify-center text-[10px] font-bold flex-shrink-0 shadow-sm border border-white">
                                                                                                        ${isAdmin ? 'B' : msg.user_name.charAt(0)}
                                                                                                    </div>
                                                                                                    <div class="max-w-[75%]">
                                                                                                        <p class="text-[10px] font-extrabold text-gray-400 mb-1 uppercase tracking-wider ${nameClass}">
                                                                                                            ${isAdmin ? 'Bộ phận Mua hàng' : msg.user_name}
                                                                                                        </p>
                                                                                                        <div class="px-4 py-3 rounded-2xl ${bubbleClass} text-sm font-medium leading-relaxed">
                                                                                                            ${msg.content}
                                                                                                            ${ratingHtml}
                                                                                                        </div>
                                                                                                        <p class="text-[9px] font-bold text-gray-300 mt-1.5 ${nameClass}">
                                                                                                            ${msg.time}
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            `;
                        }).join('');

                        if (container.dataset.lastCount !== messages.length.toString()) {
                            container.innerHTML = html;
                            container.dataset.lastCount = messages.length.toString();
                            if (isScrolledToBottom || !container.dataset.init) {
                                container.scrollTop = container.scrollHeight;
                                container.dataset.init = 'true';
                            }
                        }
                    }

                    function updateInputState(canReply, isResolved) {
                        const input = document.getElementById('chatInput');
                        const sendBtn = document.getElementById('sendBtn');
                        const resolveBtn = document.getElementById('resolveBtn');
                        const waitMessage = document.getElementById('waitMessage');

                        if (isResolved) {
                            // 🔒 khóa input thay vì reload
                            input.disabled = true;
                            sendBtn.disabled = true;
                            resolveBtn.disabled = true;

                            input.placeholder = "Cuộc hội thoại đã được giải quyết";
                            waitMessage.classList.add('hidden');
                            return;
                        }

                        if (canReply) {
                            input.disabled = false;
                            sendBtn.disabled = false;
                            waitMessage.classList.add('hidden');
                        } else {
                            input.disabled = true;
                            sendBtn.disabled = true;
                            waitMessage.classList.remove('hidden');
                        }
                    }


                    async function sendMessage(e, resolve = false) {
                        if (e) e.preventDefault();
                        const input = document.getElementById('chatInput');
                        const btn = resolve ? document.getElementById('resolveBtn') : document.getElementById('sendBtn');
                        const content = input.value.trim();

                        if (!content && !resolve) return;

                        if (resolve && !content) {
                            if (!confirm('Bạn có chắc chắn muốn đánh dấu giải quyết mà không gửi thêm phản hồi?')) return;
                        }

                        input.disabled = true;
                        const sendBtn = document.getElementById('sendBtn');
                        const resolveBtn = document.getElementById('resolveBtn');
                        sendBtn.disabled = true;
                        resolveBtn.disabled = true;

                        const originalHtml = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';

                        try {
                            const storeUrl = "{{ route('buyer.feedback.store', ['orderId' => 0]) }}".replace('/0/', '/' + orderId + '/');
                            const response = await fetch(storeUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    content: content,
                                    resolve: resolve ? 1 : 0
                                })
                            });

                            const data = await response.json();
                            if (data.success) {
                                input.value = '';
                                if (resolve) {
                                    location.reload();
                                } else {
                                    await loadMessages();
                                    document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages').scrollHeight;
                                }
                            } else {
                                alert(data.message || 'Có lỗi xảy ra!');
                            }
                        } catch (error) {
                            console.error(error);
                            alert('Lỗi kết nối!');
                        } finally {
                            const latestCanReply = document.getElementById('waitMessage').classList.contains('hidden');
                            input.disabled = !latestCanReply;
                            sendBtn.disabled = !latestCanReply;
                            resolveBtn.disabled = false;
                            btn.innerHTML = originalHtml;
                            if (!input.disabled) input.focus();
                        }
                    }

                    document.addEventListener('DOMContentLoaded', () => {
                        loadMessages();
                    });
                </script>
            @endpush
        @endif

        <!-- Footer Copyright -->
        <div class="text-center text-xs text-gray-400 mt-8 pb-4">
            &copy; 2026 Hệ thống Quản lý Vật tư Bệnh viện - Professional Healthcare Supply Chain
        </div>
    </div>
@endsection