<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use Illuminate\Http\Request;

class APIController extends Controller
{
    //

    public function searchDiagnosa(Request $request)
    {
        $search = $request->get('q', '');
        $page = $request->get('page', 1);
        $limit = 10;

        $query = Diagnosa::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosa', 'like', "%{$search}%")
                    ->orWhere('kode_icd', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $diagnosas = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->orderBy('diagnosa')
            ->get();

        $results = $diagnosas->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->kode_icd . ' - ' . $item->diagnosa
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $limit) < $total
            ]
        ]);
    }
}
