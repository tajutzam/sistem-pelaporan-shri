<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\UserSensus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUserSensus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-sensus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Cek apakah UserSensus ada untuk hari ini
        $sensus = UserSensus::whereDate('tanggal', $today)->first();

        $logSudahAda = NotificationLog::where('type', 'usersensus_check')
            ->whereDate('created_at', $today)
            ->exists();

        if (!$logSudahAda) {
            NotificationLog::create([
                'type' => 'usersensus_check',
                'status' => $sensus->status,
                'description' => $sensus->status ? 'User Sudah Mengisi sensus pada hari ini' : 'User belum mengisi sensus pada tanggal ' . $today
            ]);

            Log::info("Cek UserSensus {$today->toDateString()} => " . ($sensus->status ? 'Sudah diisi' : 'Belum diisi'));
        } else {
            Log::info("NotificationLog untuk 'usersensus_check' sudah ada di {$today->toDateString()}, tidak dibuat ulang.");
        }
    }
}
