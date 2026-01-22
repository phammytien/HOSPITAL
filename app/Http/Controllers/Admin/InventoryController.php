<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Department;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Get warehouses with their inventory
        $warehousesQuery = Warehouse::with('department')
            ->where('is_delete', false);

        // Get inventory grouped by warehouse
        $inventoryQuery = Inventory::with(['warehouse.department', 'product.category'])
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->where('warehouses.is_delete', false)
            ->where('products.is_delete', false)
            ->select('inventory.*');

        // Apply filters if any
        if ($request->filled('department_id')) {
            $warehousesQuery->where('department_id', $request->input('department_id'));
            $inventoryQuery->where('warehouses.department_id', $request->input('department_id'));
        }

        if ($request->filled('warehouse_id')) {
            $warehousesQuery->where('id', $request->input('warehouse_id'));
            $inventoryQuery->where('inventory.warehouse_id', $request->input('warehouse_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $inventoryQuery->where(function($q) use ($search) {
                $q->where('products.product_name', 'like', '%' . $search . '%')
                  ->orWhere('products.product_code', 'like', '%' . $search . '%')
                  ->orWhere('warehouses.warehouse_name', 'like', '%' . $search . '%')
                  ->orWhere('warehouses.warehouse_code', 'like', '%' . $search . '%');
            });
        }

        // Get all inventory items
        $allInventory = $inventoryQuery->get();

        // Add product images
        $allInventory->each(function($item) {
            if ($item->product) {
                $item->product->image_url = getProductImage($item->product_id);
            }
        });

        // Group by warehouse and calculate totals
        $warehouses = $warehousesQuery->get()->map(function($warehouse) use ($allInventory) {
            $warehouseInventory = $allInventory->where('warehouse_id', $warehouse->id);
            
            $warehouse->total_quantity = $warehouseInventory->sum('quantity');
            $warehouse->initial_quantity = $warehouseInventory->sum('quantity'); // Same as current for now
            $warehouse->last_updated = $warehouseInventory->max('updated_at');
            $warehouse->products = $warehouseInventory->values();
            $warehouse->product_count = $warehouseInventory->count();
            
            return $warehouse;
        })->filter(function($warehouse) {
            // Only show warehouses that have products
            return $warehouse->product_count > 0;
        });

        // Get all data for filters
        $departments = Department::where('is_delete', false)->get();
        $allWarehouses = Warehouse::with('department')
            ->where('is_delete', false)
            ->get();
        $categories = ProductCategory::where('is_delete', false)->get();

        // Calculate statistics
        $stats = $this->calculateStats($request);

        return view('admin.inventory', compact(
            'warehouses',
            'departments',
            'allWarehouses',
            'categories',
            'stats'
        ));
    }

    private function calculateStats(Request $request)
    {
        $query = Inventory::with(['warehouse', 'product'])
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->where('warehouses.is_delete', false)
            ->where('products.is_delete', false);

        // Apply same filters as main query
        if ($request->filled('department_id')) {
            $query->where('warehouses.department_id', $request->input('department_id'));
        }
        if ($request->filled('warehouse_id')) {
            $query->where('inventory.warehouse_id', $request->input('warehouse_id'));
        }
        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->input('category_id'));
        }

        $totalWarehouses = Warehouse::where('is_delete', false)->count();
        $totalProducts = $query->count();
        $lowStockCount = (clone $query)->where('inventory.quantity', '<', 10)->count();
        
        // Calculate total value
        $totalValue = (clone $query)
            ->select(DB::raw('SUM(inventory.quantity * products.unit_price) as total'))
            ->value('total') ?? 0;

        return [
            'total_warehouses' => $totalWarehouses,
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'total_value' => $totalValue,
        ];
    }

    public function export(Request $request)
    {
        $fileName = 'ton-kho-' . date('Y-m-d-His') . '.xlsx';
        return Excel::download(new InventoryExport($request->all()), $fileName);
    }

    public function printReport(Request $request)
    {
        // Get warehouses with their inventory (same logic as index but without filters for full report)
        $warehousesQuery = Warehouse::with('department')
            ->where('is_delete', false);

        $inventoryQuery = Inventory::with(['warehouse.department', 'product.category'])
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->where('warehouses.is_delete', false)
            ->where('products.is_delete', false)
            ->select('inventory.*');

        $allInventory = $inventoryQuery->get();

        // Add product images
        $allInventory->each(function($item) {
            if ($item->product) {
                $item->product->image_url = getProductImage($item->product_id);
            }
        });

        // Group by warehouse and calculate totals
        $warehouses = $warehousesQuery->get()->map(function($warehouse) use ($allInventory) {
            $warehouseInventory = $allInventory->where('warehouse_id', $warehouse->id);
            
            $warehouse->total_quantity = $warehouseInventory->sum('quantity');
            $warehouse->initial_quantity = $warehouseInventory->sum('quantity');
            $warehouse->last_updated = $warehouseInventory->max('updated_at');
            $warehouse->products = $warehouseInventory->values();
            $warehouse->product_count = $warehouseInventory->count();
            $warehouse->low_stock_count = $warehouseInventory->where('quantity', '<', 10)->count();
            
            return $warehouse;
        })->filter(function($warehouse) {
            return $warehouse->product_count > 0;
        });

        // Calculate overall statistics
        $stats = [
            'total_warehouses' => $warehouses->count(),
            'total_products' => $allInventory->count(),
            'low_stock_count' => $allInventory->where('quantity', '<', 10)->count(),
            'total_value' => $allInventory->sum(function($item) {
                return $item->quantity * $item->product->unit_price;
            }),
        ];

        // Define colors for warehouse headers (cycle through these colors)
        $warehouseColors = [
            ['bg' => 'bg-blue-600', 'text' => 'text-white'],
            ['bg' => 'bg-purple-600', 'text' => 'text-white'],
            ['bg' => 'bg-indigo-600', 'text' => 'text-white'],
            ['bg' => 'bg-teal-600', 'text' => 'text-white'],
            ['bg' => 'bg-cyan-600', 'text' => 'text-white'],
            ['bg' => 'bg-emerald-600', 'text' => 'text-white'],
        ];

        return view('admin.inventory-print', compact('warehouses', 'stats', 'warehouseColors'));
    }
}
