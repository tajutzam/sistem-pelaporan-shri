<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('checkRole')) {
    function checkRole($role)
    {
        return Auth::check() && Auth::user()->hak_akses === $role;
    }
}
