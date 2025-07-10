<?php

namespace App\Http\Controllers;

use App\Models\Dpjp;
use App\Models\Pasien;
use App\Models\Penjaminan;
use App\Models\Ruangan;
use App\Models\Shri;
use Illuminate\Http\Request;

class RegisterShriController extends Controller
{
    //start masuk
    public function masukView(Request $request)
    {
        $query = Shri::with(['pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan'])
            ->where('status', 'masuk');

        if ($request->has('search') && $request->search) {
            $search = $request->search;

            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                    ->orWhere('no_rekam_medis', 'like', '%' . $search . '%');
            });
        }

        $shris = $query->latest()->paginate(10)->withQueryString();

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
        $dpjps = Dpjp::all();
        $jaminans = Penjaminan::all();
        $ruangans = Ruangan::all();
        return view('pages.shri.masuk.create', compact('dpjps', 'jaminans', 'ruangans'));
    }

    public function masukEdit($id)
    {
        $shri = Shri::with(['pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan'])->findOrFail($id);
        $ruangans = Ruangan::all();
        $dpjps = Dpjp::all();
        $jaminans = Penjaminan::all();

        return view('pages.shri.masuk.edit', compact('shri', 'ruangans', 'dpjps', 'jaminans'));
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
            'kelas_perawatan' => 'required|exists:ruangans,id',
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

        $ruangan = Ruangan::find($validated['kelas_perawatan']);


        if (!$ruangan) {
            return back()->withErrors('ops, mohon isi ruangan terlebih dahulu');
        }

        Shri::create([
            'pasien_id' => $pasien->id,
            'dpjp_id' => $validated['dpjp'],
            'status' => $request->asal_pasien === 'pindahan-antar-ruangan' ? 'pindah' : 'masuk',
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'asal_pasien' => $validated['asal_pasien'],
            'ruang_perawatan' => $ruangan->nama_ruangan,
            'kelas_perawatan_id' => $ruangan->id,
            'jenis_penjaminan_id' => $validated['jenis_penjaminan'],
        ]);

        return redirect()->route('register-shri.masuk.view')->with('success', 'Pasien berhasil didaftarkan.');

    }





    public function pindahView()
    {
        return view("pages.shri.pindah.index");
    }


    public function keluarView()
    {
        return view("pages.shri.keluar.index");
    }

    public function pindahCreate()
    {
        return view('pages.shri.pindah.create');
    }

    public function daftarPasienDirawatPindah()
    {
        return view("pages.shri.pindah.daftar");
    }

    public function daftarPasienDirawatKeluar()
    {

    }



}
