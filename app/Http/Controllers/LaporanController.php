<?php

namespace App\Http\Controllers;

use App\Http\Services\ShriReportService;
use App\Models\Diagnosa;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriKeluar;
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
        $query = ShriKeluar::with('shri', 'shri.pasien', 'shri.pindah', 'shri.kelasPerawatan', 'shri.jenisPenjaminan', 'dpjp', 'diagnosa');

        // Filter berdasarkan ruangan
        if ($request->filled('ruangan')) {
            $query->whereHas('shri.kelasPerawatan', function ($q) use ($request) {
                $q->where('nama_ruangan', 'like', '%' . $request->ruangan . '%');
            })->orWhereHas('shri.pindah.kelas', function ($q) use ($request) {
                $q->where('nama_ruangan', 'like', '%' . $request->ruangan . '%');
            });
        }

        // Filter berdasarkan tanggal awal
        if ($request->filled('tanggal_awal')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_awal);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_akhir);
        }

        // Pencarian berdasarkan berbagai field
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('shri.pasien', function ($subQ) use ($search) {
                    $subQ->where('no_rekam_medis', 'like', '%' . $search . '%')
                        ->orWhere('nama_pasien', 'like', '%' . $search . '%')
                        ->orWhere('jenis_kelamin', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('shri.kelasPerawatan', function ($subQ) use ($search) {
                        $subQ->where('nama_ruangan', 'like', '%' . $search . '%')
                            ->orWhere('kelas_ruangan', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('shri.pindah.kelas', function ($subQ) use ($search) {
                        $subQ->where('nama_ruangan', 'like', '%' . $search . '%')
                            ->orWhere('kelas_ruangan', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('dpjp', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('diagnosa', function ($subQ) use ($search) {
                        $subQ->where('diagnosa', 'like', '%' . $search . '%');
                    })
                    ->orWhere('tanggal_keluar', 'like', '%' . $search . '%');
            });
        }

        $query->orderBy('tanggal_keluar', 'desc');

        $laporanKunjungans = $query->get();

        return view("pages.laporan.kunjungan", compact('laporanKunjungans'));
    }


    public function cetakLaporanKunjungan(Request $request)
    {
        // Gunakan query yang sama dengan laporanKunjungan
        $query = ShriKeluar::with('shri', 'shri.pasien', 'shri.pindah', 'shri.kelasPerawatan', 'shri.jenisPenjaminan', 'dpjp', 'diagnosa');

        // Filter berdasarkan ruangan
        if ($request->filled('ruangan')) {
            $query->whereHas('shri.kelasPerawatan', function ($q) use ($request) {
                $q->where('nama_ruangan', 'like', '%' . $request->ruangan . '%');
            })->orWhereHas('shri.pindah.kelas', function ($q) use ($request) {
                $q->where('nama_ruangan', 'like', '%' . $request->ruangan . '%');
            });
        }

        // Filter berdasarkan tanggal awal
        if ($request->filled('tanggal_awal')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_awal);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_akhir);
        }

        // Pencarian berdasarkan berbagai field
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('shri.pasien', function ($subQ) use ($search) {
                    $subQ->where('no_rekam_medis', 'like', '%' . $search . '%')
                        ->orWhere('nama_pasien', 'like', '%' . $search . '%')
                        ->orWhere('jenis_kelamin', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('shri.kelasPerawatan', function ($subQ) use ($search) {
                        $subQ->where('nama_ruangan', 'like', '%' . $search . '%')
                            ->orWhere('kelas_ruangan', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('shri.pindah.kelas', function ($subQ) use ($search) {
                        $subQ->where('nama_ruangan', 'like', '%' . $search . '%')
                            ->orWhere('kelas_ruangan', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('dpjp', function ($subQ) use ($search) {
                        $subQ->where('nama_lengkap', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('diagnosa', function ($subQ) use ($search) {
                        $subQ->where('diagnosa', 'like', '%' . $search . '%');
                    })
                    ->orWhere('tanggal_keluar', 'like', '%' . $search . '%');
            });
        }

        // Urutkan berdasarkan tanggal keluar terbaru
        $query->orderBy('tanggal_keluar', 'desc');

        $laporanKunjungans = $query->get();

        // Data untuk PDF
        $data = [
            'title' => 'Laporan Rekapitulasi Kunjungan Rawat Inap',
            'date' => date('d/m/Y'),
            'laporanKunjungans' => $laporanKunjungans,
            'filters' => [
                'ruangan' => $request->ruangan,
                'tanggal_awal' => $request->tanggal_awal,
                'tanggal_akhir' => $request->tanggal_akhir,
                'search' => $request->search,
            ]
        ];

        $pdf = PDF::loadView('pages.laporan.pdf-kunjungan', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-kunjungan-rawat-inap-' . date('Y-m-d') . '.pdf');
    }


    public function rekapitulasi(Request $request)
    {
        $ruangan = $request->input('ruangan');
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode');
        $search = $request->input('search');

        // Default ke bulan ini jika tidak ada periode
        if (!$periode) {
            $periode = 'bulan_' . str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        }

        $bulan = str_replace('bulan_', '', $periode);

        // Tentukan tanggal mulai dan selesai
        $tanggal_mulai = Carbon::create($tahun, $bulan, 1)->format('Y-m-d');
        $tanggal_selesai = Carbon::create($tahun, $bulan, 1)->now()->format('Y-m-d');

        // Dapatkan kelas perawatan berdasarkan ruangan jika ada
        $kelas_perawatan_id = null;
        if ($ruangan) {
            $kelas_ruangan = DB::table('ruangans')
                ->where('nama_ruangan', 'like', "%{$ruangan}%")
                ->first();

            if ($kelas_ruangan) {
                $kelas_perawatan_id = $kelas_ruangan->id;
            }
        }

        $data = $this->shriReportService->getShriReport(
            $tanggal_mulai,
            $tanggal_selesai,
            $kelas_perawatan_id
        );

        if ($search) {
            $data = array_filter($data, function ($row) use ($search) {
                return str_contains($row['tanggal'], $search) ||
                    str_contains($row['pasien_awal'], $search) ||
                    str_contains($row['pasien_masuk'], $search);
            });
        }

        $kelas_ruangan = DB::table('ruangans')
            ->select('id', 'nama_ruangan', 'kelas_ruangan')
            ->orderBy('nama_ruangan')
            ->get();

        return view('pages.laporan.rekapitulasi', compact(
            'data',
            'ruangan',
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
        $ruangan = $request->input('ruangan');
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode');
        $tahun = date('Y'); // Default: tahun saat ini

        if (!$periode || !Str::startsWith($periode, 'bulan_')) {
            $bulan = now()->format('m');
        } else {
            $bulan = str_replace('bulan_', '', $periode);
        }

        if (!is_numeric($bulan) || (int) $bulan < 1 || (int) $bulan > 12) {
            $bulan = now()->format('m');
        }
        $tanggal_mulai = Carbon::create($tahun, $bulan, 1)->format('Y-m-d');
        $tanggal_selesai = Carbon::create($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

        $kelas_perawatan_id = null;
        $kelas_ruangan_data = null;

        if ($ruangan) {
            $kelas_ruangan_data = DB::table('ruangans')
                ->where('nama_ruangan', 'like', "%{$ruangan}%")
                ->first();

            if ($kelas_ruangan_data) {
                $kelas_perawatan_id = $kelas_ruangan_data->id;
            }
        }

        // Ambil data laporan
        $data = $this->shriReportService->getFormattedReport(
            $tanggal_mulai,
            $tanggal_selesai,
            $kelas_perawatan_id
        );

        // Ambil semua kelas ruangan untuk header tabel
        $kelas_ruangan = DB::table('ruangans')
            ->select('id', 'kelas_ruangan', 'nama_ruangan')
            ->orderBy('kelas_ruangan')
            ->get();

        $pdf = PDF::loadView('pages.laporan.pdf-rekapitulasi', [
            'data' => $data,
            'kelas_ruangan' => $kelas_ruangan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'filter_ruangan' => $ruangan // Untuk informasi filter
        ]);

        $pdf->setPaper('A4', 'landscape');

        // Generate filename dengan informasi filter
        $filename = 'rekapitulasi-shri-' . $tahun . '-' . $bulan;
        if ($ruangan) {
            $filename .= '-' . str_replace(' ', '-', strtolower($ruangan));
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

    public function indikatorPelayanan()
    {
        $shris = Shri::get();


        dd($shris);
    }

    public function tenDiagnosaPenyakit(Request $request)
    {
        $ruangan = $request->input('ruangan'); // filter kelas perawatan
        $tahun = $request->input('tahun', now()->year);
        $periode = $request->input('periode', 'semua');
        $search = $request->input('search');

        // Cari ruangan berdasarkan input
        $kelas = null;
        if ($ruangan) {
            $kelas = Ruangan::where('nama_ruangan', 'like', '%' . $ruangan . '%')->first();
        }

        // Parse periode untuk mendapatkan rentang tanggal
        if ($periode == 'semua') {
            $tanggal_mulai = Carbon::create($tahun, 1, 1)->startOfDay();
            $tanggal_selesai = Carbon::create($tahun, 12, 31)->endOfDay();
        } else {
            // Parse periode bulan
            $bulan = str_replace('bulan_', '', $periode);
            $tanggal_mulai = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $tanggal_selesai = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
        }

        // Mulai query Diagnosa
        $query = Diagnosa::select('diagnosas.*');

        // Filter berdasarkan tanggal dan ruangan
        if ($kelas) {
            $kelas_perawatan_id = $kelas->id;
            $query->withCount([
                'keluars as jumlah' => function ($q) use ($tanggal_mulai, $tanggal_selesai, $kelas_perawatan_id) {
                    $q->whereBetween('tanggal_keluar', [$tanggal_mulai, $tanggal_selesai])
                        ->join('shris', 'shri_keluar.shri_id', '=', 'shris.id')
                        ->where(function ($sub) use ($kelas_perawatan_id) {
                            $sub->where('shris.kelas_perawatan_id', $kelas_perawatan_id)
                                ->orWhereExists(function ($query) use ($kelas_perawatan_id) {
                                    $query->select(DB::raw(1))
                                        ->from('shri_pindah')
                                        ->whereColumn('shri_pindah.shri_id', 'shris.id')
                                        ->where('shri_pindah.kelas_perawatan_id', $kelas_perawatan_id);
                                });
                        });
                }
            ]);
        } else {
            // Filter berdasarkan tanggal saja (semua ruangan)
            $query->withCount([
                'keluars as jumlah' => function ($q) use ($tanggal_mulai, $tanggal_selesai) {
                    $q->whereBetween('tanggal_keluar', [$tanggal_mulai, $tanggal_selesai]);
                }
            ]);
        }

        // Filter pencarian diagnosa/ICD
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosa', 'like', "%$search%")
                    ->orWhere('kode_icd', 'like', "%$search%");
            });
        }

        // Ambil data, urutkan berdasarkan jumlah terbesar, filter yang > 0, ambil 10 besar
        $diagnosas = $query->get()
            ->where('jumlah', '>', 0)
            ->sortByDesc('jumlah')
            ->take(10)
            ->values(); // Reset index setelah take

        return view('pages.laporan.10_besar_penyakit', compact('diagnosas'));
    }



    public function getLaporanBOR(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $ruanganId = $request->input('ruangan_id');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();


        $data = $this->getLaporanIndikator($startDate, $endDate, $ruanganId);


        return view('pages.laporan.indikator_pelayanan', compact('data'));

    }


    private function getLaporanIndikator($startDate, $endDate, $ruanganId)
    {
        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        // Validasi input tanggal
        if (empty($startDate) || empty($endDate)) {
            return response()->json([
                'error' => 'Tanggal mulai dan tanggal akhir harus diisi',
                'received' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ], 400);
        }

        // Hitung jumlah hari dalam periode
        $jumlahPeriode = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        // Validasi input tanggal
        if (empty($startDate) || empty($endDate)) {
            return response()->json(['error' => 'Tanggal mulai dan tanggal akhir harus diisi'], 400);
        }

        $query = DB::table('ruangans as r')
            ->leftJoin('shris as s', function ($join) use ($startDate, $endDate) {
                $join->on('r.id', '=', 's.kelas_perawatan_id')
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('s.tanggal_masuk', [$startDate, $endDate])
                            ->orWhere('s.tanggal_masuk', '<=', $endDate);
                    });
            })
            ->leftJoin('shri_keluar as sk', function ($join) use ($startDate, $endDate) {
                $join->on('s.id', '=', 'sk.shri_id')
                    ->whereBetween('sk.tanggal_keluar', [$startDate, $endDate]);
            })
            ->leftJoin('shri_pindah as sp', function ($join) use ($startDate, $endDate) {
                $join->on('s.id', '=', 'sp.shri_id')
                    ->whereBetween('sp.tanggal_pindah', [$startDate, $endDate]);
            })
            ->select([
                'r.id as ruangan_id',
                'r.nama_ruangan',
                'r.jumlah_tempat_tidur',
                DB::raw($jumlahPeriode . ' as jumlah_periode'),

                // Jumlah hari perawatan (untuk BOR)
                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL
                        THEN DATEDIFF(sk.tanggal_keluar, s.tanggal_masuk) + 1
                        WHEN s.status = "pindah" AND sp.tanggal_pindah IS NOT NULL
                        THEN DATEDIFF(sp.tanggal_pindah, s.tanggal_masuk) + 1
                        WHEN s.status = "masuk" AND s.tanggal_masuk <= ?
                        THEN DATEDIFF(?, s.tanggal_masuk) + 1
                        ELSE 0
                    END
                ), 0) as jumlah_hari_perawatan'),

                // Total lama dirawat (untuk AvLOS)
                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL
                        THEN CAST(REPLACE(sk.lama_dirawat, " hari", "") AS UNSIGNED)
                        ELSE 0
                    END
                ), 0) as total_lama_dirawat'),

                // Pasien keluar hidup
                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL
                        AND sk.cara_keluar NOT IN ("meninggal", "mati")
                        THEN 1
                        ELSE 0
                    END
                ), 0) as pasien_keluar_hidup'),

                // Pasien keluar mati
                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL
                        AND sk.cara_keluar IN ("meninggal", "mati")
                        THEN 1
                        ELSE 0
                    END
                ), 0) as pasien_keluar_mati'),

                // Total pasien keluar
                DB::raw('COALESCE(SUM(
                    CASE
                        WHEN s.status = "keluar" AND sk.tanggal_keluar IS NOT NULL
                        THEN 1
                        ELSE 0
                    END
                ), 0) as total_pasien_keluar')
            ])
            ->addBinding([$endDate, $endDate], 'select');

        if ($ruanganId) {
            $query->where('r.id', $ruanganId);
        }

        $data = $query->groupBy('r.id', 'r.nama_ruangan', 'r.jumlah_tempat_tidur')->get();

        // Hitung indikator untuk setiap ruangan
        $results = $data->map(function ($item) {
            $jumlahTempatTidur = $item->jumlah_tempat_tidur;
            $jumlahPeriode = $item->jumlah_periode;
            $jumlahHariPerawatan = $item->jumlah_hari_perawatan;
            $totalLamaRawat = $item->total_lama_dirawat;
            $pasienKeluarHidup = $item->pasien_keluar_hidup;
            $pasienKeluarMati = $item->pasien_keluar_mati;
            $totalPasienKeluar = $item->total_pasien_keluar;

            // Perhitungan BOR (Bed Occupancy Rate)
            $bor = ($jumlahTempatTidur > 0 && $jumlahPeriode > 0)
                ? ($jumlahHariPerawatan / ($jumlahTempatTidur * $jumlahPeriode)) * 100
                : 0;

            // Perhitungan AvLOS (Average Length of Stay)
            $avlos = ($totalPasienKeluar > 0)
                ? $totalLamaRawat / $totalPasienKeluar
                : 0;

            // Perhitungan BTO (Bed Turn Over)
            $bto = ($jumlahTempatTidur > 0)
                ? $totalPasienKeluar / $jumlahTempatTidur
                : 0;

            // Perhitungan TOI (Turn Over Interval)
            $toi = ($totalPasienKeluar > 0)
                ? (($jumlahTempatTidur * $jumlahPeriode) - $jumlahHariPerawatan) / $totalPasienKeluar
                : 0;

            // Perhitungan GDR (Gross Death Rate)
            $gdr = ($totalPasienKeluar > 0)
                ? ($pasienKeluarMati / $totalPasienKeluar) * 100
                : 0;

            // Perhitungan NDR (Net Death Rate)
            $ndr = ($totalPasienKeluar > 0)
                ? ($pasienKeluarMati / $totalPasienKeluar) * 100
                : 0;

            return [
                'ruangan_id' => $item->ruangan_id,
                'nama_ruangan' => $item->nama_ruangan,
                'jumlah_tempat_tidur' => $jumlahTempatTidur,
                'jumlah_periode' => $jumlahPeriode,
                'jumlah_hari_perawatan' => $jumlahHariPerawatan,
                'total_lama_dirawat' => $totalLamaRawat,
                'pasien_keluar_hidup' => $pasienKeluarHidup,
                'pasien_keluar_mati' => $pasienKeluarMati,
                'total_pasien_keluar' => $totalPasienKeluar,
                'bor' => round($bor, 2),
                'avlos' => round($avlos, 2),
                'bto' => round($bto, 2),
                'toi' => round($toi, 2),
                'gdr' => round($gdr, 2),
                'ndr' => round($ndr, 2),
            ];
        });

        return [
            'success' => true,
            'data' => $results,
            'periode' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'jumlah_hari' => round($jumlahPeriode),
                'default_used' => $startDate ? false : true
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



        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $ruanganId = $request->input('ruangan_id');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $data = $this->getLaporanIndikator($startDate, $endDate, $ruanganId);

        $pdf = Pdf::loadView('pages.laporan.indikator-pdf', [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-indikator-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    public function exportLaporanIndikatorPelayanan(Request $request)
    {
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

        $pdf = Pdf::loadView('pages.laporan.indikator-pdf', [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-indikator-pelayanan-rs-' . $startDate . '-to-' . $endDate . '.pdf');

    }







}
