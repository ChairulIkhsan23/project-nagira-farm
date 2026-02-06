<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fattening extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ternak_id',
        'bobot_awal',
        'bobot_terakhir',
        'target_bobot',
    ];

    public function ternak()
    {
        return $this->belongsTo(Ternak::class);
    }
}
