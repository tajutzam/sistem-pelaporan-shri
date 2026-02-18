<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model
{
    //
    protected $fillable = [
        'diagnosa',
        'kode_icd',
    ];


    public function keluars()
    {
        return $this->hasMany(ShriKeluar::class, "diagnosa_id", "id");
    }

}
