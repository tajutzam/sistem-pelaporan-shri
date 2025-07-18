<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShriReportService
{
    public function getShriReport($tanggal_mulai, $tanggal_selesai, $kelas_perawatan_id = null)
    {
        $results = [];

        $current_date = Carbon::parse($tanggal_mulai);
        $end_date = Carbon::parse($tanggal_selesai);

        while ($current_date <= $end_date) {
            $tanggal = $current_date->format('Y-m-d');

            // Base query dengan filter kelas perawatan
            $baseQuery = function ($query) use ($kelas_perawatan_id) {
                if ($kelas_perawatan_id) {
                    $query->where('shris.kelas_perawatan_id', $kelas_perawatan_id);
                }
            };

            // 1. Pasien Awal
            $pasien_awal = DB::table('shris')
                ->where('tanggal_masuk', '<', $tanggal)
                ->where(function ($query) use ($tanggal) {
                    $query->whereNotExists(function ($subquery) use ($tanggal) {
                        $subquery->select(DB::raw(1))
                            ->from('shri_keluar')
                            ->whereRaw('shri_keluar.shri_id = shris.id')
                            ->where('tanggal_keluar', '<', $tanggal);
                    })->whereNotExists(function ($subquery) use ($tanggal) {
                        $subquery->select(DB::raw(1))
                            ->from('shri_pindah')
                            ->whereRaw('shri_pindah.shri_id = shris.id')
                            ->where('tanggal_pindah', '<', $tanggal);
                    });
                })
                ->where($baseQuery)
                ->count();

            // 2. Pasien Masuk
            $pasien_masuk = DB::table('shris')
                ->where('tanggal_masuk', $tanggal)
                ->where('asal_pasien', '!=', 'pindahan antar ruangan')
                ->where($baseQuery)
                ->count();

            // 3. Pasien Pindahan
            $pasien_pindahan = DB::table('shris')
                ->where('tanggal_masuk', $tanggal)
                ->where('asal_pasien', 'pindahan antar ruangan')
                ->where($baseQuery)
                ->count();

            // 4. Pasien Dipindahkan
            $pasien_dipindahkan = DB::table('shri_pindah')
                ->join('shris', 'shri_pindah.shri_id', '=', 'shris.id')
                ->where('shri_pindah.tanggal_pindah', $tanggal)
                ->where($baseQuery)
                ->count();

            // 5. Pasien Keluar Hidup
            $pasien_keluar_hidup = DB::table('shri_keluar')
                ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
                ->where('shri_keluar.tanggal_keluar', $tanggal)
                ->whereIn('shri_keluar.cara_keluar', ['APS', 'APD'])
                ->where($baseQuery)
                ->count();

            // 6. Pasien Keluar Mati berdasarkan jenis kelamin dan waktu
            $pasien_keluar_mati = $this->getPasienKeluarMati($tanggal, $baseQuery);

            // 7. Jumlah Lama Dirawat
            $total_lama_dirawat = $this->getTotalLamaDirawat($tanggal, $baseQuery);

            // 8. Jumlah Hari Perawatan
            $total_keluar_mati = array_sum($pasien_keluar_mati);
            $jumlah_hari_perawatan = ($pasien_awal + $pasien_masuk + $pasien_pindahan) -
                ($pasien_dipindahkan + $pasien_keluar_hidup + $total_keluar_mati);

            // 9. Rincian per kelas
            $rincian_per_kelas = $this->getRincianPerKelas($tanggal, $baseQuery);

            $results[] = [
                'tanggal' => $tanggal,
                'pasien_awal' => $pasien_awal,
                'pasien_masuk' => $pasien_masuk,
                'pasien_pindahan' => $pasien_pindahan,
                'pasien_dipindahkan' => $pasien_dipindahkan,
                'pasien_keluar_hidup' => $pasien_keluar_hidup,
                'pasien_keluar_mati_l_kurang_48' => $pasien_keluar_mati['l_kurang_48'],
                'pasien_keluar_mati_l_lebih_48' => $pasien_keluar_mati['l_lebih_48'],
                'pasien_keluar_mati_p_kurang_48' => $pasien_keluar_mati['p_kurang_48'],
                'pasien_keluar_mati_p_lebih_48' => $pasien_keluar_mati['p_lebih_48'],
                'total_lama_dirawat' => $total_lama_dirawat,
                'jumlah_hari_perawatan' => $jumlah_hari_perawatan,
                'rincian_per_kelas' => $rincian_per_kelas
            ];

            $current_date->addDay();
        }

        return $results;
    }

    private function getPasienKeluarMati($tanggal, $baseQuery)
    {
        $base = DB::table('shri_keluar')
            ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
            ->join('pasiens', 'shris.pasien_id', '=', 'pasiens.id')
            ->where('shri_keluar.tanggal_keluar', $tanggal)
            ->where($baseQuery);

        return [
            'l_kurang_48' => (clone $base)->where('shri_keluar.cara_keluar', 'Mati < 48 Jam')
                ->where('pasiens.jenis_kelamin', 'L')->count(),
            'l_lebih_48' => (clone $base)->where('shri_keluar.cara_keluar', 'Mati > 48 Jam')
                ->where('pasiens.jenis_kelamin', 'L')->count(),
            'p_kurang_48' => (clone $base)->where('shri_keluar.cara_keluar', 'Mati < 48 Jam')
                ->where('pasiens.jenis_kelamin', 'P')->count(),
            'p_lebih_48' => (clone $base)->where('shri_keluar.cara_keluar', 'Mati > 48 Jam')
                ->where('pasiens.jenis_kelamin', 'P')->count(),
        ];
    }

    private function getTotalLamaDirawat($tanggal, $baseQuery)
    {
        $lama_dirawat_keluar = DB::table('shri_keluar')
            ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
            ->where('shri_keluar.tanggal_keluar', $tanggal)
            ->where($baseQuery)
            ->sum(DB::raw('CAST(shri_keluar.lama_dirawat AS UNSIGNED)'));

        $lama_dirawat_pindah = DB::table('shri_pindah')
            ->join('shris', 'shri_pindah.shri_id', '=', 'shris.id')
            ->where('shri_pindah.tanggal_pindah', $tanggal)
            ->where($baseQuery)
            ->sum(DB::raw('CAST(shri_pindah.lama_dirawat AS UNSIGNED)'));

        return ($lama_dirawat_keluar ?: 0) + ($lama_dirawat_pindah ?: 0);
    }

    private function getRincianPerKelas($tanggal, $baseQuery)
    {
        return DB::table('shris')
            ->join('ruangans', 'shris.kelas_perawatan_id', '=', 'ruangans.id')
            ->select('ruangans.id', 'ruangans.nama_ruangan', DB::raw('COUNT(*) as jumlah'))
            ->where(function ($query) use ($tanggal) {
                $query->where('tanggal_masuk', '<=', $tanggal)
                    ->where(function ($subquery) use ($tanggal) {
                        $subquery->whereNotExists(function ($q) use ($tanggal) {
                            $q->select(DB::raw(1))
                                ->from('shri_keluar')
                                ->whereRaw('shri_keluar.shri_id = shris.id')
                                ->where('tanggal_keluar', '<=', $tanggal);
                        })->whereNotExists(function ($q) use ($tanggal) {
                            $q->select(DB::raw(1))
                                ->from('shri_pindah')
                                ->whereRaw('shri_pindah.shri_id = shris.id')
                                ->where('tanggal_pindah', '<=', $tanggal);
                        });
                    });
            })
            ->where($baseQuery)
            ->groupBy('ruangans.id', 'ruangans.nama_ruangan')
            ->get()
            ->keyBy('id');
    }

    public function getFormattedReport($tanggal_mulai, $tanggal_selesai, $kelas_perawatan_id = null)
    {
        return $this->getShriReport($tanggal_mulai, $tanggal_selesai, $kelas_perawatan_id);
    }
}
