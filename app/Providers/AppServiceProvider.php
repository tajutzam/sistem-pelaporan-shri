<?php

namespace App\Providers;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
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

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $view->with('isKepala', $user->hak_akses !== 'perawat');

            }
        });

    }
}
