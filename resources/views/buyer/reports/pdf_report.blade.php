<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo cáo {{ $periodName }}</title>
    <style>
        @page {
            margin: 20px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'dejavu sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }
        
        .header h2 {
            color: #2563eb;
            font-size: 12px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .header h1 {
            color: #000;
            font-size: 18px;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .header p {
            color: #666;
            font-size: 10px;
            margin: 3px 0;
        }
        
        .department-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .department-header {
            background-color: #f0f7ff;
            border-left: 4px solid #2563eb;
            padding: 8px 12px;
            margin-bottom: 10px;
        }
        
        .department-header h3 {
            font-size: 12px;
            margin-bottom: 2px;
            color: #1e40af;
            font-weight: bold;
        }
        
        .department-header p {
            font-size: 9px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table thead th {
            background-color: #f3f4f6;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        table tbody td {
            padding: 6px 6px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: middle;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .subtotal-row {
            background-color: #f3f4f6 !important;
            font-weight: bold;
        }
        
        .subtotal-row td {
            padding: 8px 6px;
            border-top: 2px solid #9ca3af;
        }
        
        .grand-total-section {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .grand-total-box {
            background-color: #1f2937;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .grand-total-label {
            font-size: 11px;
            font-weight: normal;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .grand-total-amount {
            font-size: 24px;
            font-weight: bold;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        
        .signature-box .title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .signature-box .subtitle {
            font-style: italic;
            font-size: 8px;
            color: #666;
            margin-bottom: 50px;
        }
        
        .signature-box .name {
            font-weight: bold;
            font-size: 11px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 8px;
        }
        
        .col-stt { width: 5%; }
        .col-code { width: 12%; }
        .col-product { width: 30%; }
        .col-qty { width: 8%; }
        .col-price { width: 13%; }
        .col-total { width: 13%; }
        .col-date { width: 10%; }
        .col-note { width: 9%; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>BỆNH VIỆN ĐA KHOA TÂM TRÍ CAO LÃNH</h2>
        <h1>BÁO CÁO YÊU CẦU MUA HÀNG</h1>
        <p>{{ $periodName }}</p>
        <p>Ngày xuất: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    @php
        $grandTotal = 0;
        $sttCounter = 1;
    @endphp

    <!-- All departments in one page -->
    @if($requestsByDepartment->count() > 0)
        @foreach($requestsByDepartment as $departmentId => $deptOrders)
            @php
                $department = $deptOrders->first()->department;
                $deptTotal = 0;
            @endphp
            
            <div class="department-section">
                <div class="department-header">
                    <h3>{{ $department->department_name ?? 'Không xác định' }}</h3>
                    <p>Số lượng yêu cầu: {{ $deptOrders->count() }}</p>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th class="col-stt">STT</th>
                            <th class="col-code">Mã yêu cầu</th>
                            <th class="col-product">Sản phẩm</th>
                            <th class="col-qty">Số lượng</th>
                            <th class="col-price">Đơn giá<br/>(VNĐ)</th>
                            <th class="col-total">Thành tiền<br/>(VNĐ)</th>
                            <th class="col-date">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deptOrders as $order)
                            @if($order->purchaseRequest && $order->purchaseRequest->items)
                                @php
                                    $itemCount = $order->purchaseRequest->items->count();
                                @endphp
                                @foreach($order->purchaseRequest->items as $itemIndex => $item)
                                    @php
                                        $unitPrice = $item->product->unit_price ?? 0;
                                        $quantity = $item->quantity ?? 0;
                                        $subtotal = $unitPrice * $quantity;
                                        $deptTotal += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $sttCounter++ }}</td>
                                        @if($itemIndex === 0)
                                            <td class="text-center font-bold" rowspan="{{ $itemCount }}">{{ $order->purchaseRequest->request_code ?? 'N/A' }}</td>
                                        @endif
                                        <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $quantity }}</td>
                                        <td class="text-right">{{ number_format($unitPrice, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                        
                        <!-- Department subtotal -->
                        <tr class="subtotal-row">
                            <td colspan="5" class="text-right">TỔNG CỘNG {{ strtoupper($department->department_name ?? '') }}:</td>
                            <td class="text-right font-bold">{{ number_format($deptTotal, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @php
                $grandTotal += $deptTotal;
            @endphp
        @endforeach
        
        <!-- Grand Total -->
        <div class="grand-total-section">
            <div class="grand-total-box">
                <div class="grand-total-label">TỔNG CỘNG TOÀN BỆNH VIỆN</div>
                <div class="grand-total-amount">{{ number_format($grandTotal, 0, ',', '.') }} <span style="font-size: 14px;">VNĐ</span></div>
            </div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p class="title">NGƯỜI LẬP BIỂU</p>
                <p class="subtitle">(Ký, ghi rõ họ tên)</p>
                <p class="name">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
            </div>
            <div class="signature-box">
                <p class="title">KẾ TOÁN TRƯỞNG</p>
                <p class="subtitle">(Ký, ghi rõ họ tên)</p>
                <p class="name" style="color: white">.</p>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 30px; color: #666;">
            <p>Không có yêu cầu nào trong kỳ này</p>
        </div>
    @endif

    <!-- Footer -->
    <!-- <div class="footer">
        <p>HỆ THỐNG QUẢN LÝ YÊU CẦU MUA HÀNG - V1.0.2</p>
        <p>TRANG 1/1</p>
    </div> -->
</body>
</html>
