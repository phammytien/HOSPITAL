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
    protected $supplierId;

    public function __construct($categoryId = null, $search = null, $supplierId = null)
    {
        $this->categoryId = $categoryId;
        $this->search = $search;
        $this->supplierId = $supplierId;
    }

    public function collection()
    {
        $query = Product::with(['category', 'supplier'])
            ->where('is_delete', false);

        // Filter by category
        if ($this->categoryId && $this->categoryId != 'all') {
            $query->where('category_id', $this->categoryId);
        }

        // Filter by supplier
        if ($this->supplierId && $this->supplierId != 'all') {
            $query->where('supplier_id', $this->supplierId);
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
                number_format($product->unit_price, 0, ',', '.'),
                $product->stock_quantity ?? 0,
                $product->supplier->supplier_name ?? 'Chưa có',
                $product->description ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã sản phẩm',
            'Tên sản phẩm',
            'Danh mục',
            'Đơn vị',
            'Đơn giá (VNĐ)',
            'Tồn kho',
            'Nhà cung cấp',
            'Mô tả',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER, // Unit Price
            'G' => NumberFormat::FORMAT_NUMBER, // Stock Quantity
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Insert title rows at the top
                $sheet->insertNewRowBefore(1, 3);
                
                // Add main title
                $sheet->setCellValue('A1', 'DANH SÁCH SẢN PHẨM');
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Add filter info
                $filterInfo = 'Ngày xuất: ' . date('d/m/Y H:i:s');
                if ($this->categoryId) {
                    $category = \App\Models\ProductCategory::find($this->categoryId);
                    $filterInfo .= ' | Danh mục: ' . ($category->category_name ?? 'N/A');
                }
                if ($this->supplierId) {
                    $supplier = \App\Models\Supplier::find($this->supplierId);
                    $filterInfo .= ' | Nhà cung cấp: ' . ($supplier->supplier_name ?? 'N/A');
                }
                if ($this->search) {
                    $filterInfo .= ' | Tìm kiếm: ' . $this->search;
                }
                
                $sheet->setCellValue('A2', $filterInfo);
                $sheet->mergeCells('A2:I2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                
                // Adjust existing styling to account for new rows
                $highestRow = $sheet->getHighestRow();
                
                // Header row is now row 4
                $sheet->getStyle('A4:I4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Add borders to all data cells (from row 4 onwards)
                $sheet->getStyle('A4:I' . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                // Center align STT and Stock columns
                $sheet->getStyle('A5:A' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->getStyle('G5:G' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align price column
                $sheet->getStyle('F5:F' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
