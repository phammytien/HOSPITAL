@extends('layouts.admin')


@section('page-title', 'Quản lý nhân viên & Khoa phòng')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-gray-500 mt-1">Quản lý cơ cấu tổ chức và ngân sách bệnh viện</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-80">
                <input type="text" id="searchInput" placeholder="Tìm kiếm khoa/phòng..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
            <button onclick="openDepartmentModal()" class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>Thêm khoa/phòng
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Departments -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center">
            <div class="h-14 w-14 rounded-lg bg-blue-50 flex items-center justify-center mr-5">
                <i class="fas fa-network-wired text-blue-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Tổng số Khoa/Phòng</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalDepartments }}</p>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center">
            <div class="h-14 w-14 rounded-lg bg-green-50 flex items-center justify-center mr-5">
                <i class="fas fa-users text-green-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Tổng Nhân viên</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalEmployees }}</p>
            </div>
        </div>

        <!-- Total Budget -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center">
            <div class="h-14 w-14 rounded-lg bg-yellow-50 flex items-center justify-center mr-5">
                <i class="fas fa-wallet text-yellow-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Tổng Ngân sách</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalBudget) }} VNĐ</p>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($departments as $dept)
        <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 group relative">
            <!-- Header -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $dept->department_name }}</h3>
                    <span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full uppercase tracking-wider">
                        {{ $dept->department_code }}
                    </span>
                </div>
                <!-- Action Buttons -->
                <div class="flex space-x-1 opacity-100 transition-opacity">
                    <button onclick="viewEmployees({{ $dept->id }}, '{{ $dept->department_name }}')" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Xem nhân viên">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="openDepartmentModal('edit', {{ $dept->id }})" class="p-2 text-gray-400 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors" title="Chỉnh sửa">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button onclick="deleteDepartment({{ $dept->id }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6 min-h-[48px]">
                <p class="text-gray-500 text-sm leading-relaxed line-clamp-2">
                    {{ $dept->description ?: 'Chưa có mô tả cho khoa phòng này.' }}
                </p>
            </div>

            <!-- Budget Progress -->
            <div class="mb-6">
                <div class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                    <span>NGÂN SÁCH HIỆN TẠI</span>
                    <span class="text-blue-900">{{ number_format($dept->budget_amount) }} VNĐ</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 mb-2 overflow-hidden">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                         style="width: {{ $dept->usage_percent }}%"></div>
                </div>
                <div class="text-right text-xs text-gray-400">
                    Đã sử dụng {{ $dept->usage_percent }}%
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                <div class="flex items-center text-blue-700 font-medium text-sm cursor-pointer hover:underline" 
                     onclick="viewEmployees({{ $dept->id }}, '{{ $dept->department_name }}')">
                    <i class="fas fa-users mr-2 bg-blue-50 p-1.5 rounded-full text-xs"></i>
                    {{ $dept->users_count }} nhân viên
                </div>
                <div class="text-xs text-gray-400">
                    Cập nhật {{ $dept->last_updated }}
                </div>
            </div>
        </div>
        @endforeach

        <!-- Add New Card -->
        <button onclick="openDepartmentModal()" class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col items-center justify-center text-gray-400 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-300 min-h-[300px] group">
            <div class="h-16 w-16 rounded-full bg-gray-50 group-hover:bg-blue-100 flex items-center justify-center mb-4 transition-colors">
                <i class="fas fa-plus text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-1">Thêm Khoa phòng mới</h3>
            <p class="text-sm text-gray-400 group-hover:text-blue-400">Khởi tạo đơn vị quản lý mới cho bệnh viện</p>
        </button>
    </div>

    <!-- Pagination -->
    @if($departments->hasPages())
    <div class="mt-8 flex justify-center">
        <div class="flex items-center gap-2">
            {{-- Previous Button --}}
            @if ($departments->onFirstPage())
                <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-1"></i>Trước
                </span>
            @else
                <a href="{{ $departments->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-chevron-left mr-1"></i>Trước
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($departments->getUrlRange(1, $departments->lastPage()) as $page => $url)
                @if ($page == $departments->currentPage())
                    <span class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($departments->hasMorePages())
                <a href="{{ $departments->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Sau<i class="fas fa-chevron-right ml-1"></i>
                </a>
            @else
                <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                    Sau<i class="fas fa-chevron-right ml-1"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Employee List Modal -->
<div id="employeeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] flex flex-col transform transition-all scale-100 overflow-hidden font-sans">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-start bg-white">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Nhân viên - Ten Khoa</h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-0.5">DANH SÁCH NHÂN SỰ</p>
                </div>
            </div>
            <button onclick="closeEmployeeModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-full h-8 w-8 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="p-6 overflow-y-auto custom-scrollbar bg-gray-50/50 flex-1">
            <div id="employeeList" class="space-y-4">
                <!-- Cards go here -->
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-between items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <p class="text-sm text-gray-500" id="employeeCountText">Hiển thị 0 nhân viên</p>
            <div class="flex gap-3">
                <button onclick="closeEmployeeModal()" class="px-4 py-2 bg-white text-gray-700 font-bold hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                    Đóng
                </button>
                <button onclick="Swal.fire('Tính năng đang phát triển', 'Vui lòng sử dụng trang Quản lý nhân viên để thêm mới.', 'info')" class="px-4 py-2 bg-blue-500 text-white font-bold hover:bg-blue-600 rounded-lg shadow-blue-200 shadow-md hover:shadow-lg transition-all flex items-center">
                   Thêm nhân viên
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Department Modal -->
<div id="departmentModal" class="hidden fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeDepartmentModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full font-sans">
            <!-- Header -->
            <div class="bg-white px-8 py-5 flex justify-between items-center border-b border-gray-100">
                <div class="flex items-center gap-3">
                     <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-hospital-user text-lg"></i>
                     </div>
                     <h3 class="text-xl font-bold text-gray-900" id="departmentModalTitle">Thêm khoa/phòng mới</h3>
                </div>
                <button onclick="closeDepartmentModal()" class="text-gray-400 hover:text-gray-600 transition-colors h-8 w-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <div class="px-8 py-6">
                <form id="departmentForm">
                    <div class="space-y-6">
                        <!-- Name & Desc -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Tên khoa/phòng <span class="text-red-500">*</span></label>
                            <input type="text" name="department_name" id="department_name" class="block w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all sm:text-sm py-2.5 px-4" placeholder="Nhập tên khoa phòng..." required>
                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="department_name"></p>
                            <p class="mt-2 text-xs text-gray-500 flex items-center"><i class="fas fa-info-circle mr-1.5 text-blue-500"></i> Mã khoa sẽ được tạo tự động từ tên khoa</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Mô tả chức năng</label>
                            <textarea name="description" id="description" rows="3" class="block w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all sm:text-sm py-2.5 px-4" placeholder="Mô tả nhiệm vụ, chức năng của khoa phòng..."></textarea>
                            <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="description"></p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-800 mb-2">Ngân sách (VNĐ) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <span class="text-gray-400 font-bold sm:text-sm">đ</span>
                                    </div>
                                    <input type="number" name="budget_amount" id="budget_amount" class="block w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white pl-8 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all sm:text-sm py-2.5" placeholder="0" required>
                                </div>
                                <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="budget_amount"></p>
                            </div>
                            
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-800 mb-2">Kỳ ngân sách</label>
                                <input type="text" name="budget_period" id="budget_period" value="{{ date('Y') }}" readonly class="block w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed focus:border-gray-200 focus:ring-0 transition-all sm:text-sm py-2.5 px-4">
                                <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="budget_period"></p>
                            </div>
                        </div>
                        
                        <!-- Department Head Info -->
                        <div id="headInfoSection" class="bg-blue-50/50 rounded-xl p-6 border border-blue-100">
                            <div class="flex items-center mb-5">
                                <div class="bg-white p-2 rounded-lg mr-3 shadow-sm text-blue-600">
                                    <i class="fas fa-user-shield text-lg"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-900">Thông tin Trưởng khoa</h4>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-5">
                                <div class="col-span-1">
                                    <label class="block text-[11px] font-bold text-blue-800 uppercase tracking-wider mb-1.5">Họ và tên</label>
                                    <input type="text" name="head_name" id="head_name" class="block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:text-sm py-2.5 px-3" placeholder="Nguyễn Văn A">
                                    <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="head_name"></p>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-[11px] font-bold text-blue-800 uppercase tracking-wider mb-1.5">Email liên hệ</label>
                                    <input type="email" name="head_email" id="head_email" class="block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 sm:text-sm py-2.5 px-3" placeholder="email@hospital.com">
                                    <p class="mt-1 text-xs text-red-600 error-feedback hidden" data-field="head_email"></p>
                                </div>
                                <div class="col-span-2">
                                     <div class="bg-blue-100/50 rounded-lg p-3 flex items-start gap-3 border border-blue-100">
                                        <i class="fas fa-shield-alt text-blue-600 mt-0.5 ml-1"></i>
                                        <p class="text-xs text-blue-700 font-medium leading-relaxed">
                                            Tài khoản sẽ được tạo tự động và thông tin đăng nhập gửi qua email này.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="bg-gray-50/50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-gray-100">
                <button type="button" id="departmentSubmitBtn" onclick="document.getElementById('departmentForm').dispatchEvent(new Event('submit'))" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-2.5 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all hover:shadow-md">
                    Lưu khoa/phòng
                </button>
                <button type="button" onclick="closeDepartmentModal()" class="inline-flex justify-center rounded-lg border border-gray-200 shadow-sm px-6 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                    Hủy bỏ
                </button>
            </div>
        </div>
    </div>
</div>



<!-- User Detail Modal (Audit Logs) -->
<div id="userDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[70] backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] flex flex-col transform transition-all scale-100 overflow-hidden font-sans">
        <!-- Header -->
        <div class="px-8 py-5 border-b border-gray-100 bg-white flex justify-between items-start">
            <div class="flex flex-col">
                <div class="flex items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900" id="userModalTitle">Chi tiết nhật ký hoạt động</h3>
                </div>
                 <div class="flex items-center gap-3 mt-2">
                    <span id="userModalRole" class="px-3 py-1 rounded text-xs font-bold uppercase tracking-wide bg-gray-100 text-gray-600">ROLE</span>
                    <span id="userModalEmail" class="text-gray-500 text-sm">email@example.com</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                 <div class="relative">
                    <input type="text" placeholder="Tìm kiếm hành động, IP..." class="pl-10 pr-4 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-100 w-64 transition-all hover:bg-gray-100">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
                <!-- Theme Toggle (Mock) -->
                <button class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="far fa-moon text-lg"></i>
                </button>
                <button onclick="closeUserDetailModal()" class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Filters -->
         <div class="px-8 py-0 border-b border-gray-100 bg-white flex justify-between items-center text-sm sticky top-0 z-10">
            <div class="flex space-x-8 font-bold text-gray-500 pt-4">
                <button class="text-blue-600 border-b-2 border-blue-600 pb-3 font-extrabold cursor-default">TẤT CẢ HOẠT ĐỘNG</button>
                <!-- <button class="hover:text-blue-600 transition-colors pb-3 border-b-2 border-transparent hover:border-blue-100">ĐĂNG NHẬP</button>
                <button class="hover:text-blue-600 transition-colors pb-3 border-b-2 border-transparent hover:border-blue-100">THAY ĐỔI DỮ LIỆU</button>
                <button class="hover:text-blue-600 transition-colors pb-3 border-b-2 border-transparent hover:border-blue-100">BÁO CÁO</button> -->
            </div>
            <div class="flex items-center gap-3 py-2">
                 <span class="text-gray-400">Thời gian:</span>
                 <div class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 text-gray-700 font-bold cursor-pointer hover:bg-gray-100 transition-colors">
                    {{ date('d/m/Y') }} - {{ date('d/m/Y') }}
                 </div>
            </div>
        </div>
        
        <!-- Body -->
        <div class="p-8 overflow-y-auto custom-scrollbar bg-gray-50/50 flex-1">
            <!-- Headers -->
            <div class="flex items-center justify-between px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 mb-2">
                <div class="flex items-center gap-6 flex-1">
                    <div class="min-w-[100px]">Thời gian</div>
                    <div class="min-w-[180px]">Hành động</div>
                    <div class="flex-1">Chi tiết thay đổi</div>
                </div>
                <div class="flex items-center gap-8">
                    <div class="w-[100px] text-right">Địa chỉ IP</div>
                    <div class="w-[250px] text-right">Thiết bị</div>
                </div>
            </div>

            <div id="auditLogList" class="space-y-4">
                <!-- Logs will be loaded here -->
                <div class="flex flex-col items-center justify-center py-12 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin text-3xl mb-3 text-blue-500"></i>
                    <p>Đang tải dữ liệu...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="mt-8 flex justify-between items-center text-sm text-gray-500 border-t pt-4 border-gray-100" id="paginationContainer">
                <span id="logCountText">Hiển thị 0 kết quả</span>
                <div id="paginationControls" class="flex gap-2">
                    <!-- Controls -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const allUsers = @json($users ?? []);

function viewEmployees(deptId, deptName) {
    const modal = document.getElementById('employeeModal');
    const title = document.getElementById('modalTitle');
    const list = document.getElementById('employeeList');
    const countText = document.getElementById('employeeCountText');
    
    title.textContent = `Nhân viên - ${deptName}`;
    
    // Filter employees by department
    const employees = allUsers.filter(user => user.department_id === deptId);
    
    // Update count text
    if (countText) {
        countText.textContent = `Hiển thị ${employees.length} trên ${allUsers.length} nhân viên`;
    }
    
    if (employees.length === 0) {
        list.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="h-24 w-24 bg-gray-100/50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                    <i class="fas fa-users-slash text-4xl text-gray-300"></i>
                </div>
                <h4 class="text-gray-900 font-bold text-lg">Chưa có nhân viên</h4>
                <p class="text-gray-500 mt-1 max-w-xs text-sm">Khoa phòng này hiện tại chưa có nhân viên nào được ghi nhận trong hệ thống.</p>
            </div>
        `;
    } else {
        list.innerHTML = employees.map((user, index) => {
            // Determine role badge color and text
            let roleClass = 'bg-gray-100 text-gray-600';
            let roleText = 'Nhân viên';
            let userRole = user.role || 'DEPARTMENT';
            
            // Map roles roughly to what's in the image style
            if (userRole === 'ADMIN') {
                roleClass = 'bg-purple-100 text-purple-700';
                roleText = 'Quản trị viên';
            } else if (userRole === 'BUYER') {
                roleClass = 'bg-blue-100 text-blue-700';
                roleText = 'Mua hàng';
            } else {
                roleClass = 'bg-gray-200 text-gray-700'; // Generic for department staff
                roleText = 'Nhân viên';
            }
            
            return `
            <div class="flex flex-col sm:flex-row items-center justify-between p-5 bg-white border border-gray-100 rounded-2xl hover:shadow-lg hover:border-blue-100 transition-all duration-300 group relative overflow-hidden">
                <!-- Left Accent -->
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="flex items-center space-x-5 w-full sm:w-auto">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="h-14 w-14 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xl shadow-md border-2 border-white ring-2 ring-blue-50">
                            ${user.full_name.charAt(0).toUpperCase()}
                        </div>
                        <span class="absolute bottom-0 right-0 h-4 w-4 bg-green-500 border-2 border-white rounded-full shadow-sm" title="Online"></span>
                    </div>
                    
                    <!-- Info -->
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-bold text-gray-900 text-base group-hover:text-blue-700 transition-colors">${user.full_name}</h4>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded ${roleClass}">
                                ${roleText}
                            </span>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-sm text-gray-500 flex items-center mt-0.5">
                                <i class="far fa-envelope mr-2 text-gray-400 w-4"></i> ${user.email}
                            </p>
                            <p class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-phone-alt mr-2 text-gray-400 w-4 text-xs"></i> ${user.phone_number || '---'}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3 mt-4 sm:mt-0 w-full sm:w-auto pl-14 sm:pl-0">
                    <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-100 hidden sm:inline-block">
                        Khoa/Phòng
                    </span>
                    <button onclick="openUserDetailModal(${user.id})" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm flex items-center gap-2">
                        <i class="far fa-eye"></i> Chi tiết
                    </button>
                </div>
            </div>
            `;
        }).join('');
    }
    
    modal.classList.remove('hidden');
}

function closeEmployeeModal() {
    document.getElementById('employeeModal').classList.add('hidden');
}

function openDepartmentModal(mode = 'add', id = null) {
    const modal = document.getElementById('departmentModal');
    const form = document.getElementById('departmentForm');
    const modalTitle = document.getElementById('departmentModalTitle');
    const submitBtn = document.getElementById('departmentSubmitBtn');
    const headInfoSection = document.getElementById('headInfoSection');
    
    // Reset form
    form.reset();
    form.dataset.id = '';
    form.dataset.mode = mode;
    
    // Show/hide department head section
    if (mode === 'add') {
        modalTitle.textContent = 'Thêm khoa/phòng mới';
        submitBtn.classList.remove('hidden');
        submitBtn.textContent = 'Lưu khoa/phòng';
        headInfoSection.classList.remove('hidden');
        document.getElementById('head_name').required = true;
        document.getElementById('head_email').required = true;
        // Set default budget period to current year
        document.getElementById('budget_period').value = new Date().getFullYear();
    } else {
        headInfoSection.classList.add('hidden');
        document.getElementById('head_name').required = false;
        document.getElementById('head_email').required = false;
        
        if (mode === 'edit') {
            modalTitle.textContent = 'Cập nhật khoa/phòng';
            submitBtn.classList.remove('hidden');
            submitBtn.textContent = 'Cập nhật thay đổi';
        } else if (mode === 'view') {
            modalTitle.textContent = 'Chi tiết khoa/phòng';
            submitBtn.classList.add('hidden');
        }
        
        // Fetch department data
        if (id) {
            fetch(`/admin/departments/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('department_name').value = data.department_name;
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('budget_amount').value = data.budget_amount;
                    document.getElementById('budget_period').value = data.budget_period || '';
                    
                    form.dataset.id = id;
                    
                    // Disable inputs in view mode
                    if (mode === 'view') {
                        form.querySelectorAll('input, textarea').forEach(el => {
                            el.disabled = true;
                            el.classList.add('bg-gray-100');
                        });
                    }
                });
        }
    }
    
    modal.classList.remove('hidden');
}

