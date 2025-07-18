<?php

namespace App\Http\Controllers;

use App\Http\Services\ShriReportService;
use App\Models\Diagnosa;
use App\Models\Ruangan;
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
        return view('pages.laporan.indikator_pelayanan');
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





}
