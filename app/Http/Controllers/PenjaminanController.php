<?php

namespace App\Http\Controllers;

use App\Models\Penjaminan;
use Illuminate\Http\Request;

class PenjaminanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Penjaminan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jenis_penjaminan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $penjaminans = $query->paginate(5)->withQueryString();

        return view('pages.data.penjaminan.index', compact('penjaminans', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_penjaminan' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        Penjaminan::create([
            'jenis_penjaminan' => $request->jenis_penjaminan,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Penjaminan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $penjaminan = Penjaminan::findOrFail($id);

        $request->validate([
            'jenis_penjaminan' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $penjaminan->update([
            'jenis_penjaminan' => $request->jenis_penjaminan,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Penjaminan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $penjaminan = Penjaminan::findOrFail($id);
        $penjaminan->delete();

        return back()->with('success', 'Penjaminan berhasil dihapus');
    }
}
