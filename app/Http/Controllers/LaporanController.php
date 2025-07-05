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

}
