<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProduksiTernak extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jenis_produksi',
        'ternak_id',
        'jumlah',
        'keterangan',
        'tanggal',
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }
}
