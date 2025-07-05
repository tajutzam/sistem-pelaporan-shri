<?php

namespace App\Http\Controllers;

use App\Models\Dpjp;
use Illuminate\Http\Request;

class DpjpController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Dpjp::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('spesialis', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $dpjps = $query->paginate(5)->withQueryString();

        return view('pages.data.dpjp.index', compact('dpjps', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'spesialis' => 'required|string|max:255',
            'status' => 'required|string|max:100',
        ]);

        Dpjp::create($request->all());

        return back()->with('success', 'Data DPJP berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $dpjp = Dpjp::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'spesialis' => 'required|string|max:255',
            'status' => 'required|string|max:100',
        ]);

        $dpjp->update($request->all());

        return back()->with('success', 'Data DPJP berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dpjp = Dpjp::findOrFail($id);
        $dpjp->delete();

        return back()->with('success', 'Data DPJP berhasil dihapus');
    }
}
