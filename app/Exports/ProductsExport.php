<?php

namespace App\Exports;

use App\Models\Product;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $categoryId;
    protected $search;

    public function __construct($categoryId = null, $search = null)
    {
        $this->categoryId = $categoryId;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Product::with(['category', 'supplier'])
            ->where('is_delete', false);

        // Filter by category
        if ($this->categoryId && $this->categoryId != 'all') {
            $query->where('category_id', $this->categoryId);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('product_name', 'like', "%{$this->search}%")
                    ->orWhere('product_code', 'like', "%{$this->search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return $products->map(function ($product, $index) {
            return [
                $index + 1,
                $product->product_code,
                $product->product_name,
                $product->category->category_name ?? 'N/A',
                $product->unit,
                $product->unit_price, // Raw number for formatting
                $product->stock_quantity ?? 0,
                $product->supplier->supplier_name ?? 'Chưa có',
                $product->description ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['BÁO CÁO DANH SÁCH SẢN PHẨM NĂM ' . date('Y')],
            ['Bệnh viện Đa Khoa Tâm Trí Cao Lãnh'],
            ['Ngày xuất: ' . date('d/m/Y H:i:s')],
            [''], // Spacer
            [
                'STT',
                'Mã sản phẩm',
                'Tên sản phẩm',
                'Danh mục',
                'Đơn vị',
                'Đơn giá (VNĐ)',
                'Tồn kho',
                'Nhà cung cấp',
                'Mô tả',
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0', // Unit Price
            'G' => '#,##0', // Stock Quantity
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
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
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
                $sheet->getStyle('A5:' . $lastColumn . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Center align STT (A), Category (D), Unit (E), Stock (G) - Starting from Data Row (6)
                $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E6:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G6:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Right align Price (F)
                $sheet->getStyle('F6:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
