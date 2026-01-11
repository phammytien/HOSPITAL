@extends('layouts.admin')

@section('title', 'Chi tiết phản hồi')
@section('header_title', 'Chi tiết phản hồi')
@section('page-subtitle', 'Xem và trả lời phản hồi')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <a href="{{ route('admin.feedback') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Feedback Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Feedback Details --}}
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Phản hồi từ {{ $feedback->feedbackBy->full_name ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-500">{{ $feedback->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($feedback->status == 'PENDING')
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Chờ xử lý</span>
                    @else
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Đã giải quyết</span>
                    @endif
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-2">Đánh giá:</p>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= ($feedback->rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }} text-xl"></i>
                        @endfor
                        <span class="ml-2 text-gray-600">({{ $feedback->rating ?? 0 }}/5)</span>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $feedback->feedback_content }}</p>
                </div>
            </div>

            {{-- Admin Response --}}
            @if($feedback->admin_response)
            <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                <h4 class="font-semibold text-blue-900 mb-2">Phản hồi của Admin</h4>
                <p class="text-blue-800 whitespace-pre-wrap">{{ $feedback->admin_response }}</p>
                @if($feedback->response_time)
                <p class="text-sm text-blue-600 mt-2">Trả lời lúc: {{ $feedback->response_time->format('d/m/Y H:i') }}</p>
                @endif
            </div>
            @endif

            {{-- Reply Form --}}
            @if($feedback->status == 'PENDING')
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Trả lời phản hồi</h3>
                <form action="{{ route('admin.feedback.reply', $feedback->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung trả lời *</label>
                        <textarea name="response" rows="5" required 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Nhập nội dung trả lời..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-reply mr-2"></i> Gửi trả lời
                        </button>
                        <button type="button" onclick="document.getElementById('resolveForm').submit()" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i> Đánh dấu đã giải quyết
                        </button>
                    </div>
                </form>

                <form id="resolveForm" action="{{ route('admin.feedback.resolve', $feedback->id) }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
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
                        <p class="font-semibold text-gray-900">{{ $feedback->feedbackBy->role ?? 'N/A' }}</p>
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
