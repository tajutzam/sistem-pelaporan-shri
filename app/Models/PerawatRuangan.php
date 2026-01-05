<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatRuangan extends Model
{
    //
    protected $fillable = [
        'user_id',
        'ruangan_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
