<?php

namespace App\Services\Ternak;

use App\Exports\TernakExport;
use App\Models\Ternak;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TernakExportService
{
    protected $queryService;

    public function __construct(TernakQueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Generate PDF export
     */
    public function exportToPDF(array $filters = [])
    {
        $ternaks = $this->queryService->getExportData($filters);
        $stats = $this->queryService->getStatistics();

        $pdfData = $this->preparePdfData($ternaks, $stats, $filters);

        $pdf = Pdf::loadView('exports.ternak-pdf', $pdfData)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'enable_php' => true,
                'dpi' => 96,
            ]);

        $filename = 'laporan-ternak-' . date('Y-m-d') . '-' . Str::random(6) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate Excel export
     */
    public function exportToExcel(array $filters = [])
    {
        $filename = 'data-ternak-' . date('Y-m-d') . '-' . Str::random(6) . '.xlsx';

        return Excel::download(new TernakExport($filters), $filename);
    }

    /**
     * Prepare data for PDF
     */
    protected function preparePdfData($ternaks, $stats, $filters): array
    {
        // Calculate statistics
        $calculatedStats = [
            'total' => $ternaks->count(),
            'breeding' => $ternaks->where('kategori', 'breeding')->count(),
            'fattening' => $ternaks->where('kategori', 'fattening')->count(),
            'sehat' => $ternaks->where('status_kesehatan', 'sehat')->count(),
            'perawatan' => $ternaks->where('status_kesehatan', 'perawatan')->count(),
            'sakit' => $ternaks->where('status_kesehatan', 'sakit')->count(),
            'aktif' => $ternaks->where('status_aktif', 'aktif')->count(),
            'nonaktif' => $ternaks->where('status_aktif', 'nonaktif')->count(),
        ];

        // Get date range
        $firstUpdate = $ternaks->min('updated_at');
        $lastUpdate = $ternaks->max('updated_at');

        return [
            'title' => 'Laporan Data Ternak Nagira Farm',
            'ternaks' => $ternaks,
            'total' => $ternaks->count(),
            'period' => now()->translatedFormat('F Y'),
            'lastUpdate' => $firstUpdate && $lastUpdate
                ? $firstUpdate->translatedFormat('d F Y, H:i') . ' – ' . $lastUpdate->translatedFormat('d F Y, H:i')
                : '-',
            'logoBase64' => $this->getLogoBase64(),
            'documentId' => 'NF-' . date('Ymd') . '-' . Str::upper(Str::random(6)),
            'generatedBy' => 'Sistem Informasi Peternakan v1.0',
            'stats' => [
                ['label' => 'TOTAL', 'value' => $calculatedStats['total']],
                ['label' => 'BREEDING', 'value' => $calculatedStats['breeding']],
                ['label' => 'FATTENING', 'value' => $calculatedStats['fattening']],
                ['label' => 'SEHAT', 'value' => $calculatedStats['sehat']],
                ['label' => 'PERAWATAN', 'value' => $calculatedStats['perawatan']],
                ['label' => 'SAKIT', 'value' => $calculatedStats['sakit']],
                ['label' => 'AKTIF', 'value' => $calculatedStats['aktif']],
                ['label' => 'NONAKTIF', 'value' => $calculatedStats['nonaktif']],
            ],
            'filters' => $filters,
        ];
    }

    /**
     * Get logo in base64 format
     */
    protected function getLogoBase64(): string
    {
        $possiblePaths = [
            public_path('images/logo.png'),
            public_path('favicon/android-chrome-512x512.png'),
            storage_path('app/public/logo.png'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $imageData = file_get_contents($path);
                $mimeType = mime_content_type($path);
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
        }

        // Generate fallback SVG logo
        return $this->generateFallbackLogo();
    }

    /**
     * Generate fallback SVG logo
     */
    protected function generateFallbackLogo(): string
    {
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" rx="20" fill="#059669"/>
            <path d="M60 100 L100 60 L140 100 L100 140 Z" fill="white" opacity="0.9"/>
            <circle cx="100" cy="100" r="30" fill="none" stroke="white" stroke-width="8" opacity="0.9"/>
            <text x="100" y="120" text-anchor="middle" fill="white" font-family="Arial" font-size="24" font-weight="bold">NF</text>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get export formats
     */
    public function getAvailableFormats(): array
    {
        return [
            'pdf' => [
                'name' => 'PDF Document',
                'description' => 'Format dokumen untuk cetak dan presentasi',
                'icon' => 'file-text',
            ],
            'excel' => [
                'name' => 'Excel Spreadsheet',
                'description' => 'Format spreadsheet untuk analisis data',
                'icon' => 'table',
            ],
        ];
    }
}