<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShriPindah extends Model
{
    //

    protected $table = 'shri_pindah';

    protected $guarded = ['id'];


    public function shri()
    {
        return $this->belongsTo(Shri::class, "shri_id");
    }

    public function kelas()
    {
        return $this->belongsTo(Ruangan::class, 'kelas_perawatan_id');
    }

}
