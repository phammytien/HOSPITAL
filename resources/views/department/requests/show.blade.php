@extends('layouts.department')

@section('title', 'Chi tiết yêu cầu')
@section('header_title', $request->request_code)
@section('page-subtitle', 'Chi tiết yêu cầu mua hàng')

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printable-section,
            #printable-section * {
                visibility: visible;
            }

            #printable-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 30px;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 1.5cm;
                size: A4;
            }

            * {
                box-shadow: none !important;
            }
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .print-header h1 {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .print-header-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 13px;
            color: #666;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .print-table thead th {
            background-color: #f9fafb;
            padding: 12px 10px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .print-table tbody td {
            padding: 15px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            vertical-align: top;
        }

        .print-summary {
            margin: 30px 0;
            padding: 20px;
            background-color: #f9fafb;
        }

        .print-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .print-summary-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            margin-top: 10px;
            border-top: 2px solid #d1d5db;
            font-size: 16px;
            font-weight: bold;
        }

        .print-summary-total .amount {
            color: #2563eb;
            font-size: 22px;
        }

        .print-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 30px;
        }

        .print-signature-box {
            text-align: center;
            flex: 1;
        }

        .print-signature-box .title {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .print-signature-box .subtitle {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-bottom: 60px;
        }

        .print-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('department.requests.history') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
            </a>

            <div class="flex items-center space-x-3">
                @if(!$request->is_submitted)
                    <form action="{{ route('department.requests.submit', $request->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn có chắc muốn gửi yêu cầu này để duyệt?')"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition shadow-sm hover:shadow-md">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi duyệt
                        </button>
                    </form>
                    <a href="{{ route('department.requests.edit', $request->id) }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm hover:shadow-md">
                        <i class="fas fa-edit mr-2"></i> Chỉnh sửa
                    </a>

                @elseif($request->is_submitted && !$request->status)
                    <form action="{{ route('department.requests.withdraw', $request->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Bạn muốn rút yêu cầu này về nháp để chỉnh sửa?');">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition shadow-sm hover:shadow-md">
                            <i class="fas fa-undo mr-2"></i> Rút yêu cầu
                        </button>
                    </form>

                @elseif($request->status == 'REJECTED')
                    <a href="{{ route('department.requests.edit', $request->id) }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm hover:shadow-md">
                        <i class="fas fa-redo mr-2"></i> Yêu cầu làm lại
                    </a>
                @endif

                <button onclick="window.print()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-print mr-2"></i> In
                </button>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="bg-white rounded-xl p-6 border-l-4 shadow-sm no-print
            @if(!$request->is_submitted) border-gray-400 bg-gray-50
            @elseif($request->is_submitted && !$request->status) border-blue-400 bg-blue-50
            @elseif($request->status == 'APPROVED') border-green-400 bg-green-50
            @elseif($request->status == 'REJECTED') border-red-400 bg-red-50
            @endif">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold 
                        @if(!$request->is_submitted) text-gray-900
                        @elseif($request->is_submitted && !$request->status) text-blue-900
                        @elseif($request->status == 'APPROVED') text-green-900
                        @elseif($request->status == 'REJECTED') text-red-900
                        @endif">
                        @if(!$request->is_submitted)
                            <i class="fas fa-file-alt mr-2"></i> Yêu cầu đang ở trạng thái nháp
                        @elseif($request->is_submitted && !$request->status)
                            <i class="fas fa-clock mr-2"></i> Yêu cầu đang chờ phê duyệt
                        @elseif($request->status == 'APPROVED')
                            <i class="fas fa-check-circle mr-2"></i> Yêu cầu đã được phê duyệt
                        @elseif($request->status == 'REJECTED')
                            <i class="fas fa-times-circle mr-2"></i> Yêu cầu đã bị từ chối
                        @endif
                    </h3>
                    <p class="text-sm mt-1
                        @if(!$request->is_submitted) text-gray-600
                        @elseif($request->is_submitted && !$request->status) text-blue-600
                        @elseif($request->status == 'APPROVED') text-green-600
                        @elseif($request->status == 'REJECTED') text-red-600
                        @endif">
                        Mã yêu cầu: {{ $request->request_code }} •
                        Ngày tạo: {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </p>
                </div>

                @if(!$request->is_submitted)
                    <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-bold">Nháp</span>
                @elseif($request->is_submitted && !$request->status)
                    <span class="px-4 py-2 rounded-lg bg-blue-200 text-blue-800 font-bold">Chờ duyệt</span>
                @elseif($request->status == 'APPROVED')
                    <span class="px-4 py-2 rounded-lg bg-green-200 text-green-800 font-bold">Đã duyệt</span>
                @elseif($request->status == 'REJECTED')
                    <span class="px-4 py-2 rounded-lg bg-red-200 text-red-800 font-bold">Từ chối</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- PRINTABLE SECTION -->
                <div id="printable-section" class="bg-white rounded-xl border border-gray-200 overflow-hidden p-6">

                    <!-- Product Table -->
                    <table class="print-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Hình ảnh</th>
                                <th>Chi tiết sản phẩm</th>
                                <th style="width: 100px; text-align: center;">Số lượng</th>
                                <th style="width: 120px; text-align: right;">Đơn giá</th>
                                <th style="width: 120px; text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->items as $item)
                                <tr>
                                    <td style="text-align: center;">
                                        @php
                                            $imageUrl = getProductImage($item->product->id);
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product->product_name ?? 'N/A' }}"
                                                style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #e5e7eb;">
                                        @else
                                            <div
                                                style="width: 60px; height: 60px; background-color: #f3f4f6; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb;">
                                                <i class="fas fa-box" style="color: #9ca3af; font-size: 20px;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #111827; margin-bottom: 4px;">
                                            {{ $item->product->product_name ?? 'N/A' }}</div>
                                        <div style="font-size: 12px; color: #6b7280;">Loại:
                                            {{ $item->product->category->category_name ?? 'N/A' }}</div>
                                        <div style="font-size: 11px; color: #9ca3af;">Mã thuốc:
                                            {{ $item->product->product_code ?? 'N/A' }}</div>
                                    </td>
                                    <td style="text-align: center; font-weight: 600;">
                                        {{ number_format($item->quantity, 2) }} {{ $item->product->unit ?? 'Hộp' }}
                                    </td>
                                    <td style="text-align: right; font-weight: 500;">
                                        {{ number_format($item->expected_price, 0, ',', '.') }} đ
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #2563eb;">
                                        {{ number_format($item->quantity * $item->expected_price, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Summary Section -->
                    <div class="print-summary">
                        <div class="print-summary-total">
                            <span>Tổng cộng:</span>
                            <span class="amount">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>
                </div>

                <!-- Workflow History (No Print) -->
                <div class="bg-white rounded-xl border border-gray-200 no-print">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Lịch sử mua hàng & So sánh</h3>
                    </div>

                    <div class="p-6">
                        @if($request->workflows->count() > 0)
                            <div class="space-y-4">
                                @php
                                    $statusMap = [
                                        'DRAFT' => 'Nháp',
                                        'SUBMITTED' => 'Đã gửi yêu cầu',
                                        'PENDING' => 'Chờ xử lý',
                                        'APPROVED' => 'Đã duyệt',
                                        'REJECTED' => 'Đã từ chối',
                                        'COMPLETED' => 'Hoàn thành',
                                        'CANCELLED' => 'Đã hủy',
                                        'ORDERED' => 'Đã đặt hàng',
                                        'DELIVERING' => 'Đang giao hàng',
                                        'DELIVERED' => 'Đã nhận hàng',
                                        'created' => 'Mới tạo', // Cover lowercase just in case
                                        'draft' => 'Nháp',
                                        'submitted' => 'Đã gửi yêu cầu',
                                        'pending' => 'Chờ xử lý',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Đã từ chối',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                @endphp
                                @foreach($request->workflows as $workflow)
                                    <div class="flex items-start space-x-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="font-semibold text-gray-900">
                                                    {{ $workflow->actionBy->full_name ?? 'N/A' }}</h4>
                                                <span
                                                    class="text-sm text-gray-500">{{ $workflow->action_time->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                Chuyển trạng thái từ <span class="font-semibold">{{ $statusMap[$workflow->from_status] ?? $workflow->from_status }}</span>
                                                sang <span class="font-semibold">{{ $statusMap[$workflow->to_status] ?? $workflow->to_status }}</span>
                                            </p>
                                            @if($workflow->action_note)
                                                <p class="text-sm text-gray-500 mt-2 p-3 bg-gray-50 rounded-lg">
                                                    {{ $workflow->action_note }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">Chưa có lịch sử thay đổi</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Note (No Print) -->
                @if($request->note)
                    <div class="bg-white rounded-xl border border-gray-200 p-6 no-print">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Giải trình người yêu cầu</h3>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700">{{ $request->note }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar (No Print) -->
            <div class="space-y-6 no-print">
                <!-- Budget -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ngân sách ({{ $request->period }})</h3>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Tổng chi phí</span>
                            <span class="font-bold text-gray-900">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-900 font-semibold">Tổng cộng</span>
                            <span class="text-2xl font-bold text-blue-600">{{ number_format($totalAmount, 0, ',', '.') }}
                                VNĐ</span>
                        </div>
                    </div>
                </div>

                <!-- Department Info -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin khoa</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Khoa/Phòng ban</p>
                            <p class="font-semibold text-gray-900">{{ $request->department->department_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Người yêu cầu</p>
                            <p class="font-semibold text-gray-900">{{ $request->requester->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Email</p>
                            <p class="font-semibold text-gray-900">{{ $request->requester->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Kỳ yêu cầu</p>
                            <p class="font-semibold text-gray-900">{{ $request->period }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection