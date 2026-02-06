<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatTimbang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ternak_id',
        'bobot',
        'tanggal_timbang'
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }
}
