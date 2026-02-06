<?php

namespace App\Exports;

use App\Services\Ternak\TernakQueryService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TernakExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnWidths, WithEvents
{
    protected $filters;
    protected $queryService;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->queryService = new TernakQueryService();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->queryService->getExportData($this->filters);
    }

    /**
     * Headings for Excel
     */
    public function headings(): array
    {
        return [
            'Kode Ternak',
            'Nama Ternak',
            'Jenis Ternak',
            'Kategori',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur',
            'Status Kesehatan',
            'Status Aktif',
            'Dibuat Pada',
            'Diperbarui Pada',
        ];
    }

    /**
     * Map data for Excel
     */
    public function map($ternak): array
    {
        return [
            $ternak->kode_ternak,
            $ternak->nama_ternak,
            $ternak->jenis_ternak,
            $ternak->kategori_label,
            $ternak->jenis_kelamin === 'jantan' ? 'Jantan' : 'Betina',
            $ternak->tanggal_lahir?->format('d/m/Y') ?? '-',
            $ternak->umur ?? '-',
            $this->getKesehatanLabel($ternak->status_kesehatan),
            $ternak->status_aktif === 'aktif' ? 'Aktif' : 'Nonaktif',
            $ternak->created_at->format('d/m/Y H:i'),
            $ternak->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Get kesehatan label
     */
    protected function getKesehatanLabel($status): string
    {
        $labels = [
            'sehat' => 'Sehat',
            'perawatan' => 'Perawatan',
            'sakit' => 'Sakit',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Apply styles to Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Data rows style
        $sheet->getStyle('A2:K' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'EEEEEE'],
                    ],
                ],
            ]);

        // Alternating row colors
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:K{$row}")
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F9FAFB'],
                        ],
                    ]);
            }
        }

        // Auto filter
        $sheet->setAutoFilter('A1:K1');

        return [];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Data Ternak';
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kode Ternak
            'B' => 25, // Nama Ternak
            'C' => 15, // Jenis Ternak
            'D' => 12, // Kategori
            'E' => 12, // Jenis Kelamin
            'F' => 15, // Tanggal Lahir
            'G' => 10, // Umur
            'H' => 15, // Status Kesehatan
            'I' => 12, // Status Aktif
            'J' => 18, // Dibuat Pada
            'K' => 18, // Diperbarui Pada
        ];
    }

    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Add metadata
                $event->sheet->setCellValue('M1', 'Laporan Data Ternak');
                $event->sheet->setCellValue('M2', 'Tanggal: ' . date('d/m/Y'));
                $event->sheet->setCellValue('M3', 'Jumlah Data: ' . $this->collection()->count());
                
                // Apply filter info if available
                if (!empty($this->filters)) {
                    $row = 5;
                    $event->sheet->setCellValue("M{$row}", 'Filter yang diterapkan:');
                    $row++;
                    
                    foreach ($this->filters as $key => $value) {
                        if ($value) {
                            $label = $this->getFilterLabel($key);
                            $event->sheet->setCellValue("M{$row}", "{$label}: {$value}");
                            $row++;
                        }
                    }
                }
            },
        ];
    }

    /**
     * Get filter label
     */
    protected function getFilterLabel($key): string
    {
        $labels = [
            'search' => 'Pencarian',
            'kategori' => 'Kategori',
            'status_kesehatan' => 'Status Kesehatan',
            'status_aktif' => 'Status Aktif',
        ];

        return $labels[$key] ?? $key;
    }
}