<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        $namaKepala = User::where('hak_akses', 'kepala')->first()->name;
        $pelaporan = User::where('hak_akses', 'pelaporan')->first()->name;

        Paginator::useTailwind();

        View::share('namaKepala', $namaKepala);
        View::share('pelaporan', $pelaporan);

    }
}
