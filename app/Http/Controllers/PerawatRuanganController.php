<?php

namespace App\Http\Controllers;

use App\Models\PerawatRuangan;
use App\Models\User;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class PerawatRuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = PerawatRuangan::with(['user', 'ruangan']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
            })->orWhereHas('ruangan', function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', '%' . $search . '%');
            });
        }

        $perawatRuangans = $query->paginate(10);
        $users = User::where('hak_akses', 'perawat')->get(); // Assuming you have role field
        $ruangans = Ruangan::where('status', 'Aktif')
            ->select('nama_ruangan')
            ->distinct()
            ->get();
        return view('pages.data.perawat_ruangan.index', compact('perawatRuangans', 'users', 'ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_ruangan' => 'required|exists:ruangans,nama_ruangan',
        ]);

        $ruanganIds = Ruangan::where('nama_ruangan', $request->nama_ruangan)->pluck('id');

        foreach ($ruanganIds as $id) {
            $exists = PerawatRuangan::where('user_id', $request->user_id)
                ->where('ruangan_id', $id)
                ->exists();

            if (!$exists) {
                PerawatRuangan::create([
                    'user_id' => $request->user_id,
                    'ruangan_id' => $id,
                ]);
            }
        }

        return redirect()->route('hak-akses-perawat.index')
            ->with('success', 'Hak akses perawat berhasil ditambahkan ke semua ruangan dengan nama yang sama');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_ruangan' => 'required|exists:ruangans,nama_ruangan',
        ]);

        $perawatRuangan = PerawatRuangan::findOrFail($id);

        $ruanganIds = Ruangan::where('nama_ruangan', $request->nama_ruangan)->pluck('id');

        foreach ($ruanganIds as $ruanganId) {
            $exists = PerawatRuangan::where('user_id', $request->user_id)
                ->where('ruangan_id', $ruanganId)
                ->where('id', '!=', $id)
                ->exists();

            if (!$exists) {
                if ($perawatRuangan->ruangan_id == $ruanganId) {
                    $perawatRuangan->update([
                        'user_id' => $request->user_id,
                        'ruangan_id' => $ruanganId,
                    ]);
                } else {
                    PerawatRuangan::create([
                        'user_id' => $request->user_id,
                        'ruangan_id' => $ruanganId,
                    ]);
                }
            }
        }

        return redirect()->route('hak-akses-perawat.index')
            ->with('success', 'Hak akses perawat berhasil diperbarui ke semua ruangan dengan nama yang sama');
    }


    public function destroy($id)
    {
        $perawatRuangan = PerawatRuangan::findOrFail($id);
        $perawatRuangan->delete();

        return redirect()->route('hak-akses-perawat.index')
            ->with('success', 'Hak akses perawat berhasil dihapus');
    }
}
