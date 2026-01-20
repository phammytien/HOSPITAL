<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TMMC Healthcare')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Internal:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        @media print {
            .d-print-none {
                display: none !important;
            }
        }
        /* Ensure notification dropdown displays above all content */
        #notificationDropdown {
            z-index: 9999 !important;
        }
        @keyframes slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in {
            animation: slide-in 0.5s ease-out;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans text-gray-900">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex d-print-none h-full">
            <div class="flex flex-col flex-1 min-h-0">
                <!-- Logo area -->
                <div class="flex flex-col items-center justify-center px-4 py-6 border-b border-gray-100 text-center">
                    <!-- Logo -->
                    <img src="{{ asset('logo.jpg') }}" alt="TMMC Healthcare" class="w-16 h-16 object-contain mb-3">

                    <!-- Text block -->
                    <div class="space-y-1">

                        <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">
                            Bệnh viện Đa khoa
                        </p>
                        <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">
                            Tâm Trí Cao Lãnh
                        </p>
                    </div>
                </div>




                <!-- Navigation -->
                <nav class="mt-6 px-4 space-y-1 overflow-y-auto flex-1">
                    <!-- HỆ THỐNG Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Hệ thống</p>
                        @php
                            $isActiveDashboard = request()->is('dashboard') || request()->routeIs('*.dashboard');
                        @endphp
                        <a href="{{ route('buyer.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ $isActiveDashboard ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Tổng quan
                        </a>
                    </div>

                    <!-- QUẢN LÝ MUA HÀNG Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Quản lý mua hàng</p>
                        
                        @php
                            $isActiveRequests = request()->routeIs('buyer.requests.*');
                        @endphp
                        <a href="{{ route('buyer.requests.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ $isActiveRequests ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 group' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Yêu cầu mua hàng
                        </a>

                        @php
                            $isActiveOrders = request()->routeIs('buyer.orders.*');
                        @endphp
                        <a href="{{ route('buyer.orders.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ $isActiveOrders ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Đơn đặt hàng
                        </a>

                        @php
                            $isActiveTracking = request()->routeIs('buyer.tracking.*');
                        @endphp
                        <a href="{{ route('buyer.tracking.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ $isActiveTracking ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 012-2v0m12 0a2 2 0 012 2v0m-2-2h2" />
                            </svg>
                            Theo dõi giao hàng
                        </a>
                    </div>

                    <!-- DỮ LIỆU & KHO Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Dữ liệu & Kho</p>
                        
                        <a href="{{ route('buyer.products.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('buyer.products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            Sản phẩm
                        </a>

                        <a href="{{ route('buyer.proposals.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('buyer.proposals.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Đề xuất sản phẩm
                        </a>

 <a href="{{ route('buyer.suppliers.index') }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('buyer.suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" target="_blank"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                         Nhà cung cấp
                    </a>
                        <a href="{{ route('buyer.reports.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('buyer.reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Báo cáo
                        </a>
                    </div>

                    <!-- CÀI ĐẶT Section -->
                    <div class="mb-4">
                        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Cài đặt</p>
                        
                        <a href="{{ route('buyer.notifications.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('buyer.notifications.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Thông báo
                        </a>
                    </div>
                </nav>
            </div>

            <!-- User Profile -->
            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    <a href="{{ route('buyer.profile.index') }}" class="flex items-center gap-3 flex-1 min-w-0 hover:bg-gray-100 p-1 rounded-lg transition group">
                        <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                            <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . (Auth::user()->full_name ?? 'User') . '&background=random' }}"
                                alt="User Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ Auth::user()->role === 'BUYER' ? 'Chuyên viên mua sắm' : Auth::user()->role }}
                            </p>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 p-2" title="Đăng xuất">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col bg-gray-50">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 d-print-none" style="position: relative; z-index: 1000;">

                <h1 class="text-xl font-bold text-gray-800">@yield('header_title', 'Dashboard')</h1>

                <!-- <div class="flex-1 max-w-lg mx-12">
                     <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" class="w-full py-2 pl-10 pr-4 bg-gray-100 text-gray-700 rounded-lg focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100" placeholder="Tìm kiếm mã đơn, khoa phòng...">
                    </div>
                </div> -->

                <div class="flex items-center gap-4">
                    <!-- <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tạo yêu cầu mới
                    </button> -->

                    <div class="relative ml-4" style="z-index: 10000;">
                        <button id="notificationBtn"
                            class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg relative transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span
                                    class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                            @endif
                        </button>

                        <!-- Dropdown -->
                        <div id="notificationDropdown"
                            class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-[9999] transform origin-top-right transition-all"
                            style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;">

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
                                              class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer relative group flex gap-4 {{ $notify->is_read ? 'opacity-70' : '' }}">
                                             
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
                                             
                                             @if(!$notify->is_read)
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
                                <a href="{{ route('buyer.notifications.index') }}" class="text-sm text-gray-600 font-bold hover:text-blue-600 transition">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>

                    <!-- <button class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </button> -->
                </div>
            </header>

            <!-- Content Scroll Area -->
            <div class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-20 right-6 z-[10001] space-y-3 pointer-events-none" style="max-width: 420px;">
        <!-- Toasts will be inserted here dynamically -->
    </div>

    <!-- Request Detail Modal -->
    <div id="requestDetailModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <i class="fas fa-file-invoice mr-2 text-blue-600"></i> Chi tiết yêu cầu
                </h3>
                <button onclick="closeRequestDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1" id="requestDetailContent">
                <div class="flex justify-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <a href="{{ route('buyer.requests.index') }}"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors flex items-center">
                    <i class="fas fa-list-ul mr-2"></i> Danh sách yêu cầu
                </a>
                <button onclick="closeRequestDetailModal()"
                    class="px-5 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium transition-colors">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Notification Detail Modal -->
    <div id="notificationDetailModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <i class="fas fa-bell mr-2 text-blue-600"></i> Chi tiết thông báo
                </h3>
                <button onclick="closeNotificationDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <div class="mb-4">
                    <span id="notificationBadge" class="px-3 py-1 rounded-full text-xs font-semibold"></span>
                </div>
                <h4 id="notificationTitle" class="text-xl font-bold text-gray-900 mb-4"></h4>
                <p id="notificationMessage" class="text-gray-700 leading-relaxed whitespace-pre-wrap"></p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button onclick="closeNotificationDetailModal()"
                    class="px-5 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium transition-colors">Đóng</button>
            </div>
        </div>
    </div>

    <script>
        // Dropdown Toggle
        const btn = document.getElementById('notificationBtn');
        const dropdown = document.getElementById('notificationDropdown');

        if (btn && dropdown) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.classList.contains('hidden') && !dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        // Show Notification Detail
        function showNotificationDetail(id, title, message, type, isRead) {
            const modal = document.getElementById('notificationDetailModal');
            const titleEl = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');
            const badgeEl = document.getElementById('notificationBadge');

            // Set content
            titleEl.textContent = title;
            messageEl.textContent = message;

            // Set badge based on type
            const badgeConfig = {
                'success': { text: 'THÀNH CÔNG', class: 'bg-green-100 text-green-700' },
                'error': { text: 'LỖI', class: 'bg-red-100 text-red-700' },
                'warning': { text: 'CẢNH BÁO', class: 'bg-yellow-100 text-yellow-700' },
                'info': { text: 'THÔNG TIN', class: 'bg-blue-100 text-blue-700' }
            };
            const config = badgeConfig[type] || badgeConfig['info'];
            badgeEl.textContent = config.text;
            badgeEl.className = `px-3 py-1 rounded-full text-xs font-semibold ${config.class}`;

            // Show modal
            modal.classList.remove('hidden');
            dropdown.classList.add('hidden');

            // Mark as read if unread
            if (!isRead) {
                fetch(`/buyer/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    // Reload page after a short delay to update UI
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
            }
        }

        // Close Notification Detail Modal
        function closeNotificationDetailModal() {
            document.getElementById('notificationDetailModal').classList.add('hidden');
        }

        // Mark All Notifications as Read
        function markAllNotificationsAsRead() {
            fetch('/buyer/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi đánh dấu đã đọc');
            });
        }

        // Detail Modal
        function showRequestDetail(id) {
            if (!id) return;

            const modal = document.getElementById('requestDetailModal');
            const content = document.getElementById('requestDetailContent');

            modal.classList.remove('hidden');
            dropdown.classList.add('hidden');

            content.innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i></div>';

            fetch(`/buyer/requests/${id}`)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(err => {
                    content.innerHTML = '<p class="text-red-500 text-center">Không thể tải chi tiết yêu cầu.</p>';
                });
        }

        function closeRequestDetailModal() {
            document.getElementById('requestDetailModal').classList.add('hidden');
        }
        //Vinh3
         // ===== TOAST NOTIFICATION SYSTEM =====
        class ToastManager {
            constructor() {
                this.container = document.getElementById('toastContainer');
                this.queue = [];
                this.isProcessing = false;
                this.activeToasts = new Set();
            }

            // Create toast HTML
            createToastHTML(notification, uniqueId) {
                const { id, title, message, type } = notification;
                
                // Type configurations
                const typeConfig = {
                    'info': { 
                        borderColor: 'border-blue-400', 
                        bgColor: 'bg-blue-50',
                        iconBg: 'bg-blue-100',
                        iconColor: 'text-blue-600',
                        icon: 'fa-info-circle'
                    },
                    'important': { 
                        borderColor: 'border-purple-400', 
                        bgColor: 'bg-purple-50',
                        iconBg: 'bg-purple-100',
                        iconColor: 'text-purple-600',
                        icon: 'fa-star'
                    },
                    'warning': { 
                        borderColor: 'border-yellow-400', 
                        bgColor: 'bg-yellow-50',
                        iconBg: 'bg-yellow-100',
                        iconColor: 'text-yellow-600',
                        icon: 'fa-exclamation-triangle'
                    },
                    'error': { 
                        borderColor: 'border-red-400', 
                        bgColor: 'bg-red-50',
                        iconBg: 'bg-red-100',
                        iconColor: 'text-red-600',
                        icon: 'fa-exclamation-circle'
                    }
                };

                const config = typeConfig[type] || typeConfig['info'];
                
                const toastId = uniqueId;
                
                return `
                    <div id="${toastId}" onclick="showNotificationDetail(${id}, '${addslashes_js(title)}', '${addslashes_js(message)}', '${type}', false)"
                         class="toast-item bg-white rounded-xl border-2 ${config.borderColor} shadow-2xl overflow-hidden transition-all duration-500 ease-out pointer-events-auto cursor-pointer"
                         style="transform: translateX(500px); opacity: 0; max-width: 420px;">
                        <div class="flex items-start gap-4 p-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-14 h-14 rounded-full ${config.iconBg} ${config.bgColor} flex items-center justify-center border-2 ${config.borderColor}">
                                <i class="fas ${config.icon} ${config.iconColor} text-xl"></i>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-gray-900 mb-1 leading-tight">${title}</h4>
                                <p class="text-sm text-gray-700 leading-relaxed break-words">${message}</p>
                            </div>
                            
                            <!-- Close button -->
                            <button onclick="event.stopPropagation(); toastManager.removeToast('${toastId}')" 
                                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Progress bar -->
                        <div class="h-1 ${config.bgColor}">
                            <div class="toast-progress h-full ${config.borderColor.replace('border-', 'bg-')}" 
                                 style="width: 100%; transition: width 5s linear;"></div>
                        </div>
                    </div>
                `;
            }

            // Add toast to queue
            addToast(notification) {
                this.queue.push(notification);
                if (!this.isProcessing) {
                    this.processQueue();
                }
            }

            // Process toast queue
            async processQueue() {
                if (this.queue.length === 0) {
                    this.isProcessing = false;
                    return;
                }

                this.isProcessing = true;
                const notification = this.queue.shift();
                
                await this.showToast(notification);
                
                // Wait a bit before showing next toast
                setTimeout(() => {
                    this.processQueue();
                }, 800); // 0.8s delay between toasts
            }

            // Show single toast
            async showToast(notification) {
                return new Promise((resolve) => {
                    const uniqueId = `toast-${notification.id}-${Date.now()}`;
                    const toastHTML = this.createToastHTML(notification, uniqueId);
                    this.container.insertAdjacentHTML('beforeend', toastHTML);
                    
                    const toastElement = document.getElementById(uniqueId);
                    if (toastElement) {
                        this.activeToasts.add(uniqueId);
                        
                        // Trigger slide-in animation
                        setTimeout(() => {
                            toastElement.style.transform = 'translateX(0)';
                            toastElement.style.opacity = '1';
                            
                            // Start progress bar animation
                            const progressBar = toastElement.querySelector('.toast-progress');
                            if (progressBar) {
                                setTimeout(() => {
                                    progressBar.style.width = '0%';
                                }, 50);
                            }
                        }, 50);
                        
                        // Auto-dismiss after 5 seconds
                        setTimeout(() => {
                            this.removeToast(uniqueId);
                            resolve();
                        }, 5000);
                    } else {
                        resolve();
                    }
                });
            }

            // Remove toast
            removeToast(toastId) {
                const toast = document.getElementById(toastId);
                if (!toast) return;
                
                // Slide out animation
                toast.style.transform = 'translateX(500px)';
                toast.style.opacity = '0';
                
                setTimeout(() => {
                    toast.remove();
                    this.activeToasts.delete(toastId);
                }, 300);
            }

            // Show all unread notifications
            showUnreadNotifications(notifications) {
                notifications.forEach(notification => {
                    this.addToast(notification);
                });
            }
        }

        // Initialize toast manager
        const toastManager = new ToastManager();

        // Auto-display unread notifications on page load
        document.addEventListener('DOMContentLoaded', () => {
            @if(isset($notifications) && $notifications->where('is_read', false)->count() > 0)
                const unreadNotifications = [
                    @foreach($notifications->where('is_read', false) as $notify)
                        {
                            id: {{ $notify->id }},
                            title: {{ Js::from($notify->title) }},
                            message: {{ Js::from(strip_tags($notify->message)) }},
                            type: '{{ $notify->type }}'
                        },
                    @endforeach
                ];
                
                // Show toasts after a short delay
                setTimeout(() => {
                    toastManager.showUnreadNotifications(unreadNotifications);
                }, 1000);
            @endif
        });

        //master
        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.flash-message');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(50px)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });

        function addslashes_js(str) {
            return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
        }
    </script>
    @stack('scripts')

    <!-- Flash Messages (Bottom) -->
    @if(session('success'))
        <div class="flash-message fixed top-24 right-8 z-[9999] min-w-[320px] p-4 bg-white border border-green-100 text-green-800 rounded-2xl flex items-center shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border-l-4 border-l-green-500 animate-slide-in">
            <div class="flex-shrink-0 w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-sm text-gray-800">Thành công</h4>
                <p class="text-xs text-gray-600">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flash-message fixed top-24 right-8 z-[9999] min-w-[320px] p-4 bg-white border border-red-100 text-red-800 rounded-2xl flex items-center shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border-l-4 border-l-red-500 animate-slide-in">
            <div class="flex-shrink-0 w-10 h-10 bg-red-50 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-sm text-gray-800">Lỗi</h4>
                <p class="text-xs text-gray-600">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
</body>

</html>