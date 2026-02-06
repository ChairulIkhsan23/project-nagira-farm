<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pakan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ternak_id',
        'jenis_pakan',
        'jumlah_pakan',
        'tanggal_pemberian'
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }
}
