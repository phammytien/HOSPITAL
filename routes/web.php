<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Department\DepartmentProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->withoutMiddleware([\App\Http\Middleware\CheckMaintenanceMode::class]);
Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware([\App\Http\Middleware\CheckMaintenanceMode::class]);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendNewPassword'])->name('password.email');

use App\Http\Controllers\SupportController;
Route::get('/support', [SupportController::class, 'show'])->name('support.contact');
Route::post('/support', [SupportController::class, 'send'])->name('support.send');

Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::middleware(['role:ADMIN'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/purchase-requests/{id}/details', [App\Http\Controllers\Admin\DashboardController::class, 'getPurchaseRequestDetails'])->name('purchase-requests.details');
        Route::get('/departments', [App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('departments');
        Route::post('/departments', [App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departments/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'show'])->name('departments.show');
        Route::put('/departments/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{id}', [App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::get('/users/{id}/audit-logs', [App\Http\Controllers\Admin\DepartmentController::class, 'getUserLogs'])->name('users.audit-logs');
        // Products functionality
        Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/generate-code', [App\Http\Controllers\Admin\ProductController::class, 'generateCode'])->name('products.generate-code');
        Route::get('/products/suppliers-by-category', [App\Http\Controllers\Admin\ProductController::class, 'getSuppliersByCategory'])->name('products.suppliers-by-category');
        Route::get('/products/categories-by-supplier', [App\Http\Controllers\Admin\ProductController::class, 'getCategoriesBySupplier'])->name('products.categories-by-supplier');
        Route::get('/products/export', [App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
        Route::get('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');
        Route::post('/products/{id}/approve', [App\Http\Controllers\Admin\ProductController::class, 'approve'])->name('products.approve');
        Route::get('/products/{id}/image', [App\Http\Controllers\Admin\ProductController::class, 'getImage'])->name('products.image');
        Route::put('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/import', [App\Http\Controllers\Admin\CategoryController::class, 'import'])->name('categories.import');
        // Route::get('/categories/{id}/products', [App\Http\Controllers\Admin\CategoryController::class, 'getProducts'])->name('categories.products');
        Route::get('/categories/{id}/products', [App\Http\Controllers\Admin\CategoryController::class, 'getProducts'])->name('categories.products');
        Route::put('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Suppliers
        Route::get('/suppliers', [App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('suppliers');
        Route::post('/suppliers', [App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/generate-code', [App\Http\Controllers\Admin\SupplierController::class, 'generateCode'])->name('suppliers.generate-code');
        Route::get('/suppliers/{id}', [App\Http\Controllers\Admin\SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{id}/products', [App\Http\Controllers\Admin\SupplierController::class, 'getProducts'])->name('suppliers.products');
        Route::put('/suppliers/{id}', [App\Http\Controllers\Admin\SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{id}', [App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Inventory Management
        Route::get('/inventory/export', [App\Http\Controllers\Admin\InventoryController::class, 'export'])->name('inventory.export');
        Route::get('/inventory/print', [App\Http\Controllers\Admin\InventoryController::class, 'printReport'])->name('inventory.print');
        Route::get('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory');
        Route::get('/inventory/export', [App\Http\Controllers\Admin\InventoryController::class, 'export'])->name('inventory.export');

        // Placeholder routes for sidebar links
        Route::get('/users', function () {
            return redirect()->route('admin.departments');
        })->name('users');
        Route::get('/purchase-history', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('purchase-history');

        // Purchase Orders
        Route::get('/orders', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/status', [App\Http\Controllers\Admin\PurchaseOrderController::class, 'updateStatus'])->name('orders.update-status');

        // Purchase History
        Route::get('/history', [App\Http\Controllers\Admin\PurchaseHistoryController::class, 'index'])->name('history');
        Route::get('/history/export', [App\Http\Controllers\Admin\PurchaseHistoryController::class, 'export'])->name('history.export');
        Route::get('/history/{id}', [App\Http\Controllers\Admin\PurchaseHistoryController::class, 'show'])->name('history.show');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/create', [App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
        Route::put('/notifications/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'update'])->name('notifications.update');
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::delete('/notifications/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('/notifications/upload', [App\Http\Controllers\Admin\NotificationController::class, 'uploadDocument'])->name('notifications.upload');

        // Feedback
        Route::get('/feedback', [App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedback');
        Route::get('/feedback/{id}', [App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('feedback.show');
        Route::post('/feedback/{id}/reply', [App\Http\Controllers\Admin\FeedbackController::class, 'reply'])->name('feedback.reply');
        Route::post('/feedback/{id}/resolve', [App\Http\Controllers\Admin\FeedbackController::class, 'resolve'])->name('feedback.resolve');

        // Permissions (User Role Management)
        Route::get('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions');
        Route::put('/permissions/users/{userId}/role', [App\Http\Controllers\Admin\PermissionController::class, 'updateUserRole'])->name('permissions.update-role');
        Route::post('/permissions/users/{userId}/toggle-status', [App\Http\Controllers\Admin\PermissionController::class, 'toggleUserStatus'])->name('permissions.toggle-status');
        Route::get('/permissions/role-info/{role}', [App\Http\Controllers\Admin\PermissionController::class, 'getRoleInfo'])->name('permissions.role-info');

        // Product Proposals
        Route::prefix('proposals')->name('proposals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProductProposalController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\ProductProposalController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\ProductProposalController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\ProductProposalController::class, 'reject'])->name('reject');
        });

        // Settings
        Route::get('/settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('settings');

        // Backup Management
        Route::post('/settings/backup/create', [App\Http\Controllers\Admin\SystemSettingsController::class, 'createBackup'])->name('settings.backup.create');
        Route::get('/settings/backup/list', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getBackupList'])->name('settings.backup.list');
        Route::get('/settings/backup/download/{filename}', [App\Http\Controllers\Admin\SystemSettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/restore', [App\Http\Controllers\Admin\SystemSettingsController::class, 'restoreBackup'])->name('settings.backup.restore');
        Route::delete('/settings/backup/delete/{filename}', [App\Http\Controllers\Admin\SystemSettingsController::class, 'deleteBackup'])->name('settings.backup.delete');

        // Automatic Backup Settings
        Route::get('/settings/backup/auto-settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getBackupSettings'])->name('settings.backup.auto-settings');
        Route::post('/settings/backup/auto-settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateBackupSettings'])->name('settings.backup.auto-settings.update');
        Route::get('/settings/backup/status', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getBackupStatus'])->name('settings.backup.status');
        Route::post('/settings/backup/upload', [App\Http\Controllers\Admin\SystemSettingsController::class, 'uploadBackup'])->name('settings.backup.upload');

        // Maintenance Mode Management
        Route::get('/settings/maintenance', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getMaintenanceSettings'])->name('settings.maintenance');
        Route::post('/settings/maintenance/mode', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateMaintenanceMode'])->name('settings.maintenance.mode');
        Route::post('/settings/maintenance/message', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateMaintenanceMessage'])->name('settings.maintenance.message');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.password');


        // Restore deleted items
        Route::get('/settings/deleted/{table}', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getDeletedData'])->name('settings.deleted');
        Route::post('/settings/restore', [App\Http\Controllers\Admin\SystemSettingsController::class, 'restoreItem'])->name('settings.restore');
        Route::post('/settings/restore-bulk', [App\Http\Controllers\Admin\SystemSettingsController::class, 'restoreBulk'])->name('settings.restore-bulk');
        Route::delete('/settings/permanent-delete', [App\Http\Controllers\Admin\SystemSettingsController::class, 'permanentDelete'])->name('settings.permanent-delete');
    });

    // Buyer routes
    Route::middleware(['role:BUYER'])->group(function () {
        Route::get('/buyer/dashboard', [App\Http\Controllers\Buyer\DashboardController::class, 'index'])->name('buyer.dashboard');

        // Buyer Request Management
        Route::group(['prefix' => 'buyer/requests', 'as' => 'buyer.requests.'], function () {
            Route::get('/', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'index'])->name('index');
            Route::post('/{id}/approve', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'approve'])->name('approve');
            Route::post('/bulk-approve', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/{id}/reject', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'reject'])->name('reject');
            Route::post('/{id}/update-status', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'updateStatus'])->name('update-status');
            Route::get('/{id}/compare', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'compare'])->name('compare');
            Route::get('/{id}', [App\Http\Controllers\Buyer\PurchaseRequestController::class, 'show'])->name('show');
        });

        // Buyer Product Proposals
        Route::group(['prefix' => 'buyer/proposals', 'as' => 'buyer.proposals.'], function () {
            Route::get('/', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'index'])->name('index');
            Route::get('/{id}/edit', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'edit'])->name('edit');
            Route::get('/generate-code', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'generateCode'])->name('generate-code');
            Route::get('/get-suppliers', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'getSuppliersByCategory'])->name('get-suppliers');
            Route::put('/{id}', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'update'])->name('update');
            Route::post('/{id}/submit', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'submit'])->name('submit');
            Route::post('/{id}/reject', [\App\Http\Controllers\Buyer\ProductProposalController::class, 'reject'])->name('reject');
        });

        // Buyer Notifications
        Route::get('/buyer/notifications', [App\Http\Controllers\Buyer\NotificationController::class, 'index'])
            ->name('buyer.notifications.index');
        Route::post('/buyer/notifications', [App\Http\Controllers\Buyer\NotificationController::class, 'store'])
            ->name('buyer.notifications.store');
        Route::put('/buyer/notifications/{id}', [App\Http\Controllers\Buyer\NotificationController::class, 'update'])
            ->name('buyer.notifications.update');
        Route::post('/buyer/notifications/{id}/read', [App\Http\Controllers\Buyer\NotificationController::class, 'markAsRead'])
            ->name('buyer.notifications.read');
        Route::post('/buyer/notifications/read-all', [App\Http\Controllers\Buyer\NotificationController::class, 'markAllAsRead'])
            ->name('buyer.notifications.read-all');
        Route::delete('/buyer/notifications/{id}', [App\Http\Controllers\Buyer\NotificationController::class, 'destroy'])
            ->name('buyer.notifications.destroy');
        Route::post('/buyer/notifications/upload', [App\Http\Controllers\Buyer\NotificationController::class, 'uploadDocument'])
            ->name('buyer.notifications.upload');

        // Delivery Tracking (Interactive)
        Route::group(['prefix' => 'buyer/tracking', 'as' => 'buyer.tracking.'], function () {
            Route::get('/', [App\Http\Controllers\Buyer\DeliveryTrackingController::class, 'index'])->name('index');
            Route::post('/bulk-update', [App\Http\Controllers\Buyer\DeliveryTrackingController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/{id}', [App\Http\Controllers\Buyer\DeliveryTrackingController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Buyer\DeliveryTrackingController::class, 'update'])->name('update');
            Route::post('/items/{itemId}/update', [App\Http\Controllers\Buyer\DeliveryTrackingController::class, 'updateItem'])->name('update_item');
        });

        // Buyer Order Management
        Route::get('/buyer/orders', [App\Http\Controllers\Buyer\PurchaseOrderController::class, 'index'])->name('buyer.orders.index');
        Route::get('/buyer/orders/{id}/details', [App\Http\Controllers\Buyer\PurchaseOrderController::class, 'getOrderDetails'])->name('buyer.orders.details');
        Route::get('/buyer/orders/{id}', [App\Http\Controllers\Buyer\PurchaseOrderController::class, 'show'])->name('buyer.orders.show');

        // Buyer Products
        Route::get('/buyer/products', [App\Http\Controllers\Buyer\ProductController::class, 'index'])
            ->name('buyer.products.index');

        // Buyer Reports
        Route::get('/buyer/reports', [App\Http\Controllers\Buyer\ReportController::class, 'index'])
            ->name('buyer.reports.index');
        Route::get('/buyer/reports/export', [App\Http\Controllers\Buyer\ReportController::class, 'exportQuarterlyReport'])
            ->name('buyer.reports.export');
        Route::get('/buyer/reports/export-pdf', [App\Http\Controllers\Buyer\ReportController::class, 'exportPDF'])
            ->name('buyer.reports.export-pdf');

        // Buyer Settings
        Route::get('/buyer/settings', [App\Http\Controllers\Buyer\SystemSettingsController::class, 'index'])
            ->name('buyer.settings.index');
        Route::post('/buyer/settings/profile', [App\Http\Controllers\Buyer\SystemSettingsController::class, 'updateProfile'])
            ->name('buyer.settings.profile');
        Route::post('/buyer/settings/password', [App\Http\Controllers\Buyer\SystemSettingsController::class, 'updatePassword'])
            ->name('buyer.settings.password');

        // Buyer Profile
        Route::get('/buyer/profile', [App\Http\Controllers\Buyer\ProfileController::class, 'index'])->name('buyer.profile.index');
        Route::post('/buyer/profile/update', [App\Http\Controllers\Buyer\ProfileController::class, 'update'])->name('buyer.profile.update');
        Route::post('/buyer/profile/password', [App\Http\Controllers\Buyer\ProfileController::class, 'changePassword'])->name('buyer.profile.password');




        // Supplier Management
        Route::resource('buyer/suppliers', \App\Http\Controllers\Buyer\SupplierController::class, ['names' => 'buyer.suppliers']);


    });

    // Department routes
    Route::middleware(['role:DEPARTMENT'])->group(function () {
        Route::group(['prefix' => 'department', 'as' => 'department.'], function () {
            // Dashboard
            Route::get('/dashboard', [\App\Http\Controllers\Department\DashboardController::class, 'index'])->name('dashboard');

            // Inventory Management
            Route::get('/inventory', [\App\Http\Controllers\Department\InventoryController::class, 'index'])->name('inventory.index');
            Route::get('/inventory/export', [\App\Http\Controllers\Department\InventoryController::class, 'export'])->name('inventory.export');
            Route::post('/inventory/sync', [\App\Http\Controllers\Department\InventoryController::class, 'sync'])->name('inventory.sync');
            Route::post('/inventory/initialize', [\App\Http\Controllers\Department\InventoryController::class, 'initialize'])->name('inventory.initialize');
            Route::post('/inventory/quick-action', [\App\Http\Controllers\Department\InventoryController::class, 'quickAction'])->name('inventory.quick-action');
            Route::get('/inventory/history-data', [\App\Http\Controllers\Department\InventoryController::class, 'getHistory'])->name('inventory.history_data');

            // Order Management
            Route::group(['prefix' => 'orders', 'as' => 'dept_orders.'], function () {
                Route::get('/', [\App\Http\Controllers\Department\OrderController::class, 'index'])->name('index');
                Route::get('/{id}', [\App\Http\Controllers\Department\OrderController::class, 'show'])->name('show');
                Route::post('/{id}/confirm', [\App\Http\Controllers\Department\OrderController::class, 'confirm'])->name('confirm');
                Route::post('/{id}/reject', [\App\Http\Controllers\Department\OrderController::class, 'reject'])->name('reject');
            });

            // // Profile
            // Route::get('/profile', [DepartmentProfileController::class, 'index'])->name('profile.index');
            // Route::post('/profile/update', [DepartmentProfileController::class, 'update'])->name('profile.update');
            // Route::post('/profile/password', [DepartmentProfileController::class, 'changePassword'])->name('profile.password');

            // Notifications
            Route::get('/notifications', [\App\Http\Controllers\Department\NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/{id}/read', [\App\Http\Controllers\Department\NotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('/notifications/read-all', [\App\Http\Controllers\Department\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

            // Purchase Requests
            Route::post('requests/add-item', [\App\Http\Controllers\Department\PurchaseRequestController::class, 'addToDraft'])->name('requests.add_item');
            Route::post('requests/add-items-batch', [\App\Http\Controllers\Department\PurchaseRequestController::class, 'addItemsBatch'])->name('requests.add_items_batch');
            Route::get('requests/history', [\App\Http\Controllers\Department\PurchaseRequestController::class, 'history'])->name('requests.history');
            Route::resource('requests', \App\Http\Controllers\Department\PurchaseRequestController::class);
            Route::post('requests/{id}/submit', [\App\Http\Controllers\Department\PurchaseRequestController::class, 'submit'])->name('requests.submit');
            Route::post('requests/{id}/withdraw', [\App\Http\Controllers\Department\PurchaseRequestController::class, 'withdraw'])->name('requests.withdraw');

            // Product Catalog

            Route::get('/products', [\App\Http\Controllers\Department\ProductCatalogController::class, 'index'])->name('products.index');
            Route::get('/products/{id}', [\App\Http\Controllers\Department\ProductCatalogController::class, 'show'])->name('products.show');

            // Product Proposals
            Route::prefix('proposals')->name('proposals.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Department\ProductProposalController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Department\ProductProposalController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Department\ProductProposalController::class, 'store'])->name('store');
                Route::get('/{id}', [\App\Http\Controllers\Department\ProductProposalController::class, 'show'])->name('show');
            });

            // Route::post('/catalog/suggest', [\App\Http\Controllers\Department\ProductProposalController::class, 'store'])->name('catalog.suggest');
            // Route::post('/catalog/import_suggest', [\App\Http\Controllers\Department\ProductProposalController::class, 'import'])->name('catalog.import_suggest');

            // Profile
            Route::get('/profile', [\App\Http\Controllers\Department\ProfileController::class, 'index'])->name('profile.index');
            Route::post('/profile/update', [\App\Http\Controllers\Department\ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/password', [\App\Http\Controllers\Department\ProfileController::class, 'changePassword'])->name('profile.password');
        });
    });

    // Admin Utility Routes
    Route::middleware(['role:ADMIN'])->group(function () {
        // Settings - Backup & Restore
        // Settings - Backup & Restore (Admin)
        Route::get('/settings/deleted/{table}', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getDeletedData'])->name('settings.deleted');
        Route::post('/settings/restore', [App\Http\Controllers\Admin\SystemSettingsController::class, 'restoreItem'])->name('settings.restore');
        Route::post('/settings/restore-bulk', [App\Http\Controllers\Admin\SystemSettingsController::class, 'restoreBulk'])->name('settings.restore-bulk');
        Route::delete('/settings/permanent-delete', [App\Http\Controllers\Admin\SystemSettingsController::class, 'permanentDelete'])->name('settings.permanent-delete');

        // Settings - Database Backup
        Route::get('/settings/backup/list', [App\Http\Controllers\Admin\SystemSettingsController::class, 'getBackupList'])->name('settings.backup.list');
        Route::post('/settings/backup', [App\Http\Controllers\Admin\SystemSettingsController::class, 'createBackup'])->name('settings.backup.create');
        Route::get('/settings/backup/download/{filename}', [App\Http\Controllers\Admin\SystemSettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/restore', [App\Http\Controllers\Admin\SystemSettingsController::class, 'restoreBackup'])->name('settings.backup.restore');
        Route::delete('/settings/backup/delete/{filename}', [App\Http\Controllers\Admin\SystemSettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
    });

}); // End auth middleware group




