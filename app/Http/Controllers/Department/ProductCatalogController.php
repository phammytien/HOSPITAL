<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_delete', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(4)->withQueryString();

        // Sidebar data: Categories with product counts
        $categories = ProductCategory::withCount([
            'products' => function ($q) {
                $q->where('is_delete', false);
            }
        ])->where('is_delete', false)->get();

        // Cart count for header/stats
        $cartCount = 0;
        $user = auth()->user();
        if ($user) {
            $currentPeriod = date('Y') . '_' . ceil(date('n') / 3);
            $draftRequest = \App\Models\PurchaseRequest::where('department_id', $user->department_id)
                ->where('is_submitted', false)
                ->where('is_delete', false)
                ->where('period', $currentPeriod)
                ->first();

            if ($draftRequest) {
                $cartCount = $draftRequest->items()->where('is_delete', false)->count();
            }
        }

        return view('department.products.index', compact('products', 'categories', 'cartCount'));
    }
    public function show($id)
    {
        $product = Product::with(['category', 'supplier'])->findOrFail($id);

        // Get department stock
        $user = auth()->user();
        $stock = 0;

        if ($user && $user->department_id) {
            $warehouse = \App\Models\Warehouse::where('department_id', $user->department_id)->first();
            if ($warehouse) {
                $inventory = \App\Models\Inventory::where('warehouse_id', $warehouse->id)
                    ->where('product_id', $id)
                    ->first();
                $stock = $inventory ? $inventory->quantity : 0;
            }
        }

        // Add stock to product object for response
        $product->department_stock = $stock;

        // Add image URL
        $product->image_url = getProductImage($id);

        return response()->json($product);
    }
}
