<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Pasien::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_rekam_medis', 'like', "%{$search}%")
                    ->orWhere('nama_pasien', 'like', "%{$search}%")
                    ->orWhere('tanggal_lahir', 'like', "%{$search}%")
                    ->orWhere('jenis_kelamin', 'like', "%{$search}%");
            });
        }

        $pasiens = $query->paginate(5)->withQueryString();

        return view('pages.data.pasien.index', compact('pasiens', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_rekam_medis' => 'required|string|unique:pasiens,no_rekam_medis',
            'nama_pasien' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
        ]);

        Pasien::create($request->all());

        return back()->with('success', 'Pasien berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $request->validate([
            'no_rekam_medis' => 'required|string|unique:pasiens,no_rekam_medis,' . $id,
            'nama_pasien' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
        ]);

        $pasien->update($request->all());

        return back()->with('success', 'Pasien berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();

        return back()->with('success', 'Pasien berhasil dihapus');
    }
}
