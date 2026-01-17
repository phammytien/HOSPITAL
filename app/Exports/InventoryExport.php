<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $departmentId;
    protected $warehouseId;
    protected $categoryId;
    protected $search;

    public function __construct($departmentId = null, $warehouseId = null, $categoryId = null, $search = null)
    {
        $this->departmentId = $departmentId;
        $this->warehouseId = $warehouseId;
        $this->categoryId = $categoryId;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Inventory::with(['warehouse.department', 'product.category'])
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->where('warehouses.is_delete', false)
            ->where('products.is_delete', false)
            ->select('inventory.*');

        // Filter by department
        if ($this->departmentId) {
            $query->where('warehouses.department_id', $this->departmentId);
        }

        // Filter by warehouse
        if ($this->warehouseId) {
            $query->where('inventory.warehouse_id', $this->warehouseId);
        }

        // Filter by category
        if ($this->categoryId) {
            $query->where('products.category_id', $this->categoryId);
        }

        // Search by product name or code
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', '%' . $search . '%')
                    ->orWhere('products.product_code', 'like', '%' . $search . '%');
            });
        }

        $inventory = $query->orderBy('warehouses.warehouse_name', 'asc')
            ->orderBy('products.product_name', 'asc')
            ->get();

        return $inventory->map(function ($item, $index) {
            return [
                $index + 1,
                $item->warehouse->warehouse_name ?? '',
                $item->product->product_code ?? '',
                $item->product->product_name ?? '',
                $item->product->category->category_name ?? 'N/A',
                $item->product->unit ?? '',
                $item->quantity, // Raw number
                $item->warehouse->department->department_name ?? 'N/A',
                $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['BÁO CÁO SẢN PHẨM TỒN KHO'],
            ['Bệnh viện Đa Khoa Tâm Trí Cao Lãnh'],
            ['Ngày xuất: ' . date('d/m/Y H:i:s')],
            [''], // Spacer
            [
                'STT',
                'Tên kho',
                'Mã sản phẩm',
                'Tên sản phẩm',
                'Danh mục',
                'Đơn vị tính',
                'Số lượng tồn',
                'Phòng ban',
                'Ngày cập nhật',
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0', // Quantity
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Title Style (Row 1)
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Hospital Name Style (Row 2)
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Date Style (Row 3)
            3 => [
                'font' => ['italic' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Header Row Style (Row 5)
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']], // Blue to match standard theme
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $lastColumn = 'I'; // Total 9 columns (A-I)
    
                // Merge Header Rows
                $sheet->mergeCells('A1:' . $lastColumn . '1'); // Title
                $sheet->mergeCells('A2:' . $lastColumn . '2'); // Hospital Name
                $sheet->mergeCells('A3:' . $lastColumn . '3'); // Date
    
                // Adjust Row Heights
                $sheet->getRowDimension(1)->setRowHeight(30); // Title
                $sheet->getRowDimension(2)->setRowHeight(20); // Hospital Name
                $sheet->getRowDimension(5)->setRowHeight(25); // Header Table
    
                // Add borders to the table part (starting from row 5)
                if ($highestRow >= 5) {
                    $sheet->getStyle('A5:' . $lastColumn . $highestRow)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Center align STT (A), Category (E), Unit (F), Stock (G) - Starting from Data Row (6)
                if ($highestRow >= 6) {
                    $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E6:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('G6:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('I6:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
