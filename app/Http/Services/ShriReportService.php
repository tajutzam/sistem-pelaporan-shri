<?php

namespace App\Http\Services;

use App\Models\Shri;
use App\Models\ShriKeluar;
use App\Models\ShriPindah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShriReportService
{
    public function getShriReport($tanggal_mulai, $tanggal_selesai, $ruangan_ids = null)
    {
        $results = [];

        $current_date = Carbon::parse($tanggal_mulai);
        $end_date = Carbon::parse($tanggal_selesai);

        // base query helper untuk shris & shri_keluar
        $baseQueryShris = function ($query) use ($ruangan_ids) {
            if ($ruangan_ids && count($ruangan_ids) > 0) {
                $query->whereIn('shris.kelas_perawatan_id', $ruangan_ids);
            }
        };

        // base query helper khusus untuk shri_pindah
        $baseQueryPindah = function ($query) use ($ruangan_ids) {
            if ($ruangan_ids && count($ruangan_ids) > 0) {
                $query->whereIn('shri_pindah.kelas_perawatan_id', $ruangan_ids);
            }
        };

        while ($current_date <= $end_date) {
            $tanggal = $current_date->format('Y-m-d');

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
                ->where($baseQueryShris)
                ->count();

            // 2. Pasien Masuk
            // 2. Pasien Masuk Langsung
            $pasien_masuk = DB::table('shris')
                ->whereDate('tanggal_masuk', $tanggal)
                ->where('asal_pasien', 'masuk-langsung')
                ->where($baseQueryShris)
                ->count();

            // 3. Pasien Pindahan Antar Ruangan
            $pasien_pindahan = DB::table('shris')
                ->whereDate('tanggal_masuk', $tanggal)
                ->where('asal_pasien', 'pindahan-antar-ruangan')
                ->where($baseQueryShris)
                ->count();


            // 4. Pasien Dipindahkan (keluar ke ruangan lain)
            $pasien_dipindahkan = DB::table('shri_pindah')
                ->join('shris', 'shri_pindah.shri_id', '=', 'shris.id')
                ->where('shri_pindah.tanggal_pindah', $tanggal)
                ->where($baseQueryPindah) // <- fix di sini
                ->count();

            // 5. Pasien Keluar Hidup
            $pasien_keluar_hidup = DB::table('shri_keluar')
                ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
                ->where('shri_keluar.tanggal_keluar', $tanggal)
                ->whereIn('shri_keluar.cara_keluar', ['APS', 'APD'])
                ->where($baseQueryShris)
                ->count();

            // 6. Pasien Keluar Mati
            $pasien_keluar_mati = $this->getPasienKeluarMati($tanggal, $baseQueryShris);

            // 7. Lama Dirawat
            $total_lama_dirawat = $this->getTotalLamaDirawat($tanggal, $baseQueryShris);

            // 8. Jumlah Hari Perawatan
            $total_keluar_mati = array_sum($pasien_keluar_mati);
            $jumlah_hari_perawatan = ($pasien_awal + $pasien_masuk + $pasien_pindahan)
                - ($pasien_dipindahkan + $pasien_keluar_hidup + $total_keluar_mati);

            // 9. Rincian per kelas
            $rincian_per_kelas = $this->getRincianPerKelas($tanggal, $baseQueryShris);

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


    public function getMonthlyReport($tanggal_mulai, $tanggal_selesai, $ruangan = null)
    {
        $results = [];
        $current_date = Carbon::parse($tanggal_mulai);
        $end_date = Carbon::parse($tanggal_selesai);

        // Loop untuk setiap hari dalam periode
        while ($current_date <= $end_date) {
            $tanggal = $current_date->format('Y-m-d');

            $daily_data = $this->getDailyReport($tanggal, $ruangan);
            $results[] = $daily_data;

            $current_date->addDay();
        }

        return $results;
    }

    private function getDailyReport($tanggal, $ruangan = null)
    {
        $baseQuery = function ($query) use ($ruangan) {
            if ($ruangan) {
                $query->whereHas('ruangan', function ($q) use ($ruangan) {
                    $q->where('nama_ruangan', $ruangan);
                });
            }
        };

        // 1. Pasien Awal (belum keluar/pindah sebelum tanggal ini)
        $pasien_awal = Shri::where('tanggal_masuk', '<', $tanggal)
            ->whereDoesntHave('shriKeluar', function ($q) use ($tanggal) {
                $q->where('tanggal_keluar', '<', $tanggal);
            })
            ->whereDoesntHave('shriPindah', function ($q) use ($tanggal) {
                $q->where('tanggal_pindah', '<', $tanggal);
            })
            ->where($baseQuery)
            ->count();

        // 2. Pasien Masuk Langsung
        $pasien_masuk = Shri::whereDate('tanggal_masuk', $tanggal)
            ->where('asal_pasien', 'masuk-langsung')
            ->where($baseQuery)
            ->count();

        // 3. Pasien Pindahan Antar Ruangan
        $pasien_pindahan = Shri::whereDate('tanggal_masuk', $tanggal)
            ->where('asal_pasien', 'pindahan-antar-ruangan')
            ->where($baseQuery)
            ->count();


        // 4. Pasien Dipindahkan (keluar ke ruangan lain)
        $pasien_dipindahkan = ShriPindah::whereDate('tanggal_pindah', $tanggal)
            ->whereHas('shri', $baseQuery)
            ->count();

        // 5. Pasien Keluar Hidup
        $pasien_keluar_hidup = ShriKeluar::whereDate('tanggal_keluar', $tanggal)
            ->whereIn('cara_keluar', ['APS', 'APD'])
            ->whereHas('shri', $baseQuery)
            ->count();

        // 6. Pasien Keluar Mati (berdasarkan gender)
        $pasien_keluar_mati = $this->getPasienKeluarMati($tanggal, $baseQuery);

        // 7. Total Lama Dirawat
        $total_lama_dirawat = $this->getTotalLamaDirawat($tanggal, $baseQuery);

        // 8. Jumlah Hari Perawatan
        $total_keluar_mati = array_sum($pasien_keluar_mati);
        $jumlah_hari_perawatan = ($pasien_awal + $pasien_masuk + $pasien_pindahan)
            - ($pasien_dipindahkan + $pasien_keluar_hidup + $total_keluar_mati);

        // 9. Rincian per kelas
        $rincian_per_kelas = $this->getRincianPerKelas($tanggal, $ruangan);

        return [
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
    }

    private function getPasienKeluarMati($tanggal, $baseQuery)
    {
        $base = ShriKeluar::whereDate('tanggal_keluar', $tanggal)
            ->whereHas('shri', $baseQuery)
            ->with(['shri.pasien']);

        return [
            'l_kurang_48' => (clone $base)->where('cara_keluar', 'Mati ≤ 48 Jam')
                ->whereHas('shri.pasien', function ($q) {
                    $q->where('jenis_kelamin', 'Laki-Laki');
                })->count(),
            'l_lebih_48' => (clone $base)->where('cara_keluar', 'Mati ≥ 48 Jam')
                ->whereHas('shri.pasien', function ($q) {
                    $q->where('jenis_kelamin', 'Laki-Laki');
                })->count(),
            'p_kurang_48' => (clone $base)->where('cara_keluar', 'Mati ≤ 48 Jam')
                ->whereHas('shri.pasien', function ($q) {
                    $q->where('jenis_kelamin', 'Perempuan');
                })->count(),
            'p_lebih_48' => (clone $base)->where('cara_keluar', 'Mati ≥ 48 Jam')
                ->whereHas('shri.pasien', function ($q) {
                    $q->where('jenis_kelamin', 'Perempuan');
                })->count(),
        ];
    }

    private function getTotalLamaDirawat($tanggal, $baseQuery)
    {
        $lama_dirawat_keluar = ShriKeluar::whereDate('tanggal_keluar', $tanggal)
            ->whereHas('shri', $baseQuery)
            ->sum(DB::raw('CAST(lama_dirawat AS UNSIGNED)'));

        $lama_dirawat_pindah = ShriPindah::whereDate('tanggal_pindah', $tanggal)
            ->whereHas('shri', $baseQuery)
            ->sum(DB::raw('CAST(lama_dirawat AS UNSIGNED)'));

        return ($lama_dirawat_keluar ?: 0) + ($lama_dirawat_pindah ?: 0);
    }

    private function getRincianPerKelas($tanggal, $ruangan = null)
    {
        $query = Shri::with(['ruangan.kelas'])
            ->where('tanggal_masuk', '<=', $tanggal)
            ->whereDoesntHave('shriKeluar', function ($q) use ($tanggal) {
                $q->where('tanggal_keluar', '<=', $tanggal);
            })
            ->whereDoesntHave('shriPindah', function ($q) use ($tanggal) {
                $q->where('tanggal_pindah', '<=', $tanggal);
            });

        if ($ruangan) {
            $query->whereHas('ruangan', function ($q) use ($ruangan) {
                $q->where('nama_ruangan', $ruangan);
            });
        }

        $data = $query->get()
            ->groupBy('ruangan.kelas.id')
            ->map(function ($items, $kelasId) {
                $kelas = $items->first()->ruangan->kelas ?? null;
                return (object) [
                    'id' => $kelasId,
                    'nama_kelas' => $kelas->name ?? '',
                    'jumlah' => $items->count()
                ];
            });

        return collect($data)->keyBy('id');
    }


    public function getFormattedReport($tanggal_mulai, $tanggal_selesai, $kelas_perawatan_id = null)
    {
        return $this->getShriReport($tanggal_mulai, $tanggal_selesai, $kelas_perawatan_id);
    }
}
