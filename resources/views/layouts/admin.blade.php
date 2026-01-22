<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý mua sắm nội bộ') - Hệ thống Bệnh viện</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-link {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover {
            background: rgba(59, 130, 246, 0.05);
            border-left-color: rgba(59, 130, 246, 0.3);
        }
        .sidebar-link.active {
            background: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
            color: #3b82f6;
            font-weight: 500;
        }
        .sidebar-link.active i {
            color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen overflow-hidden flex flex-col">
    <!-- Top Header -->
    <header class="bg-white border-b border-gray-200 flex-none">
        <div class="px-6 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-4">
                    <img src="https://bvtamtricaolanh.com.vn/vnt_upload/weblink/logo-tam-tri-02.png" alt="Logo" class="h-14 w-14 object-contain">

                    <div>
                        <h1 class="text-lg font-bold text-blue-600">TÂM TRÍ - CAO LÃNH</h1>
                        <p class="text-xs text-gray-600">Bệnh viện Đa khoa</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <!-- <div class="hidden md:flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Hotline tổng đài</p>
                            <p class="font-semibold text-gray-800">02773 878 115</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Hotline tư vấn</p>
                            <p class="font-semibold text-gray-800">0942 762 115</p>
                        </div>
                    </div>
                </div> -->

               <!-- User Menu -->
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <button onclick="document.getElementById('adminNotificationDropdown').classList.toggle('hidden')" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg focus:outline-none">
                            <i class="fas fa-bell text-lg"></i>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 border-2 border-white">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div id="adminNotificationDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all">
                            <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center bg-white">
                                <h3 class="font-bold text-gray-800 text-base">Thông báo</h3>
                                <button onclick="markAllNotificationsAsRead()" class="text-xs text-blue-600 hover:text-blue-700 font-medium hover:underline">Đánh dấu tất cả đã đọc</button>
                            </div>
                            
                            <div class="max-h-[400px] overflow-y-auto">
                                @if(isset($notifications))
                                    @forelse($notifications as $notify)
                                        @php 
                                            $data = $notify->data ?? [];
                                            $iconClass = match($notify->type) {
                                                'success' => 'bg-green-100 text-green-600',
                                                'error', 'danger' => 'bg-red-100 text-red-600',
                                                'warning' => 'bg-yellow-100 text-yellow-600',
                                                default => 'bg-blue-100 text-blue-600'
                                            };
                                            $icon = match($notify->type) {
                                                'success' => 'fa-check',
                                                'error', 'danger' => 'fa-times',
                                                'warning' => 'fa-exclamation-triangle',
                                                default => 'fa-info'
                                            };
                                        @endphp
                                        <div onclick="showNotificationDetail({{ $notify->id }}, {{ Js::from($notify->title) }}, {{ Js::from(strip_tags($notify->message)) }}, '{{ $notify->type }}', {{ $notify->is_read ? 'true' : 'false' }})" 
                                             class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer relative group flex gap-4 {{ $notify->read_at ? 'opacity-70' : '' }}">
                                             <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $iconClass }} flex items-center justify-center">
                                                 <i class="fas {{ $icon }}"></i>
                                             </div>
                                             <div class="flex-1">
                                                 <h4 class="text-sm font-bold text-gray-800 mb-0.5">{{ $notify->title }}</h4>
                                                 <p class="text-xs text-gray-600 leading-snug mb-1 line-clamp-2">
                                                    {!! $notify->message !!}
                                                 </p>
                                                 <p class="text-[10px] text-gray-400 font-medium">{{ $notify->created_at->diffForHumans() }}</p>
                                             </div>
                                             @if(!$notify->read_at)
                                                <div class="absolute right-3 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full"></div>
                                             @endif
                                        </div>
                                    @empty
                                        <div class="p-8 text-center">
                                            <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3">
                                                <i class="far fa-bell-slash text-xl"></i>
                                            </div>
                                            <p class="text-sm text-gray-500">Không có thông báo mới</p>
                                        </div>
                                    @endforelse
                                @endif
                            </div>
                             <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                                <a href="{{ route('admin.notifications') }}" class="text-sm text-gray-600 font-bold hover:text-blue-600 transition">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                    </div>
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                            @endif
                        </button>
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('admin.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user w-5"></i> Hồ sơ
                            </a>
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog w-5"></i> Cài đặt
                            </a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt w-5"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- Main Layout with Sidebar -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <!-- TỔNG QUAN -->
                <div class="mb-6">
                    <h3 class="px-6 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tổng quan</h3>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large w-5 text-gray-500"></i>
                        <span class="ml-3">Tổng quan</span>
                    </a>
                    
                    <a href="{{ route('admin.notifications') }}" 
                       class="sidebar-link flex items-center justify-between px-6 py-3 text-gray-700 {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-bell w-5 text-gray-500"></i>
                            <span class="ml-3">Thông báo</span>
                        </div>
                        @if($unreadCount > 0)
                            <span class="bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- KHO & SẢN PHẨM -->
                <div class="mb-6">
                    <h3 class="px-6 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kho & Sản phẩm</h3>
                    <a href="{{ route('admin.products.index') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                        <i class="fas fa-box w-5 text-gray-500"></i>
                        <span class="ml-3">Sản phẩm</span>
                    </a>

                    <a href="{{ route('admin.categories') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                        <i class="fas fa-sitemap w-5 text-gray-500"></i>
                        <span class="ml-3">Danh mục</span>
                    </a>

                    <a href="{{ route('admin.suppliers') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}">
                        <i class="fas fa-truck w-5 text-gray-500"></i>
                        <span class="ml-3">Nhà cung cấp</span>
                    </a>

                    <a href="{{ route('admin.proposals.index') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.proposals*') ? 'active' : '' }}">
                        <i class="fas fa-lightbulb w-5 text-gray-500"></i>
                        <span class="ml-3">Đề xuất sản phẩm</span>
                    </a>

                    <a href="{{ route('admin.inventory') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                        <i class="fas fa-warehouse w-5 text-gray-500"></i>
                        <span class="ml-3">Quản lý kho</span>
                    </a>
                </div>

                <!-- MUA SẮM -->
                <div class="mb-6">
                    <h3 class="px-6 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Mua sắm</h3>
                    <a href="{{ route('admin.orders') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart w-5 text-gray-500"></i>
                        <span class="ml-3">Đơn hàng</span>
                    </a>

                    <a href="{{ route('admin.history') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.history*') ? 'active' : '' }}">
                        <i class="fas fa-history w-5 text-gray-500"></i>
                        <span class="ml-3">Lịch sử mua hàng</span>
                    </a>
                </div>

                <!-- QUẢN TRỊ NGƯỜI DÙNG -->
                <div class="mb-6">
                    <h3 class="px-6 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Quản trị người dùng</h3>
                    <a href="{{ route('admin.departments') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.departments*') ? 'active' : '' }}">
                        <i class="fas fa-users w-5 text-gray-500"></i>
                        <span class="ml-3">Nhân viên & Khoa phòng</span>
                    </a>

                    <a href="{{ route('admin.permissions') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield w-5 text-gray-500"></i>
                        <span class="ml-3">Phân quyền</span>
                    </a>
                </div>

                <!-- CÀI ĐẶT -->
                <div class="mb-6">
                    <h3 class="px-6 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Cài đặt</h3>
                    <a href="{{ route('admin.feedback') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.feedback*') ? 'active' : '' }}">
                        <i class="fas fa-comments w-5 text-gray-500"></i>
                        <span class="ml-3">Phản hồi</span>
                    </a>

                    <a href="{{ route('admin.settings') }}" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                        <i class="fas fa-cog w-5 text-gray-500"></i>
                        <span class="ml-3">Cài đặt hệ thống</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Page Title Bar -->
            <div class="bg-white border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Đề xuất sản phẩm')</h2>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-20 right-6 z-[10001] space-y-3 pointer-events-none" style="max-width: 420px;">
        <!-- Toasts will be inserted here dynamically -->
    </div>

    <!-- Notification Detail Modal -->
    <div id="notificationDetailModal" class="fixed inset-0 bg-black/50 z-[10002] hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <i class="fas fa-bell mr-2 text-blue-600"></i> Chi tiết thông báo
                </h3>
                <button onclick="closeNotificationDetailModal()" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-8">
                <div id="notificationBadge" class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase mb-4"></div>
                <h4 id="notificationTitle" class="text-xl font-bold text-gray-900 mb-4 leading-tight"></h4>
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <p id="notificationMessage" class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm"></p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
                <button onclick="closeNotificationDetailModal()"
                    class="px-6 py-2 bg-gray-800 text-white rounded-xl hover:bg-gray-900 font-bold transition-all shadow-lg active:scale-95 text-sm">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <!-- <footer class="bg-white border-t border-gray-200">
        <div class="px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8"> -->
                <!-- Logo & Info -->
                <!-- <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="https://bvtamtricaolanh.com.vn/vnt_upload/weblink/logo-tam-tri-02.png" alt="Logo" class="h-14 w-14 object-contain">

                        <div>
                            <h3 class="font-bold text-blue-600">TÂM TRÍ</h3>
                            <p class="text-xs text-gray-600">Tư vấn chăm sóc khỏe toàn diện</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Bệnh viện Đa khoa Tâm Trí Cao Lãnh - Đồng Tháp
                    </p>
                </div> -->

                <!-- Thông tin liên hệ -->
                <!-- <div>
                    <h4 class="font-semibold text-gray-800 mb-4">BỆNH VIỆN ĐA KHOA TÂM TRÍ CAO LÃNH</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-map-marker-alt text-blue-500 w-5"></i> Địa chỉ: Cao Lãnh, Đồng Tháp</li>
                        <li><i class="fas fa-phone text-blue-500 w-5"></i> Điện thoại: 02773.878.115</li>
                        <li><i class="fas fa-fax text-blue-500 w-5"></i> Fax: 02773.762.115</li>
                        <li><i class="fas fa-envelope text-blue-500 w-5"></i> Email: caolanhospital@gmail.com</li>
                    </ul>
                </div> -->

                <!-- Hệ thống bệnh viện -->
                <!-- <div>
                    <h4 class="font-semibold text-gray-800 mb-4">HỆ THỐNG BỆNH VIỆN</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-blue-600">Bệnh Viện Đa Khoa Tâm Trí Cao Lãnh</a></li>
                        <li><a href="#" class="hover:text-blue-600">Bệnh Viện Quận 10 Tâm Trí Sài Gòn</a></li>
                        <li><a href="#" class="hover:text-blue-600">Bệnh Viện Đa Khoa Tâm Trí Vĩnh Long</a></li>
                        <li><a href="#" class="hover:text-blue-600">Bệnh Viện Đa Khoa Tâm Trí Sài Gòn</a></li>
                    </ul>
                </div> -->

                <!-- Kết nối -->
                <!-- <div>
                    <h4 class="font-semibold text-gray-800 mb-4">KẾT NỐI VỚI CHÚNG TÔI</h4>
                    <div class="flex space-x-3 mb-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                    <p class="text-sm text-gray-600">
                        <a href="#" class="text-blue-600 hover:underline">Bản quyền bảo hộ Mobifone</a>
                    </p>
                </div>
            </div>
        </div> -->

        <!-- Copyright -->
        <!-- <div class="bg-blue-600 py-3">
            <div class="px-6">
                <div class="flex flex-col md:flex-row justify-between items-center text-white text-sm">
                    <p>Copyright © 2025 - Bệnh Viện Đa Khoa Tâm Trí Cao Lãnh</p>
                    <div class="flex space-x-4 mt-2 md:mt-0">
                        <a href="#" class="hover:underline">Chính sách bảo mật</a>
                        <a href="#" class="hover:underline">Điều khoản sử dụng</a>
                    </div>
                </div>
            </div>
        </div> -->
    <!-- </footer> -->

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        function showNotificationDetail(id, title, message, type, isRead) {
            const modal = document.getElementById('notificationDetailModal');
            document.getElementById('notificationTitle').textContent = title;
            document.getElementById('notificationMessage').textContent = message;
            
            const badge = document.getElementById('notificationBadge');
            const configs = {
                'success': { text: 'Thành công', class: 'bg-green-100 text-green-700' },
                'error': { text: 'Lỗi', class: 'bg-red-100 text-red-700' },
                'warning': { text: 'Cảnh báo', class: 'bg-yellow-100 text-yellow-700' },
                'important': { text: 'Quan trọng', class: 'bg-purple-100 text-purple-700' },
                'info': { text: 'Thông tin', class: 'bg-blue-100 text-blue-700' }
            };
            const config = configs[type] || configs.info;
            badge.textContent = config.text;
            badge.className = `inline-flex px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase mb-4 ${config.class}`;
            
            modal.classList.remove('hidden');
            document.getElementById('adminNotificationDropdown').classList.add('hidden');

            if (!isRead) {
                fetch(`/admin/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    setTimeout(() => window.location.reload(), 500);
                });
            }
        }

        function closeNotificationDetailModal() {
            document.getElementById('notificationDetailModal').classList.add('hidden');
        }

        function markAllNotificationsAsRead() {
            fetch('/admin/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if (data.success) window.location.reload();
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (userMenu && !userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }

            const notifyMenu = document.getElementById('adminNotificationDropdown');
            const notifyButton = event.target.closest('button[onclick*="adminNotificationDropdown"]');
            if (notifyMenu && !notifyButton && !notifyMenu.contains(event.target)) {
                notifyMenu.classList.add('hidden');
            }
        });

        // ===== TOAST NOTIFICATION SYSTEM =====
        class ToastManager {
            constructor() {
                this.container = document.getElementById('toastContainer');
                this.queue = [];
                this.isProcessing = false;
                this.activeToasts = new Set();
            }

            createToastHTML(notification) {
                const { id, title, message, type } = notification;
                
                const typeConfig = {
                    'info': { borderColor: 'border-blue-400', bgColor: 'bg-blue-50', iconBg: 'bg-blue-100', iconColor: 'text-blue-600', icon: 'fa-info-circle' },
                    'important': { borderColor: 'border-purple-400', bgColor: 'bg-purple-50', iconBg: 'bg-purple-100', iconColor: 'text-purple-600', icon: 'fa-star' },
                    'warning': { borderColor: 'border-yellow-400', bgColor: 'bg-yellow-50', iconBg: 'bg-yellow-100', iconColor: 'text-yellow-600', icon: 'fa-exclamation-triangle' },
                    'error': { borderColor: 'border-red-400', bgColor: 'bg-red-50', iconBg: 'bg-red-100', iconColor: 'text-red-600', icon: 'fa-exclamation-circle' }
                };

                const config = typeConfig[type] || typeConfig['info'];
                const toastId = `toast-${id}-${Date.now()}`;
                
                return `
                    <div id="${toastId}" onclick="showNotificationDetail(${id}, '${addslashes_js(title)}', '${addslashes_js(message)}', '${type}', false)"
                         class="toast-item bg-white rounded-xl border-2 ${config.borderColor} shadow-2xl overflow-hidden transition-all duration-500 ease-out pointer-events-auto cursor-pointer"
                         style="transform: translateX(500px); opacity: 0; max-width: 420px;">
                        <div class="flex items-start gap-4 p-4">
                            <div class="flex-shrink-0 w-14 h-14 rounded-full ${config.iconBg} ${config.bgColor} flex items-center justify-center border-2 ${config.borderColor}">
                                <i class="fas ${config.icon} ${config.iconColor} text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-gray-900 mb-1 leading-tight">${title}</h4>
                                <p class="text-sm text-gray-700 leading-relaxed break-words">${message}</p>
                            </div>
                            <button onclick="event.stopPropagation(); toastManager.removeToast('${toastId}')" 
                                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="h-1 ${config.bgColor}">
                            <div class="toast-progress h-full ${config.borderColor.replace('border-', 'bg-')}" 
                                 style="width: 100%; transition: width 5s linear;"></div>
                        </div>
                    </div>
                `;
            }

            addToast(notification) {
                this.queue.push(notification);
                if (!this.isProcessing) {
                    this.processQueue();
                }
            }

            async processQueue() {
                if (this.queue.length === 0) {
                    this.isProcessing = false;
                    return;
                }

                this.isProcessing = true;
                const notification = this.queue.shift();
                await this.showToast(notification);
                
                setTimeout(() => {
                    this.processQueue();
                }, 800);
            }

            async showToast(notification) {
                return new Promise((resolve) => {
                    const toastHTML = this.createToastHTML(notification);
                    this.container.insertAdjacentHTML('beforeend', toastHTML);
                    
                    const toastId = `toast-${notification.id}-${Date.now()}`;
                    const toastElement = document.getElementById(toastId);
                    this.activeToasts.add(toastId);
                    
                    setTimeout(() => {
                        toastElement.style.transform = 'translateX(0)';
                        toastElement.style.opacity = '1';
                        
                        const progressBar = toastElement.querySelector('.toast-progress');
                        setTimeout(() => {
                            progressBar.style.width = '0%';
                        }, 50);
                    }, 50);
                    
                    setTimeout(() => {
                        this.removeToast(toastId);
                        resolve();
                    }, 5000);
                });
            }

            removeToast(toastId) {
                const toast = document.getElementById(toastId);
                if (!toast) return;
                
                toast.style.transform = 'translateX(500px)';
                toast.style.opacity = '0';
                
                setTimeout(() => {
                    toast.remove();
                    this.activeToasts.delete(toastId);
                }, 300);
            }

            showUnreadNotifications(notifications) {
                notifications.forEach(notification => {
                    this.addToast(notification);
                });
            }
        }

         const toastManager = new ToastManager();

        // Auto-display unread notifications on page load
        document.addEventListener('DOMContentLoaded', () => {
            ////code dev-master
             @if($latestUnreadNotification && !session('toast_shown_' . $latestUnreadNotification->id))
                @php session()->put('toast_shown_' . $latestUnreadNotification->id, true); @endphp
                const latestNotification = {
                    id: {{ $latestUnreadNotification->id }},
                    title: `{{ addslashes($latestUnreadNotification->title) }}`,
                    message: `{!! addslashes(strip_tags($latestUnreadNotification->message)) !!}`,
                    type: '{{ $latestUnreadNotification->type }}'
                };
                
                // Show toast after a short delay
                setTimeout(() => {
                    toastManager.addToast(latestNotification);
                }, 1000);
            @endif
        });
        
        function addslashes_js(str) {
            return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
        }
    </script>

    @stack('scripts')
</body>
</html>
