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
        $query = ProductProposal::with(['department', 'createdBy', 'buyer', 'category', 'supplier'])
            ->notDeleted();

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $proposals = $query->latest()->paginate(15);

        return view('buyer.proposals.index', compact('proposals'));
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
            $image->move(public_path('images/products'), $imageName);

            // Create file record
            \App\Models\File::create([
                'file_name' => $imageName,
                'file_path' => 'images/products/' . $imageName,
                'file_type' => 'image',
                'related_table' => 'product_proposals',
                'related_id' => $proposal->id,
                'uploaded_by' => Auth::id(),
                'is_delete' => false,
            ]);
        }

        // Remove image from validated data since it's not in the table anymore
        unset($validated['image']);

        $proposal->update($validated);

        return redirect()->route('buyer.proposals.index')
            ->with('success', 'Cập nhật đề xuất thành công!');
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
}
