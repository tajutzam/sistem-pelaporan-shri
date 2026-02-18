<?php

namespace App\Services;

use App\Models\LaporanIndikatorPelayanan;
use App\Models\LaporanIndikatorDetail;
use Illuminate\Support\Facades\DB;
use Exception;

class LaporanIndikatorService
{
    /**
     * Simpan laporan indikator pelayanan
     */
    public function simpanLaporan($data, $userId)
    {
        try {
            DB::beginTransaction();

            // Buat laporan utama
            $laporan = new LaporanIndikatorPelayanan();
            $laporan->kode_laporan = $laporan->generateKodeLaporan();
            $laporan->tanggal_mulai = $data['periode']['start_date'];
            $laporan->tanggal_selesai = $data['periode']['end_date'];
            $laporan->jumlah_hari = $data['periode']['jumlah_hari'];
            $laporan->default_used = $data['periode']['default_used'];
            $laporan->status = 'draft';
            $laporan->dibuat_oleh = $userId;
            $laporan->save();

            // Simpan detail laporan
            foreach ($data['data'] as $row) {
                $detail = new LaporanIndikatorDetail();
                $detail->laporan_id = $laporan->id;
                $detail->ruangan_id = $row['ruangan_id'];
                $detail->nama_ruangan = $row['nama_ruangan'];
                $detail->jumlah_tempat_tidur = $row['jumlah_tempat_tidur'];
                $detail->jumlah_periode = $row['jumlah_periode'];
                $detail->jumlah_hari_perawatan = $row['jumlah_hari_perawatan'];
                $detail->total_lama_dirawat = $row['total_lama_dirawat'];
                $detail->pasien_keluar_hidup = $row['pasien_keluar_hidup'];
                $detail->pasien_keluar_mati = $row['pasien_keluar_mati'];
                $detail->total_pasien_keluar = $row['total_pasien_keluar'];
                $detail->bor = $row['bor'];
                $detail->avlos = $row['avlos'];
                $detail->bto = $row['bto'];
                $detail->toi = $row['toi'];
                $detail->gdr = $row['gdr'];
                $detail->ndr = $row['ndr'];
                $detail->save();
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Laporan berhasil disimpan',
                'data' => $laporan
            ];

        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim laporan ke kepala rekam medis
     */
    public function kirimLaporan($laporanId, $userId)
    {
        try {
            $laporan = LaporanIndikatorPelayanan::find($laporanId);

            if (!$laporan) {
                return [
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ];
            }

            if ($laporan->dibuat_oleh !== $userId) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengirim laporan ini'
                ];
            }

            if ($laporan->status !== 'draft') {
                return [
                    'success' => false,
                    'message' => 'Laporan sudah dikirim atau disetujui'
                ];
            }

            $laporan->kirim();

            return [
                'success' => true,
                'message' => 'Laporan berhasil dikirim ke Kepala Rekam Medis'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim laporan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Setujui laporan
     */
    public function setujuiLaporan($laporanId, $userId, $catatan = null)
    {
        try {
            $laporan = LaporanIndikatorPelayanan::find($laporanId);

            if (!$laporan) {
                return [
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ];
            }

            if ($laporan->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Laporan tidak dalam status menunggu persetujuan'
                ];
            }

            $laporan->approve($userId, $catatan);

            return [
                'success' => true,
                'message' => 'Laporan berhasil disetujui'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menyetujui laporan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tolak laporan
     */
    public function tolakLaporan($laporanId, $userId, $catatan)
    {
        try {
            $laporan = LaporanIndikatorPelayanan::find($laporanId);

            if (!$laporan) {
                return [
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ];
            }

            if ($laporan->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Laporan tidak dalam status menunggu persetujuan'
                ];
            }

            $laporan->reject($userId, $catatan);

            return [
                'success' => true,
                'message' => 'Laporan berhasil ditolak'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menolak laporan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ambil daftar laporan
     */
    public function getDaftarLaporan($filters = [])
    {
        $query = LaporanIndikatorPelayanan::with(['pembuatLaporan', 'penyetuju']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('tanggal_mulai', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['dibuat_oleh'])) {
            $query->where('dibuat_oleh', $filters['dibuat_oleh']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Ambil detail laporan
     */
    public function getDetailLaporan($laporanId)
    {
        return LaporanIndikatorPelayanan::with(['details', 'pembuatLaporan', 'penyetuju'])
            ->find($laporanId);
    }
}
