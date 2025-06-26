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

    public function masukCreate()
    {
        return view('pages.shri.masuk.create');
    }

}
