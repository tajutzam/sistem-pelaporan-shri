<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriKeluar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //


    public function index()
    {
        $pasienMasuk = Shri::where('status', "masuk")->count();
        $pasienKeluar = ShriKeluar::whereNotNull('tanggal_keluar')->count();
        $tempatTidurTersedia = Ruangan::sum('jumlah_tempat_tidur');
        return view("pages.dashboard.index", compact('pasienMasuk', 'pasienKeluar', 'tempatTidurTersedia'));
    }

}
