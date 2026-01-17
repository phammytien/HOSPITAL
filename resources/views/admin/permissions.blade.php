@extends('layouts.admin')

@section('title', 'Quản lý phân quyền')
@section('page-title', 'Quản lý phân quyền')


@section('content')
<div class="space-y-6">

    <!-- Role Statistics - Smaller Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- ADMIN -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-4 cursor-pointer hover:shadow-md transition" onclick="showRoleInfo('ADMIN')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Quản trị viên</p>
                    <h4 class="text-2xl font-bold text-blue-600">{{ $roleStats['ADMIN'] }}</h4>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Toàn quyền quản lý hệ thống</p>
            <button class="mt-3 text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center">
                <i class="fas fa-info-circle mr-1"></i> Xem chi tiết
            </button>
        </div>

        <!-- BUYER -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-4 cursor-pointer hover:shadow-md transition" onclick="showRoleInfo('BUYER')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Nhân viên mua hàng</p>
                    <h4 class="text-2xl font-bold text-green-600">{{ $roleStats['BUYER'] }}</h4>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Quản lý mua hàng và phê duyệt</p>
            <button class="mt-3 text-xs text-green-600 hover:text-green-800 font-medium flex items-center">
                <i class="fas fa-info-circle mr-1"></i> Xem chi tiết
            </button>
        </div>

        <!-- DEPARTMENT -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-purple-500 p-4 cursor-pointer hover:shadow-md transition" onclick="showRoleInfo('DEPARTMENT')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Nhân viên khoa phòng</p>
                    <h4 class="text-2xl font-bold text-purple-600">{{ $roleStats['DEPARTMENT'] }}</h4>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hospital text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Tạo yêu cầu mua hàng</p>
            <button class="mt-3 text-xs text-purple-600 hover:text-purple-800 font-medium flex items-center">
                <i class="fas fa-info-circle mr-1"></i> Xem chi tiết
            </button>
        </div>
    </div>

    <!-- Users by Role -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">Danh sách người dùng theo vai trò</h4>
            <p class="text-sm text-gray-600 mt-1">Thay đổi vai trò hoặc trạng thái của người dùng</p>
        </div>
        
        <div class="p-6">
            @if($users->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500">Chưa có người dùng nào trong hệ thống</p>
                </div>
            @else
                @foreach(['ADMIN', 'BUYER', 'DEPARTMENT'] as $role)
                    @php
                        $roleUsers = $usersByRole[$role] ?? collect();
                        $roleColor = $role === 'ADMIN' ? 'blue' : ($role === 'BUYER' ? 'green' : 'purple');
                        $roleIcon = $role === 'ADMIN' ? 'fa-user-shield' : ($role === 'BUYER' ? 'fa-shopping-cart' : 'fa-hospital');
                        $roleName = $role === 'ADMIN' ? 'Quản trị viên' : ($role === 'BUYER' ? 'Nhân viên mua hàng' : 'Nhân viên khoa phòng');
                    @endphp
                    
                    @if($roleUsers->isNotEmpty())
                        <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                            <!-- Collapsible Header -->
                            <button 
                                onclick="toggleRoleSection('{{ $role }}')"
                                class="w-full px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex items-center justify-between"
                            >
                                <div class="flex items-center">
                                    <i class="fas {{ $roleIcon }} text-{{ $roleColor }}-500 mr-3 text-lg"></i>
                                    <span class="font-semibold text-gray-800">{{ $roleName }}</span>
                                    <span class="ml-3 text-sm text-gray-500">({{ $roleUsers->count() }} người)</span>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform" id="chevron-{{ $role }}"></i>
                            </button>
                            
                            <!-- Collapsible Content -->
                            <div id="section-{{ $role }}" class="role-section hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên người dùng</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email / Liên hệ</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khoa phòng</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($roleUsers as $user)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center">
                                                            <div class="w-10 h-10 bg-{{ $roleColor }}-100 rounded-full flex items-center justify-center text-{{ $roleColor }}-600 font-semibold mr-3">
                                                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                                                <div class="text-xs text-gray-500">ID: {{ $user->username }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                                        <div class="text-xs text-gray-500">{{ $user->phone_number ?? '-' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-700">
                                                        {{ $user->department ? $user->department->name : '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            <i class="fas fa-circle text-xs mr-1"></i>
                                                            {{ $user->is_active ? 'Đang hoạt động' : 'Đã khóa' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <button 
                                                                onclick="openRoleModal({{ $user->id }}, '{{ $user->full_name }}', '{{ $user->role }}')"
                                                                class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded transition"
                                                                title="Thay đổi vai trò"
                                                            >
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                            <button 
                                                                onclick="openLockModal({{ $user->id }}, '{{ $user->full_name }}', {{ $user->is_active ? 'true' : 'false' }})"
                                                                class="text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-50 rounded transition"
                                                                title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                                                            >
                                                                <i class="fas fa-{{ $user->is_active ? 'lock' : 'lock-open' }}"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Role Info Modal -->
<div id="roleInfoModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="roleInfoTitle"></h3>
            <button onclick="closeRoleInfoModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="roleInfoContent" class="space-y-4">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div id="roleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Thay đổi vai trò</h3>
            <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="roleForm" onsubmit="updateUserRole(event)">
            <input type="hidden" id="userId" name="user_id">
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Người dùng:</p>
                <p class="text-lg font-semibold text-gray-900" id="userName"></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò hiện tại:</label>
                <div id="currentRole" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium"></div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Chọn vai trò mới:</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="role" value="ADMIN" class="form-radio h-5 w-5 text-blue-600">
                        <div class="ml-3">
                            <div class="flex items-center">
                                <i class="fas fa-user-shield text-blue-500 mr-2"></i>
                                <span class="font-medium text-gray-900">Quản trị viên</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Toàn quyền quản lý hệ thống</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition">
                        <input type="radio" name="role" value="BUYER" class="form-radio h-5 w-5 text-green-600">
                        <div class="ml-3">
                            <div class="flex items-center">
                                <i class="fas fa-shopping-cart text-green-500 mr-2"></i>
                                <span class="font-medium text-gray-900">Nhân viên mua hàng</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Quản lý mua hàng và phê duyệt</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <input type="radio" name="role" value="DEPARTMENT" class="form-radio h-5 w-5 text-purple-600">
                        <div class="ml-3">
                            <div class="flex items-center">
                                <i class="fas fa-hospital text-purple-500 mr-2"></i>
                                <span class="font-medium text-gray-900">Nhân viên khoa phòng</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Tạo yêu cầu mua hàng</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button 
                    type="button" 
                    onclick="closeRoleModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Hủy
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lock/Unlock Confirmation Modal -->
<div id="lockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-8 border w-full max-w-md shadow-2xl rounded-xl bg-white mx-4">
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4" id="lockIconContainer">
                <i class="text-4xl" id="lockIcon"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-900 mb-2" id="lockTitle"></h3>
            
            <!-- Message -->
            <p class="text-sm text-gray-600 mb-6" id="lockMessage"></p>
            
            <!-- User Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-xs text-gray-500 mb-1">Tài khoản:</p>
                <p class="text-base font-semibold text-gray-900" id="lockUserName"></p>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-3">
                <button 
                    type="button" 
                    onclick="closeLockModal()" 
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                >
                    Hủy bỏ
                </button>
                <button 
                    type="button" 
                    onclick="confirmLockUnlock()" 
                    class="flex-1 px-4 py-3 text-white rounded-lg transition font-medium"
                    id="lockConfirmBtn"
                >
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const roleCapabilities = @json($roleCapabilities);

    // Show role info modal
    function showRoleInfo(role) {
        const roleInfo = {
            'ADMIN': {
                title: 'Quản trị viên',
                icon: 'fa-user-shield',
                color: 'blue'
            },
            'BUYER': {
                title: 'Nhân viên mua hàng',
                icon: 'fa-shopping-cart',
                color: 'green'
            },
            'DEPARTMENT': {
                title: 'Nhân viên khoa phòng',
                icon: 'fa-hospital',
                color: 'purple'
            }
        };

        const info = roleInfo[role];
        const capabilities = roleCapabilities[role];

        document.getElementById('roleInfoTitle').innerHTML = `
            <i class="fas ${info.icon} text-${info.color}-500 mr-2"></i>${info.title}
        `;

        let capabilitiesHtml = '<ul class="space-y-2">';
        capabilities.forEach(capability => {
            capabilitiesHtml += `
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-${info.color}-500 mt-1 mr-3"></i>
                    <span class="text-gray-700">${capability}</span>
                </li>
            `;
        });
        capabilitiesHtml += '</ul>';

        document.getElementById('roleInfoContent').innerHTML = capabilitiesHtml;
        document.getElementById('roleInfoModal').classList.remove('hidden');
    }

    // Close role info modal
    function closeRoleInfoModal() {
        document.getElementById('roleInfoModal').classList.add('hidden');
    }

    // Open role change modal
    function openRoleModal(userId, userName, currentRole) {
        document.getElementById('userId').value = userId;
        document.getElementById('userName').textContent = userName;
        
        // Display current role
        const roleIcons = {
            'ADMIN': '<i class="fas fa-user-shield text-blue-500 mr-2"></i>Quản trị viên',
            'BUYER': '<i class="fas fa-shopping-cart text-green-500 mr-2"></i>Nhân viên mua hàng',
            'DEPARTMENT': '<i class="fas fa-hospital text-purple-500 mr-2"></i>Nhân viên khoa phòng'
        };
        document.getElementById('currentRole').innerHTML = roleIcons[currentRole];
        
        // Pre-select current role
        document.querySelector(`input[name="role"][value="${currentRole}"]`).checked = true;
        
        document.getElementById('roleModal').classList.remove('hidden');
    }

    // Close role modal
    function closeRoleModal() {
        document.getElementById('roleModal').classList.add('hidden');
    }

    // Update user role
    async function updateUserRole(event) {
        event.preventDefault();
        
        const userId = document.getElementById('userId').value;
        const formData = new FormData(event.target);
        const newRole = formData.get('role');

        try {
            const response = await fetch(`/admin/permissions/users/${userId}/role`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ role: newRole })
            });

            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                closeRoleModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi cập nhật vai trò', 'error');
        }
    }

    // Toggle role section (collapsible)
    function toggleRoleSection(role) {
        const allRoles = ['ADMIN', 'BUYER', 'DEPARTMENT'];
        const section = document.getElementById(`section-${role}`);
        const chevron = document.getElementById(`chevron-${role}`);
        
        // Check if we are expanding
        const isExpanding = section.classList.contains('hidden');

        // Close all sections first
        allRoles.forEach(r => {
            if (r !== role) {
                const s = document.getElementById(`section-${r}`);
                const c = document.getElementById(`chevron-${r}`);
                if (s) s.classList.add('hidden');
                if (c) c.classList.remove('rotate-180');
            }
        });

        // Toggle current section
        if (isExpanding) {
            section.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            section.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    // Open lock/unlock modal
    let currentLockUserId = null;
    let currentLockStatus = null;

    function openLockModal(userId, userName, isActive) {
        currentLockUserId = userId;
        currentLockStatus = isActive;
        
        const modal = document.getElementById('lockModal');
        const iconContainer = document.getElementById('lockIconContainer');
        const icon = document.getElementById('lockIcon');
        const title = document.getElementById('lockTitle');
        const message = document.getElementById('lockMessage');
        const userNameEl = document.getElementById('lockUserName');
        const confirmBtn = document.getElementById('lockConfirmBtn');
        
        userNameEl.textContent = userName;
        
        if (isActive) {
            // Locking account
            iconContainer.className = 'mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4 bg-red-100';
            icon.className = 'text-4xl fas fa-lock text-red-600';
            title.textContent = 'Khóa tài khoản';
            message.textContent = 'Bạn có chắc chắn muốn khóa tài khoản này? Người dùng sẽ không thể đăng nhập vào hệ thống.';
            confirmBtn.className = 'flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium';
            confirmBtn.innerHTML = '<i class="fas fa-lock mr-2"></i>Khóa tài khoản';
        } else {
            // Unlocking account
            iconContainer.className = 'mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4 bg-green-100';
            icon.className = 'text-4xl fas fa-lock-open text-green-600';
            title.textContent = 'Mở khóa tài khoản';
            message.textContent = 'Bạn có chắc chắn muốn mở khóa tài khoản này? Người dùng sẽ có thể đăng nhập vào hệ thống.';
            confirmBtn.className = 'flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium';
            confirmBtn.innerHTML = '<i class="fas fa-lock-open mr-2"></i>Mở khóa';
        }
        
        modal.classList.remove('hidden');
    }

    // Close lock modal
    function closeLockModal() {
        document.getElementById('lockModal').classList.add('hidden');
        currentLockUserId = null;
        currentLockStatus = null;
    }

    // Confirm lock/unlock
    async function confirmLockUnlock() {
        if (!currentLockUserId) return;
        
        try {
            const response = await fetch(`/admin/permissions/users/${currentLockUserId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                closeLockModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Có lỗi xảy ra', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi thay đổi trạng thái', 'error');
        }
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRoleModal();
            closeRoleInfoModal();
            closeLockModal();
        }
    });
</script>
@endpush
@endsection
