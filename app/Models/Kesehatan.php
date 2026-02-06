<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kesehatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ternak_id',
        'kondisi',
        'diagnosa',
        'tindakan',
        'obat',
        'tanggal_periksa',
        'jadwal_vaksin_selanjutnya'
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }
}