function closeDepartmentModal() {
    const modal = document.getElementById('departmentModal');
    const form = document.getElementById('departmentForm');
    
    // Re-enable all inputs
    form.querySelectorAll('input, textarea').forEach(el => {
        el.disabled = false;
        el.classList.remove('bg-gray-100');
    });
    
    modal.classList.add('hidden');
}

// Handle form submission
document.getElementById('departmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = this.dataset.id;
    const mode = this.dataset.mode;
    const url = id ? `/admin/departments/${id}` : '/admin/departments';
    const method = id ? 'PUT' : 'POST';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Show loading state
    const submitBtn = document.getElementById('departmentSubmitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            // Handle validation errors (422) or other errors
            if (res.status === 422) {
                // Clear previous errors
                document.querySelectorAll('.error-feedback').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('input, textarea').forEach(el => el.classList.remove('border-red-500'));
                
                // Show new errors
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.querySelector(`.error-feedback[data-field="${field}"]`);
                    const inputEl = document.querySelector(`[name="${field}"]`);
                    
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                        errorEl.classList.remove('hidden');
                    }
                    if (inputEl) {
                        inputEl.classList.add('border-red-500');
                    }
                });
                
                // Throw error to skip success block
                throw new Error('Validation failed');
            }
            throw new Error(data.message || 'Có lỗi xảy ra');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: data.message || 'Có lỗi xảy ra, vui lòng kiểm tra lại.',
                confirmButtonColor: '#3085d6',
            });
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;

        if (err.message === 'Validation failed') return;
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi hệ thống',
            text: 'Không thể kết nối đến máy chủ.',
        });
    });
});

