<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ternak extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_ternak',
        'nama_ternak',
        'jenis_ternak',
        'kategori',
        'jenis_kelamin',
        'tanggal_lahir',
        'foto',
        'status_kesehatan',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected $attributes = [
        'status_kesehatan' => 'sehat',
        'status_aktif' => 'aktif',
    ];

    public function breeding()
    {
        return $this->hasOne(Breeding::class);
    }

    public function fattening()
    {
        return $this->hasOne(Fattening::class);
    }

    public function riwayatReproduksi()
    {
        return $this->hasMany(RiwayatReproduksi::class);
    }


    public function riwayatTimbang()
    {
        return $this->hasMany(RiwayatTimbang::class);
    }

    public function kesehatans()
    {
        return $this->hasMany(Kesehatan::class);
    }

    public function pakan()
    {
        return $this->hasMany(Pakan::class);
    }

    public function produksiTernak()
    {
        return $this->hasMany(ProduksiTernak::class);
    }

    // Helper Methods
    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) return null;
        
        $usia = $this->tanggal_lahir->diff(now());
        
        if ($usia->y > 0) {
            return "{$usia->y} tahun";
        } else {
            return "{$usia->m} bulan";
        }
    }

    public function getKategoriLabelAttribute()
    {
        if ($this->kategori == 'breeding') return 'Breeding';
        if ($this->kategori == 'fattening') return 'Fattening';
        return 'Reguler';
    }

}
