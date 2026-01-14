<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Khoa/Phòng Ban') - Hospital Purchase</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-active {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(20, 184, 166, 0.3);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-draft {
            background: #e5e7eb;
            color: #374151;
        }

        .badge-submitted {
            background-color: #ccfbf1;
            color: #0f766e;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50">
    <div class="flex flex-col min-h-screen">
        <div class="flex flex-1 w-full">
            <!-- Sidebar -->
            <aside class="w-72 bg-white border-r border-gray-200 flex flex-col sticky top-0 h-screen">
                <!-- Logo & Hospital Name -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3 mb-2">
                        <img src="{{ asset('logo.jpg') }}" alt="TMMC Logo"
                            class="w-12 h-12 object-contain bg-white rounded-lg">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 leading-tight uppercase">Tâm Trí Cao Lãnh</h2>
                            <p class="text-xs text-blue-600 font-semibold">TMMC Healthcare</p>
                        </div>
                    </div>
                    <div class="mt-3 px-3 py-2 bg-blue-50 rounded-lg">
                        <p class="text-xs font-semibold text-blue-700">
                            {{ Auth::user()->department->department_name ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-blue-600 mt-1">{{ Auth::user()->full_name }}</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <!-- HỆ THỐNG Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Hệ thống</p>
                        <a href="{{ route('department.dashboard') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-th-large w-5 text-sm"></i>
                            <span class="font-medium text-sm">Tổng quan</span>
                        </a>
                    </div>

                    <!-- MUA SẮM & ĐƠN HÀNG Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Mua sắm & Đơn hàng</p>
                        
                        <a href="{{ route('department.dept_orders.index') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.dept_orders.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-clipboard-check w-5 text-sm"></i>
                            <span class="font-medium text-sm">Xác nhận đơn hàng</span>
                        </a>

                        <a href="{{ route('department.requests.create') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.requests.create') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-plus-circle w-5 text-sm"></i>
                            <span class="font-medium text-sm">Yêu cầu mới</span>
                        </a>

                        <a href="{{ route('department.requests.index') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.requests.index') && !request()->is('department/requests/history') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-shopping-cart w-5 text-sm"></i>
                            <span class="font-medium text-sm">Yêu cầu mua hàng</span>
                            @php
                                $pendingCount = \App\Models\PurchaseRequest::where('department_id', Auth::user()->department_id)
                                    ->whereIn('status', ['SUBMITTED'])
                                    ->where('is_delete', false)
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-semibold">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('department.requests.history') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.requests.history') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-history w-5 text-sm"></i>
                            <span class="font-medium text-sm">Lịch sử yêu cầu</span>
                        </a>
                    </div>

                    <!-- KHO & SẢN PHẨM Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Kho & Sản phẩm</p>
                        
                        <a href="{{ route('department.products.index') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.products.index') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-box-open w-5 text-sm"></i>
                            <span class="font-medium text-sm">Sản phẩm</span>
                        </a>

                        <a href="{{ route('department.proposals.index') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.proposals.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-lightbulb w-5 text-sm"></i>
                            <span class="font-medium text-sm">Đề xuất sản phẩm</span>
                        </a>

                        <a href="{{ route('department.inventory.index') }}"
                            class="flex items-center space-x-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('department.inventory.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-warehouse w-5 text-sm"></i>
                            <span class="font-medium text-sm">Kho khoa phòng</span>
                        </a>
                    </div>

                </nav>

                <!-- Settings -->

            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Header -->
                <header class="bg-white shadow-sm sticky top-0 z-20 h-20">
                    <div class="h-full px-6 flex items-center justify-between">
                        <!-- Left: Logo & Toggle -->
                        <div class="flex items-center space-x-4">
                            <button class="md:hidden text-gray-500 hover:text-blue-600 focus:outline-none">
                                <i class="fas fa-bars text-xl"></i>
                            </button>

                            <!-- Page Title -->
                            <div class="flex items-center">
                                @if(request()->routeIs('department.dashboard'))
                                    <h1 class="font-bold text-gray-800 text-2xl tracking-tight">
                                        @yield('title', 'Tổng quan')
                                    </h1>
                                @endif
                            </div>
                        </div>

                        <!-- Right: User & Notifications -->
                        <div class="flex items-center space-x-4">
                            <!-- Notification Bell -->
                            <div class="relative">
                                <button onclick="toggleNotifications()"
                                    class="relative p-2 text-gray-400 hover:text-blue-600 transition focus:outline-none">
                                    <i class="fas fa-bell text-xl"></i>
                                    @if(isset($unreadCount) && $unreadCount > 0)
                                        <span
                                            class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </button>

                                <!-- Notification Dropdown -->
                                <div id="notificationMenu"
                                    class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all">
                                    <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center bg-white">
                                        <h3 class="font-bold text-gray-800 text-base">Thông báo</h3>
                                        <button class="text-xs text-blue-600 hover:text-blue-700 font-medium hover:underline">Đánh dấu tất cả đã đọc</button>
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
                                                <div onclick="showRequestDetail('{{ $data['request_id'] ?? '' }}')" 
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
                                                <div class="p-8 text-center text-gray-500">
                                                    <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3">
                                                        <i class="far fa-bell-slash text-xl"></i>
                                                    </div>
                                                    <p class="text-sm">Không có thông báo mới</p>
                                                </div>
                                            @endforelse
                                        @endif
                                    </div>
                                    
                                     <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                                        <a href="#" class="text-sm text-gray-600 font-bold hover:text-blue-600 transition">Xem tất cả thông báo</a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="relative">
                                <button onclick="toggleUserMenu()"
                                    class="flex items-center space-x-2 focus:outline-none group">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold group-hover:shadow-md transition overflow-hidden">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-600">
                                            {{ Auth::user()->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ Auth::user()->department->department_name ?? 'Nhân viên' }}
                                        </p>
                                    </div>
                                    <i
                                        class="fas fa-chevron-down text-gray-300 text-xs ml-1 group-hover:text-blue-500"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="userMenu"
                                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    <a href="{{ route('department.profile.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fas fa-user-circle mr-2"></i> Hồ sơ cá nhân
                                    </a>
                                    <a href="#"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fas fa-cog mr-2"></i> Cài đặt
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Content -->
                <main class="flex-1 p-6">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div
                            class="flash-message fixed top-24 right-5 z-50 min-w-[300px] p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center shadow-lg transition-all duration-500 transform translate-x-0">
                            <i class="fas fa-check-circle mr-2 text-xl"></i>
                            <div>
                                <h4 class="font-bold text-sm">Thành công</h4>
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div
                            class="flash-message fixed top-24 right-5 z-50 min-w-[300px] p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center shadow-lg transition-all duration-500 transform translate-x-0">
                            <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                            <div>
                                <h4 class="font-bold text-sm">Lỗi</h4>
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @yield('content')

                    <!-- Footer -->
                </main>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-blue-600 py-4 border-t border-blue-700">
            <div class="max-w-7xl mx-auto px-6 text-center text-xs text-white">
                <p>&copy; 2025 Bệnh Viện Đa Khoa Tâm Trí Cao Lãnh</p>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        // Auto hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.flash-message');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            document.getElementById('notificationMenu').classList.add('hidden'); // Close other
            menu.classList.toggle('hidden');
        }

        function toggleNotifications() {
            const menu = document.getElementById('notificationMenu');
            document.getElementById('userMenu').classList.add('hidden'); // Close other
            menu.classList.toggle('hidden');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            const menu = document.getElementById('userMenu');
            const button = event.target.closest('button');

            if (!button || button.getAttribute('onclick') !== 'toggleUserMenu()') {
                document.getElementById('userMenu').classList.add('hidden');
            }
            if (!button || button.getAttribute('onclick') !== 'toggleNotifications()') {
                document.getElementById('notificationMenu').classList.add('hidden');
            }
        });

        function showRequestDetail(id) {
             if(id) window.location.href = '/department/requests/' + id;
        }
    </script>

    @stack('scripts')
</body>

</html>