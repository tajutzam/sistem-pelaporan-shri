<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use App\Models\Dpjp;
use App\Models\Kelas;
use App\Models\Pasien;
use App\Models\Penjaminan;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriKeluar;
use App\Models\ShriPindah;
use App\Models\UserSensus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RegisterShriController extends Controller
{
    public function masukView(Request $request)
    {


        $userRuanganIds = auth()->user()
            ->perawatRuangans()
            ->pluck('ruangan_id');

        $query = Shri::with([
            'pasien',
            'dpjp',
            'kelasPerawatan' => function ($query) {
                $query->withoutGlobalScope('perawat_access');
            },
            'kelasPerawatan.kelas',
            'jenisPenjaminan'
        ])->whereIn('kelas_perawatan_id', $userRuanganIds);
        // Filter by search (nama pasien atau no rekam medis)
        if ($request->has('search') && $request->search) {
            $search = $request->search;

            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                    ->orWhere('no_rekam_medis', 'like', '%' . $search . '%');
            });
        }

        $tahun = $request->input('tahun', date('Y'));
        if ($tahun) {
            $query->whereYear('tanggal_masuk', $tahun);
        }

        $bulan = $request->input('bulan', date('n'));
        if ($bulan) {
            $query->whereMonth('tanggal_masuk', $bulan);
        }

        $shris = $query->latest('tanggal_masuk')->paginate(10)->withQueryString();

        return view('pages.shri.masuk.index', compact('shris'));
    }

    public function masukDestroy($id)
    {
        $shri = Shri::findOrFail($id);
        $shri->delete();

        return redirect()->back()->with('success', 'Data pasien berhasil dihapus.');
    }


    public function masukCreate()
    {
        $dpjps = Dpjp::where('status', "Aktif")->get();
        $jaminans = Penjaminan::where('status', "Aktif")->get();
        $ruangans = Ruangan::where('status', 'Aktif')->get();
        $kelas = Kelas::with([
            'ruangans' => function ($q) {
                $q->where('status', '!=', 'Tidak Aktif');
            }
        ])->get();
        return view('pages.shri.masuk.create', compact('dpjps', 'jaminans', 'ruangans', 'kelas'));
    }

    public function masukEdit($id)
    {
        $shri = Shri::with(['pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan'])->findOrFail($id);
        $ruangans = Ruangan::where('status', 'Aktif')->get();

        $dpjps = Dpjp::where('status', "Aktif")->get();
        $jaminans = Penjaminan::where('status', "Aktif")->get();
        $kelas = Kelas::with([
            'ruangans' => function ($q) {
                $q->where('status', '!=', 'Tidak Aktif');
            }
        ])->get();


        return view('pages.shri.masuk.edit', compact('shri', 'ruangans', 'dpjps', 'jaminans', 'kelas'));
    }


    public function masukStore(Request $request)
    {
        $validated = $request->validate([
            'no_rekam_medis' => 'required|string',
            'nama_pasien' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tanggal_masuk' => 'required|date',
            'asal_pasien' => 'required|string',
            'ruang_perawatan' => 'required|exists:ruangans,id',
            'jenis_penjaminan' => 'required|exists:penjaminans,id',
            'dpjp' => 'required|exists:dpjps,id',
            'status_pasien' => 'required',
        ]);

        $pasien = Pasien::firstOrCreate(
            ['no_rekam_medis' => $validated['no_rekam_medis']],
            [
                'nama_pasien' => $validated['nama_pasien'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'status_pasien' => $validated['status_pasien']
            ]
        );

        $sedangDirawat = Shri::where('pasien_id', $pasien->id)
            ->where('status', 'masuk')
            ->exists();

        if ($sedangDirawat) {
            return back()->withInput()->withErrors("Pasien dengan No. RM {$pasien->no_rekam_medis} masih terdaftar aktif di ruangan. Mohon selesaikan administrasi keluar atau pindah terlebih dahulu.");
        }

        $ruangan = Ruangan::find($validated['ruang_perawatan']);

        if (!$ruangan) {
            return back()->withErrors('ops, mohon isi ruangan terlebih dahulu');
        }

        $jumlahPasienAktif = Shri::where('ruang_perawatan', $ruangan->nama_ruangan)
            ->where('status', 'masuk')
            ->count();

        if ($jumlahPasienAktif >= $ruangan->jumlah_tempat_tidur) {
            return back()->withInput()->withErrors("Maaf, Ruang {$ruangan->nama_ruangan} sudah penuh (Kapasitas: {$ruangan->jumlah_tempat_tidur}).");
        }

        Shri::create([
            'pasien_id' => $pasien->id,
            'dpjp_id' => $validated['dpjp'],
            'status' => "masuk",
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'asal_pasien' => $validated['asal_pasien'],
            'ruang_perawatan' => $ruangan->nama_ruangan,
            'kelas_perawatan_id' => $ruangan->id,
            'jenis_penjaminan_id' => $validated['jenis_penjaminan'],
            'status_pasien' => $validated['status_pasien']
        ]);

        return redirect()->route('register-shri.masuk.view')->with('success', 'Pasien berhasil didaftarkan.');

    }


    public function masukUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_masuk' => 'required|date',
            'asal_pasien' => 'required|string',
            'kelas_perawatan' => 'required|exists:kelas,id',
            'ruang_perawatan' => 'required|exists:ruangans,id',
            'jenis_penjaminan' => 'required|exists:penjaminans,id',
            'dpjp' => 'required|exists:dpjps,id',
        ]);



        $shri = Shri::findOrFail($id);
        $ruangan = Ruangan::find($validated['ruang_perawatan']);

        if (!$ruangan) {
            return back()->withErrors('Ops, ruangan tidak ditemukan.');
        }

        $shri->update([
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'asal_pasien' => $validated['asal_pasien'],
            'kelas_perawatan_id' => $ruangan->id,
            'ruang_perawatan' => $ruangan->nama_ruangan,
            'jenis_penjaminan_id' => $validated['jenis_penjaminan'],
            'dpjp_id' => $validated['dpjp'],
        ]);

        return redirect()->route('register-shri.masuk.view')->with('success', 'Data pasien berhasil diperbarui.');
    }
    // end masuk




    // start pindah


    public function pindahView(Request $request)
    {

        $userRuanganIds = auth()->user()
            ->perawatRuangans()->pluck('ruangan_id');

        $query = ShriPindah::with([
            'shri.pasien',
            'ruangan' => function ($q) {
                $q->withoutGlobalScopes()->with('kelas');
            }
        ])->whereHas('shri', function ($q) use ($userRuanganIds) {
            $q->whereIn('kelas_perawatan_id', $userRuanganIds);
        });


        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('shri.pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                    ->orWhere('no_rekam_medis', 'like', '%' . $search . '%');
            });
        }

        $tahun = $request->input('tahun', date('Y'));
        if ($tahun) {
            $query->whereYear('tanggal_pindah', $tahun);
        }

        $bulan = $request->input('bulan', date('n'));
        if ($bulan) {
            $query->whereMonth('tanggal_pindah', $bulan);
        }

        $pindahs = $query->latest('tanggal_pindah')->paginate(10)->withQueryString();

        return view("pages.shri.pindah.index", compact('pindahs'));
    }


    public function daftarPasienDirawatPindah()
    {
        $userRuanganIds = auth()->user()->perawatRuangans()->pluck('ruangan_id');

        $dirawats = Shri::where('status', 'masuk')->with('pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan', 'ruangan')
            ->whereIn('kelas_perawatan_id', $userRuanganIds)
            ->get();
        $kelas = Kelas::with([
            'ruangans' => function ($q) {
                $q->withoutGlobalScopes()
                    ->where('status', '!=', 'Tidak Aktif');
            }
        ])->get();

        return view("pages.shri.pindah.daftar", compact('dirawats', 'kelas'));
    }

    public function pindahCreate()
    {
        return view('pages.shri.pindah.create');
    }


    public function pindahStore(Request $request)
    {
        $validated = $request->validate(
            [
                'nama_ruangan_tujuan' => 'required|exists:ruangans,nama_ruangan',
                'tanggal_pindah' => 'required',
                'lama_dirawat' => 'required',
                'id' => 'required|exists:shris,id'
            ]
        );


        $ruangan = Ruangan::withoutGlobalScopes()->where('nama_ruangan', $validated['nama_ruangan_tujuan'])->first();

        $created = ShriPindah::create(
            [
                'kelas_perawatan_id' => $ruangan->id,
                'lama_dirawat' => $validated['lama_dirawat'],
                'tanggal_pindah' => $validated['tanggal_pindah'],
                'shri_id' => $validated['id']
            ]
        );

        if ($created) {
            $shri = Shri::findOrFail($validated['id']);
            $shri->update(
                [
                    'status' => 'pindah'
                ]
            );
            return redirect()->route('register-shri.pindah.view')->with('success', 'Berhasil memindahkan pasien');
        }

        return redirect()->back()->withErrors('gagal memindahkan pasien');
    }

    public function pindahDestroy($id)
    {
        $shri = ShriPindah::findOrFail($id);


        Shri::findOrFail($shri->shri_id)->update(
            [
                'status' => 'masuk'
            ]
        );

        $shri->delete();

        return redirect()->back()->with('success', 'berhasil menghapus data pasien pindah!');

    }

    public function pindahEdit($id)
    {
        $pindah = ShriPindah::with([
            'shri',
            'ruangan' => function ($q) {
                $q->withoutGlobalScopes()->with('kelas');
            }
        ])->findOrFail($id);
        $kelas = Kelas::with([
            'ruangans' => function ($q) {
                $q->withoutGlobalScopes()
                    ->where('status', '!=', 'Tidak Aktif');
            }
        ])->get();

        return view('pages.shri.pindah.edit', compact('pindah', 'kelas'));
    }

    public function pindahUpdate(Request $request, $id)
    {

        $validated = $request->validate([
            'id' => 'required|exists:shri_pindah,id',
            'tanggal_pindah' => 'required|date',
            'kelas_tujuan' => 'required:exists:kelas,id',
            'ruangan_tujuan' => 'required|exists:ruangans,id',
            'lama_dirawat' => 'required|numeric',
        ]);

        $ruangan = Ruangan::where('id', $validated['ruangan_tujuan'])->withoutGlobalScopes()->first();

        $pindah = ShriPindah::findOrFail($validated['id']);
        $pindah->update([
            'kelas_perawatan_id' => $ruangan->id,
            'tanggal_pindah' => $validated['tanggal_pindah'],
            'lama_dirawat' => $validated['lama_dirawat'],
        ]);

        return redirect()->route('register-shri.pindah.view')->with('success', 'Data pindah pasien berhasil diperbarui');
    }
    // end pindah

    // start keluar
    public function keluarView(Request $request)
    {
        $query = ShriKeluar::with([
            'shri' => function ($q) {
                $q->with([
                    'pindah.ruangan' => function ($qr) {
                        $qr->withoutGlobalScopes()->with('kelas');
                    },
                    'ruangan' => function ($qr) {
                        $qr->withoutGlobalScopes()->with('kelas');
                    },
                    'pasien'
                ]);
            },
            'dpjp',
            'diagnosa'
        ]);


        if (auth()->user()->hak_akses == 'perawat') {
            $userRuanganIds = auth()->user()->perawatRuangans()->pluck('ruangan_id');

            $query->whereHas('shri', function ($q) use ($userRuanganIds) {
                $q->whereIn('kelas_perawatan_id', $userRuanganIds);
            });
        }


        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('shri.pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                    ->orWhere('no_rekam_medis', 'like', '%' . $search . '%');
            });
        }


        $tahun = $request->input('tahun', date('Y'));
        if ($tahun) {
            $query->whereYear('tanggal_keluar', $tahun);
        }

        $bulan = $request->input('bulan', date('n'));
        if ($bulan) {
            $query->whereMonth('tanggal_keluar', $bulan);
        }

        $shris = $query->latest('tanggal_keluar')
            ->paginate(10)
            ->withQueryString();

        $dpjps = Dpjp::where('status', "Aktif")->get();
        $diagnosas = Diagnosa::all();

        return view("pages.shri.keluar.index", compact('shris', 'dpjps', 'diagnosas'));
    }


    public function daftarPasienDirawatKeluar()
    {
        $userRuanganIds = auth()->user()->perawatRuangans()->pluck('ruangan_id');

        $dirawats = Shri::whereIn('status', ['masuk'])
            ->whereIn('kelas_perawatan_id', $userRuanganIds)
            ->with('pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan', 'pindah', 'pindah.ruangan')
            ->get();
        $dpjps = Dpjp::where('status', "Aktif")->get();
        $diagnosas = Diagnosa::all();
        return view("pages.shri.keluar.daftar", compact('dirawats', 'dpjps', 'diagnosas'));
    }

    public function keluarStore(Request $request)
    {
        $validated = $request->validate(
            [
                'shri_id' => 'required',
                'tanggal_keluar' => 'required|date',
                'dpjp_id' => 'required|exists:dpjps,id',
                'diagnosa_id' => 'required|exists:diagnosas,id',
                'cara_keluar' => 'required',
                'lama_dirawat' => 'required'
            ]
        );

        $shri = Shri::findOrFail($validated['shri_id'])->update(
            [
                'status' => 'keluar'
            ]
        );

        ShriKeluar::create(
            [
                'shri_id' => $validated['shri_id'],
                'dpjp_id' => $validated['dpjp_id'],
                'cara_keluar' => $validated['cara_keluar'],
                'lama_dirawat' => $validated['lama_dirawat'],
                'tanggal_keluar' => $validated['tanggal_keluar'],
                'diagnosa_id' => $validated['diagnosa_id']
            ]
        );

        return redirect()->route('register-shri.keluar.view')->with('success', 'berhasil mengeluarkan pasien!');
    }

    public function keluarUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_keluar' => 'required|date',
            'dpjp_id' => 'required|exists:dpjps,id',
            'diagnosa_id' => 'required|exists:diagnosas,id',
            'cara_keluar' => 'required',
            'lama_dirawat' => 'required'
        ]);

        $shriKeluar = ShriKeluar::findOrFail($id);
        $shri = $shriKeluar->shri;

        // Ambil tanggal masuk asli dari SHRI
        $tanggalMasuk = Carbon::parse($shri->tanggal_masuk);
        $tanggalKeluar = Carbon::parse($validated['tanggal_keluar']);


        if ($tanggalKeluar->lt($tanggalMasuk)) {
            return back()->withErrors([
                'tanggal_keluar' => 'Tanggal keluar tidak boleh sebelum tanggal masuk!'
            ])->withInput();
        }


        $shriKeluar->update([
            'tanggal_keluar' => $validated['tanggal_keluar'],
            'dpjp_id' => $validated['dpjp_id'],
            'diagnosa_id' => $validated['diagnosa_id'],
            'cara_keluar' => $validated['cara_keluar'],
            'lama_dirawat' => $validated['lama_dirawat']
        ]);

        $shriKeluar->shri()->update(['status' => 'keluar']);

        return redirect()
            ->route('register-shri.keluar.view')
            ->with('success', 'Data pasien keluar berhasil diperbarui!');
    }



    public function keluarDestroy($id)
    {

        $shriKeluar = ShriKeluar::findOrFail($id);


        $shriKeluar->shri()->update(
            [
                'status' => 'masuk'
            ]
        );

        $shriKeluar->delete();

        return redirect()->route('register-shri.keluar.view')->with('success', 'berhasil menghapus data pasien keluar');

    }


    public function cekSensusYesterday(Request $request)
    {

        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'jenis' => 'required|in:masuk,pindah,keluar'
            ]);

            $tz = config('app.timezone', 'Asia/Jakarta');
            $selected = Carbon::parse($validated['date'], $tz)->startOfDay();
            $today = Carbon::today($tz);

            // Ambil tanggal terakhir entri sensus berdasarkan jenis
            $lastDataDate = match ($validated['jenis']) {
                'masuk' => Shri::max('tanggal_masuk'),
                'pindah' => ShriPindah::max('tanggal_pindah'),
                'keluar' => ShriKeluar::max('tanggal_keluar'),
            };

            $lastEmptyDate = UserSensus::where('user_id', auth()->id())
                ->where('jenis', $validated['jenis'])
                ->where('tidak_ada_pasien', true)
                ->max('tanggal');


            // Gabungkan dua sumber untuk cari tanggal terakhir yang sudah diisi
            $lastCompleted = collect([$lastDataDate, $lastEmptyDate])
                ->filter()
                ->map(fn($d) => $d ? Carbon::parse($d, $tz)->startOfDay() : null)
                ->max();


            // Tentukan tanggal berikutnya yang boleh diisi
            $nextAllowed = $lastCompleted
                ? $lastCompleted->copy()->addDay()->startOfDay()
                : $today->copy()->startOfDay();

            // Tidak boleh pilih tanggal di masa depan
            if ($selected->gt($today)) {
                return response()->json([
                    'data' => false,
                    'message' => 'Tidak boleh memilih tanggal di masa depan.',
                    'status' => false,
                ], 422);
            }

            // Jika belum ada data sama sekali, izinkan hari ini atau H-1
            if (!$lastCompleted) {
                $yesterday = $today->copy()->subDay();
                if ($selected->isSameDay($today) || $selected->isSameDay($yesterday)) {
                    return response()->json([
                        'data' => true,
                        'message' => 'Boleh mengisi sensus.',
                        'status' => true,
                    ]);
                }

                return response()->json([
                    'data' => false,
                    'required_date' => $today->toDateString(),
                    'message' => 'Silakan mulai dari hari ini atau H-1.',
                    'status' => false,
                ], 422);
            }

            // Kalau sudah ada data sebelumnya → wajib isi tanggal berikutnya (tidak boleh lompat)
            if (!$selected->equalTo($nextAllowed)) {
                return response()->json([
                    'data' => false,
                    'required_date' => $nextAllowed->toDateString(),
                    'message' => 'Tanggal tidak berurutan. Isi tanggal yang diminta terlebih dahulu.',
                    'status' => false,
                ], 422);
            }

            return response()->json([
                'data' => true,
                'message' => 'Boleh mengisi sensus untuk tanggal ini.',
                'status' => true,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'data' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors(),
                'status' => false
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error check sensus: ' . $e->getMessage());
            return response()->json([
                'data' => false,
                'message' => 'Terjadi kesalahan saat memeriksa sensus',
                'status' => false
            ], 500);
        }
    }


    public function markEmpty(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'jenis' => 'required|in:masuk,pindah,keluar',
            ]);

            $tz = config('app.timezone', 'Asia/Jakarta');
            $tanggal = Carbon::parse($validated['date'], $tz)->toDateString();

            UserSensus::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'tanggal' => $tanggal,
                    'jenis' => $validated['jenis'],
                ],
                [
                    'tidak_ada_pasien' => true,
                ]
            );

            return response()->json([
                'data' => true,
                'message' => "Berhasil menandai tanggal {$tanggal} untuk jenis {$validated['jenis']} sebagai 'tidak ada pasien'.",
                'status' => true
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'data' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors(),
                'status' => false
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error mark empty sensus: ' . $e->getMessage());
            return response()->json([
                'data' => false,
                'message' => 'Gagal menyimpan status tidak ada pasien.' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

}
