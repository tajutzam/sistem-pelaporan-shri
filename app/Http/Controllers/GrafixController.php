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

            $statusPasien = strtoupper($data->pasien->status_pasien ?? 'BARU');
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
            // Diubah menjadi nullable untuk mendukung "Semua Ruangan"
            'ruangan' => 'nullable|string|exists:ruangans,nama_ruangan',
            'tahun' => 'required|numeric',
            'bulan' => 'nullable|numeric|between:1,12'
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $namaRuangan = $request->ruangan;

        // Logic: Jika ruangan diisi, ambil ID ruangan tersebut saja. 
        // Jika kosong, ambil SEMUA ID ruangan yang ada.
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
        // 1. Ambil Data SHRI berdasarkan kumpulan ID ruangan (bisa 1 ruangan atau semua)
        $query = Shri::with(['shriKeluar', 'pindah'])
            ->whereIn('kelas_perawatan_id', $ruanganIds)
            ->whereYear('tanggal_masuk', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_masuk', $bulan);
        }

        $shriData = $query->get();

        // 2. Jumlah bed untuk semua ID yang difilter
        $jumlahTempat = Ruangan::whereIn('id', $ruanganIds)->sum('jumlah_tempat_tidur');

        // Default sAFETY jika master data kosong
        if ($jumlahTempat <= 0) {
            $jumlahTempat = 1;
        }

        // 3. Hitung periode hari
        if ($bulan) {
            $periodeDays = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        } else {
            $periodeDays = Carbon::createFromDate($tahun, 1, 1)->isLeapYear() ? 366 : 365;
        }

        $totalPasienKeluar = 0;
        $totalHariRawat = 0;
        $totalPasienMeninggal = 0;

        foreach ($shriData as $shri) {
            if ($shri->shriKeluar) {
                $totalPasienKeluar++;
                $masuk = Carbon::parse($shri->tanggal_masuk);
                $keluar = Carbon::parse($shri->shriKeluar->tanggal_keluar);

                // Standar RS: Hari yang sama dihitung 1
                $diff = $keluar->diffInDays($masuk);
                $lamaRawat = $diff <= 0 ? 1 : $diff;

                $totalHariRawat += $lamaRawat;

                // Pengecekan cara keluar (Meninggal)
                $caraKeluar = strtolower($shri->shriKeluar->cara_keluar);
                if (str_contains($caraKeluar, 'meninggal') || str_contains($caraKeluar, 'mati')) {
                    $totalPasienMeninggal++;
                }
            }
        }

        // 4. Kalkulasi Indikator Barber Johnson
        // BOR = (Hari Perawatan / (Bed * Periode)) * 100
        $bor = ($jumlahTempat * $periodeDays) > 0
            ? ($totalHariRawat / ($jumlahTempat * $periodeDays)) * 100
            : 0;

        // AvLOS = Hari Perawatan / Pasien Keluar
        $avlos = $totalPasienKeluar > 0
            ? $totalHariRawat / $totalPasienKeluar
            : 0;

        // BTO = Pasien Keluar / Bed
        $bto = $jumlahTempat > 0
            ? $totalPasienKeluar / $jumlahTempat
            : 0;

        // TOI = ((Bed * Periode) - Hari Perawatan) / Pasien Keluar
        $toi = $totalPasienKeluar > 0
            ? (($jumlahTempat * $periodeDays) - $totalHariRawat) / $totalPasienKeluar
            : 0;

        return [
            'bor' => round($bor, 2),
            'avlos' => round($avlos, 2),
            'bto' => round($bto, 2),
            'toi' => round($toi, 2),
            'total_pasien_keluar' => $totalPasienKeluar,
            'total_hari_rawat' => $totalHariRawat,
            'jumlah_tempat' => $jumlahTempat,
            'periode_hari' => $periodeDays
        ];
    }


}