// Delete department
function deleteDepartment(id) {
    Swal.fire({
        title: 'Xóa khoa phòng?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Xóa ngay',
        cancelButtonText: 'Hủy bỏ'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/departments/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Đã xóa!',
                        data.message,
                        'success'
                    ).then(() => location.reload());
                } else {
                    Swal.fire('Lỗi', 'Không thể xóa khoa/phòng.', 'error');
                }
            });
        }
    });
}

function getRoleBadgeClass(role) {
    switch(role) {
        case 'ADMIN': return 'bg-purple-100 text-purple-700';
        case 'BUYER': return 'bg-blue-100 text-blue-700';
        default: return 'bg-green-100 text-green-700';
    }
}

function getRoleText(role) {
    switch(role) {
        case 'ADMIN': return 'Quản trị viên';
        case 'BUYER': return 'Nhân viên Mua hàng';
        default: return 'Nhân viên Khoa/Phòng';
    }
}

// Search functionality
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.grid > div.group'); // Target department cards
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});


// ... [existing functions] ...

// Store current user ID for pagination
let currentUserId = null;

function openUserDetailModal(userId) {
    const user = allUsers.find(u => u.id === userId);
    if (!user) return;

    // Store user ID for pagination
    currentUserId = userId;

    // Update Header Info
    // Update Header Info
    document.getElementById('userModalTitle').innerHTML = `Chi tiết nhật ký hoạt động <span class="text-gray-400 font-normal text-lg ml-2">#${user.id}</span>`;
    document.getElementById('userModalEmail').textContent = user.email;
    
    // Role Badge
    const roleSpan = document.getElementById('userModalRole');
    roleSpan.textContent = getRoleText(user.role);
    roleSpan.className = `px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide ${getRoleBadgeClass(user.role)}`;

    // Show Modal
    const modal = document.getElementById('userDetailModal');
    modal.classList.remove('hidden');

    // Fetch Logs (page 1)
    fetchAuditLogs(userId, 1);
}

