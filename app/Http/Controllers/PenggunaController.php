<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::query();

        if ($search) {
            $users = $users->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('jenis_kelamin', 'like', "%{$search}%")
                    ->orWhere('hak_akses', 'like', "%{$search}%");
            });
        }

        $users = $users->paginate(5)->withQueryString();

        return view('pages.data.pengguna.index', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'hak_akses' => 'required|in:perawat,pelaporan,kepala',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'hak_akses' => $request->hak_akses,
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'hak_akses' => 'required|in:perawat,pelaporan,kepala',
            'password' => 'nullable|min:8'
        ]);

        $hashedPassword = bcrypt($request->password);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'hak_akses' => $request->hak_akses,
            'password' => $request->password ? $hashedPassword : $user->password
        ]);

        return back()->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus');
    }
}
