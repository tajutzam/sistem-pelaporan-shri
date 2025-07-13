<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use App\Models\Dpjp;
use App\Models\Pasien;
use App\Models\Penjaminan;
use App\Models\Ruangan;
use App\Models\Shri;
use App\Models\ShriPindah;
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


    public function masukUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_masuk' => 'required|date',
            'asal_pasien' => 'required|string',
            'kelas_perawatan' => 'required|exists:ruangans,id',
            'jenis_penjaminan' => 'required|exists:penjaminans,id',
            'dpjp' => 'required|exists:dpjps,id',
        ]);

        $shri = Shri::findOrFail($id);
        $ruangan = Ruangan::find($validated['kelas_perawatan']);

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





    public function pindahView(Request $request)
    {
        $query = ShriPindah::with(['shri.pasien', 'kelas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('shri.pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                    ->orWhere('no_rekam_medis', 'like', '%' . $search . '%');
            });
        }

        $pindahs = $query->get();

        return view("pages.shri.pindah.index", compact('pindahs'));
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
        $dirawats = Shri::where('status', 'masuk')->with('pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan')->get();
        return view("pages.shri.pindah.daftar", compact('dirawats'));
    }

    public function daftarPasienDirawatKeluar()
    {
        $dirawats = Shri::where('status', 'keluar')->with('pasien', 'dpjp', 'kelasPerawatan', 'jenisPenjaminan')->get();
        $dpjps = Dpjp::all();
        $diagnosas = Diagnosa::all();
        return view("pages.shri.keluar.daftar", compact('dirawats', 'dpjps', 'diagnosas'));
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

        $ruangan = Ruangan::where('nama_ruangan', $validated['nama_ruangan_tujuan'])->first();

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
        $shri = ShriPindah::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'berhasil menghapus data pasien pindah!');

    }

    public function pindahEdit($id)
    {
        $pindah = ShriPindah::with('shri', 'kelas')->findOrFail($id);
        return view('pages.shri.pindah.edit', compact('pindah'));

    }

    public function pindahUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'id' => 'required|exists:shri_pindah,id',
            'tanggal_pindah' => 'required|date',
            'kelas_perawatan_id' => 'required',
            'ruangan_tujuan' => 'required|exists:ruangans,nama_ruangan',
            'lama_dirawat' => 'required|numeric',
        ]);

        $ruangan = Ruangan::where('nama_ruangan', $validated['ruangan_tujuan'])->first();

        $pindah = ShriPindah::findOrFail($validated['id']);
        $pindah->update([
            'kelas_perawatan_id' => $ruangan->id,
            'tanggal_pindah' => $validated['tanggal_pindah'],
            'lama_dirawat' => $validated['lama_dirawat'],
        ]);

        return redirect()->route('register-shri.pindah.view')->with('success', 'Data pindah pasien berhasil diperbarui');
    }



}
