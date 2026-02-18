<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shri extends Model
{
    //
    protected $guarded = ['id'];


    // Relasi ke Pasien
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id');
    }
    // Relasi ke DPJP
    public function dpjp(): BelongsTo
    {
        return $this->belongsTo(Dpjp::class, 'dpjp_id', 'id');
    }

    public function kelasPerawatan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'kelas_perawatan_id', 'id')->withoutGlobalScopes();
    }


    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'kelas_perawatan_id', 'id')->withoutGlobalScopes();
    }

    public function jenisPenjaminan(): BelongsTo
    {
        return $this->belongsTo(Penjaminan::class, 'jenis_penjaminan_id', 'id');
    }


    public function pindah()
    {
        return $this->hasOne(ShriPindah::class, "shri_id", "id");
    }

    public function keluar()
    {
        return $this->hasMany(ShriKeluar::class, "shri_id");
    }

    public function shriPindah()
    {
        return $this->hasOne(ShriPindah::class, "shri_id", "id");
    }

    public function shriKeluar()
    {
        return $this->hasOne(ShriKeluar::class, "shri_id", "id");
    }

    public function kelas()
    {
        return $this->ruangan?->kelas();
    }

}
