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

    private function calculateGroupedIndicators($ruanganIds, $year, $month)
    {
        // 1. Tentukan Range Tanggal (Sama persis dengan logika Laporan)
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

        // Batasi sampai hari ini jika bulan berjalan
        if ($endDate->greaterThan(Carbon::today()->endOfDay())) {
            $endDate = Carbon::today()->endOfDay();
        }

        $endDateString = $endDate->format('Y-m-d');

        // Hitung jumlah hari dalam periode (t)
        // startOfDay() penting agar tanggal sama dihitung 1 hari
        $jumlahPeriode = $startDate->diffInDays($endDate->copy()->startOfDay()) + 1;

        // 2. Ambil Total Tempat Tidur berdasarkan ID
        $jumlahTempatTidur = Ruangan::whereIn('id', $ruanganIds)->sum('jumlah_tempat_tidur');

        // 3. Query Statistik (Copy logic Raw SQL dari getLaporanIndikator)
        // Bedanya: Kita filter pakai whereIn('kelas_perawatan_id')
        $stats = \DB::table('shris as s')
            ->leftJoin('shri_keluar as sk', 's.id', '=', 'sk.shri_id')
            ->leftJoin('shri_pindah as sp', 's.id', '=', 'sp.shri_id')
            ->whereIn('s.kelas_perawatan_id', $ruanganIds) // Filter berdasarkan array ID
            ->where(function ($q) use ($startDate, $endDate) {
                // Logika irisan tanggal: Masuk dalam periode ATAU masuk sebelumnya & belum keluar sebelum start
                $q->whereBetween('s.tanggal_masuk', [$startDate, $endDate])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                    $sub->where('s.tanggal_masuk', '<=', $endDate)
                        ->where(function ($endCheck) use ($startDate) {
                            $endCheck->whereNull('sk.tanggal_keluar') // Belum keluar
                                ->orWhere('sk.tanggal_keluar', '>=', $startDate); // Atau keluar di dalam periode
                        });
                });
            })
            ->select([
                // Rumus Hari Perawatan (HP) - Sesuai standar Depkes
                \DB::raw('COALESCE(SUM(
                CASE
                    WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL 
                        THEN DATEDIFF(LEAST(sk.tanggal_keluar, "' . $endDateString . '"), GREATEST(s.tanggal_masuk, "' . $startDate . '")) + 1
                    WHEN s.status = "pindah" AND sp.tanggal_pindah IS NOT NULL 
                        THEN DATEDIFF(LEAST(sp.tanggal_pindah, "' . $endDateString . '"), GREATEST(s.tanggal_masuk, "' . $startDate . '")) + 1
                    WHEN s.status = "masuk" 
                        THEN DATEDIFF("' . $endDateString . '", GREATEST(s.tanggal_masuk, "' . $startDate . '")) + 1
                    ELSE 0
                END
            ), 0) as total_hp'),

                // Rumus Lama Dirawat (LD) - Hanya untuk pasien pulang
                \DB::raw('COALESCE(SUM(
                CASE
                    WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL AND sk.tanggal_keluar BETWEEN "' . $startDate . '" AND "' . $endDate . '"
                        THEN CAST(REPLACE(sk.lama_dirawat, " hari", "") AS UNSIGNED)
                    ELSE 0
                END
            ), 0) as total_ld'),

                \DB::raw('COUNT(CASE WHEN s.status = "keluar" AND sk.tanggal_keluar BETWEEN "' . $startDate . '" AND "' . $endDate . '" THEN 1 END) as total_keluar')
            ])->first();

        $hp = (float) $stats->total_hp;
        $ld = (float) $stats->total_ld;
        $keluar = (int) $stats->total_keluar; 

        $bor = ($jumlahTempatTidur * $jumlahPeriode) > 0
            ? ($hp / ($jumlahTempatTidur * $jumlahPeriode)) * 100
            : 0;

        $avlos = $keluar > 0 ? $ld / $keluar : 0;

        $bto = $jumlahTempatTidur > 0 ? $keluar / $jumlahTempatTidur : 0;

        $toi = $keluar > 0 ? (($jumlahTempatTidur * $jumlahPeriode) - $hp) / $keluar : 0;

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
