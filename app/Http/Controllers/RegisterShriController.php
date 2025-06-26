<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterShriController extends Controller
{
    //

    public function masukView()
    {
        return view("pages.shri.masuk.index");
    }


    public function pindahView()
    {
        return view("pages.shri.pindah.index");
    }


    public function keluarView()
    {
        return view("pages.shri.keluar.index");
    }

    public function pindahCreate()
    {
        return view('pages.shri.pindah.create');
    }

    public function masukCreate()
    {
        return view('pages.shri.masuk.create');
    }

}
