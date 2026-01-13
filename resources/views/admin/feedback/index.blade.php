@extends('layouts.admin')

@section('title', 'Quản lý phản hồi')
@section('header_title', 'Quản lý phản hồi')
@section('page-subtitle', 'Xem và trả lời phản hồi từ người dùng')

@section('content')
    <div class="space-y-6">
        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tổng phản hồi</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Chờ xử lý</p>
                        <h3 class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Đã giải quyết</p>
                        <h3 class="text-2xl font-bold text-green-600">{{ number_format($stats['resolved']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <form method="GET" action="{{ route('admin.feedback') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nội dung phản hồi..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>Đã giải quyết
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i> Lọc
                    </button>
                </div>
            </form>
        </div>

        {{-- Feedback List --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Người gửi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nội dung</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Đánh giá</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày gửi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($feedbacks as $feedback)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $feedback->feedbackBy->full_name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $feedback->feedbackBy->email ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900 line-clamp-2">{{ Str::limit($feedback->feedback_content, 100) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= ($feedback->rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-600">
                                        {{ $feedback->created_at ? $feedback->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($feedback->status == 'PENDING')
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Chờ
                                            xử lý</span>
                                    @else
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Hoàn
                                            thành</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.feedback.show', $feedback->id) }}"
                                        class="text-blue-600 hover:text-blue-700 font-medium">
                                        <i class="fas fa-eye mr-1"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-comment-slash text-4xl mb-3 text-gray-300"></i>
                                    <p>Chưa có phản hồi nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($feedbacks->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection