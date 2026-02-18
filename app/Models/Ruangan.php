<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class Ruangan extends Model
{
    //
    protected $fillable = [
        'nama_ruangan',
        'jumlah_tempat_tidur',
        'status',
        'kelas_ruangan_id'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_ruangan_id', 'id');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('perawat_access', function (Builder $builder) {
            if (Auth::check() && Auth::user()->hak_akses === 'perawat') {
                $builder->whereHas('perawatRuangans', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            }
        });
    }

    public function perawatRuangans()
    {
        return $this->hasMany(PerawatRuangan::class);
    }

    public function perawats()
    {
        return $this->belongsToMany(User::class, 'perawat_ruangans', 'ruangan_id', 'user_id');
    }


    public function scopeWithAll($query)
    {
        return $query->withoutGlobalScopes();
    }

}
