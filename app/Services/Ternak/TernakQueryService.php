<?php

namespace App\Services\Ternak;

use App\Models\Ternak;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TernakQueryService
{
    protected $query;

    public function __construct()
    {
        $this->query = Ternak::query();
    }

    /**
     * Inisialisasi query dasar dengan eager loading
     */
    public function baseQuery(): self
    {
        $this->query = Ternak::query()
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
                'breeding:id,ternak_id,status_reproduksi,tanggal_kawin,perkiraan_melahirkan,total_melahirkan',
                'fattening:id,ternak_id,bobot_awal,target_bobot,bobot_terakhir',
                'kesehatans:id,ternak_id,kondisi,tanggal_periksa',
                'riwayatTimbang:id,ternak_id,bobot,tanggal_timbang',
            ]);

        return $this;
    }

    /**
     * Filter berdasarkan pencarian
     */
    public function applySearch(?string $search): self
    {
        if ($search) {
            $this->query->where(function(Builder $q) use ($search) {
                $q->where('nama_ternak', 'like', "%{$search}%")
                ->orWhere('kode_ternak', 'like', "%{$search}%");
            });
        }

        return $this;
    }

    /**
     * Filter berdasarkan kategori
     */
    public function applyKategoriFilter(?string $kategori): self
    {
        if ($kategori) {
            if ($kategori === 'unknown') {
                $this->query->where(function(Builder $q) {
                    $q->whereNull('kategori')
                    ->orWhere('kategori', 'null');
                });
            } else {
                $this->query->where('kategori', $kategori);
            }
        }

        return $this;
    }

    /**
     * Filter berdasarkan status kesehatan
     */
    public function applyKesehatanFilter(?string $kesehatan): self
    {
        if ($kesehatan) {
            $this->query->where('status_kesehatan', $kesehatan);
        }

        return $this;
    }

    /**
     * Filter berdasarkan status aktif
     */
    public function applyStatusFilter(?string $status): self
    {
        if ($status) {
            $this->query->where('status_aktif', $status);
        }

        return $this;
    }

    /**
     * Apply sorting
     */
    public function applySorting(?string $sortBy): self
    {
        if ($sortBy) {
            switch ($sortBy) {
                case 'name_asc':
                    $this->query->orderBy('nama_ternak', 'asc');
                    break;
                case 'name_desc':
                    $this->query->orderBy('nama_ternak', 'desc');
                    break;
                case 'health_asc':
                    $this->query->orderBy('status_kesehatan', 'asc');
                    break;
                case 'health_desc':
                    $this->query->orderBy('status_kesehatan', 'desc');
                    break;
                default:
                    $this->query->latest();
            }
        } else {
            $this->query->latest();
        }

        return $this;
    }

    /**
     * Apply pagination
     */
    public function paginate(int $perPage = 10)
    {
        return $this->query->paginate($perPage);
    }

    /**
     * Get all results
     */
    public function get()
    {
        return $this->query->get();
    }

    /**
     * Apply filters from request
     */
    public function applyFiltersFromRequest(Request $request): self
    {
        return $this->baseQuery()
            ->applySearch($request->search)
            ->applyKategoriFilter($request->kategori)
            ->applyKesehatanFilter($request->kesehatan)
            ->applyStatusFilter($request->status)
            ->applySorting($request->sort_by);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Ternak::count(),
            'breeding' => Ternak::where('kategori', 'breeding')->count(),
            'fattening' => Ternak::where('kategori', 'fattening')->count(),
            'unknown' => Ternak::where(function($query) {
                $query->whereNull('kategori')
                    ->orWhere('kategori', 'null');
            })->count(),
            'sakit' => Ternak::where('status_kesehatan', 'sakit')->count(),
            'nonaktif' => Ternak::where('status_aktif', 'nonaktif')->count(),
        ];
    }

    /**
     * Get export data
     */
    public function getExportData(array $filters = [])
    {
        $query = Ternak::query();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_ternak', 'like', "%{$filters['search']}%")
                ->orWhere('kode_ternak', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['kategori']) && $filters['kategori'] !== 'unknown') {
            $query->where('kategori', $filters['kategori']);
        }

        if (!empty($filters['status_kesehatan'])) {
            $query->where('status_kesehatan', $filters['status_kesehatan']);
        }

        if (!empty($filters['status_aktif'])) {
            $query->where('status_aktif', $filters['status_aktif']);
        }

        return $query->orderBy('kode_ternak')->get();
    }
}