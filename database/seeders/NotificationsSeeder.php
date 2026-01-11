<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'title' => 'Hệ thống bảo trì',
                'message' => 'Hệ thống sẽ được bảo trì vào 20:00 ngày 10/01/2026. Vui lòng lưu công việc trước thời gian này.',
                'type' => 'warning',
                'target_role' => null,
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Yêu cầu mới cần duyệt',
                'message' => 'Có 5 yêu cầu mua hàng mới đang chờ phê duyệt từ các khoa phòng.',
                'type' => 'info',
                'target_role' => 'ADMIN',
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Đơn hàng đã hoàn thành',
                'message' => 'Đơn hàng PO-2026-001 đã được giao thành công và hoàn thành.',
                'type' => 'success',
                'target_role' => 'BUYER',
                'is_read' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Ngân sách sắp hết',
                'message' => 'Khoa Tim Mạch đã sử dụng 95% ngân sách quý 1. Vui lòng xem xét điều chỉnh.',
                'type' => 'warning',
                'target_role' => 'ADMIN',
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Lỗi thanh toán',
                'message' => 'Đơn hàng PO-2026-005 gặp lỗi khi xử lý thanh toán. Vui lòng kiểm tra lại.',
                'type' => 'error',
                'target_role' => 'BUYER',
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Chào mừng năm mới',
                'message' => 'Chúc mừng năm mới 2026! Chúc các bạn một năm làm việc hiệu quả và thành công.',
                'type' => 'success',
                'target_role' => null,
                'is_read' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cập nhật chính sách',
                'message' => 'Chính sách mua sắm mới đã được cập nhật. Vui lòng xem tài liệu hướng dẫn.',
                'type' => 'info',
                'target_role' => 'DEPARTMENT',
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Phản hồi cần xử lý',
                'message' => 'Có 3 phản hồi mới từ người dùng cần được trả lời.',
                'type' => 'warning',
                'target_role' => 'ADMIN',
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Báo cáo tháng đã sẵn sàng',
                'message' => 'Báo cáo mua sắm tháng 12/2025 đã được tạo và sẵn sàng để xem.',
                'type' => 'info',
                'target_role' => 'ADMIN',
                'is_read' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Nhà cung cấp mới',
                'message' => 'Đã thêm 2 nhà cung cấp mới vào hệ thống: ABC Medical và XYZ Pharma.',
                'type' => 'success',
                'target_role' => 'BUYER',
                'is_read' => false,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
