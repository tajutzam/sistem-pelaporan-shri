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

        $selectedRuangan = $request->get('ruangan', 'all');
        $kelasList = Kelas::withoutGlobalScopes()->orderBy('id')->get();
        $data = [];

        foreach ($kelasList as $kelas) {
            $queryRuangan = Ruangan::withoutGlobalScopes()
                ->where('kelas_ruangan_id', $kelas->id);

            if ($selectedRuangan !== 'all') {
                $queryRuangan->where('nama_ruangan', $selectedRuangan);
            }

            $total = $queryRuangan->sum('jumlah_tempat_tidur');

         
            $terisi = Shri::withoutGlobalScopes()
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
