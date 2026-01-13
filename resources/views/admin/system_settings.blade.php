@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt hệ thống')

@section('content')
<div class="space-y-6">

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('restore')" id="tab-restore" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    <i class="fas fa-undo mr-2"></i>Khôi phục dữ liệu
                </button>
                <button onclick="switchTab('backup')" id="tab-backup" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-database mr-2"></i>Backup Database
                </button>
                <button onclick="switchTab('maintenance')" id="tab-maintenance" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-tools mr-2"></i>Bảo trì hệ thống
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Restore Data Tab -->
            <div id="content-restore" class="tab-content">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 max-w-md">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn bảng dữ liệu</label>
                            <select id="tableSelect" onchange="loadDeletedData()" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Chọn bảng --</option>
                                <option value="products">Sản phẩm</option>
                                <option value="product_categories">Danh mục sản phẩm</option>
                                <option value="departments">Khoa phòng</option>
                                <option value="purchase_orders">Đơn hàng</option>
                                <option value="purchase_requests">Yêu cầu mua hàng</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="restoreSelected()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium">
                                <i class="fas fa-undo mr-2"></i>Khôi phục đã chọn
                            </button>
                            <button onclick="permanentDeleteSelected()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium">
                                <i class="fas fa-trash mr-2"></i>Xóa vĩnh viễn
                            </button>
                        </div>
                    </div>

                    <!-- Deleted Data Table -->
                    <div id="deletedDataContainer" class="hidden">
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left">
                                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="deletedDataBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="noDataMessage" class="text-center py-12 text-gray-500">
                        <i class="fas fa-info-circle text-4xl mb-3"></i>
                        <p>Vui lòng chọn bảng để xem dữ liệu đã xóa</p>
                    </div>
                </div>
            </div>

            <!-- Backup Database Tab -->
            <div id="content-backup" class="tab-content hidden">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Quản lý Backup</h3>
                            <p class="text-sm text-gray-600">Tạo và quản lý các bản sao lưu database</p>
                        </div>
                        <button onclick="createBackup()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-plus mr-2"></i>Tạo Backup Mới
                        </button>
                    </div>

                    <!-- Warning Alert -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Lưu ý:</strong> Khi restore backup, toàn bộ dữ liệu hiện tại sẽ bị ghi đè. Hãy chắc chắn bạn đã backup dữ liệu hiện tại trước khi restore.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Backup List -->
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên File</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kích thước</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="backupListBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Backup files will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Mode Tab -->
            <div id="content-maintenance" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Quản lý Bảo trì Hệ thống</h3>
                            <p class="text-sm text-gray-600">Bật/tắt chế độ bảo trì và tùy chỉnh thông báo</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span id="maintenanceStatusText" class="text-sm font-medium"></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="maintenanceToggle" onchange="toggleMaintenanceMode()" class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-7 peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Lưu ý:</strong> Khi bật chế độ bảo trì, tất cả người dùng (trừ Admin) sẽ không thể truy cập hệ thống. Chỉ bật khi thực sự cần thiết.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Message Editor -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thông báo bảo trì</label>
                        <textarea id="maintenanceMessage" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nhập nội dung thông báo bảo trì..."></textarea>
                        <div class="mt-4 flex justify-end">
                            <button onclick="updateMaintenanceMessage()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                                <i class="fas fa-save mr-2"></i>Lưu thông báo
                            </button>
                        </div>
                    </div>

                    <!-- Current Status Info -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Trạng thái hiện tại</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Chế độ bảo trì</p>
                                <p id="currentStatus" class="text-sm font-medium">Đang tải...</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Cập nhật lần cuối</p>
                                <p id="lastUpdated" class="text-sm font-medium">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('styles')
<style>
/* Toggle Switch Styles - Removed custom styles in favor of pure Tailwind peer classes */
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Tab switching
function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('tab-' + tab).classList.add('active', 'border-blue-500', 'text-blue-600');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');

    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.getElementById('content-' + tab).classList.remove('hidden');

    // Load data for the active tab
    if (tab === 'backup') {
        loadBackupList();
    } else if (tab === 'maintenance') {
        loadMaintenanceSettings();
    }
}

// ========== RESTORE DATA FUNCTIONS ==========

