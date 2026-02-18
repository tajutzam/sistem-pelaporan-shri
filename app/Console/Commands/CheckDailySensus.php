<?php

namespace App\Console\Commands;

use App\Models\Shri;
use App\Models\ShriKeluar;
use App\Models\ShriPindah;
use App\Models\UserSensus;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckDailySensus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-daily-sensus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command untuk melakukan pengecekan apakah user sudah mengisi daftar sensus atau tidak';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $adaShris = Shri::whereDate('created_at', $today)->exists();
        $adaPindah = ShriPindah::whereDate('created_at', $today)->exists();
        $adaKeluar = ShriKeluar::whereDate('created_at', $today)->exists();

        $sudahIsi = $adaShris || $adaPindah || $adaKeluar;

        $sudahAdaLog = UserSensus::whereDate('tanggal', $today)->exists();

        if (!$sudahAdaLog) {
            UserSensus::create([
                'status' => $sudahIsi,
                'tanggal' => $today
            ]);

            Log::info("Pengecekan sensus harian selesai pada " . now() . " | Status: " . ($sudahIsi ? 'Sudah isi' : 'Belum isi'));
        } else {
            Log::info("Pengecekan sensus untuk hari {$today->toDateString()} sudah pernah dilakukan, tidak dibuat ulang.");
        }
    }
}
