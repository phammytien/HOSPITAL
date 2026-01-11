@extends('layouts.admin')

@section('title', 'Tạo thông báo mới')
@section('header_title', 'Tạo thông báo mới')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tạo thông báo mới</h2>
            <p class="text-gray-600 mt-1">Tạo thông báo hệ thống gửi đến người dùng</p>
        </div>
        <a href="{{ route('admin.notifications') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại
        </a>
    </div>

    {{-- Create Form --}}
    <div class="bg-white rounded-xl p-8 border border-gray-200">
        <form method="POST" action="{{ route('admin.notifications.store') }}">
            @csrf

            {{-- Title --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tiêu đề thông báo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nhập tiêu đề thông báo...">
            </div>

            {{-- Message --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nội dung <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="5" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Nhập nội dung thông báo..."></textarea>
            </div>

            {{-- Type --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Loại thông báo <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-4 gap-4">
                    <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="type" value="info" class="mr-3" checked>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-info-circle text-blue-600"></i>
                                <span class="font-medium">Thông tin</span>
                            </div>
                            <p class="text-xs text-gray-500">Thông báo chung</p>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                        <input type="radio" name="type" value="success" class="mr-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span class="font-medium">Thành công</span>
                            </div>
                            <p class="text-xs text-gray-500">Hoàn thành</p>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition">
                        <input type="radio" name="type" value="warning" class="mr-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-exclamation-triangle text-orange-600"></i>
                                <span class="font-medium">Cảnh báo</span>
                            </div>
                            <p class="text-xs text-gray-500">Lưu ý quan trọng</p>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition">
                        <input type="radio" name="type" value="error" class="mr-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-times-circle text-red-600"></i>
                                <span class="font-medium">Lỗi</span>
                            </div>
                            <p class="text-xs text-gray-500">Khẩn cấp</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Target Role --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Đối tượng nhận <span class="text-red-500">*</span>
                </label>
                <select name="target_role" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Chọn đối tượng nhận</option>
                    <option value="ALL">Tất cả người dùng</option>
                    <option value="ADMIN">Quản trị viên</option>
                    <option value="BUYER">Nhân viên mua hàng</option>
                    <option value="DEPARTMENT">Khoa/Phòng</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.notifications') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Gửi thông báo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
