<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perkawinan extends Model
{
    protected $fillable = [
        'betina_id',
        'pejantan_id',
        'tanggal_kawin',
        'jenis_kawin',
        'hasil',
        'keterangan',
    ];

    public function betina()
    {
        return $this->belongsTo(Ternak::class, 'betina_id');
    }

    public function pejantan()
    {
        return $this->belongsTo(Ternak::class, 'pejantan_id');
    }
}
