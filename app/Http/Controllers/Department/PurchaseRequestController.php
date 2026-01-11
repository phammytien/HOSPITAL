<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class PurchaseRequestController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu mua hàng (Active: Draft, Submitted, Approved, Processing)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $query = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->with(['items.product', 'requester']);

        // Default to Active statuses if no specific status requested
        // Active = DRAFT, SUBMITTED (Pending), APPROVED, PROCESSING, REJECTED (needs fix)
        // Exclude: COMPLETED, PAID, DELIVERED, CANCELLED
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            $query->whereNotIn('status', ['COMPLETED', 'PAID', 'DELIVERED', 'CANCELLED', 'REJECTED']);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('request_code', 'like', '%' . $request->search . '%')
                    ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);
        $pageTitle = 'Yêu cầu mua hàng';
        $activeTab = 'index'; // For UI handling if needed

        return view('department.requests.index', compact('requests', 'pageTitle', 'activeTab'));
    }

    /**
     * Hiển thị lịch sử yêu cầu (Completed: Completed, Paid, Delivered, Cancelled)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $query = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->with(['items.product', 'requester']);

        // Default to History statuses (completed or rejected requests)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['COMPLETED', 'CANCELLED', 'REJECTED']); // Added REJECTED
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('request_code', 'like', '%' . $request->search . '%')
                    ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);
        $pageTitle = 'Lịch sử yêu cầu';
        $activeTab = 'history';

        return view('department.requests.index', compact('requests', 'pageTitle', 'activeTab'));
    }

    /**
     * Hiển thị form tạo yêu cầu mới
     */
    public function create()
    {
        $products = Product::where('is_delete', false)
            ->whereNotNull('category_id')
            ->with('category')
            ->orderBy('product_name')
            ->get();

        $categories = ProductCategory::where('is_delete', false)
            ->orderBy('category_name')
            ->get();

        // Calculate Budget Stats
        $user = Auth::user();
        $department = $user->department;
        $budgetTotal = $department->budget_amount ?? 500000000;

        $usedBudget = DB::table('purchase_requests')
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_requests.department_id', $user->department_id)
            ->whereIn('purchase_requests.status', ['APPROVED', 'COMPLETED', 'PAID'])
            ->where('purchase_requests.is_delete', 0)
            ->where('purchase_request_items.is_delete', 0)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        $pendingBudget = DB::table('purchase_requests')
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_requests.department_id', $user->department_id)
            ->where('purchase_requests.status', 'SUBMITTED')
            ->where('purchase_requests.is_delete', 0)
            ->where('purchase_request_items.is_delete', 0)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        return view('department.requests.create', compact('products', 'categories', 'budgetTotal', 'usedBudget', 'pendingBudget'));
    }

    /**
     * Lưu yêu cầu mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|string',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.expected_price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Tạo mã yêu cầu tự động
            $requestCode = $this->generateRequestCode($user->department_id, $validated['period']);

            // Validate Budget
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['expected_price'];
            }

            // Calculate Budget Stats
            $department = $user->department;
            $budgetTotal = $department->budget_amount ?? 500000000;

            $usedBudget = DB::table('purchase_requests')
                ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
                ->where('purchase_requests.department_id', $user->department_id)
                ->whereIn('purchase_requests.status', ['APPROVED', 'COMPLETED', 'PAID'])
                ->where('purchase_requests.is_delete', 0)
                ->where('purchase_request_items.is_delete', 0)
                ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

            $pendingBudget = DB::table('purchase_requests')
                ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
                ->where('purchase_requests.department_id', $user->department_id)
                ->where('purchase_requests.status', 'SUBMITTED')
                ->where('purchase_requests.is_delete', 0)
                ->where('purchase_request_items.is_delete', 0)
                ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

            if ($totalAmount + $usedBudget + $pendingBudget > $budgetTotal) {
                return back()
                    ->withInput()
                    ->with('error', 'Tổng giá trị yêu cầu vượt quá ngân sách còn lại!');
            }

            // Determine status based on button clicked
            $status = $request->input('submit_action') === 'submit' ? 'SUBMITTED' : 'DRAFT';

            // Tạo purchase request
            $purchaseRequest = PurchaseRequest::create([
                'request_code' => $requestCode,
                'department_id' => $user->department_id,
                'period' => $validated['period'],
                'requested_by' => $user->id,
                'status' => $status,
                'note' => $validated['note'] ?? null,
            ]);

            // Tạo các items
            foreach ($validated['items'] as $item) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'expected_price' => $item['expected_price'],
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            DB::commit();

            if ($status === 'SUBMITTED') {
                Notification::create([
                    'title' => 'Yêu cầu mua hàng mới',
                    'message' => "Khoa {$user->department->department_name} đã gửi yêu cầu #{$purchaseRequest->request_code}",
                    'type' => 'info',
                    'target_role' => 'buyer',
                    'created_by' => $user->id,
                    'data' => ['request_id' => $purchaseRequest->id, 'type' => 'request']
                ]);
            }

            return redirect()
                ->route('department.requests.show', $purchaseRequest->id)
                ->with('success', 'Tạo yêu cầu mua hàng thành công!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error creating purchase request', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . json_encode($e->errors()));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating purchase request: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo yêu cầu: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết yêu cầu
     */
    public function show($id)
    {
        $user = Auth::user();

        $request = PurchaseRequest::where('id', $id)
            ->where('department_id', $user->department_id)
            ->where('is_delete', false)
            ->with([
                'items.product.category',
                'requester',
                'department',
                'workflows.actionBy'
            ])
            ->firstOrFail();

        // Tính tổng tiền
        $totalAmount = $request->items->sum(function ($item) {
            return $item->quantity * $item->expected_price;
        });

        return view('department.requests.show', compact('request', 'totalAmount'));
    }

    /**
     * Hiển thị form sửa yêu cầu
     */
    public function edit($id)
    {
        $user = Auth::user();

        $purchaseRequest = PurchaseRequest::where('id', $id)
            ->where('department_id', $user->department_id)
            ->where('is_delete', false)
            ->with('items.product')
            ->firstOrFail();

        // Chỉ cho phép sửa nếu status là DRAFT hoặc REJECTED
        if (!in_array($purchaseRequest->status, ['DRAFT', 'REJECTED'])) {
            return redirect()
                ->route('department.requests.show', $id)
                ->with('error', 'Không thể sửa yêu cầu đã được gửi hoặc duyệt!');
        }

        $products = Product::where('is_delete', false)
            ->whereNotNull('category_id')
            ->with('category')
            ->orderBy('product_name')
            ->get();

        $categories = ProductCategory::where('is_delete', false)
            ->orderBy('category_name')
            ->get();

        // Calculate Budget Stats
        $department = $user->department;
        $budgetTotal = $department->budget_amount ?? 500000000;

        $usedBudget = PurchaseRequest::where('department_id', $user->department_id)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID'])
            ->where('purchase_requests.is_delete', false)
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('quantity * expected_price'));

        $pendingBudget = PurchaseRequest::where('department_id', $user->department_id)
            ->where('status', 'SUBMITTED')
            ->where('purchase_requests.is_delete', false)
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('quantity * expected_price'));

        return view('department.requests.edit', compact('purchaseRequest', 'products', 'categories', 'budgetTotal', 'usedBudget', 'pendingBudget'));
    }

    /**
     * Cập nhật yêu cầu
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'period' => 'required|string',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.expected_price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $purchaseRequest = PurchaseRequest::where('id', $id)
                ->where('department_id', $user->department_id)
                ->where('is_delete', false)
                ->firstOrFail();

            // Chỉ cho phép sửa nếu status là DRAFT hoặc REJECTED
            if (!in_array($purchaseRequest->status, ['DRAFT', 'REJECTED'])) {
                return redirect()
                    ->route('department.requests.show', $id)
                    ->with('error', 'Không thể sửa yêu cầu đã được gửi hoặc duyệt!');
            }

            // Validate Budget
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['expected_price'];
            }

            $department = $user->department;
            $budgetTotal = $department->budget_amount ?? 500000000;

            $usedBudget = PurchaseRequest::where('department_id', $user->department_id)
                ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID'])
                ->where('purchase_requests.is_delete', false)
                ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
                ->sum(DB::raw('quantity * expected_price'));

            $pendingBudget = PurchaseRequest::where('department_id', $user->department_id)
                ->where('status', 'SUBMITTED')
                ->where('purchase_requests.is_delete', false)
                ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
                ->sum(DB::raw('quantity * expected_price'));

            // Note: Since we are updating DRAFT/REJECTED, they are NOT in pendingBudget or usedBudget yet.
            // So we just check against the remaining.

            if ($totalAmount + $usedBudget + $pendingBudget > $budgetTotal) {
                return back()
                    ->withInput()
                    ->with('error', 'Tổng giá trị yêu cầu vượt quá ngân sách còn lại!');
            }

            // Determine status based on button clicked
            $status = $request->input('submit_action') === 'submit' ? 'SUBMITTED' : 'DRAFT';

            // Cập nhật purchase request
            $purchaseRequest->update([
                'period' => $validated['period'],
                'note' => $validated['note'] ?? null,
                'status' => $status,
            ]);

            // Xóa items cũ
            PurchaseRequestItem::where('purchase_request_id', $purchaseRequest->id)->delete();

            // Tạo items mới
            foreach ($validated['items'] as $item) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'expected_price' => $item['expected_price'],
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            DB::commit();

            if ($status === 'SUBMITTED') {
                Notification::create([
                    'title' => 'Yêu cầu mua hàng mới',
                    'message' => "Khoa {$user->department->department_name} đã cập nhật và gửi lại yêu cầu #{$purchaseRequest->request_code}",
                    'type' => 'info',
                    'target_role' => 'buyer',
                    'created_by' => $user->id,
                    'data' => ['request_id' => $purchaseRequest->id, 'type' => 'request']
                ]);
            }

            return redirect()
                ->route('department.requests.show', $purchaseRequest->id)
                ->with('success', 'Cập nhật yêu cầu thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase request: ' . $e->getMessage());


            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật yêu cầu: ' . $e->getMessage());
        }
    }

    /**
     * Xóa yêu cầu (soft delete)
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $purchaseRequest = PurchaseRequest::where('id', $id)
            ->where('department_id', $user->department_id)
            ->where('is_delete', false)
            ->firstOrFail();

        // Chỉ cho phép xóa nếu status là DRAFT
        if ($purchaseRequest->status !== 'DRAFT') {
            return redirect()
                ->route('department.requests.index')
                ->with('error', 'Chỉ có thể xóa yêu cầu ở trạng thái nháp!');
        }

        $purchaseRequest->update(['is_delete' => true]);

        return redirect()
            ->route('department.requests.index')
            ->with('success', 'Xóa yêu cầu thành công!');
    }

    /**
     * Gửi yêu cầu để duyệt
     */
    public function submit($id)
    {
        $user = Auth::user();

        $purchaseRequest = PurchaseRequest::where('id', $id)
            ->where('department_id', $user->department_id)
            ->where('is_delete', false)
            ->firstOrFail();

        if ($purchaseRequest->status !== 'DRAFT') {
            return redirect()
                ->route('department.requests.show', $id)
                ->with('error', 'Yêu cầu đã được gửi trước đó!');
        }

        $purchaseRequest->update(['status' => 'SUBMITTED']);

        Notification::create([
            'title' => 'Yêu cầu mua hàng mới',
            'message' => "Khoa {$user->department->department_name} đã gửi yêu cầu #{$purchaseRequest->request_code}",
            'type' => 'info',
            'target_role' => 'buyer',
            'created_by' => $user->id,
            'data' => ['request_id' => $purchaseRequest->id, 'type' => 'request']
        ]);

        return redirect()
            ->route('department.requests.show', $id)
            ->with('success', 'Gửi yêu cầu thành công! Đang chờ phê duyệt.');
    }

    /**
     * Tạo mã yêu cầu tự động
     */
    private function generateRequestCode($departmentId, $period)
    {
        $department = DB::table('departments')->find($departmentId);
        $deptCode = $department->department_code ?? 'DEPT';

        $year = date('Y');
        $count = PurchaseRequest::where('department_id', $departmentId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('REQ_%s_%s_%04d', $year, $deptCode, $count);
    }

    /**
     * Rút yêu cầu (chuyển về nháp)
     */
    public function withdraw($id)
    {
        $user = Auth::user();
        $request = PurchaseRequest::where('id', $id)
            ->where('department_id', $user->department_id)
            ->firstOrFail();

        if ($request->status == 'SUBMITTED') {
            $request->status = 'DRAFT';
            $request->save();
            return redirect()->back()->with('success', 'Đã rút yêu cầu về nháp thành công.');
        }

        return redirect()->back()->with('error', 'Không thể rút yêu cầu ở trạng thái này.');
    }
}
