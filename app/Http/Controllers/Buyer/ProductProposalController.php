<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\ProductProposal;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductProposalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = ProductProposal::with(['department', 'createdBy', 'buyer', 'category', 'supplier', 'primaryImage'])
            ->notDeleted();

        // Apply status filter
        if ($status && $status !== 'all') {
            if ($status === 'PENDING') {
                $query->whereIn('status', ['PENDING', 'CREATED']);
            } else {
                $query->where('status', $status);
            }
        }

        $proposals = $query->latest()->paginate(15);

        // Get counts for tabs
        $allCounts = ProductProposal::notDeleted()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $counts = [
            'all' => array_sum($allCounts),
            'PENDING' => ($allCounts['PENDING'] ?? 0) + ($allCounts['CREATED'] ?? 0),
            'APPROVED' => $allCounts['APPROVED'] ?? 0,
            'REJECTED' => $allCounts['REJECTED'] ?? 0,
        ];

        return view('buyer.proposals.index', compact('proposals', 'status', 'counts'));
    }

    public function edit($id)
    {
        $proposal = ProductProposal::with(['department', 'createdBy'])->findOrFail($id);

        // Only allow editing PENDING proposals
        if ($proposal->status !== 'PENDING') {
            return redirect()->route('buyer.proposals.index')
                ->with('error', 'Chỉ có thể sửa đề xuất ở trạng thái Chờ xử lý!');
        }

        $categories = ProductCategory::all();
        $suppliers = Supplier::all();

        return view('buyer.proposals.edit', compact('proposal', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $proposal = ProductProposal::findOrFail($id);

        if ($proposal->status !== 'PENDING') {
            return redirect()->route('buyer.proposals.index')
                ->with('error', 'Chỉ có thể sửa đề xuất ở trạng thái Chờ xử lý!');
        }

        $validated = $request->validate([
            'product_code' => 'nullable|string|max:50',
            'category_id' => 'required|exists:product_categories,id',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload using files table
        if ($request->hasFile('image')) {
            // Delete old image from files table
            $oldImages = $proposal->images;
            foreach ($oldImages as $oldImage) {
                if (file_exists(public_path($oldImage->file_path))) {
                    unlink(public_path($oldImage->file_path));
                }
                $oldImage->delete();
            }

            // Upload new image
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('products'), $imageName);

            // Create file record
            \App\Models\File::create([
                'file_name' => $imageName,
                'file_path' => 'products/' . $imageName,
                'file_type' => 'image',
                'related_table' => 'product_proposals',
                'related_id' => $proposal->id,
                'uploaded_by' => Auth::id(),
                'is_delete' => false,
            ]);
        }

        // Remove image from validated data since it's not in the table anymore
        unset($validated['image']);

        // Check if we are also submitting for approval
        if ($request->input('action') === 'submit') {
            // Validation for submission (all fields must be present)
            if (empty($validated['product_code']) || empty($validated['category_id']) || empty($validated['unit']) || empty($validated['supplier_id'])) {
                return back()->withInput()->with('error', 'Vui lòng điền đầy đủ thông tin (bao gồm Mã sản phẩm) trước khi gửi duyệt!');
            }
            $validated['status'] = 'CREATED';
            $validated['buyer_id'] = Auth::id();
            $msg = 'Đã lưu và gửi đề xuất lên Admin để duyệt!';
        } else {
            $msg = 'Cập nhật đề xuất thành công!';
        }

        $proposal->update($validated);

        return redirect()->route('buyer.proposals.index')
            ->with('success', $msg);
    }

    public function submit($id)
    {
        $proposal = ProductProposal::findOrFail($id);

        if ($proposal->status !== 'PENDING') {
            return redirect()->route('buyer.proposals.index')
                ->with('error', 'Đề xuất này không thể gửi!');
        }

        // Check if all required fields are filled
        if (!$proposal->category_id || !$proposal->supplier_id || !$proposal->unit) {
            return redirect()->route('buyer.proposals.edit', $id)
                ->with('error', 'Vui lòng điền đầy đủ thông tin trước khi gửi!');
        }

        $proposal->update([
            'status' => 'CREATED',
            'buyer_id' => Auth::id(),
        ]);

        return redirect()->route('buyer.proposals.index')
            ->with('success', 'Đã gửi đề xuất lên Admin để duyệt!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $proposal = ProductProposal::findOrFail($id);

        if ($proposal->status !== 'PENDING') {
            return redirect()->route('buyer.proposals.index')
                ->with('error', 'Chỉ có thể từ chối đề xuất ở trạng thái Chờ xử lý!');
        }

        $proposal->update([
            'status' => 'REJECTED',
            'rejection_reason' => $request->rejection_reason,
            'buyer_id' => Auth::id(),
        ]);

        return redirect()->route('buyer.proposals.index')
            ->with('success', 'Đã từ chối đề xuất và gửi về Khoa!');
    }

    public function generateCode(Request $request)
    {
        $categoryId = $request->input('category_id');
        $proposalId = $request->input('proposal_id'); // For checking existing proposals if needed

        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn danh mục trước'
            ]);
        }

        $category = ProductCategory::find($categoryId);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục không tồn tại'
            ]);
        }

        $categoryCode = $category->category_code;

        // Dynamic Prefix Generation from Category Name
        // Logic: "Thuốc Kháng Sinh" -> "Thuoc Khang Sinh" -> "TKS"
        $categoryNameAscii = \Illuminate\Support\Str::ascii($category->category_name);
        $words = preg_split('/\s+/', $categoryNameAscii);
        $dynamicPrefix = '';

        foreach ($words as $word) {
            // Take the first character of each word if it's alphanumeric
            $firstChar = substr($word, 0, 1);
            if (ctype_alnum($firstChar)) {
                $dynamicPrefix .= strtoupper($firstChar);
            }
        }

        // Use the generated prefix if valid, otherwise fallback to existing category code
        if (!empty($dynamicPrefix)) {
            $categoryCode = $dynamicPrefix;
        }

        // Check against ACTUAL products table to ensure uniqueness
        // Because a proposal will eventually become a product
        $query = \App\Models\Product::where('category_id', $categoryId)
            ->where('product_code', 'like', $categoryCode . '%');

        $latestProduct = $query->orderBy('product_code', 'desc')->first();

        // Also check against other proposals that might have taken the code?
        // Ideally we should but for simplicity let's stick to Product table to avoid gaps
        // If we want to be super safe we should also check ProductProposal where status=APPROVED (but not yet products?)

        if ($latestProduct) {
            // Extract number from code (e.g., TKS0002 -> 0002)
            $currentPrefixLen = strlen($categoryCode);
            $numberStr = substr($latestProduct->product_code, $currentPrefixLen);

            if (is_numeric($numberStr)) {
                $number = (int) $numberStr;
            } else {
                $number = 0;
            }

            $newNumber = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $newCode = $categoryCode . $newNumber;

        return response()->json([
            'success' => true,
            'code' => $newCode
        ]);
    }
    public function getSuppliersByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        if (!$categoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn danh mục'
            ]);
        }

        $suppliers = Supplier::whereHas('categories', function($query) use ($categoryId) {
            $query->where('product_categories.id', $categoryId);
        })->get(['id', 'supplier_name']);

        return response()->json([
            'success' => true,
            'suppliers' => $suppliers
        ]);
    }
}
