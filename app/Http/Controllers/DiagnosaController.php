<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use Illuminate\Http\Request;

class DiagnosaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Diagnosa::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosa', 'like', "%{$search}%")
                    ->orWhere('kode_icd', 'like', "%{$search}%");
            });
        }

        $diagnosas = $query->paginate(5)->withQueryString();

        return view('pages.data.diagnosa.index', compact('diagnosas', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'diagnosa' => 'required|string|max:255',
            'kode_icd' => 'required|string|max:100|unique:diagnosas,kode_icd',
        ]);

        Diagnosa::create($request->all());

        return back()->with('success', 'Data Diagnosa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $diagnosa = Diagnosa::findOrFail($id);

        $request->validate([
            'diagnosa' => 'required|string|max:255',
            'kode_icd' => 'required|string|max:100|unique:diagnosas,kode_icd,' . $id,
        ]);

        $diagnosa->update($request->all());

        return back()->with('success', 'Data Diagnosa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $diagnosa = Diagnosa::findOrFail($id);
        $diagnosa->delete();

        return back()->with('success', 'Data Diagnosa berhasil dihapus');
    }
}