function closeUserDetailModal() {
    document.getElementById('userDetailModal').classList.add('hidden');
}

function fetchAuditLogs(userId, page = 1) {
    const listContainer = document.getElementById('auditLogList');
    listContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center py-12 text-center text-gray-400">
            <i class="fas fa-spinner fa-spin text-3xl mb-3 text-blue-500"></i>
            <p>Đang tải dữ liệu...</p>
        </div>
    `;

    fetch(`/admin/users/${userId}/audit-logs?page=${page}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                renderAuditLogs(data.data, data.pagination);
            } else {
                listContainer.innerHTML = `
                   <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="h-24 w-24 bg-gray-100/50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                            <i class="far fa-clipboard text-4xl text-gray-300"></i>
                        </div>
                        <h4 class="text-gray-900 font-bold text-lg">Không có dữ liệu</h4>
                        <p class="text-gray-500 mt-1 max-w-xs text-sm">Chưa có nhật ký hoạt động nào được ghi nhận cho người dùng này.</p>
                    </div>
                `;
                document.getElementById('logCountText').textContent = 'Hiển thị 0 kết quả';
                renderPagination(null);
            }
        })
        .catch(err => {
            console.error(err);
            listContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p>Không thể tải dữ liệu.</p>
                </div>
            `;
            renderPagination(null);
        });
}

function renderAuditLogs(logs, pagination) {
    const listContainer = document.getElementById('auditLogList');
    
    // Update count text with pagination info
    if (pagination) {
        document.getElementById('logCountText').textContent = `Hiển thị ${pagination.from} - ${pagination.to} trên ${pagination.total} kết quả`;
    } else {
        document.getElementById('logCountText').textContent = `Hiển thị ${logs.length} kết quả`;
    }
    
    listContainer.innerHTML = logs.map(log => {
        let actionClass = 'bg-gray-100 text-gray-600 border border-gray-200';
        let icon = 'fa-info-circle';
        let actionText = log.action;

        if (log.action.includes('Đăng nhập thành công') || log.action.includes('Login')) {
            actionClass = 'bg-green-50 text-green-700 border border-green-100';
            icon = 'fa-check-circle';
            actionText = 'Đăng nhập thành công';
        } else if (log.action.includes('thất bại') || log.action.includes('error') || log.action.includes('Sai mật khẩu')) {
            actionClass = 'bg-red-50 text-red-700 border border-red-100';
            icon = 'fa-exclamation-triangle';
        } else if (log.action.includes('Truy cập') || log.action.includes('Xem')) {
            actionClass = 'bg-blue-50 text-blue-700 border border-blue-100';
            icon = 'fa-eye';
            if (log.action.includes('hồ sơ')) actionText = 'Truy cập hồ sơ';
        } else if (log.action.includes('Chỉnh sửa') || log.action.includes('Cập nhật')) {
            actionClass = 'bg-yellow-50 text-yellow-700 border border-yellow-100';
            icon = 'fa-pen';
             if (log.action.includes('bệnh án')) actionText = 'Chỉnh sửa bệnh án';
        } else if (log.action.includes('Xuất')) {
            actionClass = 'bg-gray-100 text-gray-700 border border-gray-200';
            icon = 'fa-file-export';
            actionText = 'Xuất báo cáo';
        }

        let date = log.created_at || '';
        let time = '';

        // Manually parse YYYY-MM-DD HH:mm:ss to avoid Invalid Date
        try {
            if (log.created_at && log.created_at.includes(' ')) {
                const parts = log.created_at.split(' ');
                if (parts.length === 2) {
                    // Convert YYYY-MM-DD to dd/mm/yyyy
                    const dateParts = parts[0].split('-');
                    if (dateParts.length === 3) {
                        date = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                    } else {
                        date = parts[0];
                    }
                    time = parts[1];
                }
            } else {
                // Fallback for other formats
                 const d = new Date(log.created_at);
                 if (!isNaN(d.getTime())) {
                     date = d.toLocaleDateString('vi-VN');
                     time = d.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
                 }
            }
        } catch (e) {
            console.error(e);
        }

        return `
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between group">
                <div class="flex items-center gap-6 flex-1">
                    <!-- Time -->
                    <div class="min-w-[100px]">
                        <div class="font-bold text-gray-900">${date}</div>
                        <div class="text-sm text-gray-400 mt-0.5">${time}</div>
                    </div>
                    
                    <!-- Badge -->
                    <div class="min-w-[180px]">
                         <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold ${actionClass}">
                            <i class="fas ${icon}"></i> ${actionText}
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="text-gray-600 text-sm font-medium line-clamp-1 flex-1 pr-4" title="${log.description}">
                        ${log.description || 'Không có mô tả chi tiết'}
                    </div>
                </div>

                <div class="flex items-center gap-8 text-sm">
                    <!-- IP -->
                    <div class="font-bold text-gray-800 font-mono w-[100px] text-right">${log.ip_address || 'N/A'}</div>
                    <!-- User Agent -->
                    <div class="text-gray-400 text-xs text-right w-[250px] leading-tight line-clamp-2" title="${log.device_agent}">
                        ${log.device_agent || 'Unknown Agent'}
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Render pagination controls (same function as before, just ensuring it's called)
    renderPagination(pagination);
}

function renderPagination(pagination) {
    const container = document.getElementById('paginationControls');
    
    if (!pagination || pagination.total === 0) {
        container.innerHTML = '';
        return;
    }
    
    const currentPage = pagination.current_page;
    const totalPages = pagination.total_pages;
    
    let html = '';
    
    // Previous button
    html += `<button onclick="changePage(${currentPage - 1})" class="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50 transition-colors" ${currentPage === 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i>
    </button>`;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            html += `<button class="px-3 py-1 bg-blue-600 text-white rounded font-bold shadow-sm">${i}</button>`;
        } else {
            html += `<button onclick="changePage(${i})" class="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 transition-colors">${i}</button>`;
        }
    }
    
    // Next button
    html += `<button onclick="changePage(${currentPage + 1})" class="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50 transition-colors" ${currentPage === totalPages ? 'disabled' : ''}>
        <i class="fas fa-chevron-right"></i>
    </button>`;
    
    container.innerHTML = html;
}

function changePage(page) {
    if (currentUserId) {
        fetchAuditLogs(currentUserId, page);
    }
}
</script>
@endpush
