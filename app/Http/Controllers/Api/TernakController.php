<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ternak;
use App\Services\Ternak\TernakQueryService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TernakController extends Controller
{
    protected $queryService;

    public function __construct(TernakQueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * GET: /api/ternak
     * Mengambil semua data ternak dengan filter opsional
     */
    public function index(Request $request)
    {
        try {
            $ternaks = $this->queryService
                ->applyFiltersFromRequest($request)
                ->paginate($request->get('per_page', 10));

            $transformedData = $this->transformTernakData($ternaks);

            return response()->json([
                'success' => true,
                'message' => 'Data ternak berhasil diambil',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $ternaks->currentPage(),
                    'total' => $ternaks->total(),
                    'per_page' => $ternaks->perPage(),
                    'last_page' => $ternaks->lastPage(),
                ],
                'filters_applied' => [
                    'search' => $request->search,
                    'kategori' => $request->kategori,
                    'kesehatan' => $request->kesehatan,
                    'status' => $request->status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: /api/ternak/{id}
     * Mengambil detail data satu ternak berdasarkan ID
     */
    public function show($id)
    {
        try {
            $ternak = Ternak::with([
                'breeding',
                'fattening',
                'kesehatans' => function($query) {
                    $query->latest()->limit(5);
                },
                'riwayatTimbang' => function($query) {
                    $query->latest()->limit(10);
                }
            ])->find($id);

            if (!$ternak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data ternak tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail ternak berhasil diambil',
                'data' => $this->transformSingleTernakData($ternak)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: /api/ternak/stats/summary
     * Mengambil statistik keseluruhan data ternak
     */
    public function stats()
    {
        try {
            $stats = $this->queryService->getStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistik ternak berhasil diambil',
                'data' => [
                    ['title' => 'Total Ternak', 'value' => $stats['total'], 'description' => 'Semua ternak terdaftar'],
                    ['title' => 'Breeding', 'value' => $stats['breeding'], 'description' => 'Untuk pengembangbiakan'],
                    ['title' => 'Fattening', 'value' => $stats['fattening'], 'description' => 'Untuk penggemukan'],
                    ['title' => 'Sakit', 'value' => $stats['sakit'], 'description' => 'Membutuhkan penanganan'],
                    ['title' => 'Nonaktif', 'value' => $stats['nonaktif'], 'description' => 'Ternak tidak aktif'],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST: /api/ternak
     * Menambahkan data ternak baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_ternak' => 'required|unique:ternaks|max:50',
                'nama_ternak' => 'required|string|max:255',
                'jenis_ternak' => 'required|string',
                'kategori' => 'required|in:breeding,fattening,null',
                'jenis_kelamin' => 'required|in:jantan,betina',
                'tanggal_lahir' => 'nullable|date',
                'status_kesehatan' => 'required|in:sehat,perawatan,sakit',
                'status_aktif' => 'required|in:aktif,nonaktif',
            ]);

            $ternak = Ternak::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Ternak berhasil ditambahkan',
                'data' => $ternak
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ternak',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Transform collection of ternak data
     */
    protected function transformTernakData($ternaks)
    {
        return $ternaks->map(function ($ternak) {
            return $this->transformSingleTernakData($ternak);
        });
    }

    /**
     * Transform single ternak data
     */
    protected function transformSingleTernakData($ternak)
    {
        $umur = null;
        if ($ternak->tanggal_lahir) {
            $birthDate = Carbon::parse($ternak->tanggal_lahir);
            $now = Carbon::now();
            $diff = $birthDate->diff($now);
            
            if ($diff->y > 0) {
                $umur = "{$diff->y} tahun";
                if ($diff->m > 0) {
                    $umur .= " {$diff->m} bulan";
                }
            } else {
                $umur = "{$diff->m} bulan";
                if ($diff->d > 0) {
                    $umur .= " {$diff->d} hari";
                }
            }
        }

        $data = [
            'id' => $ternak->id,
            'kode_ternak' => $ternak->kode_ternak,
            'nama_ternak' => $ternak->nama_ternak,
            'foto' => $ternak->foto
                ? asset('images/ternak/' . $ternak->foto)
                : asset('images/ternak/ternak1.jpg'),
            'jenis_ternak' => $ternak->jenis_ternak,
            'kategori' => $ternak->kategori,
            'jenis_kelamin' => $ternak->jenis_kelamin,
            'status_aktif' => $ternak->status_aktif,
            'status_kesehatan' => $ternak->status_kesehatan,
            'tanggal_lahir' => $ternak->tanggal_lahir 
                ? $ternak->tanggal_lahir->format('Y-m-d')
                : null,
            'created_at' => $ternak->created_at ? $ternak->created_at->toIso8601String() : null,
            'updated_at' => $ternak->updated_at ? $ternak->updated_at->toIso8601String() : null,
            'umur' => $umur,
            'jumlah_timbang' => $ternak->riwayatTimbang->count(),
            'berat_terakhir' => optional(
                $ternak->riwayatTimbang->sortByDesc('tanggal_timbang')->first()
            )->bobot,
        ];

        // Add breeding data if exists
        if ($ternak->kategori === 'breeding' && $ternak->breeding) {
            $data['breeding'] = [
                'status_reproduksi' => $ternak->breeding->status_reproduksi,
                'tanggal_kawin' => $ternak->breeding->tanggal_kawin?->toIso8601String(),
                'perkiraan_melahirkan' => $ternak->breeding->perkiraan_melahirkan?->toIso8601String(),
                'total_melahirkan' => $ternak->breeding->total_melahirkan ?? 0,
            ];
        }

        // Add fattening data if exists
        if ($ternak->kategori === 'fattening' && $ternak->fattening) {
            $data['fattening'] = [
                'berat_awal' => $ternak->fattening->bobot_awal,
                'target_berat' => $ternak->fattening->target_bobot,
                'berat_saat_ini' => $ternak->fattening->bobot_terakhir ?? $ternak->fattening->bobot_awal ?? null,
                'tanggal_mulai' => null,
                'estimasi_panen' => null,
            ];
        }

        // Add health and weight history for single view
        if (isset($ternak->kesehatans)) {
            $data['kesehatans'] = $ternak->kesehatans->map(function($kesehatan) {
                return [
                    'kondisi' => $kesehatan->kondisi,
                    'tanggal_periksa' => $kesehatan->tanggal_periksa?->format('Y-m-d H:i'),
                ];
            });
        }

        if (isset($ternak->riwayatTimbang)) {
            $data['riwayat_timbang'] = $ternak->riwayatTimbang->map(function($timbang) {
                return [
                    'bobot' => $timbang->bobot,
                    'tanggal_timbang' => $timbang->tanggal_timbang?->format('Y-m-d H:i'),
                ];
            });
        }

        return $data;
    }
}