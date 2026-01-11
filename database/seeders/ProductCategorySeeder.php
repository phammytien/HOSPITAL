<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_code' => 'TBYT', 'category_name' => 'Thiết bị y tế', 'description' => 'Trang thiết bị phục vụ khám chữa bệnh'],
            ['category_code' => 'TKS', 'category_name' => 'Thuốc kháng sinh', 'description' => 'Các loại thuốc kháng sinh thông dụng'],
            ['category_code' => 'TTT', 'category_name' => 'Thuốc tiêm / Truyền', 'description' => 'Thuốc dạng tiêm và dịch truyền'],
            ['category_code' => 'TPCN', 'category_name' => 'Thực phẩm chức năng', 'description' => 'Thực phẩm bảo vệ và hỗ trợ sức khỏe'],
            ['category_code' => 'VTTH', 'category_name' => 'Vật tư tiêu hao', 'description' => 'Bông, băng, gạc, kim tiêm, găng tay'],
            ['category_code' => 'DCXN', 'category_name' => 'Dụng cụ xét nghiệm', 'description' => 'Dụng cụ và hóa chất xét nghiệm'],
            ['category_code' => 'VPP', 'category_name' => 'Văn phòng phẩm', 'description' => 'Giấy in, bút, sổ sách văn phòng'],
            ['category_code' => 'CNTT', 'category_name' => 'Thiết bị CNTT', 'description' => 'Máy tính, máy in, thiết bị mạng'],
            ['category_code' => 'DL', 'category_name' => 'Điện lạnh', 'description' => 'Máy điều hòa, tủ lạnh bảo quản thuốc'],
            ['category_code' => 'NT', 'category_name' => 'Nội thất', 'description' => 'Bàn ghế, tủ kệ bệnh viện'],
        ];

        foreach ($categories as $cat) {
            DB::table('product_categories')->updateOrInsert(
                ['category_code' => $cat['category_code']],
                [
                    'category_name' => $cat['category_name'],
                    'description' => $cat['description'],
                    'is_delete' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
