@extends('layouts.department')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Header / Banner -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-300"></div>
            <div class="px-8 pb-8 flex flex-col md:flex-row items-center md:items-end -mt-12 gap-6">
                <!-- Avatar -->
                <div
                    class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-teal-700 flex items-center justify-center overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <!-- Medical Illusion Avatar -->
                        <img src="https://img.freepik.com/free-vector/doctor-character-background_1270-84.jpg"
                            alt="Default Avatar" class="w-full h-full object-cover opacity-90">
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 text-center md:text-left mb-2">
                    <h2 class="text-3xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                    <p class="text-gray-500"><i class="fas fa-id-badge mr-1"></i> Mã nhân viên: <span
                            class="font-semibold text-blue-600">{{ $user->code ?? 'NV-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 mb-2">
                    <button onclick="document.getElementById('editInfoModal').classList.remove('hidden')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-pen mr-2"></i> Chỉnh sửa thông tin
                    </button>
                    <button onclick="document.getElementById('changePassModal').classList.remove('hidden')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i class="fas fa-key mr-2"></i> Đổi mật khẩu
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Info Card -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Thông tin chi tiết</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">VAI
                            TRÒ</label>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold font-mono">QUẢN TRỊ
                            KHOA</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">KHOA PHÒNG
                            TRỰC THUỘC</label>
                        <p class="text-gray-800 font-medium">{{ $user->department->department_name ?? 'Chưa cập nhật' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">ĐỊA CHỈ
                            EMAIL</label>
                        <p class="text-gray-800 font-medium flex items-center">
                            <i class="fas fa-envelope text-gray-300 mr-2"></i> {{ $user->email }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">SỐ ĐIỆN
                            THOẠI</label>
                        <p class="text-gray-800 font-medium flex items-center">
                            <i class="fas fa-phone text-gray-300 mr-2"></i> {{ $user->phone_number ?? 'Chưa cập nhật' }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-600">Trạng thái tài khoản</span>
                        <span class="flex h-2 w-2 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                    </div>
                    <p class="text-green-600 font-bold text-sm"><i class="fas fa-check-circle mr-1"></i> Đang hoạt động</p>
                    <p class="text-xs text-gray-400 mt-1">Đăng nhập lần cuối: {{ now()->format('H:i A - d/m/Y') }}</p>
                </div>
            </div>

            <!-- Right Activity -->
            <div class="space-y-6">
                <!-- Work Scope -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Phạm vi công việc</h3>
                    <div class="flex gap-4">
                        <div class="flex-1 bg-blue-50 p-3 rounded-lg text-center">
                            <h4 class="text-2xl font-bold text-blue-600">12</h4>
                            <p class="text-[10px] uppercase font-bold text-blue-400">Dự án quản lý</p>
                        </div>
                        <div class="flex-1 bg-purple-50 p-3 rounded-lg text-center">
                            <h4 class="text-2xl font-bold text-purple-600">85%</h4>
                            <p class="text-[10px] uppercase font-bold text-purple-400">Hiệu suất tháng</p>
                        </div>
                    </div>

                    <h4 class="text-sm font-bold text-gray-700 mt-6 mb-3">Quyền hạn hệ thống</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            Phê duyệt yêu cầu mua hàng (Tất cả mức giá)
                        </li>
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            Quản lý danh mục vật tư & Kho hàng
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Edit Info Modal -->
    <div id="editInfoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Chỉnh sửa thông tin</h3>
            <form action="{{ route('department.profile.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="full_name" value="{{ $user->full_name }}"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone_number" value="{{ $user->phone_number }}"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editInfoModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu thay
                        đổi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePassModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Đổi mật khẩu</h3>
            <form action="{{ route('department.profile.password') }}" method="POST" autocomplete="off">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                        <input type="password" name="new_password" required
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('changePassModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Đổi mật
                        khẩu</button>
                </div>
            </form>
        </div>
    </div>
@endsection