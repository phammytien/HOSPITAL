<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Find or Create the warehouse for this department
        $warehouse = Warehouse::firstOrCreate(
            ['department_id' => $departmentId],
            [
                'warehouse_code' => 'WH-' . strtoupper($user->department->code ?? 'DEPT' . $departmentId),
                'warehouse_name' => 'Kho ' . ($user->department->department_name ?? 'Khoa Phòng'),
                'location' => 'Tại khoa',
            ]
        );

        // Query Products that have been REQUESTED by this department
        // This answers the user's requirement: "load những sản phẩm mà yêu cầu của khoa phòng ban sẽ load vào kho hàng"
        $query = \App\Models\Product::where('is_delete', 0)
            ->whereHas('purchaseRequestItems.purchaseRequest', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->with(['category']);

        // Manual join for inventory quantity
        if ($warehouse) {
            $query->leftJoin('inventory', function ($join) use ($warehouse) {
                $join->on('products.id', '=', 'inventory.product_id')
                    ->where('inventory.warehouse_id', '=', $warehouse->id);
            })
                ->select('products.*', 'inventory.quantity as stock_quantity_dept');
        } else {
            $query->select('products.*');
        }

        // Filter by Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->has('cat') && $request->cat != '') {
            $query->where('category_id', $request->cat);
        }

        // Filter by Stock Status
        if ($request->has('stock_status') && $request->stock_status != '') {
            if ($request->stock_status == 'out_of_stock') {
                $query->where(function ($q) {
                    $q->whereNull('inventory.quantity')->orWhere('inventory.quantity', '<=', 0);
                });
            } elseif ($request->stock_status == 'low_stock') {
                $query->where('inventory.quantity', '>', 0)->where('inventory.quantity', '<=', 10);
            } elseif ($request->stock_status == 'in_stock') {
                $query->where('inventory.quantity', '>', 10);
            }
        }

        $products = $query->paginate(10);

        // Prepare categories for filter
        $categories = \App\Models\ProductCategory::where('is_delete', 0)->get();

        return view('department.inventory.index', compact('products', 'warehouse', 'categories'));
    }

    public function export()
    {
        // Placeholder for export functionality
        return redirect()->back()->with('success', 'Chức năng xuất báo cáo đang được phát triển.');
    }

    public function sync()
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        \Log::info("Sync started for department: {$departmentId}");

        // Find or Create the warehouse for this department
        $warehouse = Warehouse::firstOrCreate(
            ['department_id' => $departmentId],
            [
                'warehouse_code' => 'WH-' . strtoupper($user->department->code ?? 'DEPT' . $departmentId),
                'warehouse_name' => 'Kho ' . ($user->department->department_name ?? 'Khoa Phòng'),
                'location' => 'Tại khoa',
            ]
        );

        \Log::info("Warehouse ID: {$warehouse->id}");

        // Step 1: Get initial stock from completed purchase orders (from OrderController confirm)
        $orderImports = \App\Models\WarehouseInventory::where('warehouse_id', $warehouse->id)
            ->where('transaction_type', 'IMPORT')
            ->whereNotNull('related_order_id') // From order confirmations
            ->select('product_id', \DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Step 2: Get manual takes (EXPORT from quick actions)
        $manualExports = \App\Models\WarehouseInventory::where('warehouse_id', $warehouse->id)
            ->where('transaction_type', 'EXPORT')
            ->whereNotNull('performed_by')
            ->where('note', 'LIKE', 'Thao tác nhanh:%')
            ->select('product_id', \DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Step 3: Get manual returns (IMPORT from quick actions)
        $manualReturns = \App\Models\WarehouseInventory::where('warehouse_id', $warehouse->id)
            ->where('transaction_type', 'IMPORT')
            ->whereNotNull('performed_by')
            ->where('note', 'LIKE', 'Thao tác nhanh:%')
            ->select('product_id', \DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Calculate net inventory for each product
        $productTotals = [];

        // Add order imports
        foreach ($orderImports as $productId => $data) {
            $productTotals[$productId] = $data->total;
        }

        // Subtract manual exports
        foreach ($manualExports as $productId => $data) {
            if (!isset($productTotals[$productId])) {
                $productTotals[$productId] = 0;
            }
            $productTotals[$productId] -= $data->total;
        }

        // Add manual returns
        foreach ($manualReturns as $productId => $data) {
            if (!isset($productTotals[$productId])) {
                $productTotals[$productId] = 0;
            }
            $productTotals[$productId] += $data->total;
        }

        \Log::info("Found " . count($productTotals) . " products to sync");

        if (empty($productTotals)) {
            return redirect()->back()->with('warning', 'Không tìm thấy giao dịch nào để đồng bộ.');
        }

        $updatedCount = 0;
        foreach ($productTotals as $productId => $netQuantity) {
            \Log::info("Updating product {$productId} with quantity {$netQuantity}");

            Inventory::updateOrCreate(
                ['warehouse_id' => $warehouse->id, 'product_id' => $productId],
                ['quantity' => max(0, $netQuantity)] // Ensure non-negative
            );
            $updatedCount++;
        }

        \Log::info("Sync completed. Updated {$updatedCount} products");

        return redirect()->back()->with('success', "Đã đồng bộ tồn kho thành công! Cập nhật {$updatedCount} sản phẩm từ lịch sử giao dịch.");
    }

    public function quickAction(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:take,return',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get warehouse
        $warehouse = Warehouse::where('department_id', $departmentId)->first();

        if (!$warehouse) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kho hàng.'], 404);
        }

        // Get or create inventory record
        $inventory = Inventory::firstOrCreate(
            ['warehouse_id' => $warehouse->id, 'product_id' => $request->product_id],
            ['quantity' => 0]
        );

        // Update quantity based on action
        if ($request->action === 'take') {
            // Subtract (export from warehouse)
            if ($inventory->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ hàng tồn! Hiện có: {$inventory->quantity}"
                ], 400);
            }
            $inventory->quantity -= $request->quantity;
            $actionText = 'lấy';
            $transactionType = 'EXPORT';
        } else {
            // RETURN validation: Check how much was taken (EXPORT) vs returned (IMPORT)
            $totalExported = \App\Models\WarehouseInventory::where('warehouse_id', $warehouse->id)
                ->where('product_id', $request->product_id)
                ->where('transaction_type', 'EXPORT')
                ->sum('quantity');

            $totalReturned = \App\Models\WarehouseInventory::where('warehouse_id', $warehouse->id)
                ->where('product_id', $request->product_id)
                ->where('transaction_type', 'IMPORT')
                ->whereNotNull('performed_by') // Only count manual returns, not order imports
                ->where('note', 'LIKE', 'Thao tác nhanh:%')
                ->sum('quantity');

            $canReturn = $totalExported - $totalReturned;

            if ($request->quantity > $canReturn) {
                return response()->json([
                    'success' => false,
                    'message' => "Chỉ có thể trả tối đa {$canReturn} sản phẩm! (Đã lấy: {$totalExported}, Đã trả: {$totalReturned})"
                ], 400);
            }

            // Add (return to warehouse)
            $inventory->quantity += $request->quantity;
            $actionText = 'trả';
            $transactionType = 'IMPORT';
        }

        $inventory->save();

        // Log to warehouse_inventory
        \App\Models\WarehouseInventory::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $request->product_id,
            'transaction_type' => $transactionType,
            'quantity' => $request->quantity,
            'performed_by' => $user->id,
            'note' => "Thao tác nhanh: {$actionText} {$request->quantity} sản phẩm",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã {$actionText} {$request->quantity} sản phẩm thành công!",
            'new_quantity' => $inventory->quantity
        ]);
    }

    public function initialize()
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get warehouse
        $warehouse = Warehouse::firstOrCreate(
            ['department_id' => $departmentId],
            [
                'warehouse_code' => 'WH-' . strtoupper($user->department->code ?? 'DEPT' . $departmentId),
                'warehouse_name' => 'Kho ' . ($user->department->department_name ?? 'Khoa Phòng'),
                'location' => 'Tại khoa',
            ]
        );

        // Find all COMPLETED orders for this department that haven't been logged yet
        $orders = \App\Models\PurchaseOrder::where('department_id', $departmentId)
            ->where('status', 'COMPLETED')
            ->with('items')
            ->get();

        $importedCount = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // Check if this order item has already been logged
                $exists = \App\Models\WarehouseInventory::where('warehouse_id', $warehouse->id)
                    ->where('product_id', $item->product_id)
                    ->where('related_order_id', $order->id)
                    ->exists();

                if (!$exists) {
                    // Create import record
                    \App\Models\WarehouseInventory::create([
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $item->product_id,
                        'transaction_type' => 'IMPORT',
                        'quantity' => $item->quantity,
                        'related_order_id' => $order->id,
                        'related_request_id' => $order->purchase_request_id,
                        'performed_by' => $user->id,
                        'note' => 'Khởi tạo kho từ đơn hàng #' . $order->order_code,
                    ]);
                    $importedCount++;
                }
            }
        }

        if ($importedCount > 0) {
            return redirect()->back()->with('success', "Đã khởi tạo kho thành công! Nhập {$importedCount} giao dịch từ đơn hàng cũ. Hãy nhấn 'Đồng bộ' để cập nhật số liệu.");
        } else {
            return redirect()->back()->with('info', 'Kho đã được khởi tạo trước đó. Nhấn "Đồng bộ" để cập nhật số liệu.');
        }
    }
}
