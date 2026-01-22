@extends('layouts.admin')

@section('title', 'Chi tiết phản hồi')
@section('header_title', 'Chi tiết phản hồi')
@section('page-subtitle', 'Xem và trả lời phản hồi')

@section('content')
    @php $feedback = $currentFeedback; @endphp
    <div class="space-y-6">
        {{-- Back Button --}}
        <a href="{{ route('admin.feedback') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Sidebar (Moved to Left) --}}
            <div class="lg:col-span-2 space-y-6">
                @foreach($feedbacks as $msg)
                    {{-- User Message Block --}}
                    <div
                        class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm {{ $msg->status == 'PENDING' ? 'border-l-4 border-l-yellow-400' : '' }}">

                        {{-- Header: Name and Time on same line --}}
                        <div class="flex items-center gap-3 mb-4 border-b border-gray-100 pb-2">
                            <span class="font-bold text-gray-900">{{ $msg->feedbackBy->full_name ?? 'N/A' }}</span>
                            <span class="text-xs text-gray-500">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                            <div class="ml-auto">
                                @if($msg->status == 'PENDING')
                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Chờ xử
                                        lý</span>
                                @elseif($msg->status == 'RESOLVED')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Đã giải
                                        quyết</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            @if($msg->rating)
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm text-gray-500">Đánh giá:</span>
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $msg->rating ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $msg->feedback_content }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Admin Response Block (Separated) --}}
                    @if($msg->admin_response)
                        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200 mt-2 mb-6 ml-6">
                            <div class="flex items-center gap-3 mb-2 border-b border-blue-100 pb-2">
                                <span class="font-bold text-blue-900"><i class="fas fa-user-shield mr-1"></i> Admin</span>
                                @if($msg->response_time)
                                    <span class="text-xs text-blue-600">
                                        {{ \Carbon\Carbon::parse($msg->response_time)->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-blue-800 whitespace-pre-wrap leading-relaxed">{{ $msg->admin_response }}</p>
                        </div>
                    @endif
                @endforeach

                {{-- Reply / Action Area --}}
                @if($replyTarget)
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-lg mt-8" id="replySection">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-edit text-blue-600"></i> Trả lời phản hồi
                            </h3>
                            @if($replyTarget->admin_response)
                                <div
                                    class="flex items-center gap-2 text-amber-600 bg-amber-50 px-4 py-1.5 rounded-full border border-amber-100 animate-pulse">
                                    <i class="fas fa-clock text-xs"></i>
                                    <span class="text-xs font-bold">Đang chờ Khoa phòng phản hồi...</span>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('admin.feedback.reply', $replyTarget->id) }}" method="POST" id="replyForm">
                            @csrf
                            <input type="hidden" name="resolve" id="resolveFlag" value="0">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nội dung trả lời
                                    (@if($replyTarget->feedbackBy){{ $replyTarget->feedbackBy->last_name }}@endif vừa nhắn)
                                </label>
                                <textarea name="response" id="adminResponseText" rows="5" {{ $replyTarget->admin_response ? 'disabled' : 'autofocus' }}
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $replyTarget->admin_response ? 'bg-gray-50' : '' }}"
                                    placeholder="{{ $replyTarget->admin_response ? 'Vui lòng đợi Khoa phòng phản hồi tin nhắn trước đó...' : 'Nhập nội dung trả lời (không bắt buộc nếu chỉ muốn đánh dấu giải quyết)...' }}"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" {{ $replyTarget->admin_response ? 'disabled' : '' }}
                                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md flex items-center {{ $replyTarget->admin_response ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <i class="fas fa-reply mr-2"></i> Gửi trả lời
                                </button>
                                <button type="button" onclick="submitResolve()"
                                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow-md flex items-center border border-transparent">
                                    <i class="fas fa-check mr-2"></i> Đánh dấu đã giải quyết
                                </button>
                            </div>
                        </form>
                    </div>

                    @push('scripts')
                        <script>
                            function submitResolve() {
                                document.getElementById('resolveFlag').value = '1';
                                document.getElementById('replyForm').submit();
                            }
                        </script>
                    @endpush
                @endif
            </div>
            <div class="space-y-6">
                {{-- User Info --}}
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin người gửi</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Họ tên</p>
                            <p class="font-semibold text-gray-900">{{ $feedback->feedbackBy->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Email</p>
                            <p class="font-semibold text-gray-900">{{ $feedback->feedbackBy->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Vai trò</p>
                            <p class="font-semibold text-gray-900">
                                {{ ($feedback->feedbackBy->role ?? '') === 'DEPARTMENT' ? 'Khoa/Phòng' : ($feedback->feedbackBy->role ?? 'N/A') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Order Info --}}
                @if($feedback->purchaseOrder)
                    <div class="bg-white rounded-xl p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Đơn hàng liên quan</h3>
                        <div class="space-y-2 text-sm">
                            <p class="font-semibold text-gray-900">{{ $feedback->purchaseOrder->order_code }}</p>
                            <a href="{{ route('admin.orders.show', $feedback->purchaseOrder->id) }}"
                                class="text-blue-600 hover:text-blue-700 inline-flex items-center">
                                <i class="fas fa-external-link-alt mr-1"></i> Xem đơn hàng
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                @if($feedback->status == 'RESOLVED')
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-600 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-green-900 mb-1">Đã giải quyết</h4>
                                <p class="text-sm text-green-700">
                                    Phản hồi này đã được đánh dấu là đã giải quyết.
                                </p>
                                @if($feedback->resolved_at)
                                    <p class="text-sm text-green-600 mt-2">{{ $feedback->resolved_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>



        </div>
    </div>
@endsection