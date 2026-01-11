<?php

if (!function_exists('get_status_label')) {
    /**
     * Get Vietnamese label for order status
     *
     * @param string $status
     * @return string
     */
    function get_status_label($status)
    {
        return match ($status) {
            'CREATED' => 'Mới tạo',
            'PENDING' => 'Chờ xử lý',
            'PROCESSING' => 'Đang xử lý',
            'ORDERED' => 'Đã đặt hàng',
            'DELIVERING' => 'Đang giao',
            'DELIVERED' => 'Đã nhận hàng',
            'PAID', 'COMPLETED' => 'Hoàn thành',
            'CANCELLED' => 'Đã hủy',
            'REJECTED' => 'Đã từ chối',
            default => $status
        };
    }
}

if (!function_exists('get_status_class')) {
    /**
     * Get CSS class for order status badge
     *
     * @param string $status
     * @return string
     */
    function get_status_class($status)
    {
        return match ($status) {
            'CREATED', 'PENDING' => 'bg-orange-100 text-orange-700',
            'PROCESSING', 'ORDERED' => 'bg-blue-100 text-blue-700',
            'DELIVERING' => 'bg-purple-100 text-purple-700',
            'DELIVERED' => 'bg-cyan-100 text-cyan-700',
            'PAID', 'COMPLETED' => 'bg-green-100 text-green-700',
            'CANCELLED', 'REJECTED' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700'
        };
    }
}

if (!function_exists('get_request_status_label')) {
    /**
     * Get Vietnamese label for purchase request status
     *
     * @param string $status
     * @return string
     */
    // function get_request_status_label($status)
    // {
    //     return match ($status) {
    //         'DRAFT' => 'Bản nháp',
    //         'SUBMITTED' => 'Đã gửi',
    //         'APPROVED' => 'Đã duyệt',
    //         'REJECTED' => 'Đã từ chối',
    //         'PROCESSING' => 'Đang xử lý',
    //         default => $status
    //     };
    // }
    function get_request_status_label($status)
    {
        return match ($status) {
            'DRAFT' => 'Bản nháp',
            'SUBMITTED' => 'Đã gửi',
            'APPROVED' => 'Đã duyệt',
            'REJECTED' => 'Đã từ chối',
            'PROCESSING' => 'Đang xử lý',
            'PAID' => 'Đã thanh toán',
            'COMPLETED' => 'Hoàn thành',
            default => $status
        };
    }

}

if (!function_exists('get_request_status_class')) {
    /**
     * Get CSS class for purchase request status badge
     *
     * @param string $status
     * @return string
     */
    function get_request_status_class($status)
    {
        return match ($status) {
            'DRAFT' => 'bg-gray-100 text-gray-700',
            'SUBMITTED' => 'bg-blue-100 text-blue-700',
            'APPROVED' => 'bg-green-100 text-green-700',
            'REJECTED' => 'bg-red-100 text-red-700',
            'PROCESSING' => 'bg-purple-100 text-purple-700',
            'PAID', 'COMPLETED' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-700'
        };
    }
}

if (!function_exists('get_transaction_type_label')) {
    /**
     * Get Vietnamese label for warehouse transaction type
     *
     * @param string $type
     * @return string
     */
    function get_transaction_type_label($type)
    {
        return match ($type) {
            'IMPORT' => 'Nhập kho',
            'EXPORT' => 'Xuất kho',
            default => $type
        };
    }
}
