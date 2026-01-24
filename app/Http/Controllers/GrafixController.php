<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Shri;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrafixController extends Controller
{
    public function kunjungan(Request $request)
    {
        // Default filter
        $tahun = $request->get('tahun', now()->year);
        $ruangan = $request->get('ruangan');

        // Ambil data kunjungan
        $dataKunjungan = $this->getKunjunganData($tahun, $ruangan);

        $ruangans = Ruangan::select('nama_ruangan')
            ->distinct()
            ->orderBy('nama_ruangan')
            ->get();
        return view('pages.grafix.kunjungan', compact('dataKunjungan', 'ruangans', 'tahun', 'ruangan'));
    }

    private function getKunjunganData($tahun, $ruangan = null)
    {
        // Pastikan memuat relasi pasien untuk mengambil data jenis_kelamin
        $query = Shri::with(['pasien', 'kelasPerawatan'])
            ->whereYear('tanggal_masuk', $tahun);

        if ($ruangan) {
            $query->whereHas('kelasPerawatan', function ($q) use ($ruangan) {
                $q->where('nama_ruangan', $ruangan);
            });
        }

        $rawData = $query->get();

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        // Inisialisasi array
        $pasienBaru = array_fill(1, 12, 0);
        $pasienLama = array_fill(1, 12, 0);
        $lakiLaki = array_fill(1, 12, 0);
        $perempuan = array_fill(1, 12, 0);
        $totalKunjungan = array_fill(1, 12, 0);

        foreach ($rawData as $data) {
            $bulan = Carbon::parse($data->tanggal_masuk)->month;
            $totalKunjungan[$bulan]++;

            $statusPasien = strtoupper($data->status_pasien ?? 'BARU');
            if ($statusPasien === 'BARU') {
                $pasienBaru[$bulan]++;
            } else {
                $pasienLama[$bulan]++;
            }


            $jk = strtoupper($data->pasien->jenis_kelamin ?? '');
            if ($jk === 'LAKI-LAKI') {
                $lakiLaki[$bulan]++;
            } elseif ($jk === 'PEREMPUAN') {
                $perempuan[$bulan]++;
            }
        }

        return [
            'labels' => array_values($months),
            'pasien_baru' => array_values($pasienBaru),
            'pasien_lama' => array_values($pasienLama),
            'laki_laki' => array_values($lakiLaki),
            'perempuan' => array_values($perempuan),
            'total_kunjungan' => array_values($totalKunjungan),
            'summary' => [
                'total_baru' => array_sum($pasienBaru),
                'total_lama' => array_sum($pasienLama),
                'total_l' => array_sum($lakiLaki),
                'total_p' => array_sum($perempuan),
                'total_keseluruhan' => array_sum($totalKunjungan),
                'bulan_tertinggi' => $this->getBulanTertinggi($totalKunjungan, $months),
                'rata_rata_bulanan' => round(array_sum($totalKunjungan) / 12, 2)
            ]
        ];
    }


    private function getBulanTertinggi($data, $months)
    {
        $maxValue = max($data);
        $maxMonth = array_search($maxValue, $data);

        return [
            'bulan' => $months[$maxMonth],
            'jumlah' => $maxValue
        ];
    }

    public function getKunjunganJson(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $ruangan = $request->get('ruangan');

        $dataKunjungan = $this->getKunjunganData($tahun, $ruangan);

        return response()->json($dataKunjungan);
    }

    // Method untuk statistik tambahan
    public function getStatistikRuangan(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $statistikRuangan = DB::table('shris')
            ->join('ruangans', 'shris.kelas_perawatan_id', '=', 'ruangans.id')
            ->join('pasiens', 'shris.pasien_id', '=', 'pasiens.id')
            ->whereYear('shris.tanggal_masuk', $tahun)
            ->select(
                'ruangans.nama_ruangan',
                DB::raw('COUNT(*) as total_kunjungan'),
                DB::raw('SUM(CASE WHEN pasiens.status_pasien = "BARU" THEN 1 ELSE 0 END) as pasien_baru'),
                DB::raw('SUM(CASE WHEN pasiens.status_pasien != "BARU" THEN 1 ELSE 0 END) as pasien_lama')
            )
            ->groupBy('ruangans.id', 'ruangans.nama_ruangan')
            ->orderBy('total_kunjungan', 'desc')
            ->get();

        return response()->json($statistikRuangan);
    }

    // Method untuk trend kunjungan (perbandingan tahun)
    public function getTrendKunjungan(Request $request)
    {
        $tahunSekarang = $request->get('tahun', now()->year);
        $tahunSebelumnya = $tahunSekarang - 1;

        $dataTahunIni = $this->getKunjunganData($tahunSekarang, $request->get('ruangan'));
        $dataTahunLalu = $this->getKunjunganData($tahunSebelumnya, $request->get('ruangan'));

        return response()->json([
            'tahun_ini' => [
                'tahun' => $tahunSekarang,
                'data' => $dataTahunIni
            ],
            'tahun_lalu' => [
                'tahun' => $tahunSebelumnya,
                'data' => $dataTahunLalu
            ]
        ]);
    }

    public function barberJohnson(Request $request)
    {
        $ruangans = Ruangan::select('nama_ruangan')
            ->distinct()
            ->orderBy('nama_ruangan')
            ->get();



        return view('pages.grafix.barber-jhonson', compact('ruangans'));
    }

    // api/barber-jhonson-data
    public function getData(Request $request)
    {
        $request->validate([
            'ruangan' => 'nullable|string|exists:ruangans,nama_ruangan',
            'tahun' => 'required|numeric',
            'bulan' => 'nullable|numeric|between:1,12'
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $namaRuangan = $request->ruangan;

        if ($namaRuangan) {
            $ruanganIds = Ruangan::where('nama_ruangan', $namaRuangan)->pluck('id');
        } else {
            $ruanganIds = Ruangan::pluck('id');
        }

        $indikator = $this->calculateIndikator($ruanganIds, $tahun, $bulan);

        return response()->json([
            'indikator' => $indikator,
            'ruangan_nama' => $namaRuangan ?: 'Seluruh Ruangan',
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);
    }


    private function calculateIndikator($ruanganIds, $tahun, $bulan = null)
    {
        if ($bulan) {
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->endOfDay();
        } else {
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
        }

        // Batasi sampai hari ini jika periode mencakup masa depan
        if ($endDate->greaterThan(Carbon::today()->endOfDay())) {
            $endDate = Carbon::today()->endOfDay();
        }

        $startDateString = $startDate->toDateTimeString();
        $endDateString = $endDate->toDateTimeString();
        $endDateOnly = $endDate->format('Y-m-d');

        // 2. Hitung jumlah hari dalam periode (t)
        $periodeDays = $startDate->diffInDays($endDate->copy()->startOfDay()) + 1;

        // 3. Ambil Total Tempat Tidur
        $jumlahTempat = Ruangan::whereIn('id', $ruanganIds)->sum('jumlah_tempat_tidur');
        if ($jumlahTempat <= 0)
            $jumlahTempat = 1;

        // 4. Query Statistik dengan Raw SQL (Sesuai rumus getComparisonChartData)
        $stats = \DB::table('shris as s')
            ->leftJoin('shri_keluar as sk', 's.id', '=', 'sk.shri_id')
            ->leftJoin('shri_pindah as sp', 's.id', '=', 'sp.shri_id')
            ->whereIn('s.kelas_perawatan_id', $ruanganIds)
            ->where(function ($q) use ($startDateString, $endDateString) {
                $q->whereBetween('s.tanggal_masuk', [$startDateString, $endDateString])
                    ->orWhere(function ($sub) use ($startDateString, $endDateString) {
                        $sub->where('s.tanggal_masuk', '<=', $endDateString)
                            ->where(function ($endCheck) use ($startDateString) {
                                $endCheck->whereNull('sk.tanggal_keluar')
                                    ->orWhere('sk.tanggal_keluar', '>=', $startDateString);
                            });
                    });
            })
            ->select([
                // Hari Perawatan (HP)
                \DB::raw('COALESCE(SUM(
                CASE
                    WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL 
                        THEN DATEDIFF(LEAST(sk.tanggal_keluar, "' . $endDateOnly . '"), GREATEST(s.tanggal_masuk, "' . $startDateString . '")) + 1
                    WHEN s.status = "pindah" AND sp.tanggal_pindah IS NOT NULL 
                        THEN DATEDIFF(LEAST(sp.tanggal_pindah, "' . $endDateOnly . '"), GREATEST(s.tanggal_masuk, "' . $startDateString . '")) + 1
                    WHEN s.status = "masuk" 
                        THEN DATEDIFF("' . $endDateOnly . '", GREATEST(s.tanggal_masuk, "' . $startDateString . '")) + 1
                    ELSE 0
                END
            ), 0) as total_hp'),

                // Lama Dirawat (LD) - Hanya untuk yang keluar di periode ini
                \DB::raw('COALESCE(SUM(
                CASE
                    WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL AND sk.tanggal_keluar BETWEEN "' . $startDateString . '" AND "' . $endDateString . '"
                        THEN CAST(REPLACE(sk.lama_dirawat, " hari", "") AS UNSIGNED)
                    ELSE 0
                END
            ), 0) as total_ld'),

                \DB::raw('COUNT(CASE WHEN s.status = "keluar" AND sk.tanggal_keluar BETWEEN "' . $startDateString . '" AND "' . $endDateString . '" THEN 1 END) as total_keluar')
            ])->first();

        $hp = (float) $stats->total_hp;
        $ld = (float) $stats->total_ld;
        $keluar = (int) $stats->total_keluar;

        $bor = ($jumlahTempat * $periodeDays) > 0 ? ($hp / ($jumlahTempat * $periodeDays)) * 100 : 0;
        $avlos = $keluar > 0 ? $ld / $keluar : 0;
        $bto = $jumlahTempat > 0 ? $keluar / $jumlahTempat : 0;
        $toi = $keluar > 0 ? (($jumlahTempat * $periodeDays) - $hp) / $keluar : 0;

        return [
            'bor' => round($bor, 2),
            'avlos' => round($avlos, 2),
            'bto' => round($bto, 2),
            'toi' => round($toi, 2),
            'total_pasien_keluar' => $keluar,
            'total_hari_rawat' => $hp,
            'total_lama_dirawat' => $ld,
            'jumlah_tempat' => $jumlahTempat,
            'periode_hari' => $periodeDays
        ];
    }

    public function barberPrint(Request $request)
    {
        $data = $request->all();
        $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $data['periode'] = $request->bulan ? $monthNames[(int) $request->bulan] . ' ' . $request->tahun : $request->tahun;

        return view('pages.grafix.babrber_johnson_print', $data);
    }


    public function kunjunganPrint(Request $request)
    {
        // Menerima data dari form dinamis JS
        return view('pages.grafix.print_kunjungan', $request->all());
    }


}
