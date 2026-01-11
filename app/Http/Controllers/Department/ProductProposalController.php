<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductProposalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $user = auth()->user();
        // Generate temporary code: SUG_{timestamp}_{department_id}
        $tempCode = 'SUG_' . time() . '_' . ($user->department_id ?? '0');

        Product::create([
            'product_code' => $tempCode,
            'product_name' => $request->product_name,
            'category_id' => null, // Pending category
            'unit' => $request->unit,
            'unit_price' => $request->unit_price ?? 0,
            'stock_quantity' => 0,
            'description' => '(Đề xuất bởi ' . ($user->full_name ?? 'User') . ') ' . $request->description,
            'is_delete' => 0
        ]);

        return redirect()->back()->with('success', 'Đã gửi đề xuất sản phẩm thành công!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $user = auth()->user();
        $deptId = $user->department_id ?? '0';

        try {
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle); // Skip header

            DB::beginTransaction();
            while (($row = fgetcsv($handle)) !== false) {
                // Expected CSV format: Name, Unit, Price, Description
                if (count($row) < 2)
                    continue;

                $name = $row[0] ?? 'Sản phẩm mới';
                $unit = $row[1] ?? 'Cái';
                $price = isset($row[2]) ? (float) preg_replace('/[^0-9.]/', '', $row[2]) : 0;
                $desc = $row[3] ?? '';

                $tempCode = 'SUG_' . uniqid() . '_' . $deptId;

                Product::create([
                    'product_code' => $tempCode,
                    'product_name' => $name,
                    'category_id' => null,
                    'unit' => $unit,
                    'unit_price' => $price,
                    'stock_quantity' => 0,
                    'description' => "(Import đề xuất) " . $desc,
                    'is_delete' => 0
                ]);
            }
            DB::commit();
            fclose($handle);

            return redirect()->back()->with('success', 'Đã import đề xuất sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', 'Lỗi khi import file: ' . $e->getMessage());
        }
    }
}