function loadDeletedData() {
    const table = document.getElementById('tableSelect').value;
    
    if (!table) {
        document.getElementById('deletedDataContainer').classList.add('hidden');
        document.getElementById('noDataMessage').classList.remove('hidden');
        return;
    }

    fetch(`/admin/settings/deleted/${table}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayDeletedData(data.data, table);
            } else {
                Swal.fire('Lỗi', data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Lỗi', 'Không thể tải dữ liệu', 'error');
        });
}

function displayDeletedData(data, table) {
    const tbody = document.getElementById('deletedDataBody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Không có dữ liệu đã xóa</td></tr>';
        document.getElementById('deletedDataContainer').classList.remove('hidden');
        document.getElementById('noDataMessage').classList.add('hidden');
        return;
    }

    data.forEach(item => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-50');
        
        // Get display info based on table
        let displayInfo = getDisplayInfo(item, table);
        
        row.innerHTML = `
            <td class="px-6 py-4">
                <input type="checkbox" class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${item.id}">
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">${item.id}</td>
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${displayInfo.title}</div>
                <div class="text-xs text-gray-500">${displayInfo.subtitle}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">${formatDate(item.created_at)}</td>
            <td class="px-6 py-4 text-right text-sm font-medium">
                <button onclick="restoreSingle(${item.id})" class="text-green-600 hover:text-green-900 mr-3" title="Khôi phục">
                    <i class="fas fa-undo"></i>
                </button>
                <button onclick="permanentDeleteSingle(${item.id})" class="text-red-600 hover:text-red-900" title="Xóa vĩnh viễn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });

    document.getElementById('deletedDataContainer').classList.remove('hidden');
    document.getElementById('noDataMessage').classList.add('hidden');
}

function getDisplayInfo(item, table) {
    switch(table) {
        case 'products':
            return {
                title: item.product_name || 'N/A',
                subtitle: item.product_code || ''
            };
        case 'product_categories':
            return {
                title: item.category_name || 'N/A',
                subtitle: item.category_code || ''
            };
        case 'departments':
            return {
                title: item.department_name || 'N/A',
                subtitle: item.department_code || ''
            };
        case 'purchase_orders':
            return {
                title: item.order_code || 'N/A',
                subtitle: `Tổng tiền: ${Number(item.total_amount || 0).toLocaleString()} VNĐ`
            };
        case 'purchase_requests':
            return {
                title: item.request_code || 'N/A',
                subtitle: `Trạng thái: ${item.status || 'N/A'}`
            };
        default:
            return {
                title: 'Item #' + item.id,
                subtitle: ''
            };
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll').checked;
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = selectAll;
    });
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => parseInt(cb.value));
}

function restoreSingle(id) {
    const table = document.getElementById('tableSelect').value;
    
    Swal.fire({
        title: 'Xác nhận',
        text: 'Bạn có chắc muốn khôi phục mục này?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Khôi phục',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/restore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ table, id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success');
                    loadDeletedData();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

function restoreSelected() {
    const table = document.getElementById('tableSelect').value;
    const ids = getSelectedIds();
    
    if (!table) {
        Swal.fire('Lỗi', 'Vui lòng chọn bảng', 'error');
        return;
    }
    
    if (ids.length === 0) {
        Swal.fire('Lỗi', 'Vui lòng chọn ít nhất một mục', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Xác nhận',
        text: `Bạn có chắc muốn khôi phục ${ids.length} mục đã chọn?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Khôi phục',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/restore-bulk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ table, ids })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success');
                    loadDeletedData();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

function permanentDeleteSingle(id) {
    const table = document.getElementById('tableSelect').value;
    
    Swal.fire({
        title: 'Cảnh báo!',
        text: 'Bạn có chắc muốn XÓA VĨNH VIỄN mục này? Hành động này không thể hoàn tác!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa vĩnh viễn',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/permanent-delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ table, ids: [id] })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Đã xóa!', data.message, 'success');
                    loadDeletedData();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

function permanentDeleteSelected() {
    const table = document.getElementById('tableSelect').value;
    const ids = getSelectedIds();
    
    if (!table) {
        Swal.fire('Lỗi', 'Vui lòng chọn bảng', 'error');
        return;
    }
    
    if (ids.length === 0) {
        Swal.fire('Lỗi', 'Vui lòng chọn ít nhất một mục', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Cảnh báo!',
        text: `Bạn có chắc muốn XÓA VĨNH VIỄN ${ids.length} mục đã chọn? Hành động này không thể hoàn tác!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa vĩnh viễn',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/permanent-delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ table, ids })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Đã xóa!', data.message, 'success');
                    loadDeletedData();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

// ========== BACKUP FUNCTIONS ==========

function loadBackupList() {
    fetch('/admin/settings/backup/list')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayBackupList(data.backups);
            }
        })
        .catch(err => {
            console.error(err);
        });
}

function displayBackupList(backups) {
    const tbody = document.getElementById('backupListBody');
    tbody.innerHTML = '';

    if (backups.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có file backup nào</td></tr>';
        return;
    }

    backups.forEach(backup => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-50');
        
        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <i class="fas fa-file-archive text-blue-500 mr-3"></i>
                    <span class="text-sm font-medium text-gray-900">${backup.filename}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">${backup.size}</td>
            <td class="px-6 py-4 text-sm text-gray-500">${backup.created_at}</td>
            <td class="px-6 py-4 text-right text-sm font-medium">
                <button onclick="downloadBackup('${backup.filename}')" class="text-blue-600 hover:text-blue-900 mr-3" title="Tải xuống">
                    <i class="fas fa-download"></i>
                </button>
                <button onclick="restoreBackup('${backup.filename}')" class="text-green-600 hover:text-green-900 mr-3" title="Restore">
                    <i class="fas fa-undo"></i>
                </button>
                <button onclick="deleteBackup('${backup.filename}')" class="text-red-600 hover:text-red-900" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function createBackup() {
    Swal.fire({
        title: 'Đang tạo backup...',
        text: 'Vui lòng đợi',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/admin/settings/backup/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire('Thành công!', data.message, 'success');
            loadBackupList();
        } else {
            Swal.fire('Lỗi', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.close();
        console.error(err);
        Swal.fire('Lỗi', 'Không thể tạo backup', 'error');
    });
}

function downloadBackup(filename) {
    window.location.href = `/admin/settings/backup/download/${filename}`;
}

function restoreBackup(filename) {
    Swal.fire({
        title: 'Cảnh báo!',
        html: `<p>Bạn có chắc muốn restore từ file <strong>${filename}</strong>?</p>
               <p class="text-red-600 mt-2">Toàn bộ dữ liệu hiện tại sẽ bị ghi đè!</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Restore',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đang restore...',
                text: 'Vui lòng đợi',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/admin/settings/backup/restore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ filename })
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire('Thành công!', data.message + ' Trang sẽ tự động tải lại.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.close();
                console.error(err);
                Swal.fire('Lỗi', 'Không thể restore backup', 'error');
            });
        }
    });
}

function deleteBackup(filename) {
    Swal.fire({
        title: 'Xác nhận',
        text: `Bạn có chắc muốn xóa file backup ${filename}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/settings/backup/delete/${filename}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Đã xóa!', data.message, 'success');
                    loadBackupList();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            });
        }
    });
}

// ========== MAINTENANCE MODE FUNCTIONS ==========

function loadMaintenanceSettings() {
    fetch('/admin/settings/maintenance')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update toggle switch
                const toggle = document.getElementById('maintenanceToggle');
                toggle.checked = data.maintenance_mode === 1;
                
                // Update message textarea
                document.getElementById('maintenanceMessage').value = data.maintenance_message;
                
                // Update status display
                updateMaintenanceStatusDisplay(data.maintenance_mode);
                
                // Update last updated time
                document.getElementById('lastUpdated').textContent = new Date().toLocaleString('vi-VN');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Lỗi', 'Không thể tải cài đặt bảo trì', 'error');
        });
}

