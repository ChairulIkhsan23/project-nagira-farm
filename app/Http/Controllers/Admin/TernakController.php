<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ternak;
use Inertia\Inertia;
use Carbon\Carbon;

class TernakController extends Controller
{
    public function index()
    {
        $ternaks = Ternak::query()
            ->select([
                'id',
                'kode_ternak',
                'nama_ternak',
                'jenis_ternak',
                'kategori',
                'jenis_kelamin',
                'tanggal_lahir',
                'foto',
                'status_kesehatan',
                'status_aktif',
                'created_at',
                'updated_at',
            ])
            ->with([
                // Untuk kategori breeding
                'breeding:id,ternak_id,status_reproduksi,tanggal_kawin,perkiraan_melahirkan,total_melahirkan',
                
                // Untuk kategori fattening
                'fattening:id,ternak_id,bobot_awal,target_bobot,bobot_terakhir',
                
                // kesehatan terakhir
                'kesehatans:id,ternak_id,kondisi,tanggal_periksa',

                // riwayat timbang
                'riwayatTimbang:id,ternak_id,bobot,tanggal_timbang',
            ])
            ->latest()
            ->get()
            ->map(function ($t) {
                $umur = null;
                if ($t->tanggal_lahir) {
                    $birthDate = Carbon::parse($t->tanggal_lahir);
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

                $tanggalLahirFormatted = null;
                if ($t->tanggal_lahir) {
                    $tanggalLahirFormatted = Carbon::parse($t->tanggal_lahir)->format('Y-m-d');
                }

                // Format created_at dan updated_at
                $createdAt = $t->created_at ? $t->created_at->toIso8601String() : null;
                $updatedAt = $t->updated_at ? $t->updated_at->toIso8601String() : null;

                return [
                    'id' => $t->id,
                    'kode_ternak' => $t->kode_ternak,
                    'nama_ternak' => $t->nama_ternak,
                    'foto' => $t->foto
                        ? asset('images/ternak/' . $t->foto)
                        : asset('images/ternak/ternak1.jpg'),
                    'jenis_ternak' => $t->jenis_ternak,
                    'kategori' => $t->kategori,
                    'jenis_kelamin' => $t->jenis_kelamin,
                    'status_aktif' => $t->status_aktif,
                    'status_kesehatan' => $t->status_kesehatan,
                    'tanggal_lahir' => $tanggalLahirFormatted,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'umur' => $umur,
                    'jumlah_timbang' => $t->riwayatTimbang->count(),
                    'berat_terakhir' => optional(
                        $t->riwayatTimbang->sortByDesc('tanggal_timbang')->first()
                    )->bobot,
                    
                    // Data berdasarkan kategori
                    'breeding' => ( $t->kategori === 'breeding' 
                        && $t->breeding
                    ) ? [
                        'status_reproduksi' => $t->breeding->status_reproduksi,
                        'tanggal_kawin' => $t->breeding->tanggal_kawin
                            ? $t->breeding->tanggal_kawin->toIso8601String()
                            : null,
                        'perkiraan_melahirkan' => $t->breeding->perkiraan_melahirkan
                            ? $t->breeding->perkiraan_melahirkan->toIso8601String()
                            : null,
                        'total_melahirkan' => $t->breeding->total_melahirkan ?? 0,
                    ] : null,
                    
                   'fattening' => ( $t->kategori === 'fattening'
                        && $t->fattening
                    ) ? [
                        'berat_awal' => $t->fattening->bobot_awal,
                        'target_berat' => $t->fattening->target_bobot,
                        'berat_saat_ini' => $t->fattening->bobot_terakhir
                            ?? $t->fattening->bobot_awal
                            ?? null,
                        'tanggal_mulai' => null,
                        'estimasi_panen' => null,
                    ] : null,
                ];
            });

        return Inertia::render('Admin/Ternak/Index', [
            'ternaks' => $ternaks,
        ]);
    }
}
