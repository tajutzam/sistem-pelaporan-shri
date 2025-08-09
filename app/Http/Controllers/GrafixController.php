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

        $ruangans = Ruangan::all();

        return view('pages.grafix.kunjungan', compact('dataKunjungan', 'ruangans', 'tahun', 'ruangan'));
    }

    private function getKunjunganData($tahun, $ruangan = null)
    {
        $query = Shri::whereYear('tanggal_masuk', $tahun)
            ->with(['pasien', 'kelasPerawatan']);

        if ($ruangan) {
            $query->whereHas('kelasPerawatan', function ($q) use ($ruangan) {
                $q->where('nama_ruangan', 'LIKE', '%' . $ruangan . '%');
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

        $pasienBaru = array_fill(1, 12, 0);
        $pasienLama = array_fill(1, 12, 0);
        $totalKunjungan = array_fill(1, 12, 0);

        foreach ($rawData as $data) {
            $bulan = Carbon::parse($data->tanggal_masuk)->month;
            $statusPasien = $data->pasien->status_pasien ?? 'BARU';

            $totalKunjungan[$bulan]++;

            if (strtoupper($statusPasien) === 'BARU') {
                $pasienBaru[$bulan]++;
            } else {
                $pasienLama[$bulan]++;
            }
        }

        return [
            'labels' => array_values($months),
            'pasien_baru' => array_values($pasienBaru),
            'pasien_lama' => array_values($pasienLama),
            'total_kunjungan' => array_values($totalKunjungan),
            'summary' => [
                'total_baru' => array_sum($pasienBaru),
                'total_lama' => array_sum($pasienLama),
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
        return view('pages.grafix.barber-jhonson');
    }

}
