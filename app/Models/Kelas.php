<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    //
    protected $table = 'kelas';

    protected $guarded = ['id'];

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class, 'kelas_ruangan_id', 'id');
    }

}
