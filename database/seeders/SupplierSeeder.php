<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\Product;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Sample Suppliers
        $suppliers = [
            [
                'supplier_code' => 'SUP001',
                'supplier_name' => 'Dược phẩm TW2',
                'contact_person' => 'Nguyễn Văn A',
                'phone_number' => '0901234567',
                'email' => 'contact@tw2.vn',
                'address' => 'Hà Nội',
            ],
            [
                'supplier_code' => 'SUP002',
                'supplier_name' => 'MediEquipment Japan',
                'contact_person' => 'Tran Thi B',
                'phone_number' => '0909888777',
                'email' => 'sales@medijapan.com',
                'address' => 'Tokyo, Japan',
            ],
            [
                'supplier_code' => 'SUP003',
                'supplier_name' => 'Công ty Thiết bị Y tế HN',
                'contact_person' => 'Le Van C',
                'phone_number' => '0243999888',
                'email' => 'info@hanoimedical.vn',
                'address' => 'Hai Bà Trưng, Hà Nội',
            ],
            [
                'supplier_code' => 'SUP999',
                'supplier_name' => 'May mặc y tế 10/10',
                'contact_person' => 'Pham Thi D',
                'phone_number' => '0912341234',
                'email' => 'maymac1010@gmail.com',
                'address' => 'Hà Đông, Hà Nội',
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::updateOrCreate(['supplier_code' => $data['supplier_code']], $data);
        }

        // 2. Assign Suppliers to Random Products
        $allSuppliers = Supplier::all();
        $products = Product::whereNull('supplier_id')->get();

        if ($allSuppliers->count() > 0) {
            foreach ($products as $product) {
                // Randomly assign a supplier
                $product->update([
                    'supplier_id' => $allSuppliers->random()->id
                ]);
            }
        }
    }
}
