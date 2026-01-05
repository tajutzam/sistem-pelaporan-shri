<?php

namespace App\Http\Controllers;

use App\Models\ApprovedDay;
use App\Models\NotificationLog;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriKeluar;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //


    public function index()
    {
        $pasienMasuk = $this->getPasienMasuk();
        $pasienKeluar = $this->getPasienKeluar();
        $tempatTidurTersedia = $this->getTempatTidurTersedia();
        $chartData = $this->getComparisonChartData();

        return view("pages.dashboard.index", compact('pasienMasuk', 'pasienKeluar', 'tempatTidurTersedia', 'chartData'));
    }


    private function getPasienMasuk()
    {
        return Shri::with([
            'kelasPerawatan.ruangan' => function ($q) {
                $q->withoutGlobalScopes();
            }
        ])
            ->whereDay('tanggal_masuk', Carbon::now()->day)
            ->count();
    }


    private function getPasienKeluar()
    {
        return Shri::whereHas('shriKeluar', function ($query) {
            $query->whereDay('tanggal_keluar', Carbon::now()->day)
                ->with([
                    'ruangan' => function ($q) {
                        $q->withoutGlobalScopes();
                    }
                ]);
        })->count();
    }


    private function getTempatTidurTersedia($ruanganId = null)
    {
        $totalTT = Ruangan::withoutGlobalScopes()
            ->when($ruanganId, function ($q) use ($ruanganId) {
                $q->where('id', $ruanganId);
            })
            ->sum('jumlah_tempat_tidur');


        $terisi = Shri::withoutGlobalScopes()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('shris')
                    ->groupBy('pasien_id');
            })
            ->where('status', '!=', 'keluar')
            ->when($ruanganId, function ($q) use ($ruanganId) {
                $q->where('ruang_perawatan_id', $ruanganId);
            })
            ->count();

        return max($totalTT - $terisi, 0);
    }

    public function notificationToday(Request $request)
    {
        $notifications = NotificationLog::whereDate('created_at', $request->query('date'))->get();
        return response()->json(
            [
                'data' => $notifications,
                'status' => true
            ]
        );
    }

    private function getComparisonChartData($year = null, $month = null)
    {
        $year = $year ?: Carbon::now()->year;
        $month = $month ?: Carbon::now()->month;

        // ambil distinct nama_ruangan
        $ruangans = Ruangan::select('nama_ruangan')
            ->groupBy('nama_ruangan')
            ->get();

        $chartData = [
            'labels' => [],
            'bor' => [],
            'avlos' => [],
            'bto' => [],
            'toi' => []
        ];

        foreach ($ruangans as $ruangan) {
            $chartData['labels'][] = $ruangan->nama_ruangan;

            // ambil semua id ruangan dengan nama sama
            $ruanganIds = Ruangan::where('nama_ruangan', $ruangan->nama_ruangan)->pluck('id');

            $indicators = $this->calculateGroupedIndicators($ruanganIds, $year, $month);

            $chartData['bor'][] = $indicators['bor'];
            $chartData['avlos'][] = $indicators['avlos'];
            $chartData['bto'][] = $indicators['bto'];
            $chartData['toi'][] = $indicators['toi'];
        }

        return $chartData;
    }

    private function calculateGroupedIndicators($ruanganIds, $tahun, $bulan)
    {
        $shriData = Shri::with(['shriKeluar', 'pindah'])
            ->whereIn('kelas_perawatan_id', $ruanganIds)
            ->whereYear('tanggal_masuk', $tahun)
            ->whereMonth('tanggal_masuk', $bulan)
            ->get();

        $jumlahTempat = Ruangan::whereIn('id', $ruanganIds)->sum('jumlah_tempat_tidur');
        $periodeDays = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        $totalPasienKeluar = 0;
        $totalHariRawat = 0;

        foreach ($shriData as $shri) {
            if ($shri->shriKeluar) {
                $totalPasienKeluar++;

                $masuk = Carbon::parse($shri->tanggal_masuk);
                $keluar = Carbon::parse($shri->shriKeluar->tanggal_keluar);
                $lamaRawat = $keluar->diffInDays($masuk) + 1;

                $totalHariRawat += $lamaRawat;
            }
        }

        $bor = $jumlahTempat > 0 && $periodeDays > 0
            ? ($totalHariRawat / ($jumlahTempat * $periodeDays)) * 100
            : 0;

        $avlos = $totalPasienKeluar > 0
            ? $totalHariRawat / $totalPasienKeluar
            : 0;

        $bto = $jumlahTempat > 0
            ? $totalPasienKeluar / $jumlahTempat
            : 0;

        $toi = $totalPasienKeluar > 0
            ? (($jumlahTempat * $periodeDays) - $totalHariRawat) / $totalPasienKeluar
            : 0;

        return [
            'bor' => round($bor, 2),
            'avlos' => round($avlos, 2),
            'bto' => round($bto, 2),
            'toi' => round($toi, 2)
        ];
    }


    public function getChartData(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $chartData = $this->getComparisonChartData($year, $month);

        return response()->json($chartData);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id): JsonResponse
    {
        try {
            $notification = NotificationLog::findOrFail($id);
            $notification->update(['status_read' => 1]);

            return response()->json([
                'message' => 'Notification marked as read',
                'status' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating notification',
                'status' => false
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));

            NotificationLog::whereDate('created_at', $date)
                ->where(function ($query) {
                    $query->where('status_read', 0)
                        ->orWhereNull('status_read');
                })
                ->update(['status_read' => 1]);

            return response()->json([
                'message' => 'All notifications marked as read',
                'status' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating notifications',
                'status' => false
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $count = NotificationLog::where(function ($query) {
                $query->where('status_read', 0)
                    ->orWhereNull('status_read');
            })
                ->whereDate('created_at', now()->format('Y-m-d'))
                ->count();

            return response()->json([
                'count' => $count,
                'status' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching notification count',
                'status' => false
            ], 500);
        }
    }

    public function checkIsVerified(Request $request)
    {
        $jenis_laporan = $request->query('jenis');

        $today = Carbon::today();

        $approved = ApprovedDay::whereDate('created_at', $today)->where('jenis_laporan', $jenis_laporan)->first();
        return response()->json(
            [
                'data' => $approved
            ]
        );

    }




}
