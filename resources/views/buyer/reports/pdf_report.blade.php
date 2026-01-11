<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo cáo {{ $quarterName }}</title>
    <style>
        @page {
            margin: 15px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'dejavu sans', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 16px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .header h2 {
            color: #0ea5e9;
            font-size: 13px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        
        .header p {
            color: #666;
            font-size: 9px;
            margin: 2px 0;
        }
        
        .department-section {
            margin-bottom: 15px;
        }
        
        .department-header {
            background-color: #2563eb;
            color: white;
            padding: 6px 8px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .department-header h3 {
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .department-header p {
            font-size: 9px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        
        table thead th {
            background-color: #dbeafe;
            padding: 5px 3px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            color: #000;
            border: 1px solid #000;
        }
        
        table tbody td {
            padding: 4px 3px;
            border: 1px solid #000;
            font-size: 9px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8fafc;
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
            background-color: #e0e7ff !important;
            font-weight: bold;
        }
        
        .grand-total-row {
            background-color: #bfdbfe !important;
            font-weight: bold;
            font-size: 10px;
        }
        
        .signature-section {
            margin-top: 25px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-box p {
            margin: 2px 0;
            font-size: 9px;
        }
        
        .signature-box .title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
        }
        
        .signature-box .note {
            font-style: italic;
            font-size: 8px;
            margin-top: 40px;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            color: #666;
            font-size: 7px;
        }
        
        .col-stt { width: 4%; }
        .col-code { width: 12%; }
        .col-product { width: 25%; }
        .col-qty { width: 7%; }
        .col-price { width: 13%; }
        .col-total { width: 13%; }
        .col-date { width: 10%; }
        .col-note { width: 16%; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>Bệnh viện Đa khoa Tâm Trí Cao Lãnh</h2>
        <h1>Báo cáo Yêu cầu Mua hàng</h1>
        <p>{{ $quarterName }}</p>
        <p>Ngày xuất: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    @php
        $grandTotal = 0;
        $sttCounter = 1;
    @endphp

    <!-- All departments in one page -->
    @if($requestsByDepartment->count() > 0)
        @foreach($requestsByDepartment as $departmentId => $deptRequests)
            @php
                $department = $deptRequests->first()->department;
                $deptTotal = 0;
            @endphp
            
            <div class="department-section">
                <div class="department-header">
                    <h3>{{ $department->department_name ?? 'Không xác định' }}</h3>
                    <p>Số lượng yêu cầu: {{ $deptRequests->count() }}</p>
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
                            <th class="col-note">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deptRequests as $request)
                            @if($request->status === 'APPROVED')
                                @php
                                    $itemCount = $request->items->count();
                                @endphp
                                @foreach($request->items as $itemIndex => $item)
                                    @php
                                        $unitPrice = $item->product->unit_price ?? 0;
                                        $quantity = $item->quantity ?? 0;
                                        $subtotal = $unitPrice * $quantity;
                                        $deptTotal += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $sttCounter++ }}</td>
                                        @if($itemIndex === 0)
                                            <td class="text-center font-bold" rowspan="{{ $itemCount }}">{{ $request->request_code }}</td>
                                        @endif
                                        <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $quantity }}</td>
                                        <td class="text-right">{{ number_format($unitPrice, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $request->created_at->format('d/m/Y') }}</td>
                                        <td style="font-size: 8px;">{{ $item->reason ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                        
                        <!-- Department subtotal -->
                        <tr class="subtotal-row">
                            <td colspan="6" class="text-right" style="padding: 5px;">Tổng cộng:</td>
                            <td colspan="2" class="text-right" style="padding: 5px;">{{ number_format($deptTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @php
                $grandTotal += $deptTotal;
            @endphp
        @endforeach
        
        <!-- Grand Total -->
        <table>
            <tbody>
                <tr class="grand-total-row">
                    <td colspan="6" class="text-right" style="padding: 6px;">TỔNG CỘNG:</td>
                    <td colspan="2" class="text-right" style="padding: 6px;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Signature Section with two signers -->
        <div class="signature-section">
            <div class="signature-box">
                <p class="title">Người lập biểu</p>
                <p class="note">(Ký, ghi rõ họ tên)</p>
            </div>
            <div class="signature-box">
                <p class="title">Kế toán trưởng</p>
                <p class="note">(Ký, ghi rõ họ tên)</p>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 30px; color: #666;">
            <p>Không có yêu cầu nào trong kỳ này</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Báo cáo được tạo tự động bởi Hệ thống Quản lý Mua sắm - Bệnh viện Đa khoa Tâm Trí Cao Lãnh</p>
        <p>&copy; {{ date('Y') }} TMMC Healthcare. All rights reserved.</p>
    </div>
</body>
</html>
