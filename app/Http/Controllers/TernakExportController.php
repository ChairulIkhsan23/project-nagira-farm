<?php

namespace App\Http\Controllers;

use App\Models\Ternak;
use App\Exports\TernakExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TernakExportController extends Controller
{
    /**
     * Export data ternak ke PDF
     */
    public function exportPDF(Request $request)
    {
        // Validasi parameter
        $validated = $request->validate([
            'kategori' => 'nullable|string|in:breeding,fattening,null',
            'status_kesehatan' => 'nullable|string|in:sehat,perawatan,sakit',
            'status_aktif' => 'nullable|string|in:aktif,nonaktif',
            'search' => 'nullable|string|max:100',
            'format' => 'nullable|string|in:table,card',
        ]);

        // Query data dengan eager loading jika ada relasi
        $query = Ternak::query()
            ->when($request->has('search') && $request->search, function($q) use ($request) {
                $search = $request->search;
                $q->where(function($query) use ($search) {
                    $query->where('nama_ternak', 'like', "%{$search}%")
                          ->orWhere('kode_ternak', 'like', "%{$search}%")
                          ->orWhere('jenis_ternak', 'like', "%{$search}%");
                });
            })
            ->when($request->has('kategori') && $request->kategori, function($q) use ($request) {
                $q->where('kategori', $request->kategori);
            })
            ->when($request->has('status_kesehatan') && $request->status_kesehatan, function($q) use ($request) {
                $q->where('status_kesehatan', $request->status_kesehatan);
            })
            ->when($request->has('status_aktif') && $request->status_aktif, function($q) use ($request) {
                $q->where('status_aktif', $request->status_aktif);
            })
            ->orderBy('kode_ternak', 'asc');

        $ternaks = $query->get();

        // Hitung statistik lanjutan
        $stats = [
            'total' => $ternaks->count(),
            'breeding' => $ternaks->where('kategori', 'breeding')->count(),
            'fattening' => $ternaks->where('kategori', 'fattening')->count(),
            'reguler' => $ternaks->where('kategori', null)->count(),
            'sehat' => $ternaks->where('status_kesehatan', 'sehat')->count(),
            'perawatan' => $ternaks->where('status_kesehatan', 'perawatan')->count(),
            'sakit' => $ternaks->where('status_kesehatan', 'sakit')->count(),
            'aktif' => $ternaks->where('status_aktif', 'aktif')->count(),
            'nonaktif' => $ternaks->where('status_aktif', 'nonaktif')->count(),
            'jantan' => $ternaks->where('jenis_kelamin', 'jantan')->count(),
            'betina' => $ternaks->where('jenis_kelamin', 'betina')->count(),
        ];

        // Hitung rata-rata umur jika data tersedia
        $totalUmur = $ternaks->filter(fn($t) => $t->umur)->sum(function($ternak) {
            return (int) preg_replace('/[^0-9]/', '', $ternak->umur);
        });
        
        $avgUmur = $ternaks->filter(fn($t) => $t->umur)->isNotEmpty() 
            ? round($totalUmur / $ternaks->filter(fn($t) => $t->umur)->count(), 1)
            : 0;

        
        // Random Code 
        $randomCode = Str::upper(Str::random(6));

        // Rentang Update 
        $firstUpdate = $ternaks->min('updated_at');
        $lastUpdate  = $ternaks->max('updated_at');

        // Siapkan logo
        $logoBase64 = $this->getLogoBase64();

        // Format data untuk PDF
        $pdfData = [
            'title' => 'Laporan Data Ternak Nagira Farm',
            'date' => date('d F Y'),
            'period' => now()->translatedFormat('F Y'),
            'ternaks' => $ternaks,
            'total' => $ternaks->count(),
            'logoBase64' => $logoBase64,
            'lastUpdate' => $ternaks->isNotEmpty()
                ? $firstUpdate->translatedFormat('d F Y, H:i')
                    . ' – '
                    . $lastUpdate->translatedFormat('d F Y, H:i')
                : '-',
            'pageInfo' => 'Halaman 1 dari 1',
            'stats' => [
                ['label' => 'TOTAL TERNAK', 'value' => $stats['total']],
                ['label' => 'AKTIF', 'value' => $stats['aktif']],
                ['label' => 'BREEDING', 'value' => $stats['breeding']],
                ['label' => 'SEHAT', 'value' => $stats['sehat']],
                ['label' => 'FATTENING', 'value' => $stats['fattening']],
                ['label' => 'PERAWATAN', 'value' => $stats['perawatan']],
                ['label' => 'RATA UMUR', 'value' => $avgUmur . ' bulan'],
                ['label' => 'SAKIT', 'value' => $stats['sakit']],
            ],
            'filters' => [
                'kategori' => $request->kategori,
                'status_kesehatan' => $request->status_kesehatan,
                'status_aktif' => $request->status_aktif,
                'search' => $request->search,
            ],
            'documentId' => 'NF-' . date('Ymd') . '-' . $randomCode,
            'generatedBy' => 'Nagira Farm v1.0.0',
        ];

        // Konfigurasi PDF
        $paperSize = 'a4';
        $orientation = 'portrait';
        
        // Tentukan view berdasarkan format
        $view = 'export.ternak-pdf';
        if ($request->has('format') && $request->format === 'card') {
            $view = 'export.ternak-cards-pdf';
        }

        // Generate PDF dengan optimasi
        $pdf = Pdf::loadView($view, $pdfData)
                  ->setPaper($paperSize, $orientation)
                  ->setOptions([
                      'defaultFont' => 'sans-serif',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'chroot' => public_path(),
                      'enable_php' => true,
                      'dpi' => 96,
                  ]);

        // Nama file untuk download
        $filename = 'laporan-ternak-nagira-farm-' . date('Y-m-d') . '-' . $randomCode . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export data ternak ke Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'kategori' => $request->kategori,
            'status_kesehatan' => $request->status_kesehatan,
            'status_aktif' => $request->status_aktif,
            'search' => $request->search,
        ];

        $filename = 'data-ternak-nagira-farm-' . date('Y-m-d') . '-' . Str::random(6) . '.xlsx';

        return Excel::download(new TernakExport($filters), $filename);
    }

    /**
     * Export data ternak ke CSV
     */
    public function exportCSV(Request $request)
    {
        // Query data dengan filter yang sama
        $query = Ternak::query()
            ->when($request->has('search') && $request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('nama_ternak', 'like', "%{$request->search}%")
                          ->orWhere('kode_ternak', 'like', "%{$request->search}%");
                });
            })
            ->when($request->has('kategori') && $request->kategori, function($q) use ($request) {
                $q->where('kategori', $request->kategori);
            })
            ->when($request->has('status_kesehatan') && $request->status_kesehatan, function($q) use ($request) {
                $q->where('status_kesehatan', $request->status_kesehatan);
            })
            ->when($request->has('status_aktif') && $request->status_aktif, function($q) use ($request) {
                $q->where('status_aktif', $request->status_aktif);
            });

        $ternaks = $query->get();

        // Headers untuk CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data-ternak-' . date('Y-m-d') . '.csv"',
        ];

        // Callback untuk generate CSV
        $callback = function() use ($ternaks) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Kode Ternak',
                'Nama Ternak',
                'Jenis Ternak',
                'Jenis Kelamin',
                'Kategori',
                'Status Kesehatan',
                'Status Aktif',
                'Tanggal Lahir',
                'Umur',
                'Catatan',
                'Dibuat Pada',
                'Diupdate Pada'
            ]);

            // Data rows
            foreach ($ternaks as $ternak) {
                fputcsv($file, [
                    $ternak->kode_ternak,
                    $ternak->nama_ternak,
                    $ternak->jenis_ternak,
                    $ternak->jenis_kelamin,
                    $ternak->kategori,
                    $ternak->status_kesehatan,
                    $ternak->status_aktif,
                    $ternak->tanggal_lahir,
                    $ternak->umur,
                    $ternak->catatan,
                    $ternak->created_at->format('Y-m-d H:i:s'),
                    $ternak->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Preview PDF sebelum download
     */
    public function previewPDF(Request $request)
    {
        // Logika sama dengan exportPDF tapi return view
        $query = Ternak::query()->limit(10)->get();
        
        $logoBase64 = $this->getLogoBase64();
        
        $previewData = [
            'title' => 'Preview Laporan Data Ternak',
            'date' => date('d F Y'),
            'ternaks' => $query,
            'total' => $query->count(),
            'logoBase64' => $logoBase64,
            'lastUpdate' => date('d F Y, H:i'),
            'stats' => [
                ['label' => 'TOTAL TERNAK', 'value' => $query->count()],
                ['label' => 'AKTIF', 'value' => 8],
                ['label' => 'SEHAT', 'value' => 7],
                ['label' => 'PERAWATAN', 'value' => 2],
            ],
            'filters' => [
                'kategori' => $request->kategori,
                'status_kesehatan' => $request->status_kesehatan,
                'status_aktif' => $request->status_aktif,
                'search' => $request->search,
            ]
        ];

        return view('export.ternak-pdf', $previewData);
    }

    /**
     * Get logo in base64 format
     */
    private function getLogoBase64(): string
    {
        $possiblePaths = [
            public_path('favicon/android-chrome-512x512.png'),
            public_path('images/logo.png'),
            public_path('assets/images/logo.png'),
            storage_path('app/public/logo.png'),
            resource_path('assets/images/logo.png'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $imageData = file_get_contents($path);
                $mimeType = mime_content_type($path);
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
        }

        // Generate SVG logo as fallback
        return $this->generateSVGLogo();
    }

    /**
     * Generate SVG logo as fallback
     */
    private function generateSVGLogo(): string
    {
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#059669;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#047857;stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="200" height="200" fill="url(#gradient)" rx="20"/>
            <path d="M60 100 L100 60 L140 100 L100 140 Z" fill="white" opacity="0.9"/>
            <circle cx="100" cy="100" r="30" fill="none" stroke="white" stroke-width="8" opacity="0.9"/>
            <text x="100" y="120" text-anchor="middle" fill="white" font-family="Arial" font-size="24" font-weight="bold">NF</text>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}