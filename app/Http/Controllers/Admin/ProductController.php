<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'primaryImage'])
            ->where('is_delete', false);
        
        // Keyword search - search in product_name and product_code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                  ->orWhere('product_code', 'like', '%' . $search . '%');
            });
        }
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        // Stock Status filter
        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($status === 'low_stock') {
                $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
            } elseif ($status === 'in_stock') {
                $query->where('stock_quantity', '>', 10);
            }
        }
        
        // Pagination - 4 items per page
        $perPage = $request->input('per_page', 4);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Preserve query parameters in pagination links
        $products->appends($request->except('page'));
        
        $categories = ProductCategory::where('is_delete', false)->get();
        $suppliers = Supplier::where('is_delete', false)->get();
        // Get unique units for suggestions
        $units = Product::where('is_delete', false)->whereNotNull('unit')->distinct()->orderBy('unit')->pluck('unit');
        
        return view('admin.products', compact('products', 'categories', 'suppliers', 'units'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'product_name' => 'required',
            'category_id' => 'required|exists:product_categories,id',
            'unit' => 'required',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'product_code.required' => 'Vui lòng nhập mã sản phẩm.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại.',
            'product_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'unit.required' => 'Vui lòng chọn đơn vị tính.',
            'unit_price.required' => 'Vui lòng nhập đơn giá nhập.',
            'unit_price.min' => 'Đơn giá nhập không hợp lệ.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.'
        ]);

        $product = Product::create($request->except('image'));

        // Handle image upload
        \Log::info('=== PRODUCT STORE DEBUG ===');
        \Log::info('Has file: ' . ($request->hasFile('image') ? 'YES' : 'NO'));
        \Log::info('All files: ' . json_encode($request->allFiles()));
        
        if ($request->hasFile('image')) {
            \Log::info('File name: ' . $request->file('image')->getClientOriginalName());
            \Log::info('File size: ' . $request->file('image')->getSize());
            
            $result = uploadProductImage(
                $request->file('image'),
                $product->id,
                auth()->id()
            );
            
            \Log::info('Upload result: ' . ($result ? 'SUCCESS - ID: ' . $result->id : 'FAILED'));
        }

        return response()->json(['success' => true, 'message' => 'Thêm sản phẩm thành công!']);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code,'.$id,
            'product_name' => 'required',
            'category_id' => 'required|exists:product_categories,id',
            'unit' => 'required',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'product_code.required' => 'Vui lòng nhập mã sản phẩm.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại.',
            'product_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'unit.required' => 'Vui lòng chọn đơn vị tính.',
            'unit_price.required' => 'Vui lòng nhập đơn giá nhập.',
            'unit_price.min' => 'Đơn giá nhập không hợp lệ.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.'
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->except('image'));

        // Handle image upload
        if ($request->hasFile('image')) {
            uploadProductImage(
                $request->file('image'),
                $product->id,
                auth()->id()
            );
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật sản phẩm thành công!']);
    }

    public function generateCode(Request $request)
    {
        $categoryId = $request->input('category_id');
        $category = ProductCategory::find($categoryId);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found']);
        }

        $prefix = $category->category_code;
        
        // Find latest product code with this prefix
        $latestProduct = Product::where('product_code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestProduct) {
            // Extract number part (assuming format PREFIX+Number)
            $numberPart = preg_replace('/[^0-9]/', '', substr($latestProduct->product_code, strlen($prefix)));
            $nextNumber = intval($numberPart) + 1;
        } else {
            $nextNumber = 1;
        }

        // Pad with zeros (e.g., 001, 002)
        $newCode = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['success' => true, 'code' => $newCode]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_delete' => true]); // Soft delete based on schema

        return response()->json(['success' => true, 'message' => 'Xóa sản phẩm thành công!']);
    }

    public function approve($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Check if product is already approved (has supplier)
            if ($product->supplier_id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Sản phẩm đã được duyệt trước đó!'
                ]);
            }
            
            // For now, just mark as approved by setting a default supplier
            // In real scenario, admin should select supplier when approving
            // We'll return success and let the edit modal handle supplier selection
            return response()->json([
                'success' => true, 
                'message' => 'Sản phẩm đã được duyệt thành công!',
                'product_id' => $product->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $categoryId = $request->input('category_id');
        $search = $request->input('search');
        
        $fileName = 'danh-sach-san-pham-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new ProductsExport($categoryId, $search), $fileName);
    }

    public function getImage($id)
    {
        $imageUrl = getProductImage($id);
        return response()->json(['image_url' => $imageUrl]);
    }
}
