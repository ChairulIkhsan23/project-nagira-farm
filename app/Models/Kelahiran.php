<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelahiran extends Model
{
    protected $fillable = [
        'betina_id',
        'tanggal_melahirkan',
        'jumlah_anak',
        'keterangan',
    ];

    public function betina()
    {
        return $this->belongsTo(Ternak::class, 'betina_id');
    }
}
