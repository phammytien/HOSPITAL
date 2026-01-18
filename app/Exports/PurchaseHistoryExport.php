<?php

namespace App\Exports;

use App\Models\PurchaseRequest;
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
use Illuminate\Support\Facades\DB;

class PurchaseHistoryExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $departmentId;
    protected $search;
    protected $monthFrom;
    protected $monthTo;

    public function __construct($departmentId = null, $search = null, $monthFrom = null, $monthTo = null)
    {
        $this->departmentId = $departmentId;
        $this->search = $search;
        $this->monthFrom = $monthFrom;
        $this->monthTo = $monthTo;
    }

    public function collection()
    {
        $query = PurchaseRequest::with(['department', 'requester', 'items'])
            ->where('purchase_requests.is_delete', false)
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'PAID']);

        // Filter by department
        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', '%' . $search . '%')
                    ->orWhere('note', 'like', '%' . $search . '%');
            });
        }

        // Date range filter (Month/Year)
        if ($this->monthFrom) {
            $query->whereDate('purchase_requests.created_at', '>=', $this->monthFrom . '-01');
        }

        if ($this->monthTo) {
            $dateTo = \Carbon\Carbon::parse($this->monthTo)->endOfMonth();
            $query->whereDate('purchase_requests.created_at', '<=', $dateTo);
        }

        $history = $query->orderByRaw('YEAR(purchase_requests.created_at) DESC')
            ->orderByRaw('QUARTER(purchase_requests.created_at) DESC')
            ->orderBy('department_id', 'ASC')
            ->orderBy('purchase_requests.created_at', 'DESC')
            ->get();

        return $history->map(function ($request, $index) {
            $total = $request->items->sum(function ($item) {
                return $item->quantity * $item->expected_price;
            });

            return [
                $index + 1,
                $request->request_code,
                $request->created_at->format('d/m/Y H:i'),
                ($request->status == 'COMPLETED' || $request->status == 'PAID') && $request->updated_at
                ? $request->updated_at->format('d/m/Y H:i')
                : 'Chưa hoàn thành',
                $request->department->department_name ?? 'N/A',
                $request->requester->full_name ?? 'N/A',
                $total,
                $this->getStatusLabel($request->status),
            ];
        });
    }

    protected function getStatusLabel($status)
    {
        $labels = [
            'APPROVED' => 'Đã duyệt',
            'COMPLETED' => 'Hoàn thành',
            'PAID' => 'Đã thanh toán',
            'PENDING' => 'Chờ xử lý',
            'REJECTED' => 'Từ chối',
            'CANCELLED' => 'Đã hủy',
        ];

        return $labels[$status] ?? $status;
    }

    public function headings(): array
    {
        return [
            ['BÁO CÁO LỊCH SỬ MUA HÀNG'],
            ['Bệnh viện Đa Khoa Tâm Trí Cao Lãnh'],
            ['Ngày xuất: ' . date('d/m/Y H:i:s')],
            [''], // Spacer
            [
                'STT',
                'Mã yêu cầu',
                'Ngày tạo',
                'Ngày hoàn thành',
                'Khoa/Phòng',
                'Người yêu cầu',
                'Tổng tiền',
                'Trạng thái',
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0', // Total Amount
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
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']], // Blue
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
                $lastColumn = 'H'; // Total 8 columns (A-H)
    
                // Merge Header Rows
                $sheet->mergeCells('A1:' . $lastColumn . '1'); // Title
                $sheet->mergeCells('A2:' . $lastColumn . '2'); // Hospital Name
                $sheet->mergeCells('A3:' . $lastColumn . '3'); // Date
    
                // Adjust Row Heights
                $sheet->getRowDimension(1)->setRowHeight(30); // Title
                $sheet->getRowDimension(2)->setRowHeight(20); // Hospital Name
                $sheet->getRowDimension(5)->setRowHeight(25); // Header Table
    
                // Add borders
                if ($highestRow >= 5) {
                    $sheet->getStyle('A5:' . $lastColumn . $highestRow)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Center align STT (A), Date (C), Completion Date (D), Status (H)
                if ($highestRow >= 6) {
                    $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C6:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H6:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
