<?php

namespace App\Http\Controllers;

use App\Http\Services\ShriReportService;
use App\Models\ApprovedDay;
use App\Models\Diagnosa;
use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriKeluar;
use App\Models\ShriPindah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    protected $shriReportService;

    public function __construct(ShriReportService $shriReportService)
    {
        $this->shriReportService = $shriReportService;
    }
    public function laporanKunjungan(Request $request)
    {
        // Ambil data menggunakan helper internal
        $data = $this->getKunjunganData($request);

        $laporanKunjungans = $data['laporanKunjungans'];
        $ruangans = $data['ruangans'];

        return view("pages.laporan.kunjungan", compact('laporanKunjungans', 'ruangans'));
    }

    public function cetakLaporanKunjungan(Request $request)
    {
        $data = $this->getKunjunganData($request);

        $pdf = Pdf::loadView('pages.laporan.pdf-kunjungan', [
            'laporanKunjungans' => $data['laporanKunjungans'],
            'tanggal_mulai' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'ruangan_filter' => $request->ruangan,
            'title' => 'Laporan Kunjungan rawat inap',
            'date' => Carbon::now()
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Kunjungan_Pasien.pdf');
    }


    /**
     * Logic internal untuk memproses data kunjungan
     */
    private function getKunjunganData(Request $request)
    {
        $userRuanganIds = auth()->user()->perawatRuangans()->pluck('ruangan_id');

        $query = Shri::with([
            'pasien',
            'kelasPerawatan.kelas',
            'jenisPenjaminan',
            'dpjp',
            'pindah.ruangan.kelas',
            'shriKeluar.diagnosa',
        ]);

        if (auth()->user()->hak_akses == 'perawat') {
            $query->whereIn('kelas_perawatan_id', $userRuanganIds);
        }

        if ($request->filled('ruangan')) {
            $query->whereHas('kelasPerawatan', function ($q) use ($request) {
                $q->where('nama_ruangan', $request->ruangan);
            });
        }

        if ($request->filled('tanggal_awal')) {
            $query->where('tanggal_masuk', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_masuk', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pasien', function ($sub) use ($search) {
                    $sub->where('no_rekam_medis', 'like', "%$search%")
                        ->orWhere('nama_pasien', 'like', "%$search%")
                        ->orWhere('jenis_kelamin', 'like', "%$search%");
                });
            });
        }

        $result = $query->get()->map(function ($item) {
            $ruanganAwal = $item->kelasPerawatan;
            $pindah = $item->pindah;
            $keluar = $item->shriKeluar;

            $ruanganAkhir = $pindah && $pindah->ruangan ? $pindah->ruangan : $ruanganAwal;

            $status = "dirawat";
            $tanggalKeluar = null;
            $diagnosa = "-";
            $tanggalPindah = '-';

            if ($pindah && !$keluar) {
                $status = "pindah";
                $tanggalPindah = $pindah->tanggal_pindah;
            }
            if ($keluar) {
                $status = "keluar";
                $tanggalKeluar = $keluar->tanggal_keluar;
                $diagnosa = $keluar->diagnosa->diagnosa ?? "-";
            }

            $ruanganDisplay = $ruanganAwal->nama_ruangan;
            
            if ($keluar) {
                $ruanganDisplay = $ruanganAkhir->nama_ruangan;
            }

            return [
                'no_rm' => $item->pasien->no_rekam_medis,
                'nama_pasien' => $item->pasien->nama_pasien,
                'jenis_kelamin' => $item->pasien->jenis_kelamin,
                'ruangan' => $ruanganDisplay,
                'kelas' => $ruanganAkhir->kelas->name ?? '-',
                'penjaminan' => $item->jenisPenjaminan->jenis_penjaminan ?? '-',
                'dpjp' => $item->dpjp->nama_lengkap ?? '-',
                'tanggal_masuk' => $item->tanggal_masuk,
                'tanggal_keluar' => $tanggalKeluar,
                'tanggal_pindah' => $tanggalPindah,
                'status' => $status,
                'diagnosa' => $diagnosa,
                'status_pasien' => $item->status_pasien,
                'asal_pasien' => $item->asal_pasien
            ];
        });

        $laporanKunjungans = $result->sortByDesc(fn($i) => $i['tanggal_keluar'] ?? $i['tanggal_masuk'])->values();

        $ruangans = DB::table('ruangans')
            ->select('nama_ruangan')
            ->when($userRuanganIds->isNotEmpty(), fn($q) => $q->whereIn('id', $userRuanganIds))
            ->groupBy('nama_ruangan')
            ->orderBy('nama_ruangan')
            ->get();

        return [
            'laporanKunjungans' => $laporanKunjungans,
            'ruangans' => $ruangans
        ];
    }


    public function rekapitulasi(Request $request)
    {
        $ruangan = $request->input('ruangan');
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode');
        $search = $request->input('search');

        // Default periode = bulan sekarang
        if (!$periode) {
            $periode = 'bulan_' . str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        }

        // Tentukan Range Tanggal
        if ($periode === 'bulan_semua_bulan') {
            $tanggal_mulai = Carbon::create($tahun, 1, 1)->format('Y-m-d');
            $tanggal_selesai = Carbon::create($tahun, 12, 31)->format('Y-m-d');
        } else {
            $bulan = str_replace('bulan_', '', $periode);
            if (!is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
                $bulan = now()->month;
            }
            $tanggal_mulai = Carbon::create($tahun, (int) $bulan, 1)->format('Y-m-d');
            $tanggal_selesai = Carbon::create($tahun, (int) $bulan, 1)->endOfMonth()->format('Y-m-d');
        }

        // Ambil data dasar (Harian)
        $dataRaw = $this->shriReportService->getMonthlyReport($tanggal_mulai, $tanggal_selesai, $ruangan);

        // Ambil Rincian Kelas untuk Header
        $kelas_ruangan = Kelas::select('id', 'name as nama_kelas')->orderBy('name')->get();

        // LOGIKA PENYEDERHANAAN (Jika Semua Bulan dipilih)
        if ($periode === 'bulan_semua_bulan') {
            $data = collect($dataRaw)->groupBy(function ($item) {
                return Carbon::parse($item['tanggal'])->format('Y-m');
            })->map(function ($rows, $key) use ($kelas_ruangan) {
                // Urutkan berdasarkan tanggal untuk mendapatkan hari pertama
                $sortedRows = $rows->sortBy('tanggal');
                $firstDay = $sortedRows->first();

                return [
                    'tanggal' => Carbon::parse($key . '-01')->translatedFormat('F'), // Nama Bulan
                    'is_bulanan' => true,
                    'pasien_awal' => $firstDay['pasien_awal'], // Pasien awal diambil dari tanggal 1
                    'pasien_masuk' => $rows->sum('pasien_masuk'),
                    'pasien_pindahan' => $rows->sum('pasien_pindahan'),
                    'pasien_dipindahkan' => $rows->sum('pasien_dipindahkan'),
                    'pasien_keluar_hidup' => $rows->sum('pasien_keluar_hidup'),
                    'pasien_keluar_mati_l_kurang_48' => $rows->sum('pasien_keluar_mati_l_kurang_48'),
                    'pasien_keluar_mati_l_lebih_48' => $rows->sum('pasien_keluar_mati_l_lebih_48'),
                    'pasien_keluar_mati_p_kurang_48' => $rows->sum('pasien_keluar_mati_p_kurang_48'),
                    'pasien_keluar_mati_p_lebih_48' => $rows->sum('pasien_keluar_mati_p_lebih_48'),
                    'total_lama_dirawat' => $rows->sum('total_lama_dirawat'),
                    'jumlah_hari_perawatan' => $rows->sum('jumlah_hari_perawatan'),
                    'rincian_per_kelas' => $kelas_ruangan->mapWithKeys(function ($kelas) use ($rows) {
                        return [
                            $kelas->id => (object) [
                                'jumlah' => $rows->sum(fn($r) => $r['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0)
                            ]
                        ];
                    })
                ];
            })->values()->toArray();
        } else {
            $data = $dataRaw;
        }

        // Filter search (opsional jika masih dibutuhkan)
        if ($search) {
            $data = collect($data)->filter(function ($row) use ($search) {
                return str_contains(strtolower($row['tanggal']), strtolower($search));
            })->values()->toArray();
        }

        $ruangans = Ruangan::select('nama_ruangan')->distinct()->orderBy('nama_ruangan')->get();

        return view('pages.laporan.rekapitulasi', compact(
            'data',
            'ruangan',
            'ruangans',
            'tahun',
            'periode',
            'search',
            'kelas_ruangan',
            'tanggal_mulai',
            'tanggal_selesai'
        ));
    }





    public function exportRekapitulasi(Request $request)
    {
        $today = Carbon::today();
        $approved = ApprovedDay::whereDate('created_at', $today)
            ->where('jenis_laporan', 'rekapitulasi')
            ->first();

        if (!$approved) {
            return back()->withErrors('Silahkan menunggu kepala rumah sakit untuk memverifikasi');
        }

        $ruangan = $request->input('ruangan');
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode');

        if (!$periode) {
            $periode = 'bulan_' . str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        }

        // Tentukan Range Tanggal
        if ($periode === 'bulan_semua_bulan') {
            $tanggal_mulai = Carbon::create($tahun, 1, 1)->format('Y-m-d');
            $tanggal_selesai = Carbon::create($tahun, 12, 31)->format('Y-m-d');
        } else {
            $bulan = str_replace('bulan_', '', $periode);
            $tanggal_mulai = Carbon::create($tahun, (int) $bulan, 1)->format('Y-m-d');
            $tanggal_selesai = Carbon::create($tahun, (int) $bulan, 1)->endOfMonth()->format('Y-m-d');
        }

        $dataRaw = $this->shriReportService->getMonthlyReport($tanggal_mulai, $tanggal_selesai, $ruangan);

        $kelas_ruangan = Kelas::select('id', 'name as nama_kelas')->orderBy('name')->get();

        if ($periode === 'bulan_semua_bulan') {
            $data = collect($dataRaw)->groupBy(function ($item) {
                return Carbon::parse($item['tanggal'])->format('Y-m');
            })->map(function ($rows, $key) use ($kelas_ruangan) {
                $sortedRows = $rows->sortBy('tanggal');
                $firstDay = $sortedRows->first();

                return [
                    'tanggal' => Carbon::parse($key . '-01')->translatedFormat('F'),
                    'is_bulanan' => true,
                    'pasien_awal' => $firstDay['pasien_awal'],
                    'pasien_masuk' => $rows->sum('pasien_masuk'),
                    'pasien_pindahan' => $rows->sum('pasien_pindahan'),
                    'pasien_dipindahkan' => $rows->sum('pasien_dipindahkan'),
                    'pasien_keluar_hidup' => $rows->sum('pasien_keluar_hidup'),
                    'pasien_keluar_mati_l_kurang_48' => $rows->sum('pasien_keluar_mati_l_kurang_48'),
                    'pasien_keluar_mati_l_lebih_48' => $rows->sum('pasien_keluar_mati_l_lebih_48'),
                    'pasien_keluar_mati_p_kurang_48' => $rows->sum('pasien_keluar_mati_p_kurang_48'),
                    'pasien_keluar_mati_p_lebih_48' => $rows->sum('pasien_keluar_mati_p_lebih_48'),
                    'total_lama_dirawat' => $rows->sum('total_lama_dirawat'),
                    'jumlah_hari_perawatan' => $rows->sum('jumlah_hari_perawatan'),
                    'rincian_per_kelas' => $kelas_ruangan->mapWithKeys(function ($kelas) use ($rows) {
                        return [
                            $kelas->id => (object) [
                                'jumlah' => $rows->sum(fn($r) => $r['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0)
                            ]
                        ];
                    })
                ];
            })->values()->toArray();
        } else {
            $data = $dataRaw;
        }

        $pdf = Pdf::loadView('pages.laporan.pdf-rekapitulasi', [
            'data' => $data,
            'kelas_ruangan' => $kelas_ruangan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'ruangan_pilihan' => $ruangan,
            'tahun' => $tahun,
            'periode' => $periode
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Rekapitulasi_SHRI_' . $tanggal_mulai . '_ke_' . $tanggal_selesai . '.pdf');
    }
    public function indikatorPelayanan()
    {
        $shris = Shri::get();


        dd($shris);
    }

    public function tenDiagnosaPenyakit(Request $request)
    {
        $ruanganNama = $request->input('ruangan'); // sekarang pakai nama ruangan
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode', 'semua');
        $search = $request->input('search');

        // Ambil ruangan aktif untuk dropdown - dikelompokkan berdasarkan nama
        $ruangans = Ruangan::select('nama_ruangan')
            ->where('status', '!=', 'Tidak Aktif')
            ->groupBy('nama_ruangan')
            ->orderBy('nama_ruangan')
            ->get();

        // Parse periode untuk tanggal
        if ($periode == 'semua') {
            $tanggal_mulai = Carbon::create($tahun, 1, 1)->startOfDay();
            $tanggal_selesai = Carbon::create($tahun, 12, 31)->endOfDay();
        } else {
            $bulan = str_replace('bulan_', '', $periode);
            $tanggal_mulai = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $tanggal_selesai = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
        }

        // Mulai query Diagnosa
        $query = Diagnosa::select('diagnosas.*');

        // Filter berdasarkan nama ruangan
        if ($ruanganNama) {
            $query->withCount([
                'keluars as jumlah' => function ($q) use ($tanggal_mulai, $tanggal_selesai, $ruanganNama) {
                    $q->whereBetween('tanggal_keluar', [$tanggal_mulai, $tanggal_selesai])
                        ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
                        ->join('ruangans', 'shris.kelas_perawatan_id', '=', 'ruangans.id')
                        ->where(function ($sub) use ($ruanganNama) {
                            $sub->where('ruangans.nama_ruangan', $ruanganNama)
                                ->orWhereExists(function ($subQ) use ($ruanganNama) {
                                    $subQ->select(DB::raw(1))
                                        ->from('shri_pindah')
                                        ->join('ruangans as r2', 'shri_pindah.kelas_perawatan_id', '=', 'r2.id')
                                        ->whereColumn('shri_pindah.shri_id', 'shris.id')
                                        ->where('r2.nama_ruangan', $ruanganNama);
                                });
                        });
                }
            ]);
        } else {
            // Semua ruangan
            $query->withCount([
                'keluars as jumlah' => function ($q) use ($tanggal_mulai, $tanggal_selesai) {
                    $q->whereBetween('tanggal_keluar', [$tanggal_mulai, $tanggal_selesai]);
                }
            ]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosa', 'like', "%$search%")
                    ->orWhere('kode_icd', 'like', "%$search%");
            });
        }

        $diagnosas = $query->get()
            ->where('jumlah', '>', 0)
            ->sortByDesc('jumlah')
            ->take(10)
            ->values();

        return view('pages.laporan.10_besar_penyakit', compact('diagnosas', 'ruangans'));
    }


    public function cetakTenDiagnosaPenyakit(Request $request)
    {
        $ruanganNama = $request->input('ruangan');
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode', 'semua');
        $search = $request->input('search');

        if ($periode == 'semua') {
            $tanggal_mulai = Carbon::create($tahun, 1, 1)->startOfDay();
            $tanggal_selesai = Carbon::create($tahun, 12, 31)->endOfDay();
            $label_periode = "Tahun $tahun";
        } else {
            $bulan = str_replace('bulan_', '', $periode);
            $tanggal_mulai = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $tanggal_selesai = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
            $label_periode = Carbon::parse($tanggal_mulai)->translatedFormat('F') . " $tahun";
        }

        $query = Diagnosa::select('diagnosas.*');

        if ($ruanganNama) {
            $query->withCount([
                'keluars as jumlah' => function ($q) use ($tanggal_mulai, $tanggal_selesai, $ruanganNama) {
                    $q->whereBetween('tanggal_keluar', [$tanggal_mulai, $tanggal_selesai])
                        ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
                        ->join('ruangans', 'shris.kelas_perawatan_id', '=', 'ruangans.id')
                        ->where(function ($sub) use ($ruanganNama) {
                            $sub->where('ruangans.nama_ruangan', $ruanganNama)
                                ->orWhereExists(function ($subQ) use ($ruanganNama) {
                                    $subQ->select(DB::raw(1))->from('shri_pindah')
                                        ->join('ruangans as r2', 'shri_pindah.kelas_perawatan_id', '=', 'r2.id')
                                        ->whereColumn('shri_pindah.shri_id', 'shris.id')
                                        ->where('r2.nama_ruangan', $ruanganNama);
                                });
                        });
                }
            ]);
        } else {
            $query->withCount([
                'keluars as jumlah' => function ($q) use ($tanggal_mulai, $tanggal_selesai) {
                    $q->whereBetween('tanggal_keluar', [$tanggal_mulai, $tanggal_selesai]);
                }
            ]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosa', 'like', "%$search%")->orWhere('kode_icd', 'like', "%$search%");
            });
        }

        $diagnosas = $query->get()->where('jumlah', '>', 0)->sortByDesc('jumlah')->take(20)->values();

        $pdf = Pdf::loadView('pages.laporan.pdf_10_penyakit', [
            'diagnosas' => $diagnosas,
            'filter' => [
                'ruangan' => $ruanganNama ?? 'Semua Ruangan',
                'periode' => $label_periode,
            ]
        ]);

        return $pdf->stream('Laporan_10_Besar_Penyakit.pdf');
    }




    public function getLaporanBOR(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month'); // boleh kosong (semua bulan)
        $ruanganId = $request->input('ruangan_id');

        // Tentukan rentang waktu
        if ($month) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

            // Jika bulan berjalan, batasi sampai hari ini
            if ($endDate->greaterThan(Carbon::today()->endOfDay())) {
                $endDate = Carbon::today()->endOfDay();
            }
        } else {
            // Semua bulan = setahun penuh
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
        }

        $data = $this->getLaporanIndikator($startDate, $endDate, $ruanganId, $month);

        return view('pages.laporan.indikator_pelayanan', compact('data', 'year', 'month'));
    }
    private function getLaporanIndikator($startDate, $endDate, $ruanganId = null)
    {
        // 1. Parsing tanggal dan standarisasi ke awal/akhir hari
        $dtStart = \Carbon\Carbon::parse($startDate)->startOfDay();
        $dtEnd = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Proteksi agar tidak menarik data masa depan
        if ($dtEnd->greaterThan(\Carbon\Carbon::today()->endOfDay())) {
            $dtEnd = \Carbon\Carbon::today()->endOfDay();
        }

        // HITUNG PERIODE (t): Pastikan inklusif menggunakan startOfDay pada keduanya
        // Contoh: 01 Jan s/d 01 Jan harus terhitung 1 hari.
        $jumlahPeriode = $dtStart->diffInDays($dtEnd->copy()->startOfDay()) + 1;

        // 2. Ambil daftar ruangan (Group by Nama Ruangan untuk menggabung bed per kelas)
        $ruangans = \DB::table('ruangans')
            ->select('nama_ruangan', \DB::raw('SUM(jumlah_tempat_tidur) as total_tt'))
            ->when($ruanganId, function ($q) use ($ruanganId) {
                $name = \DB::table('ruangans')->where('id', $ruanganId)->value('nama_ruangan');
                $q->where('nama_ruangan', $name);
            })
            ->groupBy('nama_ruangan')
            ->get();

        $results = $ruangans->map(function ($ruangan) use ($dtStart, $dtEnd, $jumlahPeriode) {
            $jumlahTempatTidur = $ruangan->total_tt;
            $endDateString = $dtEnd->format('Y-m-d');

            // 3. Query Data SHRI (Sensus Harian Rawat Inap)
            $stats = \DB::table('shris as s')
                ->join('ruangans as r', 's.kelas_perawatan_id', '=', 'r.id')
                ->leftJoin('shri_keluar as sk', 's.id', '=', 'sk.shri_id')
                ->leftJoin('shri_pindah as sp', 's.id', '=', 'sp.shri_id')
                ->where('r.nama_ruangan', $ruangan->nama_ruangan)
                ->where(function ($q) use ($dtStart, $dtEnd) {
                    // Pasien yang masuk di periode tersebut ATAU sudah masuk sebelum periode berakhir
                    $q->whereBetween('s.tanggal_masuk', [$dtStart, $dtEnd])
                        ->orWhere('s.tanggal_masuk', '<=', $dtEnd);
                })
                ->select([
                    // Hari Perawatan (HP): Total hari penggunaan bed oleh semua pasien dalam periode t
                    \DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL 
                            THEN DATEDIFF(sk.tanggal_keluar, s.tanggal_masuk) + 1
                        WHEN s.status = "pindah" AND sp.tanggal_pindah IS NOT NULL 
                            THEN DATEDIFF(sp.tanggal_pindah, s.tanggal_masuk) + 1
                        WHEN s.status = "masuk" 
                            THEN DATEDIFF("' . $endDateString . '", s.tanggal_masuk) + 1
                        ELSE 0
                    END
                ), 0) as total_hp'),

                    // Lama Dirawat (LD): Total hari menginap khusus untuk pasien yang sudah keluar (hidup/mati)
                    \DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL
                            THEN CAST(REPLACE(sk.lama_dirawat, " hari", "") AS UNSIGNED)
                        ELSE 0
                    END
                ), 0) as total_ld'),

                    // Agregasi Pasien Keluar
                    \DB::raw('COUNT(CASE WHEN s.status = "keluar" THEN 1 END) as total_keluar'),
                    \DB::raw('COUNT(CASE WHEN s.status = "keluar" AND sk.cara_keluar NOT LIKE "Mati%" THEN 1 END) as keluar_hidup'),
                    \DB::raw('COUNT(CASE WHEN s.status = "keluar" AND sk.cara_keluar = "Mati ≥ 48 Jam" THEN 1 END) as mati_lebih_48'),
                    \DB::raw('COUNT(CASE WHEN s.status = "keluar" AND sk.cara_keluar = "Mati ≤ 48 Jam" THEN 1 END) as mati_kurang_48'),
                ])->first();

            // 4. Inisialisasi Variabel Perhitungan
            $hp = (float) $stats->total_hp;
            $ld = (float) $stats->total_ld;
            $keluar = (int) $stats->total_keluar;
            $mati48Plus = (int) $stats->mati_lebih_48;
            $matiTotal = $mati48Plus + (int) $stats->mati_kurang_48;

            // 5. Rumus Indikator Pelayanan (Standar Depkes/Barber-Johnson)

            // BOR (Bed Occupancy Ratio): (HP / (TT * t)) * 100
            $bor = ($jumlahTempatTidur * $jumlahPeriode) > 0
                ? ($hp / ($jumlahTempatTidur * $jumlahPeriode)) * 100 : 0;

            // AVLOS (Average Length of Stay): LD / Keluar (Hidup + Mati)
            $avlos = $keluar > 0 ? $ld / $keluar : 0;

            // BTO (Bed Turn Over): Keluar (Hidup + Mati) / TT
            $bto = $jumlahTempatTidur > 0 ? $keluar / $jumlahTempatTidur : 0;

            // TOI (Turn Over Interval): ((TT * t) - HP) / Keluar (Hidup + Mati)
            $toi = $keluar > 0 ? (($jumlahTempatTidur * $jumlahPeriode) - $hp) / $keluar : 0;

            // GDR (Gross Death Rate): (Mati Total / Keluar) * 1000
            $gdr = $keluar > 0 ? ($matiTotal / $keluar) * 1000 : 0;

            // NDR (Net Death Rate): (Mati >= 48 Jam / Keluar) * 1000
            $ndr = $keluar > 0 ? ($mati48Plus / $keluar) * 1000 : 0;

            return [
                'nama_ruangan' => $ruangan->nama_ruangan,
                'jumlah_tempat_tidur' => $jumlahTempatTidur,
                'jumlah_periode' => $jumlahPeriode,
                'total_lama_dirawat' => $ld,
                'jumlah_hari_perawatan' => $hp,
                'pasien_keluar_hidup' => $stats->keluar_hidup,
                'pasien_keluar_mati_48_plus' => $mati48Plus,
                'pasien_keluar_mati_48_minus' => $stats->mati_kurang_48,
                'bor' => round($bor, 2),
                'avlos' => round($avlos, 2),
                'bto' => round($bto, 2),
                'toi' => round($toi, 2),
                'gdr' => round($gdr, 2),
                'ndr' => round($ndr, 2),
            ];
        });

        return [
            'data' => $results,
            'periode' => [
                'start_date' => $dtStart->toDateString(),
                'end_date' => $dtEnd->toDateString(),
                'jumlah_hari' => $jumlahPeriode,
            ]
        ];
    }



    // Method untuk mendapatkan data ruangan saja
    public function getRuangans()
    {
        $ruangans = DB::table('ruangans')
            ->select('id', 'nama_ruangan', 'kelas_ruangan', 'jumlah_tempat_tidur', 'status')
            ->where('status', 'aktif') // assuming ada status aktif
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ruangans
        ]);
    }

    // Method untuk mendapatkan data berdasarkan bulan dan tahun
    public function getLaporanByBulan(Request $request)
    {
        $bulan = $request->input('bulan'); // 1-12
        $tahun = $request->input('tahun', date('Y'));

        // Validasi input
        if (empty($bulan) || $bulan < 1 || $bulan > 12) {
            return response()->json([
                'error' => 'Bulan tidak valid. Gunakan angka 1-12',
                'received' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]
            ], 400);
        }

        // Buat tanggal mulai dan akhir bulan
        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $endDate = date('Y-m-t', strtotime($startDate)); // Tanggal terakhir bulan

        // Merge ke request
        $request->merge([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $this->getLaporanBOR($request);
    }

    // Method untuk mendapatkan data berdasarkan tahun penuh
    public function getLaporanByTahun(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $startDate = $tahun . '-01-01';
        $endDate = $tahun . '-12-31';

        $request->merge([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $this->getLaporanBOR($request);
    }

    public function perviewLaporanIndikatorPelayanan(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $today = Carbon::today();
        $approved = ApprovedDay::whereDate('created_at', $today)->where('jenis_laporan', 'indikator')->first();

        if (!$approved) {
            return back()->withErrors('Silahkan menunggu kepala rumah sakit untuk memverifikasi');
        }

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $ruanganId = $request->input('ruangan_id');

        $data = $this->getLaporanIndikator($startDate, $endDate, $ruanganId);

        $pdf = Pdf::loadView('pages.laporan.indikator-pdf', [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-indikator-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }

    public function exportLaporanIndikatorPelayanan(Request $request)
    {

        $today = Carbon::today();

        $approved = ApprovedDay::whereDate('created_at', $today)->where('jenis_laporan', 'indikator')->first();

        if (!$approved) {
            return back()->withErrors('Silahkan menunggu kepala rumah sakit untuk memverifikasi');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);



        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $ruanganId = $request->input('ruangan_id');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $data = $this->getLaporanIndikator($startDate, $endDate, $ruanganId);

        $ruanganNama = '-';
        if ($ruanganId) {
            $ruanganNama = Ruangan::findOrFail($ruanganId)->name;
        }

        $pdf = Pdf::loadView('pages.laporan.indikator-pdf', [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'nama_ruangan' => $ruanganNama
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-indikator-pelayanan-rs-' . $startDate . '-to-' . $endDate . '.pdf');

    }


    public function verifikasi(Request $request)
    {
        $validated = $request->validate(
            [
                'jenis_laporan' => 'required'
            ]
        );

        $today = Carbon::today();
        $approved = ApprovedDay::whereDate('created_at', $today)->where('jenis_laporan', $validated['jenis_laporan'])->first();

        if ($approved) {
            return back()->with('error', 'Kamu sudah memverifikasi laporan untuk hari ini');
        }

        ApprovedDay::create(
            [
                'status' => true,
                'jenis_laporan' => $validated['jenis_laporan'],
                'tanggal' => $today
            ]
        );

        return redirect()->back()->with('success', 'berhasil memverifikasi laporan ' . $validated['jenis_laporan']);


    }







}
