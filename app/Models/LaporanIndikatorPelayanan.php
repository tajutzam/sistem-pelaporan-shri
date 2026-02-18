<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanIndikatorPelayanan extends Model
{
    //
    use HasFactory;

    protected $table = 'laporan_indikator_pelayanan';

    protected $fillable = [
        'kode_laporan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'default_used',
        'status',
        'catatan',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_disetujui',
        'tanggal_dikirim'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_hari' => 'decimal:6',
        'default_used' => 'boolean',
        'tanggal_disetujui' => 'datetime',
        'tanggal_dikirim' => 'datetime'
    ];

    // Relationships
    public function details()
    {
        return $this->hasMany(LaporanIndikatorDetail::class, 'laporan_id');
    }

    public function pembuatLaporan()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_mulai', [$startDate, $endDate]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'draft' => 'Draft',
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    public function getPeriodeTextAttribute()
    {
        return Carbon::parse($this->tanggal_mulai)->format('d/m/Y') . ' - ' .
            Carbon::parse($this->tanggal_selesai)->format('d/m/Y');
    }

    // Methods
    public function generateKodeLaporan()
    {
        $prefix = 'LIP';
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $lastLaporan = self::where('kode_laporan', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('kode_laporan', 'desc')
            ->first();

        if ($lastLaporan) {
            $lastNumber = (int) substr($lastLaporan->kode_laporan, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function approve($userId, $catatan = null)
    {
        $this->update([
            'status' => 'approved',
            'disetujui_oleh' => $userId,
            'tanggal_disetujui' => now(),
            'catatan' => $catatan
        ]);
    }

    public function reject($userId, $catatan)
    {
        $this->update([
            'status' => 'rejected',
            'disetujui_oleh' => $userId,
            'tanggal_disetujui' => now(),
            'catatan' => $catatan
        ]);
    }

    public function kirim()
    {
        $this->update([
            'status' => 'pending',
            'tanggal_dikirim' => now()
        ]);
    }
}
