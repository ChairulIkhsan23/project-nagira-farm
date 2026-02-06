<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatReproduksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ternak_id',
        'tipe',
        'tanggal',
        'keterangan'
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }
}
