<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PurchaseHistoryExport implements FromCollection, WithHeadings, WithEvents, WithColumnWidths
{
    protected $history;
    protected $filters;

    public function __construct($history, $filters = [])
    {
        $this->history = $history;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->history->map(function ($request, $index) {
            $total = $request->items->sum(function($item) {
                return $item->quantity * $item->expected_price;
            });

            return [
                $index + 1,
                $request->request_code,
                $request->created_at->format('d/m/Y'),
                $request->created_at->format('H:i'),
                $request->department->department_name ?? 'N/A',
                $request->requester->full_name ?? 'N/A',
                $total,
                $this->getStatusLabel($request->status),
                $request->note ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã yêu cầu',
            'Ngày tạo',
            'Giờ',
            'Khoa/Phòng',
            'Người yêu cầu',
            'Tổng tiền (VNĐ)',
            'Trạng thái',
            'Ghi chú',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // STT
            'B' => 25,  // Mã yêu cầu
            'C' => 15,  // Ngày tạo
            'D' => 10,  // Giờ
            'E' => 30,  // Khoa/Phòng
            'F' => 25,  // Người yêu cầu
            'G' => 20,  // Tổng tiền
            'H' => 15,  // Trạng thái
            'I' => 40,  // Ghi chú
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert title rows at the top
                $sheet->insertNewRowBefore(1, 3);
                
                // Build filter info
                $filterInfo = [];
                
                if (!empty($this->filters['department_id'])) {
                    $department = \App\Models\Department::find($this->filters['department_id']);
                    if ($department) {
                        $filterInfo[] = 'Khoa/Phòng: ' . $department->department_name;
                    }
                }
                
                if (!empty($this->filters['month_from'])) {
                    $filterInfo[] = 'Từ tháng: ' . date('m/Y', strtotime($this->filters['month_from']));
                }
                
                if (!empty($this->filters['month_to'])) {
                    $filterInfo[] = 'Đến tháng: ' . date('m/Y', strtotime($this->filters['month_to']));
                }
                
                if (!empty($this->filters['search'])) {
                    $filterInfo[] = 'Tìm kiếm: "' . $this->filters['search'] . '"';
                }
                
                // Build export info line
                $exportInfo = 'Ngày xuất: ' . date('d/m/Y H:i:s');
                if (!empty($filterInfo)) {
                    $exportInfo .= ' | ' . implode(' | ', $filterInfo);
                }
                
                // Set export info (row 1)
                $sheet->setCellValue('A1', $exportInfo);
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                
                // Set main title (row 3)
                $sheet->setCellValue('A3', 'BÁO CÁO LỊCH SỬ MUA HÀNG');
                $sheet->mergeCells('A3:I3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(30);
                $sheet->getRowDimension(4)->setRowHeight(25);
                
                // Get last row
                $lastRow = $sheet->getHighestRow();
                
                // Style header row (row 4) - Blue background with white text
                $sheet->getStyle('A4:I4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                
                // Enable text wrapping for long text columns
                $sheet->getStyle('I5:I' . $lastRow)->getAlignment()->setWrapText(true);
                
                // Center align specific columns
                $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H4:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align number columns
                $sheet->getStyle('G5:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Add borders to all data cells
                $sheet->getStyle('A4:I' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
                
                // Add alternating row colors (light gray for even rows)
                for ($row = 6; $row <= $lastRow; $row += 2) {
                    $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F2F2F2'],
                        ],
                    ]);
                }
                
                // Format number columns with thousand separator
                $sheet->getStyle('G5:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Vertical alignment for all data cells
                $sheet->getStyle('A5:I' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Freeze panes (freeze title and header rows)
                $sheet->freezePane('A5');
            },
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'PENDING' => 'Chờ duyệt',
            'APPROVED' => 'Đã duyệt',
            'REJECTED' => 'Từ chối',
            'COMPLETED' => 'Hoàn thành',
            'PAID' => 'Đã thanh toán',
        ];

        return $labels[$status] ?? $status;
    }
}
