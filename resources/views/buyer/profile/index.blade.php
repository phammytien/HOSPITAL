@extends('layouts.buyer')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Header / Banner -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-300"></div>
            <div class="px-8 pb-8 flex flex-col md:flex-row items-center md:items-end -mt-12 gap-6">
                <!-- Avatar -->
                <div
                    class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-teal-700 flex items-center justify-center overflow-hidden">
                    @if($user->avatar)
                        @php
                            $avatarPath = str_starts_with($user->avatar, 'images/') ? asset($user->avatar) : asset('storage/' . $user->avatar);
                        @endphp
                        <img src="{{ $avatarPath }}" alt="Avatar" class="w-full h-full object-cover">
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
                            class="font-semibold text-blue-600">{{ $user->code ?? 'BUYER-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
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
                        <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-bold font-mono">CHUYÊN
                            VIÊN MUA SẮM</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">PHÒNG
                            BAN</label>
                        <p class="text-gray-800 font-medium">Phòng Vật tư</p>
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
                    <div class="flex gap-4 mb-6">
                        <div class="flex-1 text-center">
                            <h4 class="text-3xl font-bold text-blue-600">{{ $workScope['total_requests'] ?? 0 }}</h4>
                            <p class="text-xs uppercase font-bold text-blue-400 mt-1">Yêu cầu</p>
                        </div>
                        <div class="flex-1 text-center">
                            @php
                                $totalReqs = $workScope['total_requests'] ?? 1;
                                $completedReqs = $totalReqs - ($workScope['pending_requests'] ?? 0);
                                $percentage = $totalReqs > 0 ? round(($completedReqs / $totalReqs) * 100) : 0;
                            @endphp
                            <h4 class="text-3xl font-bold text-purple-600">{{ $percentage }}%</h4>
                            <p class="text-xs uppercase font-bold text-purple-400 mt-1">Hiệu suất</p>
                        </div>
                    </div>

                    <h4 class="text-sm font-bold text-gray-700 mb-3">Quyền hạn hệ thống</h4>
                    <ul class="space-y-2">
                        @foreach($workScope['permissions'] ?? [] as $permission)
                            <li class="flex items-start text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                {{ $permission }}
                            </li>
                        @endforeach
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
            <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="flex justify-center mb-4">
                        <div
                            class="w-24 h-24 rounded-full border-4 border-gray-100 shadow-sm overflow-hidden bg-gray-100 relative group">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <img src="https://img.freepik.com/free-vector/doctor-character-background_1270-84.jpg"
                                    class="w-full h-full object-cover opacity-80">
                            @endif
                            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 cursor-pointer"
                                onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-camera text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="hidden">
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" class="w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
                        <input type="file" name="avatar"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" name="full_name" value="{{ $user->full_name }}"
                            class="w-full px-4 py-3 text-base font-medium rounded-lg border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                        <input type="text" name="phone_number" value="{{ $user->phone_number }}"
                            class="w-full px-4 py-3 text-base font-medium rounded-lg border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
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
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-key mr-3 text-xl"></i> Đổi mật khẩu
                </h3>
                <p class="text-blue-100 text-sm mt-1">Vui lòng nhập thông tin để thay đổi mật khẩu</p>
            </div>

            <form action="{{ route('buyer.profile.password') }}" method="POST" autocomplete="off" class="p-8">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-2"></i>Mật khẩu hiện tại
                        </label>
                        <div class="relative">
                            <input type="password" id="currentPassword" name="current_password" required
                                class="w-full px-5 py-4 pr-12 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('current_password') border-red-500 @enderror"
                                placeholder="Nhập mật khẩu hiện tại">
                            <button type="button" onclick="togglePasswordVisibility('currentPassword', 'currentPasswordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <i id="currentPasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-key text-gray-400 mr-2"></i>Mật khẩu mới
                        </label>
                        <div class="relative">
                            <input type="password" id="newPassword" name="new_password" required
                                class="w-full px-5 py-4 pr-12 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('new_password') border-red-500 @enderror"
                                placeholder="Nhập mật khẩu mới">
                            <button type="button" onclick="togglePasswordVisibility('newPassword', 'newPasswordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <i id="newPasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>Tối thiểu 6 ký tự
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-check-circle text-gray-400 mr-2"></i>Xác nhận mật khẩu mới
                        </label>
                        <div class="relative">
                            <input type="password" id="confirmPassword" name="new_password_confirmation" required
                                class="w-full px-5 py-4 pr-12 text-base rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Nhập lại mật khẩu mới">
                            <button type="button" onclick="togglePasswordVisibility('confirmPassword', 'confirmPasswordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <i id="confirmPasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('changePassModal').classList.add('hidden')"
                        class="px-8 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition text-base">Hủy</button>
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 font-medium transition shadow-lg hover:shadow-xl text-base">
                        <i class="fas fa-check mr-2"></i>Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Close modal if password change was successful
        @if(session('success') && str_contains(session('success'), 'mật khẩu'))
            document.getElementById('changePassModal').classList.add('hidden');
        @endif
    </script>
@endsection