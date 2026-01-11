<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\CategoriesImport;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::where('is_delete', false)
            ->withCount(['products' => function($query) {
                $query->where('is_delete', false);
            }]);

        if ($request->has('category_id')) {
            $query->where('id', $request->category_id);
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(6);
        
        // Get all categories for the sidebar (unfiltered)
        $allCategories = ProductCategory::where('is_delete', false)
            ->withCount(['products' => function($q) {
                $q->where('is_delete', false);
            }])
            ->get();
        
        // Get all products for stats (keeping as is)
        $allProducts = Product::with('category')
            ->where('is_delete', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get filter-specific products if category is selected
        $filteredProducts = null;
        if ($request->has('category_id')) {
            $filteredProducts = Product::with('category')
                ->where('is_delete', false)
                ->where('category_id', $request->category_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('admin.categories', compact('categories', 'allProducts', 'allCategories', 'filteredProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_code' => 'nullable|string|max:50|unique:product_categories,category_code',
            'description' => 'nullable|string',
        ]);

        try {
            $category = ProductCategory::create([
                'category_name' => $request->category_name,
                'category_code' => $request->category_code ?? ('CAT_' . time() . '_' . rand(100, 999)),
                'description' => $request->description,
                'is_delete' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm danh mục thành công!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $category = ProductCategory::findOrFail($id);
            $category->update([
                'category_name' => $request->category_name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            
            // Check if category has products
            $productCount = Product::where('category_id', $id)
                ->where('is_delete', false)
                ->count();
            
            if ($productCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục này vì còn ' . $productCount . ' sản phẩm đang sử dụng!'
                ], 400);
            }

            // Soft delete
            $category->update(['is_delete' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Xóa danh mục thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new CategoriesImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Nhập danh mục từ Excel thành công!'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = 'Dòng ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi trong file Excel',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
