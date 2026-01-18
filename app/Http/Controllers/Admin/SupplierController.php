<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('products')
            ->where('is_delete', false);
        
        // Keyword search - search in supplier_name, supplier_code, contact_person
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', '%' . $search . '%')
                  ->orWhere('supplier_code', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%');
            });
        }
        
        // Pagination - 4 items per page
        $perPage = $request->input('per_page', 4);
        $suppliers = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Preserve query parameters in pagination links
        $suppliers->appends($request->except('page'));
        
        return view('admin.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|unique:suppliers,supplier_code',
            'supplier_name' => 'required',
            'phone_number' => 'nullable|digits:10',
            'email' => 'nullable|email',
            'contact_person' => 'nullable',
            'address' => 'nullable',
            'note' => 'nullable'
        ], [
            'supplier_code.required' => 'Vui lòng nhập mã nhà cung cấp.',
            'supplier_code.unique' => 'Mã nhà cung cấp đã tồn tại.',
            'supplier_name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'phone_number.digits' => 'Số điện thoại phải gồm đúng 10 số.',
            'email.email' => 'Email không hợp lệ.'
        ]);

        Supplier::create($request->all());

        return response()->json(['success' => true, 'message' => 'Thêm nhà cung cấp thành công!']);
    }

    public function show($id)
    {
        $supplier = Supplier::withCount('products')->findOrFail($id);
        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_code' => 'required|unique:suppliers,supplier_code,'.$id,
            'supplier_name' => 'required',
            'phone_number' => 'nullable|digits:10',
            'email' => 'nullable|email',
            'contact_person' => 'nullable',
            'address' => 'nullable',
            'note' => 'nullable'
        ], [
            'supplier_code.required' => 'Vui lòng nhập mã nhà cung cấp.',
            'supplier_code.unique' => 'Mã nhà cung cấp đã tồn tại.',
            'supplier_name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'phone_number.digits' => 'Số điện thoại phải gồm đúng 10 số.',
            'email.email' => 'Email không hợp lệ.'
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return response()->json(['success' => true, 'message' => 'Cập nhật nhà cung cấp thành công!']);
    }

    public function generateCode()
    {
        // Find latest supplier code
        $latestSupplier = Supplier::where('supplier_code', 'like', 'NCC%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestSupplier) {
            // Extract number part (assuming format NCC+Number)
            $numberPart = preg_replace('/[^0-9]/', '', substr($latestSupplier->supplier_code, 3));
            $nextNumber = intval($numberPart) + 1;
        } else {
            $nextNumber = 1;
        }

        // Pad with zeros (e.g., NCC001, NCC002)
        $newCode = 'NCC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['success' => true, 'code' => $newCode]);
    }

    public function getProducts($id)
    {
        $supplier = Supplier::findOrFail($id);
        $products = $supplier->products()
            ->with(['category', 'primaryImage'])
            ->where('is_delete', false)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'category_name' => $product->category->category_name ?? '-',
                    'unit' => $product->unit,
                    'stock_quantity' => $product->stock_quantity,
                    'unit_price' => $product->unit_price,
                    'image_url' => getProductImage($product->id)
                ];
            });

        return response()->json(['success' => true, 'products' => $products]);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // Check if supplier has products
        if ($supplier->products()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Không thể xóa nhà cung cấp đang có sản phẩm liên kết!'
            ], 400);
        }
        
        $supplier->update(['is_delete' => true]); // Soft delete

        return response()->json(['success' => true, 'message' => 'Xóa nhà cung cấp thành công!']);
    }
}
