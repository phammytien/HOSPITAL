<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\ProductCategory;
use App\Models\PurchaseRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::with('categories');

        // Filter by search (name or code)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $categoryId = $request->category_id;
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('product_categories.id', $categoryId);
            });
        }

        $suppliers = $query->orderBy('id', 'desc')->paginate(10);
        $categories = ProductCategory::where('is_delete', false)->orderBy('category_name')->get();

        return view('buyer.suppliers.index', compact('suppliers', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::where('is_delete', false)->get();

        // Auto-generate Supplier Code
        $lastSupplier = Supplier::latest('id')->first();
        $nextCode = 'SUP001';
        
        if ($lastSupplier && $lastSupplier->supplier_code) {
            // Extract numbers from the code
            if (preg_match('/^SUP(\d+)$/', $lastSupplier->supplier_code, $matches)) {
                $number = intval($matches[1]) + 1;
                // Pad with zeros to ensure at least 3 digits, or maintain existing length if longer
                $length = max(3, strlen($matches[1]));
                $nextCode = 'SUP' . str_pad($number, $length, '0', STR_PAD_LEFT);
            }
        }

        return view('buyer.suppliers.create', compact('categories', 'nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|unique:suppliers,supplier_code',
            'supplier_name' => 'required',
            'phone_number' => ['required', 'regex:/^\d{10}$/'],
            'email' => 'required|email',
            'product_category_ids' => 'array',
        ], [
            'phone_number.required' => 'Số điện thoại không được để trống.',
            'phone_number.regex' => 'Số điện thoại phải đủ 10 chữ số và không chứa ký tự đặc biệt.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'supplier_code.unique' => 'Mã nhà cung cấp đã tồn tại.',
            'supplier_name.required' => 'Tên nhà cung cấp không được để trống.',
        ]);

        $supplier = Supplier::create($request->all());

        if ($request->has('product_category_ids')) {
            $supplier->categories()->sync($request->product_category_ids);
        }

        return redirect()->route('buyer.suppliers.index')->with('success', 'Thêm nhà cung cấp thành công.');
    }

    /**
     * Display the specified resource.
     * Shows supplier info and products from departments that match this supplier's categories.
     */
    public function show(Request $request, $id)
    {
        $supplier = Supplier::with('categories')->findOrFail($id);

        // Always use current period based on system clock
        $currentMonth = now()->month;
        $quarter = ceil($currentMonth / 3);
        $year = now()->year;

        // Format period string consistent with PurchaseRequest logic (YYYY_QX)
        $periodString = $year . '_Q' . $quarter;

        // Get IDs of categories provided by this supplier
        $categoryIds = $supplier->categories->pluck('id')->toArray();

        // Find approved purchase request items
        $itemsToOrder = PurchaseRequestItem::whereHas('purchaseRequest', function ($query) use ($periodString) {
                $query->whereIn('status', ['APPROVED', 'COMPLETED', 'PROCESSING']) // Added PROCESSING just in case
                      ->where('period', $periodString);
            })
            ->whereHas('product', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->with(['product', 'purchaseRequest.department'])
            ->get();
            
        // Aggregate items by Product ID to sum quantity and merge notes
        $aggregatedItems = $itemsToOrder->groupBy('product_id')->map(function ($rows) {
            $first = $rows->first();
            
            // Sum quantity
            $totalQty = $rows->sum('quantity');
            
            // Merge notes (unique and non-empty)
            $notes = $rows->pluck('note')->filter()->unique()->implode('; ');
            
            // Create a custom object or modify the first item to hold the aggregate data
            // We use the first item as a base to keep relationships (product, unit, etc.)
            $first->original_quantity = $totalQty; // Using a custom attribute to avoid saving to DB if accidental
            $first->quantity = $totalQty;
            $first->note = $notes;
            
            return $first;
        })->values(); // Reset keys

        return view('buyer.suppliers.show', compact('supplier', 'aggregatedItems', 'quarter', 'year'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplier = Supplier::with('categories')->findOrFail($id);
        $categories = ProductCategory::where('is_delete', false)->get();
        $selectedCategories = $supplier->categories->pluck('id')->toArray();

        return view('buyer.suppliers.edit', compact('supplier', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_code' => 'required|unique:suppliers,supplier_code,' . $id,
            'supplier_name' => 'required',
            'phone_number' => ['required', 'regex:/^\d{10}$/'],
            'email' => 'required|email',
            'product_category_ids' => 'array',
        ], [
            'phone_number.required' => 'Số điện thoại không được để trống.',
            'phone_number.regex' => 'Số điện thoại phải đủ 10 chữ số và không chứa ký tự đặc biệt.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'supplier_code.unique' => 'Mã nhà cung cấp đã tồn tại.',
            'supplier_name.required' => 'Tên nhà cung cấp không được để trống.',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        if ($request->has('product_category_ids')) {
            $supplier->categories()->sync($request->product_category_ids);
        } else {
             $supplier->categories()->detach();
        }

        return redirect()->route('buyer.suppliers.index')->with('success', 'Cập nhật nhà cung cấp thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_delete' => true]); // Soft delete logic usually
        // Or actual delete: $supplier->delete();
        
        return redirect()->route('buyer.suppliers.index')->with('success', 'Xóa nhà cung cấp thành công.');
    }
}
