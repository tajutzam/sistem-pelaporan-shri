<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    //
    protected $fillable = [
        'nama_ruangan',
        'kelas_ruangan',
        'jumlah_tempat_tidur',
        'status',
    ];
}
