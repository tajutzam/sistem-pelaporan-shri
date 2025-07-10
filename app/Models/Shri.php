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
        return $this->belongsTo(Ruangan::class, 'kelas_perawatan_id', 'id');
    }

    public function jenisPenjaminan(): BelongsTo
    {
        return $this->belongsTo(Penjaminan::class, 'jenis_penjaminan_id', 'id');
    }

}
