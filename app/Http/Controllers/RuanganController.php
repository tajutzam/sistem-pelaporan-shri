<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Ruangan::query();
        $kelas = Kelas::all();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', "%{$search}%")
                    ->orWhere('kelas_ruangan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }


        $ruangans = $query->with('kelas')->paginate(5)->withQueryString();

        return view('pages.data.ruangan.index', compact('ruangans', 'search', 'kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kelas_ruangan' => 'required|exists:kelas,id',
            'jumlah_tempat_tidur' => 'required|integer|min:1',
            'status' => 'required|string|max:255',
        ]);

        $validated['kelas_ruangan_id'] = $validated['kelas_ruangan'];
        unset($validated['kelas_ruangan']);


        Ruangan::create($validated);

        return back()->with('success', 'Ruangan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kelas_ruangan' => 'required|exists:kelas,id',
            'jumlah_tempat_tidur' => 'required|integer|min:1',
            'status' => 'required|string|max:255',
        ]);

        $validated['kelas_ruangan_id'] = $validated['kelas_ruangan'];

        unset($validated['kelas_ruangan']);

        $ruangan->update($validated);

        return back()->with('success', 'Ruangan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return back()->with('success', 'Ruangan berhasil dihapus');
    }

    public function getByKelas($kelas)
    {
        $ruangans = Ruangan::where('kelas_ruangan', $kelas)
            ->where('status', 'tersedia')
            ->get(['id', 'nama_ruangan']);
        return response()->json($ruangans);
    }
}
