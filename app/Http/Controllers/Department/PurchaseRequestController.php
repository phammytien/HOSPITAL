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
     * Hiển thị danh sách yêu cầu mua hàng (Active: Draft, Submitted, Approved)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $query = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->with(['items.product', 'requester']);

        // Active = Draft (is_submitted=false) OR Submitted (is_submitted=true, status NOT Rejected/Completed/etc)
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'DRAFT') {
                $query->where('is_submitted', false);
            } elseif ($request->status == 'SUBMITTED') {
                $query->where('is_submitted', true)->whereNull('status'); // Assuming submitted has null status or we need to define what "just submitted" is
            } else {
                $query->where('status', $request->status);
            }
        } else {
            // Default: Show Draft + All Submitted (except closed ones)
            $query->where(function ($q) {
                // Case 1: Drafts (Not submitted)
                $q->where('is_submitted', false)
                    // Case 2: Submitted (Submitted but status is NULL OR status is not closed)
                    ->orWhere(function ($sub) {
                        $sub->where('is_submitted', true)
                            ->where(function ($s) {
                                $s->whereNull('status')
                                    ->orWhereNotIn('status', ['COMPLETED', 'CANCELLED', 'REJECTED']);
                            });
                    });
            });
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
     * Hiển thị lịch sử yêu cầu (Completed: Completed, Delivered, Cancelled)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $query = PurchaseRequest::where('department_id', $departmentId)
            ->where('is_delete', false)
            ->with(['items.product', 'requester']);

        // Default to History statuses (completed, rejected, cancelled - NOT submitted)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Chỉ hiển thị đã hoàn thành, từ chối, hủy - KHÔNG hiển thị đã gửi (đã gửi nằm ở Yêu cầu mua hàng)
            $query->whereIn('status', ['COMPLETED', 'PAID', 'REJECTED', 'CANCELLED']);
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
            ->whereIn('purchase_requests.status', ['APPROVED', 'COMPLETED'])
            ->where('purchase_requests.is_delete', 0)
            ->where('purchase_request_items.is_delete', 0)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        $pendingBudget = DB::table('purchase_requests')
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_requests.department_id', $user->department_id)
            ->where('purchase_requests.is_submitted', true)
            ->whereNull('purchase_requests.status') // Or whereNotIn('Approved' etc) if we want "Pending Approval" specifically
            ->where('purchase_requests.is_delete', 0)
            ->where('purchase_request_items.is_delete', 0)
            ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

        // Check for session draft items (from Product Catalog)
        $sessionItems = session('draft_items', []);
        $preselectedProducts = [];

        if (!empty($sessionItems)) {
            $productIds = array_column($sessionItems, 'product_id');
            $productsInfo = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($sessionItems as $item) {
                if (isset($productsInfo[$item['product_id']])) {
                    $prod = $productsInfo[$item['product_id']];
                    $preselectedProducts[] = [
                        'id' => $prod->id,
                        'name' => $prod->product_name,
                        'price' => $prod->unit_price,
                        'unit' => $prod->unit,
                        'quantity' => $item['quantity'],
                        'reason' => ''
                    ];
                }
            }

            // Clear session after retrieving to avoid it persisting
            session()->forget('draft_items');
        }

        return view('department.requests.create', compact('products', 'categories', 'budgetTotal', 'usedBudget', 'pendingBudget', 'preselectedProducts'));
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
                ->whereIn('purchase_requests.status', ['APPROVED', 'COMPLETED'])
                ->where('purchase_requests.is_delete', 0)
                ->where('purchase_request_items.is_delete', 0)
                ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

            $pendingBudget = DB::table('purchase_requests')
                ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
                ->where('purchase_requests.department_id', $user->department_id)
                ->where('purchase_requests.is_submitted', true)
                ->whereNull('purchase_requests.status')
                ->where('purchase_requests.is_delete', 0)
                ->where('purchase_request_items.is_delete', 0)
                ->sum(DB::raw('purchase_request_items.quantity * purchase_request_items.expected_price'));

            if ($totalAmount + $usedBudget + $pendingBudget > $budgetTotal) {
                return back()
                    ->withInput()
                    ->with('error', 'Tổng giá trị yêu cầu vượt quá ngân sách còn lại!');
            }

            // Determine status based on button clicked
            $isSubmitted = $request->input('submit_action') === 'submit';

            // Tạo purchase request
            $purchaseRequest = PurchaseRequest::create([
                'request_code' => $requestCode,
                'department_id' => $user->department_id,
                'period' => $validated['period'],
                'requested_by' => $user->id,
                'status' => null, // No text status for draft/submitted
                'is_submitted' => $isSubmitted,
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
                    'is_submitted' => $isSubmitted
                ]);
            }

            DB::commit();


            if ($isSubmitted) {
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

        // Chỉ cho phép sửa nếu chưa gửi hoặc đã bị từ chối
        if ($purchaseRequest->is_submitted && $purchaseRequest->status !== 'REJECTED') {
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
            ->whereIn('status', ['APPROVED', 'COMPLETED'])
            ->where('purchase_requests.is_delete', false)
            ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
            ->where('purchase_request_items.is_delete', false)
            ->sum(DB::raw('quantity * expected_price'));

        $pendingBudget = PurchaseRequest::where('department_id', $user->department_id)
            ->where('purchase_requests.is_submitted', true)
            ->whereNull('status')
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

            // Chỉ cho phép sửa nếu chưa gửi hoặc đã bị từ chối
            if ($purchaseRequest->is_submitted && $purchaseRequest->status !== 'REJECTED') {
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
                ->whereIn('status', ['APPROVED', 'COMPLETED'])
                ->where('purchase_requests.is_delete', false)
                ->join('purchase_request_items', 'purchase_requests.id', '=', 'purchase_request_items.purchase_request_id')
                ->sum(DB::raw('quantity * expected_price'));

            $pendingBudget = PurchaseRequest::where('department_id', $user->department_id)
                ->where('purchase_requests.is_submitted', true)
                ->whereNull('status')
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
            $isSubmitted = $request->input('submit_action') === 'submit';

            // Cập nhật purchase request
            $purchaseRequest->update([
                'period' => $validated['period'],
                'note' => $validated['note'] ?? null,
                'status' => null, // Reset status
                'is_submitted' => $isSubmitted
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
                    'is_submitted' => $isSubmitted
                ]);
            }

            DB::commit();

            if ($isSubmitted) {
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

        // Chỉ cho phép xóa nếu chưa gửi
        if ($purchaseRequest->is_submitted) {
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

        if ($purchaseRequest->is_submitted) {
            return redirect()
                ->route('department.requests.show', $id)
                ->with('error', 'Yêu cầu đã được gửi trước đó!');
        }

        $purchaseRequest->update(['is_submitted' => true, 'status' => null]);

        // Also update items
        $purchaseRequest->items()->update(['is_submitted' => true]);

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

        // 1. Generate Department Code Part
        $deptName = $department->department_name;
        $slug = \Illuminate\Support\Str::ascii($deptName);
        $slug = strtoupper(str_replace(' ', '_', $slug));
        $slug = preg_replace('/[^A-Z0-9_]/', '', $slug);

        if (str_starts_with($slug, 'KHOA_')) {
            $slug = substr($slug, 5);
        } elseif (str_starts_with($slug, 'PHONG_')) {
            $slug = substr($slug, 6);
        }

        // 2. Determine Year and Quarter from REAL TIME
        $year = date('Y');
        $month = (int) date('n');
        $quarter = 'Q' . ceil($month / 3);

        // Pattern: REQ_2026_Q1_NOI_0001
        $prefix = sprintf('REQ_%s_%s_%s_', $year, $quarter, $slug);

        // 3. Count existing requests with THIS prefix
        $count = PurchaseRequest::where('request_code', 'LIKE', $prefix . '%')
            ->count() + 1;

        return $prefix . sprintf('%04d', $count);
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

        if ($request->is_submitted && !$request->status) {
            $request->is_submitted = false;
            $request->save();
            // Also update items
            $request->items()->update(['is_submitted' => false]);
            return redirect()->back()->with('success', 'Đã rút yêu cầu về nháp thành công.');
        }

        return redirect()->back()->with('error', 'Không thể rút yêu cầu ở trạng thái này.');
    }


    // ... (rest of the file content until the end) ...

    /**
     * Add item to draft request (Cart functionality)
     */
    public function addToDraft(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $currentPeriod = date('Y') . '_' . ceil(date('n') / 3);

            // 1. Find or create Draft Request
            $draftRequest = PurchaseRequest::where('department_id', $user->department_id)
                ->where('is_submitted', false)
                ->where('is_delete', false)
                ->where('period', $currentPeriod)
                ->first();

            if (!$draftRequest) {
                $requestCode = $this->generateRequestCode($user->department_id, $currentPeriod);
                $draftRequest = PurchaseRequest::create([
                    'request_code' => $requestCode,
                    'department_id' => $user->department_id,
                    'period' => $currentPeriod,
                    'requested_by' => $user->id,
                    'status' => null,
                    'is_submitted' => false,
                    'note' => 'Tự động tạo từ danh mục sản phẩm'
                ]);
            }

            // 2. Add or Update Item
            $product = \App\Models\Product::find($request->product_id);
            $existingItem = PurchaseRequestItem::where('purchase_request_id', $draftRequest->id)
                ->where('product_id', $request->product_id)
                ->where('is_delete', false)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
            } else {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $draftRequest->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'expected_price' => $product->unit_price,
                    // 'reason' => null
                    'is_submitted' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào phiếu yêu cầu nháp!',
                'cart_count' => $draftRequest->items()->where('is_delete', false)->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Batch add items to draft request
     * Find existing draft or create new one, then add items
     */
    public function addItemsBatch(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $currentPeriod = date('Y') . '_Q' . ceil(date('n') / 3); // Format: 2026_Q1

            // 1. Find or Create Draft Request
            $draftRequest = PurchaseRequest::where('department_id', $user->department_id)
                ->where('is_submitted', false)
                ->where('is_delete', false)
                ->where('period', $currentPeriod)
                ->first();

            if (!$draftRequest) {
                // Create new draft request
                $requestCode = $this->generateRequestCode($user->department_id, $currentPeriod);

                $draftRequest = PurchaseRequest::create([
                    'request_code' => $requestCode,
                    'department_id' => $user->department_id,
                    'period' => $currentPeriod,
                    'requested_by' => $user->id,
                    'status' => null,
                    'is_submitted' => false,
                    'note' => 'Tự động tạo từ danh mục sản phẩm'
                ]);
            }

            // 2. Add Items (check for duplicates)
            $addedCount = 0;
            $updatedCount = 0;

            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['product_id']);

                // Check if item already exists in this draft
                $existingItem = PurchaseRequestItem::where('purchase_request_id', $draftRequest->id)
                    ->where('product_id', $item['product_id'])
                    ->where('is_delete', false)
                    ->first();

                if ($existingItem) {
                    // Update quantity instead of creating duplicate
                    $existingItem->quantity += $item['quantity'];
                    $existingItem->save();
                    $updatedCount++;
                } else {
                    // Create new item
                    PurchaseRequestItem::create([
                        'purchase_request_id' => $draftRequest->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'expected_price' => $product->unit_price,
                        'is_submitted' => false
                    ]);
                    $addedCount++;
                }
            }

            DB::commit();

            // Build success message
            $message = '';
            if ($addedCount > 0 && $updatedCount > 0) {
                $message = "Đã thêm {$addedCount} sản phẩm mới và cập nhật {$updatedCount} sản phẩm đã có!";
            } elseif ($addedCount > 0) {
                $message = "Đã thêm {$addedCount} sản phẩm vào đơn nháp!";
            } else {
                $message = "Đã cập nhật số lượng cho {$updatedCount} sản phẩm!";
            }

            // Return success with redirect URL to EDIT page
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('department.requests.edit', $draftRequest->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