function updateMaintenanceStatusDisplay(mode) {
    const statusText = document.getElementById('maintenanceStatusText');
    const currentStatus = document.getElementById('currentStatus');
    
    if (mode === 1) {
        statusText.textContent = 'Đang bật';
        statusText.className = 'text-sm font-medium text-red-600';
        currentStatus.textContent = '🔴 Đang bảo trì';
        currentStatus.className = 'text-sm font-medium text-red-600';
    } else {
        statusText.textContent = 'Đang tắt';
        statusText.className = 'text-sm font-medium text-green-600';
        currentStatus.textContent = '🟢 Hoạt động bình thường';
        currentStatus.className = 'text-sm font-medium text-green-600';
    }
}

function toggleMaintenanceMode() {
    const toggle = document.getElementById('maintenanceToggle');
    const status = toggle.checked;
    
    const confirmText = status 
        ? 'Bạn có chắc muốn BẬT chế độ bảo trì? Người dùng (trừ Admin) sẽ không thể truy cập hệ thống.'
        : 'Bạn có chắc muốn TẮT chế độ bảo trì?';
    
    Swal.fire({
        title: 'Xác nhận',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: status ? 'Bật bảo trì' : 'Tắt bảo trì',
        cancelButtonText: 'Hủy',
        confirmButtonColor: status ? '#d33' : '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/settings/maintenance/mode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success');
                    updateMaintenanceStatusDisplay(status ? 1 : 0);
                    document.getElementById('lastUpdated').textContent = new Date().toLocaleString('vi-VN');
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                    toggle.checked = !status; // Revert toggle
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Lỗi', 'Không thể cập nhật chế độ bảo trì', 'error');
                toggle.checked = !status; // Revert toggle
            });
        } else {
            toggle.checked = !status; // Revert toggle if cancelled
        }
    });
}

function updateMaintenanceMessage() {
    const message = document.getElementById('maintenanceMessage').value.trim();
    
    if (!message) {
        Swal.fire('Lỗi', 'Vui lòng nhập nội dung thông báo', 'error');
        return;
    }
    
    fetch('/admin/settings/maintenance/message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message: message })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Thành công!', data.message, 'success');
            document.getElementById('lastUpdated').textContent = new Date().toLocaleString('vi-VN');
        } else {
            Swal.fire('Lỗi', data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Lỗi', 'Không thể cập nhật thông báo', 'error');
    });
}

// Helper function
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN');
}

// Load backup list on page load
document.addEventListener('DOMContentLoaded', function() {
    // Tab is already on restore by default
});
</script>
@endpush
@endsection
