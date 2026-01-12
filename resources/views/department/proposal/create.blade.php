@extends('layouts.department')

@section('title', 'Đề xuất vật tư mới')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('department.products.index') }}" class="text-gray-500 hover:text-blue-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh mục
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Đề xuất thêm vật tư mới</h2>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Vật tư được đề xuất sẽ được gắn mã tạm thời là <span class="font-bold">SUGGEST</span>.
                            Sau khi được phê duyệt, mã chính thức sẽ được cập nhật (ví dụ: YT004).
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('department.proposal.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên vật tư <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ví dụ: Máy đo huyết áp điện tử">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="unit" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ví dụ: Cái, Hộp">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá dự kiến (VNĐ)</label>
                            <input type="number" name="estimated_price"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ví dụ: 1500000">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả / Ghi chú</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Mô tả công dụng, thông số kỹ thuật..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('department.products.index') }}"
                            class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">Hủy
                            bỏ</a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi đề xuất
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection