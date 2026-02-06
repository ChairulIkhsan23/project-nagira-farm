<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ternak\TernakExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(TernakExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export data ternak ke PDF
     */
    public function exportPDF(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'kategori' => 'nullable|string|in:breeding,fattening',
            'status_kesehatan' => 'nullable|string|in:sehat,perawatan,sakit',
            'status_aktif' => 'nullable|string|in:aktif,nonaktif',
        ]);

        return $this->exportService->exportToPDF($validated);
    }

    /**
     * Export data ternak ke Excel
     */
    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'kategori' => 'nullable|string|in:breeding,fattening',
            'status_kesehatan' => 'nullable|string|in:sehat,perawatan,sakit',
            'status_aktif' => 'nullable|string|in:aktif,nonaktif',
        ]);

        return $this->exportService->exportToExcel($validated);
    }

    /**
     * Get available export formats
     */
    public function getFormats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->exportService->getAvailableFormats()
        ]);
    }
}