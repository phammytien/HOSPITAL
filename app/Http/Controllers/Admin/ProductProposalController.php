<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductProposal;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductProposalController extends Controller
{
    public function index()
    {
        $proposals = ProductProposal::with(['department', 'createdBy', 'buyer', 'category', 'supplier'])
            ->where('status', 'CREATED')
            ->notDeleted()
            ->latest()
            ->paginate(15);

        return view('admin.proposals.index', compact('proposals'));
    }

    public function approve($id)
    {
        $proposal = ProductProposal::findOrFail($id);

        if ($proposal->status !== 'CREATED') {
            return redirect()->route('admin.proposals.index')
                ->with('error', 'Chỉ có thể duyệt đề xuất ở trạng thái Mới tạo!');
        }

        DB::beginTransaction();
        try {
            // Create new product
            $product = Product::create([
                'product_code' => $proposal->product_code ?? 'SP' . time(),
                'product_name' => $proposal->product_name,
                'category_id' => $proposal->category_id,
                'unit' => $proposal->unit,
                'unit_price' => $proposal->unit_price,
                'stock_quantity' => 0,
                'supplier_id' => $proposal->supplier_id,
                'description' => $proposal->description,
            ]);

            // Copy image from proposal to product (using files table)
            $proposalImage = $proposal->primaryImage;
            if ($proposalImage) {
                \App\Models\File::create([
                    'file_name' => $proposalImage->file_name,
                    'file_path' => $proposalImage->file_path,
                    'file_type' => 'image',
                    'related_table' => 'products',
                    'related_id' => $product->id,
                    'uploaded_by' => Auth::id(),
                    'is_delete' => false,
                ]);
            }

            // Update proposal
            $proposal->update([
                'status' => 'APPROVED',
                'product_id' => $product->id,
                'approver_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.proposals.index')
                ->with('success', 'Đã duyệt đề xuất và tạo sản phẩm mới thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.proposals.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $proposal = ProductProposal::findOrFail($id);

        if ($proposal->status !== 'CREATED') {
            return redirect()->route('admin.proposals.index')
                ->with('error', 'Chỉ có thể từ chối đề xuất ở trạng thái Mới tạo!');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $proposal->update([
            'status' => 'REJECTED',
            'rejection_reason' => $validated['rejection_reason'],
            'approver_id' => Auth::id(),
        ]);

        return redirect()->route('admin.proposals.index')
            ->with('success', 'Đã từ chối đề xuất!');
    }
}
