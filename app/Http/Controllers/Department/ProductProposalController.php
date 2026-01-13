<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\ProductProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductProposalController extends Controller
{
    public function index()
    {
        $proposals = ProductProposal::with(['department', 'createdBy', 'buyer'])
            ->where('department_id', Auth::user()->department_id)
            ->notDeleted()
            ->latest()
            ->paginate(15);

        return view('department.proposals.index', compact('proposals'));
    }

    public function create()
    {
        return view('department.proposals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $proposal = ProductProposal::create([
            'product_name' => $validated['product_name'],
            'description' => $validated['description'],
            'department_id' => Auth::user()->department_id,
            'created_by' => Auth::id(),
            'status' => 'PENDING',
        ]);

        return redirect()->route('department.proposals.index')
            ->with('success', 'Đề xuất sản phẩm đã được tạo thành công!');
    }

    public function show($id)
    {
        $proposal = ProductProposal::with(['category', 'supplier', 'department', 'createdBy', 'buyer', 'approver', 'primaryImage'])
            ->findOrFail($id);

        return response()->json([
            'id' => $proposal->id,
            'product_name' => $proposal->product_name,
            'description' => $proposal->description,
            'product_code' => $proposal->product_code,
            'category' => $proposal->category,
            'unit' => $proposal->unit,
            'unit_price' => $proposal->unit_price,
            'supplier' => $proposal->supplier,
            'image' => $proposal->primaryImage ? $proposal->primaryImage->file_name : null,
            'status' => $proposal->status,
            'status_label' => get_status_label($proposal->status),
            'status_class' => get_status_class($proposal->status),
            'rejection_reason' => $proposal->rejection_reason,
        ]);
    }
}
