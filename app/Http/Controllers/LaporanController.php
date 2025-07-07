<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    //

    public function laporanKunjungan()
    {
        return view("pages.laporan.kunjungan");
    }

    public function rekapitulasi()
    {
        return view('pages.laporan.rekapitulasi');
    }

    public function indikatorPelayanan(){
        return view('pages.laporan.indikator_pelayanan');
    }

    public function tenDiagnosaPenyakit(){
        return view('pages.laporan.10_besar_penyakit');
    }

}
