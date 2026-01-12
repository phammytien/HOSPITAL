@extends('layouts.admin')

@section('title', 'Tổng quan')
@section('page-title', 'Tổng quan')

@section('content')
    <!-- Container -->
    <div class="space-y-6">
        
        <!-- 1. Top Section: Filters -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2 bg-white rounded-lg p-1.5 shadow-sm border border-gray-100 w-fit">
                <h2 class="text-gray-800 font-bold text-base whitespace-nowrap px-2 flex items-center">
                    <i class="bi bi-speedometer2 text-blue-600 mr-2"></i>Tổng quan
                </h2>
                <div class="h-5 w-px bg-gray-200 mx-1 hidden md:block"></div>
                
                <!-- Year Select -->
                <select name="year" class="bg-gray-50 border-0 rounded-md text-sm font-medium text-gray-700 py-1.5 pl-3 pr-8 focus:ring-0 focus:bg-gray-100 cursor-pointer hover:bg-gray-100 transition-colors" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endforeach
                </select>

                <!-- Period Select -->
                <select name="period_type" class="bg-gray-50 border-0 rounded-md text-sm font-medium text-gray-700 py-1.5 pl-3 pr-8 focus:ring-0 focus:bg-gray-100 cursor-pointer hover:bg-gray-100 transition-colors" onchange="this.form.submit()">
                    <option value="month" {{ $periodType == 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="quarter" {{ $periodType == 'quarter' ? 'selected' : '' }}>Quý này</option>
                    <option value="year" {{ $periodType == 'year' ? 'selected' : '' }}>Năm nay</option>
                </select>

                <!-- Department Select -->
                <select name="department_id" class="bg-gray-50 border-0 rounded-md text-sm font-medium text-gray-700 py-1.5 pl-3 pr-8 focus:ring-0 focus:bg-gray-100 cursor-pointer hover:bg-gray-100 transition-colors" onchange="this.form.submit()">
                    <option value="">Tất cả Khoa/Phòng</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pending -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 h-full w-1 bg-orange-400"></div>
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Chờ duyệt</p>
                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $pendingRequests }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 group-hover:bg-orange-100 transition-colors">
                        <i class="bi bi-hourglass-split text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs text-orange-600 font-bold bg-orange-50 w-fit px-2 py-1 rounded">
                    <i class="bi bi-arrow-up-short mr-1"></i>{{ $newToday }} yêu cầu mới
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 h-full w-1 bg-green-500"></div>
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Đã duyệt ({{ $periodType == 'year' ? 'Năm' : ($periodType == 'quarter' ? 'Quý' : 'Tháng') }})</p>
                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $approvedThisPeriod }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-500 group-hover:bg-green-100 transition-colors">
                        <i class="bi bi-check2-circle text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs {{ $approvedGrowth >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} font-bold w-fit px-2 py-1 rounded">
                    <i class="bi {{ $approvedGrowth >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short' }} mr-1"></i>{{ abs($approvedGrowth) }}% so với kỳ trước
                </div>
            </div>

            <!-- Cost -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng chi phí</p>
                        <h3 class="text-3xl font-extrabold text-blue-600 mt-1">{{ number_format($totalValue / 1000000, 1) }}<span class="text-sm text-gray-500 ml-1">Tr</span></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-100 transition-colors">
                        <i class="bi bi-currency-dollar text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs {{ $spendingGrowth >= 0 ? 'text-blue-600 bg-blue-50' : 'text-red-600 bg-red-50' }} font-bold w-fit px-2 py-1 rounded">
                    <i class="bi {{ $spendingGrowth >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short' }} mr-1"></i>{{ abs($spendingGrowth) }}% so với kỳ trước
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 h-full w-1 bg-purple-500"></div>
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sản phẩm</p>
                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalProducts }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500 group-hover:bg-purple-100 transition-colors">
                        <i class="bi bi-box-seam text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-xs text-red-600 bg-red-50 font-bold w-fit px-2 py-1 rounded">
                    <i class="bi bi-exclamation-circle mr-1"></i>{{ $lowStock }} sắp hết hàng
                </div>
            </div>
        </div>

        <!-- 2. Charts Row (Trend & Category) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Trend Chart (2/3) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-gray-800">Xu hướng chi tiêu</h3>
                    <button class="text-gray-400 hover:text-blue-600 transition-colors"><i class="bi bi-three-dots"></i></button>
                </div>
                <div class="relative w-full h-80">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Right: Category Donut (1/3) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-800">Tỷ trọng danh mục</h3>
                    <button class="text-gray-400 hover:text-blue-600 transition-colors"><i class="bi bi-three-dots"></i></button>
                </div>
                <div class="relative w-full h-60 flex justify-center mb-4">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="space-y-3">
                    @php $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']; @endphp
                    @foreach(array_slice($chartData['categories']['labels'], 0, 3) as $index => $label)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full mr-2" style="background-color: {{ $colors[$index % 5] }}"></span>
                            <span class="text-gray-600 truncate max-w-[140px]">{{ $label }}</span>
                        </div>
                        <span class="font-bold text-gray-800">{{ number_format($chartData['categories']['data'][$index] ?? 0) }}đ</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- 3. Comparison Row (Full Width) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-6">So sánh chi tiêu giữa các Khoa/Phòng</h3>
            <div class="relative w-full h-[350px]">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>

        <!-- 4. Bottom Row: Table & Sidebar Widgets -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Recent Requests Table (2/3) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-800 flex items-center">
                        <i class="bi bi-file-earmark-text text-blue-600 mr-2"></i> Yêu cầu mua sắm gần đây
                    </h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center group">
                        Xem tất cả <i class="bi bi-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="overflow-x-auto flex-grow p-2">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-3 font-semibold rounded-l-lg">Mã phiếu</th>
                                <th class="px-4 py-3 font-semibold">Khoa/Phòng</th>
                                <th class="px-4 py-3 font-semibold">Ngày tạo</th>
                                <th class="px-4 py-3 font-semibold">Trạng thái</th>
                                <th class="px-4 py-3 font-semibold text-center rounded-r-lg">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentRequests as $request)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-4 py-4 font-bold text-blue-600 group-hover:text-blue-700">
                                    {{ $request->request_code }}
                                </td>
                                <td class="px-4 py-4 text-gray-700 font-medium">{{ $request->department->department_name ?? '-' }}</td>
                                <td class="px-4 py-4 text-gray-500">{{ $request->created_at ? $request->created_at->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-4">
                                     @if($request->status == 'APPROVED')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Đã duyệt
                                        </span>
                                    @elseif($request->status == 'SUBMITTED')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span>Chờ duyệt
                                        </span>
                                    @elseif($request->status == 'REJECTED')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>Từ chối
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $request->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button onclick="viewOrderDetail({{ $request->id }})" class="text-gray-400 hover:text-blue-600 transition-colors" title="Xem chi tiết">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Chưa có dữ liệu giao dịch</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Widgets Stack (1/3) -->
            <div class="space-y-6">
                <!-- Quick Actions (Grid of 4) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-lightning-charge-fill text-blue-600 mr-2"></i> Lối tắt nhanh
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- 1. Quản lý sản phẩm -->
                        <a href="{{ route('admin.products.index') }}" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-xl text-blue-600 hover:bg-blue-100 transition-all hover:scale-105 duration-200 group h-32 text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                                <i class="bi bi-box-seam-fill text-2xl"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-700 group-hover:text-blue-700">Quản lý<br>sản phẩm</span>
                        </a>

                        <!-- 2. Quản lý danh mục -->
                        <a href="{{ route('admin.categories') }}" class="flex flex-col items-center justify-center p-4 bg-indigo-50 rounded-xl text-indigo-600 hover:bg-indigo-100 transition-all hover:scale-105 duration-200 group h-32 text-center">
                            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-indigo-200 transition-colors">
                                <i class="bi bi-grid-3x3-gap-fill text-2xl"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-700 group-hover:text-indigo-700">Quản lý<br>danh mục</span>
                        </a>

                        <!-- 3. Lịch sử mua hàng -->
                         <a href="{{ route('admin.history') }}" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-xl text-purple-600 hover:bg-purple-100 transition-all hover:scale-105 duration-200 group h-32 text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-200 transition-colors">
                                <i class="bi bi-clock-history text-2xl"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-700 group-hover:text-purple-700">Lịch sử mua<br>hàng</span>
                        </a>

                        <!-- 4. Thông báo -->
                        <a href="{{ route('admin.notifications') }}" class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-xl text-orange-600 hover:bg-orange-100 transition-all hover:scale-105 duration-200 group h-32 text-center relative">
                            <div class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></div>
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-200 transition-colors">
                                <i class="bi bi-bell-fill text-2xl"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-700 group-hover:text-orange-700">Thông báo</span>
                        </a>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center">
                            <i class="bi bi-activity text-green-500 mr-2"></i> Hoạt động mới
                        </h3>
                        <a href="#" class="text-xs text-blue-600 hover:underline">Đã đọc</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentActivities->take(4) as $activity)
                        <div class="flex items-start gap-3 relative pb-4 border-l-2 border-gray-100 pl-4 last:pb-0 last:border-0">
                            <div class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full bg-{{ $activity['icon_color'] ?? 'blue' }}-500 ring-4 ring-white"></div>
                            <div>
                                <p class="text-xs text-gray-800 font-medium leading-relaxed">{!! $activity['message'] !!}</p>
                                <span class="text-[10px] text-gray-400 font-medium mt-1 block">{{ $activity['time'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-400 text-xs">Chưa có hoạt động nào</div>
                        @endforelse
                        <button class="w-full text-center text-xs text-blue-600 font-bold hover:bg-blue-50 py-2 rounded-lg transition-colors mt-2">Xem tất cả <i class="bi bi-arrow-right ml-1"></i></button>
                    </div>
                </div>

                <!-- Dept Budget (Progress Bars) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center"><i class="bi bi-wallet2 text-gray-400 mr-2"></i> Ngân sách theo khoa</h3>
                    </div>
                    <div class="space-y-4">
                        @php 
                            $deptData = $chartData['departments']['data'] ?? [];
                            $maxSpending = (!empty($deptData) && max($deptData) > 0) ? max($deptData) : 1;
                        @endphp
                        @foreach(array_slice($chartData['departments']['labels'], 0, 4) as $index => $label)
                            @php 
                                $value = $chartData['departments']['data'][$index] ?? 0;
                                $percent = ($value / $maxSpending) * 100;
                            @endphp
                            <div>
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-xs font-bold text-gray-700">{{ $label }}</span>
                                    <span class="text-xs font-bold text-blue-600">{{ number_format($value) }} <span class="text-gray-400 font-normal">VNĐ</span></span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">70% đã sử dụng</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div id="orderDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[60] flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-gray-50 rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto transform transition-all scale-100">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-5 flex justify-between items-center z-10 shadow-sm">
                <div class="flex items-center space-x-4">
                    <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">Chi tiết đơn hàng</h2>
                </div>
                <div class="flex space-x-3">
                    <button class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors shadow-sm">
                        <i class="fas fa-print mr-2"></i> In đơn hàng
                    </button>
                    <button class="flex items-center px-4 py-2 border border-transparent rounded-lg text-white bg-blue-600 hover:bg-blue-700 font-medium text-sm transition-colors shadow-sm">
                        <i class="fas fa-file-pdf mr-2"></i> Xuất PDF
                    </button>
                    <button onclick="darkToggle()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                         <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
            
            <div id="orderDetailContent" class="p-8">
                <!-- Loading State -->
                <div class="flex flex-col justify-center items-center py-24">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent"></div>
                    <p class="mt-4 text-gray-500 font-medium">Đang tải dữ liệu...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const chartData = @json($chartData);
    
    // Config
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                displayColors: true,
                boxPadding: 6
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: { border: { display: false }, grid: { color: '#f1f5f9' } }
        }
    };

    // Create gradients
    const createGradient = (ctx, color1, color2) => {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    };

    // 1. Trend Chart (Bar + Line Combo)
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendGradient = createGradient(trendCtx, '#3b82f6', '#60a5fa');
    
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: chartData.trend.labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Chi phí',
                    data: chartData.trend.data,
                    backgroundColor: trendGradient,
                    hoverBackgroundColor: '#2563eb',
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                    order: 2
                },
                {
                    type: 'line',
                    label: 'Xu hướng',
                    data: chartData.trend.data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#059669',
                    pointHoverBorderWidth: 3,
                    order: 1
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        padding: 15,
                        font: { size: 11, weight: '600' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                y: {
                    ...commonOptions.scales.y,
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => v >= 1000000 ? (v/1000000).toFixed(0) + 'M' : v,
                        font: { size: 11, weight: 500 },
                        color: '#94a3b8'
                    }
                },
                x: {
                    ...commonOptions.scales.x,
                    ticks: { 
                        font: { size: 11 },
                        color: '#94a3b8'
                    }
                }
            }
        }
    });

    // 2. Category Chart (Doughnut)
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.categories.labels,
            datasets: [{
                data: chartData.categories.data,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            ...commonOptions,
            cutout: '75%',
            scales: { display: false }
        }
    });

    // 3. Department Comparison (Bar + Line Combo)
    const deptCtx = document.getElementById('departmentChart').getContext('2d');
    const deptGradient = createGradient(deptCtx, '#94a3b8', '#cbd5e1');
    
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: chartData.departments.labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Chi tiêu',
                    data: chartData.departments.data,
                    backgroundColor: deptGradient,
                    hoverBackgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barPercentage: 0.6,
                    order: 2
                },
                {
                    type: 'line',
                    label: 'Xu hướng',
                    data: chartData.departments.data,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#d97706',
                    pointHoverBorderWidth: 3,
                    order: 1
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        padding: 15,
                        font: { size: 11, weight: '600' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                y: {
                    ...commonOptions.scales.y,
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => v >= 1000000 ? (v/1000000).toFixed(0) + 'M' : v,
                        font: { size: 11, weight: 500 },
                        color: '#94a3b8'
                    }
                },
                x: {
                    ...commonOptions.scales.x,
                    ticks: { 
                        font: { size: 11 },
                        color: '#94a3b8'
                    }
                }
            }
        }
    });

    // Order Detail Modal Functions
    function viewOrderDetail(requestId) {
        const modal = document.getElementById('orderDetailModal');
        const content = document.getElementById('orderDetailContent');
        
        modal.classList.remove('hidden');
        // Reset content to loading state each time opened
        content.innerHTML = `
            <div class="flex flex-col justify-center items-center py-24">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent"></div>
                <p class="mt-4 text-gray-500 font-medium">Đang tải dữ liệu...</p>
            </div>
        `;
        
        // Fetch order details
        fetch(`/admin/purchase-requests/${requestId}/details`)
            .then(response => response.json())
            .then(data => {
                content.innerHTML = renderOrderDetails(data);
            })
            .catch(error => {
                console.error(error);
                content.innerHTML = `
                    <div class="flex flex-col justify-center items-center py-24 text-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800">Không thể tải thông tin</h3>
                        <p class="text-gray-500 mt-2">Đã có lỗi xảy ra khi tải dữ liệu đơn hàng.</p>
                        <button onclick="closeOrderModal()" class="mt-6 px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-medium">Đóng</button>
                    </div>
                `;
            });
    }
    
    function closeOrderModal() {
        document.getElementById('orderDetailModal').classList.add('hidden');
    }
    
    function darkToggle() {
       // Placeholder for dark mode toggle from design
       alert('Dark mode toggle placeholder');
    }
    
    function renderOrderDetails(data) {
        const items = data.items || [];
        const workflows = data.workflows || [];
        
        return `
            <div class="space-y-8">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">MÃ ĐƠN HÀNG</p>
                        <p class="text-lg font-bold text-gray-900 truncate" title="${data.request_code}">${data.request_code || 'N/A'}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">KHOA/PHÒNG</p>
                        <p class="text-lg font-bold text-gray-900 truncate" title="${data.department_name}">${data.department_name || 'N/A'}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">TRẠNG THÁI</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusClass(data.status)}">
                            <span class="w-2 h-2 rounded-full bg-current mr-2"></span>
                            ${getStatusText(data.status)}
                        </span>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">NGÀY TẠO</p>
                        <p class="text-lg font-bold text-gray-900">${data.created_at || 'N/A'}</p>
                    </div>
                </div>
                
                <!-- Product List -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 text-lg">Danh sách sản phẩm</h3>
                        <span class="text-sm text-gray-500 font-medium">${items.length} mặt hàng</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white text-gray-500 font-bold uppercase text-xs border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 w-1/2">Sản phẩm</th>
                                    <th class="px-6 py-4 text-center">Số lượng</th>
                                    <th class="px-6 py-4 text-right">Đơn giá</th>
                                    <th class="px-6 py-4 text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                ${items.map(item => `
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-12 w-12 flex-shrink-0 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden">
                                                    ${item.product_image 
                                                        ? `<img src="${item.product_image}" class="h-full w-full object-cover">`
                                                        : `<i class="fas fa-box text-gray-400 text-xl"></i>`
                                                    }
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-bold text-gray-900 text-base">${item.product_name}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">SKU: ${item.product_code}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-medium text-gray-900">${item.quantity}</span>
                                            <span class="text-xs text-gray-500 ml-1">${item.unit}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-gray-600">
                                            ${formatCurrency(item.unit_price)}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900 text-base">
                                            ${formatCurrency(item.quantity * item.unit_price)}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right text-gray-600 font-medium">Tạm tính:</td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900">${formatCurrency(data.total_amount)}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right text-gray-600 font-medium border-none">Thuế (0%):</td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900 border-none">0 ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-800 text-lg">TỔNG THANH TOÁN:</td>
                                    <td class="px-6 py-4 text-right font-bold text-blue-600 text-2xl">${formatCurrency(data.total_amount)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Bottom Section: Note & History -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Note Section -->
                    <div class="bg-blue-50 bg-opacity-50 rounded-xl border border-blue-100 p-6">
                        <h4 class="flex items-center text-blue-800 font-bold mb-3">
                            <i class="fas fa-info-circle mr-2"></i> GHI CHÚ ĐƠN HÀNG
                        </h4>
                        <div class="text-blue-900 italic leading-relaxed text-sm bg-white bg-opacity-60 p-4 rounded-lg border border-blue-100">
                            "${data.note ? data.note : 'Không có ghi chú cho đơn hàng này.'}"
                        </div>
                    </div>
                    
                    <!-- History Section -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h4 class="flex items-center text-gray-800 font-bold mb-6">
                            <i class="fas fa-history mr-2"></i> LỊCH SỬ THAY ĐỔI
                        </h4>
                        
                        <div class="relative pl-4 border-l-2 border-gray-200 space-y-8">
                            ${workflows.length > 0 ? workflows.map((flow, index) => `
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full border-2 border-white ${index === 0 ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-gray-300'}"></div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">
                                            ${getStatusText(flow.status)}
                                            ${flow.status === 'APPROVED' ? 'đã được duyệt' : 
                                              flow.status === 'REJECTED' ? 'đã bị từ chối' : 
                                              flow.status === 'SUBMITTED' ? 'đã được gửi đi' : 
                                              flow.status === 'CREATED' ? 'đã được tạo' : ''}
                                        </span>
                                        <span class="text-xs text-gray-500 mt-1">
                                            Bởi <span class="font-medium text-gray-700">${flow.user_name}</span> - ${flow.action_time}
                                        </span>
                                        ${flow.note ? `<p class="text-sm text-gray-600 mt-2 bg-gray-50 p-2 rounded border border-gray-100 italic">"${flow.note}"</p>` : ''}
                                    </div>
                                </div>
                            `).join('') : `
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full border-2 border-white bg-blue-600 ring-4 ring-blue-100"></div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">Khởi tạo đơn hàng</span>
                                        <span class="text-xs text-gray-500 mt-1">Bởi <span class="font-medium text-gray-700">${data.department_name}</span> - ${data.created_at}</span>
                                    </div>
                                </div>
                            `}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function getStatusClass(status) {
        const classes = {
            'APPROVED': 'bg-green-100 text-green-800',
            'SUBMITTED': 'bg-orange-100 text-orange-800',
            'REJECTED': 'bg-red-100 text-red-800',
            'COMPLETED': 'bg-blue-100 text-blue-800',
            'PENDING': 'bg-yellow-100 text-yellow-800',
            'DRAFT': 'bg-gray-100 text-gray-800',
            'CANCELLED': 'bg-gray-200 text-gray-600'
        };
        return classes[status] || 'bg-gray-100 text-gray-700';
    }
    
    function getStatusText(status) {
        const texts = {
            'APPROVED': 'Đã duyệt',
            'SUBMITTED': 'Chờ duyệt',
            'REJECTED': 'Từ chối',
            'COMPLETED': 'Hoàn thành',
            'PENDING': 'Chờ xử lý',
            'DRAFT': 'Nháp',
            'CANCELLED': 'Đã hủy'
        };
        return texts[status] || status;
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
</script>
@endpush
