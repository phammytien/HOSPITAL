<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $feedbacks = [
            [
                'purchase_order_id' => 1,
                'purchase_request_id' => null,
                'feedback_by' => 3,
                'feedback_content' => 'Đơn hàng được giao đúng hạn, chất lượng sản phẩm tốt. Rất hài lòng với dịch vụ.',
                'feedback_date' => Carbon::now()->subDays(5),
                'rating' => 5,
                'status' => 'RESOLVED',
                'admin_response' => 'Cảm ơn phản hồi của bạn. Chúng tôi rất vui khi được phục vụ!',
                'response_time' => Carbon::now()->subDays(4),
                'resolved_at' => Carbon::now()->subDays(4),
                'is_delete' => false,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'purchase_order_id' => 2,
                'purchase_request_id' => null,
                'feedback_by' => 3,
                'feedback_content' => 'Một số sản phẩm bị hư hỏng khi nhận hàng. Cần kiểm tra kỹ hơn trước khi giao.',
                'feedback_date' => Carbon::now()->subDays(3),
                'rating' => 2,
                'status' => 'PENDING',
                'admin_response' => null,
                'response_time' => null,
                'resolved_at' => null,
                'is_delete' => false,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'purchase_order_id' => 1,
                'purchase_request_id' => null,
                'feedback_by' => 4,
                'feedback_content' => 'Thời gian giao hàng hơi chậm so với dự kiến, nhưng sản phẩm vẫn đảm bảo chất lượng.',
                'feedback_date' => Carbon::now()->subDays(2),
                'rating' => 3,
                'status' => 'RESOLVED',
                'admin_response' => 'Xin lỗi về sự chậm trễ. Chúng tôi sẽ cải thiện quy trình giao hàng.',
                'response_time' => Carbon::now()->subDays(1),
                'resolved_at' => Carbon::now()->subDays(1),
                'is_delete' => false,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'purchase_order_id' => 2,
                'purchase_request_id' => null,
                'feedback_by' => 3,
                'feedback_content' => 'Rất hài lòng! Sản phẩm chất lượng cao, đóng gói cẩn thận, giao hàng nhanh chóng.',
                'feedback_date' => Carbon::now()->subDays(1),
                'rating' => 5,
                'status' => 'PENDING',
                'admin_response' => null,
                'response_time' => null,
                'resolved_at' => null,
                'is_delete' => false,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'purchase_order_id' => 1,
                'purchase_request_id' => null,
                'feedback_by' => 4,
                'feedback_content' => 'Sản phẩm không đúng với mô tả. Yêu cầu đổi trả hoặc hoàn tiền.',
                'feedback_date' => Carbon::now()->subHours(12),
                'rating' => 1,
                'status' => 'PENDING',
                'admin_response' => null,
                'response_time' => null,
                'resolved_at' => null,
                'is_delete' => false,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'purchase_order_id' => 2,
                'purchase_request_id' => null,
                'feedback_by' => 4,
                'feedback_content' => 'Dịch vụ chăm sóc khách hàng rất tốt. Nhân viên nhiệt tình và hỗ trợ nhanh chóng.',
                'feedback_date' => Carbon::now()->subHours(6),
                'rating' => 4,
                'status' => 'RESOLVED',
                'admin_response' => 'Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi!',
                'response_time' => Carbon::now()->subHours(4),
                'resolved_at' => Carbon::now()->subHours(4),
                'is_delete' => false,
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(4),
            ],
        ];

        DB::table('purchase_feedbacks')->insert($feedbacks);
    }
}
