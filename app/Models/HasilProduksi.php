<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilProduksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jenis_produk',
        'jumlah',
        'satuan',
        'foto',
        'deskripsi',
        'tanggal_produksi',
    ];
}
