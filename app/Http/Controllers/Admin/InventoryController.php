<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Department;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['warehouse.department', 'product.category'])
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->where('warehouses.is_delete', false)
            ->where('products.is_delete', false)
            ->select('inventory.*');

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('warehouses.department_id', $request->input('department_id'));
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('inventory.warehouse_id', $request->input('warehouse_id'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->input('category_id'));
        }

        // Search by product name or code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', '%' . $search . '%')
                    ->orWhere('products.product_code', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $inventory = $query->orderBy('inventory.updated_at', 'desc')->paginate(20);
        $inventory->appends($request->except('page'));

        // Add product images
        $inventory->each(function ($item) {
            if ($item->product) {
                $item->product->image_url = getProductImage($item->product_id);
            }
        });

        // Get all data for filters
        $departments = Department::where('is_delete', false)->get();
        $warehouses = Warehouse::with('department')
            ->where('is_delete', false)
            ->get();
        $categories = ProductCategory::where('is_delete', false)->get();

        // Calculate statistics
        $stats = $this->calculateStats($request);

        return view('admin.inventory', compact(
            'inventory',
            'departments',
            'warehouses',
            'categories',
            'stats'
        ));
    }

    public function export(Request $request)
    {
        return Excel::download(new InventoryExport(
            $request->input('department_id'),
            $request->input('warehouse_id'),
            $request->input('category_id'),
            $request->input('search')
        ), 'Bao_cao_ton_kho_' . date('Y_m_d_His') . '.xlsx');
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
}
