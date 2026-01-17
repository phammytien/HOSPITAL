<?php

namespace App\Exports;

use App\Models\Inventory;
use App\Models\Department;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Inventory::with(['warehouse.department', 'product.category'])
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->where('warehouses.is_delete', false)
            ->where('products.is_delete', false)
            ->select('inventory.*');

        if (!empty($this->filters['department_id'])) {
            $query->where('warehouses.department_id', $this->filters['department_id']);
        }

        if (!empty($this->filters['warehouse_id'])) {
            $query->where('inventory.warehouse_id', $this->filters['warehouse_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                  ->orWhere('products.product_code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('inventory.updated_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã kho',
            'Tên kho',
            'Phòng ban',
            'Mã sản phẩm',
            'Tên sản phẩm',
            'Danh mục',
            'Số lượng tồn',
            'Đơn vị',
            'Đơn giá',
            'Thành tiền',
            'Trạng thái',
            'Cập nhật lúc',
        ];
    }

    public function map($inventory): array
    {
        static $index = 0;
        $index++;

        $quantity   = $inventory->quantity;
        $unitPrice  = $inventory->product->unit_price ?? 0;
        $totalValue = $quantity * $unitPrice;

        if ($quantity < 10) {
            $status = 'Sắp hết hàng';
        } elseif ($quantity < 50) {
            $status = 'Còn ít hàng';
        } else {
            $status = 'Đủ hàng';
        }

        return [
            $index,
            $inventory->warehouse->warehouse_code ?? '',
            $inventory->warehouse->warehouse_name ?? '',
            $inventory->warehouse->department->department_name ?? '',
            $inventory->product->product_code ?? '',
            $inventory->product->product_name ?? '',
            $inventory->product->category->category_name ?? '',
            $quantity,
            $inventory->product->unit ?? '',
            number_format($unitPrice, 0, ',', '.'),
            number_format($totalValue, 0, ',', '.'),
            $status,
            $inventory->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function title(): string
    {
        return 'Báo cáo tồn kho';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Chèn 3 dòng đầu
                $sheet->insertNewRowBefore(1, 3);

                // ===== DÒNG 1: NGÀY XUẤT =====
                $filterInfo = [];

                if (!empty($this->filters['department_id'])) {
                    $department = Department::find($this->filters['department_id']);
                    if ($department) {
                        $filterInfo[] = 'Phòng ban: ' . $department->department_name;
                    }
                }

                if (!empty($this->filters['warehouse_id'])) {
                    $warehouse = Warehouse::find($this->filters['warehouse_id']);
                    if ($warehouse) {
                        $filterInfo[] = 'Kho: ' . $warehouse->warehouse_name;
                    }
                }

                if (!empty($this->filters['search'])) {
                    $filterInfo[] = 'Tìm kiếm: "' . $this->filters['search'] . '"';
                }

                $exportInfo = 'Ngày xuất: ' . date('d/m/Y H:i:s');
                if ($filterInfo) {
                    $exportInfo .= ' | ' . implode(' | ', $filterInfo);
                }

                $sheet->setCellValue('A1', $exportInfo);
                $sheet->mergeCells('A1:M1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ===== DÒNG 2: SUBTITLE (IN NGHIÊNG) =====
                $sheet->setCellValue('A2', 'Báo cáo theo điều kiện lọc');
                $sheet->mergeCells('A2:M2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ===== DÒNG 3: TIÊU ĐỀ =====
                $sheet->setCellValue('A3', 'BÁO CÁO TỒN KHO');
                $sheet->mergeCells('A3:M3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Chiều cao dòng
                $sheet->getRowDimension(1)->setRowHeight(18);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(30);
                $sheet->getRowDimension(4)->setRowHeight(25);

                $lastRow = $sheet->getHighestRow();

                // Auto size
                foreach (range('A', 'M') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // ===== HEADER (ROW 4) =====
                $sheet->getStyle('A4:M4')->applyFromArray([
                    'font' => [
                        'bold' => true,
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

                // Wrap text
                $sheet->getStyle("C5:C{$lastRow}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("F5:F{$lastRow}")->getAlignment()->setWrapText(true);

                // Căn lề
                $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H5:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("L5:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J5:K{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Border toàn bảng
                $sheet->getStyle("A4:M{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // ===== TÔ MÀU XEN KẼ (KHÔNG ĐỤNG HEADER) =====
                for ($row = 5; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'],
                            ],
                        ]);
                    }
                }

                // Format số
                $sheet->getStyle("H5:H{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Freeze header
                $sheet->freezePane('A5');
            },
        ];
    }
}
