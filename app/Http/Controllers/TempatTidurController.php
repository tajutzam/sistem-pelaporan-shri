<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TempatTidurController extends Controller
{

    public function index()
    {
        return view("pages.tempat-tidur.index");
    }

}
