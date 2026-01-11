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
                $highestRow = $sheet->getHighestRow();
                
                // Add borders to all cells
                $sheet->getStyle('A1:I' . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                // Center align STT and Stock columns
                $sheet->getStyle('A2:A' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->getStyle('G2:G' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align price column
                $sheet->getStyle('F2:F' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
