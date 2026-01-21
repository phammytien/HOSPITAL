<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class QuarterlyReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents
{
    protected $data;
    protected $quarterName;
    protected $grandTotal;

    public function __construct($data, $quarterName)
    {
        $this->data = $data;
        $this->quarterName = $quarterName;
        $this->grandTotal = 0;
    }

    public function collection()
    {
        $rows = collect();
        $this->grandTotal = 0;
        $sttCounter = 1;

        foreach ($this->data as $departmentId => $requests) {
            $department = $requests->first()->department;
            
            // Department Header
            $rows->push(['', '', '', '', '', '', '', '']); // Spacer
            $rows->push(['KHOA/PHÒNG: ' . ($department->department_name ?? 'N/A') . ' (SL: ' . $requests->count() . ')', '', '', '', '', '', '', '']);
            
            $deptTotal = 0;

            foreach ($requests as $order) {
                // Process purchase order items
                if (!$order->purchaseRequest || !$order->purchaseRequest->items) continue;

                foreach ($order->purchaseRequest->items as $item) {
                    $unitPrice = $item->product->unit_price ?? 0;
                    $quantity = $item->quantity ?? 0;
                    $subtotal = $unitPrice * $quantity;
                    $deptTotal += $subtotal;

                    $rows->push([
                        $sttCounter++,
                        $order->purchaseRequest->request_code ?? 'N/A',
                        $item->product->product_name ?? 'N/A',
                        $quantity,
                        $unitPrice,
                        $subtotal,
                        $order->order_date ? $order->order_date->format('d/m/Y') : 'N/A',
                        $item->reason ?? ''
                    ]);
                }
            }
            $this->grandTotal += $deptTotal;
            
            // Subtotal row
            $rows->push(['', '', '', '', 'Tổng cộng:', $deptTotal, '', '']);
        }
        
        // Grand Total
        $rows->push(['', '', '', '', '', '', '', '']);
        $rows->push(['', '', '', '', 'TỔNG CỘNG TOÀN VIỆN:', $this->grandTotal, '', '']);

        // Signatures
        $rows->push(['', '', '', '', '', '', '', '']);
        $rows->push(['', '', '', '', '', '', '', '']);
        $rows->push(['', '', 'Người lập biểu', '', '', '', 'Giám đốc', '']);
        $rows->push(['', '', '(Ký, ghi rõ họ tên)', '', '', '', '(Ký, ghi rõ họ tên)', '']);

        return $rows;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã yêu cầu',
            'Sản phẩm',
            'Số lượng',
            'Đơn giá (VNĐ)',
            'Thành tiền (VNĐ)',
            'Ngày tạo',
            'Ghi chú'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0', // Quantity
            'E' => '#,##0', // Unit Price
            'F' => '#,##0', // Subtotal
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Add Title
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'BÁO CÁO YÊU CẦU MUA HÀNG ' . strtoupper($this->quarterName));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Bệnh viện Đa khoa Tâm Trí Cao Lãnh');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', 'Ngày xuất: ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Department Headers Styling (Bold and Blue text)
                $highestRow = $sheet->getHighestRow();
                for ($row = 6; $row <= $highestRow; $row++) {
                    $val = $sheet->getCell("A{$row}")->getValue();
                    if (str_starts_with((string)$val, 'KHOA/PHÒNG:')) {
                        $sheet->mergeCells("A{$row}:H{$row}");
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE));
                        $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E7F3FF');
                    }
                    
                    // Subtotal styling
                    $valE = $sheet->getCell("E{$row}")->getValue();
                    if ($valE === 'Tổng cộng:') {
                        $sheet->getStyle("E{$row}:F{$row}")->getFont()->setBold(true);
                        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                    }

                    // Grand Total styling
                    if ($valE === 'TỔNG CỘNG TOÀN VIỆN:') {
                        $sheet->getStyle("E{$row}:F{$row}")->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                    }
                }
                
                // Borders for data table
                $sheet->getStyle('A5:H' . ($highestRow - 4))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Merge "Mã yêu cầu" (Column B) for same request codes
                $startRow = 6;
                $currentCode = null;
                $mergeStart = null;

                for ($row = 6; $row <= $highestRow; $row++) {
                    $code = $sheet->getCell("B{$row}")->getValue();

                    // If code is empty (like in spacer rows, department headers, or subtotals), reset merge tracking
                    if (empty($code) || $code === 'Mã yêu cầu') { // Safety check
                        if ($mergeStart && $row - 1 > $mergeStart) {
                            $sheet->mergeCells("B{$mergeStart}:B" . ($row - 1));
                            $sheet->getStyle("B{$mergeStart}:B" . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }
                        $mergeStart = null;
                        $currentCode = null;
                        continue;
                    }

                    if ($code !== $currentCode) {
                        // New code encountered, close previous merge if valid
                        if ($mergeStart && $row - 1 > $mergeStart) {
                            $sheet->mergeCells("B{$mergeStart}:B" . ($row - 1));
                            $sheet->getStyle("B{$mergeStart}:B" . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }

                        // Start tracking new code
                        $currentCode = $code;
                        $mergeStart = $row;
                    }
                    // If code is same, continue loop
                }

                // Close final merge if exists
                if ($mergeStart && $highestRow > $mergeStart) {
                    $sheet->mergeCells("B{$mergeStart}:B{$highestRow}");
                    $sheet->getStyle("B{$mergeStart}:B{$highestRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
            },
        ];
    }
}
