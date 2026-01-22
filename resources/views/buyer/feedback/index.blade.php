@extends('layouts.buyer')

@section('title', 'Quản lý phản hồi')
@section('header_title', 'Quản lý phản hồi')

@section('content')
    <div class="space-y-6">
        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Tổng phản hồi</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-comments text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Chờ xử lý</p>
                        <h3 class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Đã giải quyết</p>
                        <h3 class="text-2xl font-bold text-green-600">{{ number_format($stats['resolved']) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Tabs --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="flex border-b border-gray-100">
                <a href="{{ route('buyer.feedback.index') }}" 
                   class="flex-1 px-6 py-4 text-center font-bold text-sm transition {{ !request('status') ? 'bg-blue-50 text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:bg-gray-50' }}">
                    <i class="fas fa-list-ul mr-2"></i>
                    Tất cả
                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">{{ $stats['total'] }}</span>
                </a>
                <a href="{{ route('buyer.feedback.index', ['status' => 'PENDING']) }}" 
                   class="flex-1 px-6 py-4 text-center font-bold text-sm transition {{ request('status') == 'PENDING' ? 'bg-orange-50 text-orange-600 border-b-2 border-orange-600' : 'text-gray-500 hover:bg-gray-50' }}">
                    <i class="fas fa-hourglass-start mr-2"></i>
                    Chờ xử lý
                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">{{ $stats['pending'] }}</span>
                </a>
                <a href="{{ route('buyer.feedback.index', ['status' => 'RESOLVED']) }}" 
                   class="flex-1 px-6 py-4 text-center font-bold text-sm transition {{ request('status') == 'RESOLVED' ? 'bg-green-50 text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:bg-gray-50' }}">
                    <i class="fas fa-check-double mr-2"></i>
                    Đã giải quyết
                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $stats['resolved'] }}</span>
                </a>
            </div>
        </div>

        {{-- Filters & Content --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-white">
                <form method="GET" action="{{ route('buyer.feedback.index') }}" id="filterForm" class="flex flex-wrap items-center gap-5">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <div class="text-[12px] font-bold text-gray-400 uppercase tracking-widest">Khoa/Phòng:</div>
                    <div class="flex-grow max-w-[300px]">
                        <select name="department_id" id="departmentFilter"
                            class="w-full border-gray-100 shadow-sm bg-white rounded-xl text-sm font-semibold text-gray-700 focus:border-blue-400 focus:ring-blue-100 hover:border-gray-200 transition-all cursor-pointer py-2.5 px-4">
                            <option value="">Tất cả khoa/phòng</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request('department_id'))
                        <button type="button" onclick="clearFilters()"
                            class="px-8 py-2.5 bg-white border border-gray-100 rounded-xl text-sm font-extrabold text-red-600 hover:bg-red-50 hover:border-red-100 transition-all shadow-sm">
                            <i class="fas fa-times mr-2"></i> Xóa lọc
                        </button>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
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
                        @forelse($feedbacks as $feedback)
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
                </table>
            </div>

            @if($feedbacks->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>

@push('scripts')
<script>
function clearFilters() {
    window.location.href = '{{ route('buyer.feedback.index') }}';
}

document.getElementById('departmentFilter')?.addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
</script>
@endpush
@endsection
