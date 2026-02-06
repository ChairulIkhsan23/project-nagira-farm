<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Breeding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ternak_id',
        'status_reproduksi',
        'tanggal_kawin',
        'perkiraan_melahirkan',
        'total_melahirkan'
    ];

    protected $casts = [
        'tanggal_kawin' => 'date',
        'perkiraan_melahirkan' => 'date',
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        return match($this->status_reproduksi) {
            'kosong' => 'Tidak Kawin',
            'kawin' => 'Sedang Kawin',
            'bunting' => 'Bunting',
            'nifas' => 'Nifas',
            default => '-'
        };
    }

    public function isBunting()
    {
        return $this->status_reproduksi == 'bunting';
    }

    public function isKawin()
    {
        return $this->status_reproduksi == 'kawin';
    }

    public function getHariBuntingAttribute()
    {
        if (!$this->tanggal_kawin || !$this->isBunting()) {
            return null;
        }

        return $this->tanggal_kawin->diffInDays(now());
    }

    public function getSisaHariAttribute()
    {
        if (!$this->perkiraan_melahirkan || !$this->isBunting()) {
            return null;
        }

        return now()->diffInDays($this->perkiraan_melahirkan, false);
    }
}