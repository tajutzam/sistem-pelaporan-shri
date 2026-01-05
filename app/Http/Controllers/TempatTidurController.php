<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriPindah;
use Illuminate\Http\Request;

class TempatTidurController extends Controller
{
    public function index(Request $request)
    {
        $ruanganList = Ruangan::withoutGlobalScopes()
            ->select('nama_ruangan')
            ->distinct()
            ->get();

        $selectedRuangan = $request->get('ruangan', 'all'); // default "all"

        $kelasList = Kelas::withoutGlobalScopes()->orderBy('id')->get();
        $data = [];

        foreach ($kelasList as $kelas) {
            $queryRuangan = Ruangan::withoutGlobalScopes()
                ->where('kelas_ruangan_id', $kelas->id);

            if ($selectedRuangan !== 'all') {
                $queryRuangan->where('nama_ruangan', $selectedRuangan);
            }

            // total tempat tidur
            $total = $queryRuangan->sum('jumlah_tempat_tidur');

            // ===========================
            // HITUNG TERISI
            // ===========================

            // 1. Pasien DIRAWAT (belum pindah / keluar)
            $dirawatCount = Shri::withoutGlobalScopes()
                ->whereDoesntHave('keluar')
                ->whereDoesntHave('pindah')
                ->whereHas('kelasPerawatan', function ($q) use ($kelas, $selectedRuangan) {
                    $q->withoutGlobalScopes()
                        ->where('kelas_ruangan_id', $kelas->id);

                    if ($selectedRuangan !== 'all') {
                        $q->where('nama_ruangan', $selectedRuangan);
                    }
                })
                ->count();

            // 2. Pasien PINDAH (hitung hanya ruangan tujuan terakhir)
            $pindahCount = ShriPindah::withoutGlobalScopes()
                ->whereHas('ruangan', function ($q) use ($kelas, $selectedRuangan) {
                    $q->withoutGlobalScopes()
                        ->where('kelas_ruangan_id', $kelas->id);

                    if ($selectedRuangan !== 'all') {
                        $q->where('nama_ruangan', $selectedRuangan);
                    }
                })
                ->count();

            // total terisi
            $terisi = $dirawatCount + $pindahCount;

            // ===========================
            // HITUNG TERSISA
            // ===========================
            $tersedia = $total - $terisi;

            $data[$kelas->id] = [
                'name' => $kelas->name,
                'total' => $total,
                'terisi' => $terisi,
                'tersedia' => max($tersedia, 0),
            ];
        }

        return view('pages.tempat-tidur.index', compact('ruanganList', 'selectedRuangan', 'kelasList', 'data'));
    }
}
