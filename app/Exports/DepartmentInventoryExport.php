<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DepartmentInventoryExport implements FromCollection, WithHeadings, WithStyles, WithEvents, ShouldAutoSize
{
    protected $products;
    protected $departmentName;

    public function __construct($products)
    {
        $this->products = $products;
        $this->departmentName = Auth::user()->department->department_name ?? 'Khoa Phòng';
    }

    public function collection()
    {
        $data = [];
        $i = 1;
        foreach ($this->products as $product) {
            $data[] = [
                'stt' => $i++,
                'code' => $product->product_code,
                'name' => $product->product_name,
                'quantity' => $product->stock_quantity_dept ?? 0,
                'unit' => $product->unit,
                'price' => $product->unit_price,
                'total' => ($product->stock_quantity_dept ?? 0) * $product->unit_price,
                'date' => Carbon::now()->format('d/m/Y'),
                'note' => '',
            ];
        }
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã yêu cầu',
            'Sản phẩm',
            'Số lượng',
            'Đơn vị',
            'Đơn giá (VNĐ)',
            'Thành tiền (VNĐ)',
            'Ngày tạo',
            'Ghi chú'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row (row 5 in this case due to custom headers)
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F497D'] // Dark Blue
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $rows = $this->products->count() + 5; // Start at 5 (headers) + count
    
                // 1. Insert Custom Header Rows
                $sheet->insertNewRowBefore(1, 4);

                // Merged Title
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'BÁO CÁO YÊU CẦU MUA HÀNG QUÝ ' . ceil(date('n') / 3) . ' NĂM ' . date('Y'));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Sub-title (Hospital Name)
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Bệnh viện Đa khoa Tâm Trí Cao Lãnh');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Export Date
                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', 'Ngày xuất: ' . Carbon::now()->format('d/m/Y H:i:s'));
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. Style Data Table Borders
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ],
                ];
                // Apply borders from row 5 (headers) to end of data
                $sheet->getStyle('A5:I' . $rows)->applyFromArray($styleArray);

                // 3. Total Row
                $totalRow = $rows + 1;
                $sheet->mergeCells("A{$totalRow}:D{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TỔNG CỘNG TOÀN VIỆN:');

                // Calculate Total Amount
                $totalAmount = 0;
                foreach ($this->products as $p) {
                    $qty = $p->stock_quantity_dept ?? 0;
                    $totalAmount += $qty * $p->unit_price;
                }
                $sheet->setCellValue("G{$totalRow}", $totalAmount);

                // Style Total Row
                $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9D9D9'] // Light Gray
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // 4. Signatures
                $sigRow = $totalRow + 2;
                $sheet->setCellValue("C{$sigRow}", "Người lập biểu");
                $sheet->setCellValue("G{$sigRow}", "Giám đốc");

                $sheet->setCellValue("C" . ($sigRow + 1), "(Ký, ghi rõ họ tên)");
                $sheet->setCellValue("G" . ($sigRow + 1), "(Ký, ghi rõ họ tên)");

                $sheet->getStyle("C{$sigRow}:G" . ($sigRow + 1))->getFont()->setBold(true);
            },
        ];
    }
}
