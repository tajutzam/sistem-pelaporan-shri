<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShriKeluar extends Model
{
    //

    protected $table = 'shri_keluar';

     protected $fillable = [
        'shri_id',
        'dpjp_id',
        'diagnosa_id',
        'cara_keluar',
        'lama_dirawat',
        'tanggal_keluar',
    ];

    // Relasi ke model Shri
    public function shri()
    {
        return $this->belongsTo(Shri::class);
    }

    // Relasi ke model Dpjp
    public function dpjp()
    {
        return $this->belongsTo(Dpjp::class);
    }

    // Relasi ke model Diagnosa
    public function diagnosa()
    {
        return $this->belongsTo(Diagnosa::class);
    }


}
