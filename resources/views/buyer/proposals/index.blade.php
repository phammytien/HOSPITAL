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
                                    <a href="{{ route('admin.products.index') }}" class="text-xs text-green-600 hover:underline">
                                        <i class="fas fa-check-circle mr-1"></i> Xem sản phẩm
                                    </a>
                                @elseif($proposal->status == 'REJECTED')
                                    <span class="text-xs text-red-600" title="{{ $proposal->rejection_reason }}">
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
@endsection