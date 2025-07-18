<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanIndikatorDetail extends Model
{
    //
    use HasFactory;

    protected $table = 'laporan_indikator_detail';

    protected $fillable = [
        'laporan_id',
        'ruangan_id',
        'nama_ruangan',
        'jumlah_tempat_tidur',
        'jumlah_periode',
        'jumlah_hari_perawatan',
        'total_lama_dirawat',
        'pasien_keluar_hidup',
        'pasien_keluar_mati',
        'total_pasien_keluar',
        'bor',
        'avlos',
        'bto',
        'toi',
        'gdr',
        'ndr'
    ];

    protected $casts = [
        'jumlah_periode' => 'decimal:6',
        'bor' => 'decimal:2',
        'avlos' => 'decimal:2',
        'bto' => 'decimal:2',
        'toi' => 'decimal:2',
        'gdr' => 'decimal:2',
        'ndr' => 'decimal:2'
    ];

    // Relationships
    public function laporan()
    {
        return $this->belongsTo(LaporanIndikatorPelayanan::class, 'laporan_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    // Accessors
    public function getBorPercentAttribute()
    {
        return number_format($this->bor, 2) . '%';
    }

    public function getGdrPercentAttribute()
    {
        return number_format($this->gdr, 2) . '%';
    }

    public function getNdrPercentAttribute()
    {
        return number_format($this->ndr, 2) . '%';
    }

    // Methods
    public function getTotalPasienMati()
    {
        return $this->pasien_keluar_mati;
    }

    public function hasActivity()
    {
        return $this->total_pasien_keluar > 0 || $this->jumlah_hari_perawatan > 0;
    }
}
